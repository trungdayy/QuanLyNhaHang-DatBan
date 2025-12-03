<?php

namespace App\Http\Controllers\Shop\NhanVien\KhuVuc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BanAn;
use App\Models\DatBan;
use App\Models\NhanVien; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NhanVienBanAnController extends Controller
{
    /**
     * Hiển thị sơ đồ bàn theo khu vực + danh sách khách đặt trước trong ngày
     */
    public function index(Request $request)
    {
        $autoCancelAfter = 30; // phút

        // 1. Huỷ các đơn quá hạn (Logic cũ giữ nguyên)
        $donQuahan = DatBan::where('trang_thai', 'da_xac_nhan')
            ->get()
            ->filter(function($don) use ($autoCancelAfter) {
                return now()->greaterThanOrEqualTo(Carbon::parse($don->gio_den)->addMinutes($autoCancelAfter));
            });
    
        foreach ($donQuahan as $don) {
            $don->update(['trang_thai' => 'huy']);
            if ($don->banAn) {
                $don->banAn->update([
                    'trang_thai' => 'trong',
                    'giu_ban_tu' => null,
                    'giu_den' => null,
                ]);
            }
        }

        // 2. Lấy khu vực + bàn
        $khuVucs = \App\Models\KhuVuc::with('banAns')->get();

        // 3. Lấy danh sách đơn
        $now = Carbon::now();
        
        // --- SỬA LOGIC THỜI GIAN: Trước và Sau 30 phút ---
        $timeStart = $now->copy()->subMinutes(30); // Thời điểm hiện tại lùi lại 30p
        $timeEnd = $now->copy()->addMinutes(30);   // Thời điểm hiện tại cộng thêm 30p

        $datBansQuery = DatBan::with(['banAn', 'nhanVien']) 
            ->where(function ($q) use ($timeStart, $timeEnd, $now) {
                // Đơn đã xác nhận: nằm trong khoảng [Now - 30p, Now + 30p]
                $q->where('trang_thai', 'da_xac_nhan')
                    ->whereBetween('gio_den', [$timeStart, $timeEnd]);
            })
            ->orWhere(function ($q) use ($now) {
                // Đơn khách đã đến: vẫn giữ logic cũ (hiển thị để biết ai đang ngồi)
                $q->where('trang_thai', 'khach_da_den')
                    ->where('gio_den', '<=', $now);
            })
            ->orderBy('gio_den', 'asc');

        // Nếu có search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $datBansQuery->where(function($q) use ($search) {
                $q->where('ten_khach', 'like', "%{$search}%")
                  ->orWhere('ma_dat_ban', 'like', "%{$search}%")
                  ->orWhere('sdt_khach', 'like', "%{$search}%");
            });
        }
    
        $datBans = $datBansQuery->get();

        // 4. Gắn thông tin khách đang ngồi vào bàn
        foreach ($khuVucs as $khu) {
            foreach ($khu->banAns as $ban) {
                $khach = $datBans->where('ban_id', $ban->id)
                                 ->where('trang_thai', 'khach_da_den')
                                 ->first();
                if ($khach) {
                    $ban->khach_dang_ngoi = $khach->ten_khach;
                    $ban->gio_bat_dau = $khach->gio_den;
                } else {
                    $ban->khach_dang_ngoi = null;
                    $ban->gio_bat_dau = null;
                }
            }
        }

        // 5. LOGIC CHO MODAL CHECK-IN
        $checkInBooking = null;
        $banTrongsSorted = null;
        $tongKhachCheckIn = 0;

        if ($request->has('checkin_id')) {
            $checkInBooking = DatBan::find($request->checkin_id);
            
            if ($checkInBooking) {
                $tongKhachCheckIn = ($checkInBooking->nguoi_lon ?? 0) + ($checkInBooking->tre_em ?? 0);
                if ($tongKhachCheckIn == 0 && isset($checkInBooking->so_khach)) $tongKhachCheckIn = $checkInBooking->so_khach;
                if ($tongKhachCheckIn == 0) $tongKhachCheckIn = 1;

                $banTrongs = \App\Models\BanAn::with('khuVuc')->where('trang_thai', 'trong')->get();
                
                $banTrongsSorted = $banTrongs->sortBy(function ($ban) use ($tongKhachCheckIn) {
                    if ($ban->so_ghe >= $tongKhachCheckIn) {
                        return $ban->so_ghe;
                    } else {
                        return 1000 + $ban->so_ghe;
                    }
                });
            }
        }

        return view('shop.nhanVien.ban_khuvuc', compact('khuVucs', 'datBans', 'checkInBooking', 'banTrongsSorted', 'tongKhachCheckIn'));
    }
 
   // --- Hiển thị form chọn bàn (Giữ nguyên) ---
   public function showCheckInForm($id)
   {
       $datBan = DatBan::findOrFail($id);
       
       $tongKhach = ($datBan->nguoi_lon ?? 0) + ($datBan->tre_em ?? 0);
       if ($tongKhach == 0 && isset($datBan->so_khach)) $tongKhach = $datBan->so_khach;
       if ($tongKhach == 0) $tongKhach = 1;

       $banTrongs = BanAn::with('khuVuc')->where('trang_thai', 'trong')->get();

       $banTrongsSorted = $banTrongs->sortBy(function ($ban) use ($tongKhach) {
           if ($ban->so_ghe >= $tongKhach) {
               return $ban->so_ghe; 
           } else {
               return 1000 + $ban->so_ghe;
           }
       });

       return view('shop.nhanVien.checkin_confirm', compact('datBan', 'banTrongsSorted', 'tongKhach'));
   }

   // --- SỬA: Xử lý Check-in với Logic phân công ngẫu nhiên người ít việc nhất ---
   public function processCheckIn(Request $request)
   {
       $request->validate([
           'dat_ban_id' => 'required|exists:dat_ban,id',
           'ban_id'     => 'required|exists:ban_an,id',
       ]);

       $datBan = DatBan::findOrFail($request->dat_ban_id);
       $banMoi = BanAn::with('khuVuc')->findOrFail($request->ban_id);

       if ($banMoi->trang_thai !== 'trong') {
           return redirect()->route('nhanVien.ban-an.index')
               ->with('error', 'Bàn ' . $banMoi->so_ban . ' vừa có người nhận rồi!');
       }

       // 1. Xử lý bàn cũ (nếu có)
       if ($datBan->ban_id && $datBan->ban_id != $banMoi->id) {
           BanAn::where('id', $datBan->ban_id)->update(['trang_thai' => 'trong']);
       }

       // ====================================================
       // === LOGIC PHÂN CÔNG: NGẪU NHIÊN NGƯỜI ÍT BÀN NHẤT ===
       // ====================================================
       
       // Lấy danh sách phục vụ đang đi làm + đếm số đơn đang phục vụ (khach_da_den)
       $phucVuList = \App\Models\NhanVien::where('vai_tro', 'phuc_vu')
           ->where('trang_thai', 1)
           ->withCount(['datBans' => function($q) {
               $q->where('trang_thai', 'khach_da_den');
           }])
           ->get();

       $assignedNhanVien = null;
       $messsageBonus = "";

       if ($phucVuList->isEmpty()) {
           // Không có nhân viên phục vụ nào -> Gán cho người đang login
           $datBan->nhan_vien_id = Auth::id();
           $messsageBonus = "Không tìm thấy NV Phục vụ. Gán cho bạn.";
       } else {
           // Tìm số bàn ít nhất hiện tại (ví dụ: min là 0, hoặc 1, hoặc 5...)
           $minBan = $phucVuList->min('dat_bans_count');

           // Lọc ra danh sách các nhân viên đang có số bàn = min
           $ungVienTiemNang = $phucVuList->where('dat_bans_count', $minBan);

           // Chọn ngẫu nhiên 1 người trong số những người ít việc nhất
           if ($ungVienTiemNang->isNotEmpty()) {
               $assignedNhanVien = $ungVienTiemNang->random();
               
               $datBan->nhan_vien_id = $assignedNhanVien->id;
               $messsageBonus = "Đã phân công: " . $assignedNhanVien->ho_ten . " (Đang phục vụ: " . $minBan . " bàn)";
           } else {
               // Fallback an toàn (hiếm khi xảy ra)
               $datBan->nhan_vien_id = Auth::id();
           }
       }
       // ====================================================

       // 2. Update dữ liệu
       $datBan->ban_id = $banMoi->id;
       $datBan->trang_thai = 'khach_da_den';
       $datBan->gio_den = Carbon::now();
       $datBan->save();

       $banMoi->update(['trang_thai' => 'dang_phuc_vu']);

       return redirect()->route('nhanVien.ban-an.index')
           ->with('success', "Check-in bàn {$banMoi->so_ban}. $messsageBonus");
   }

    /**
     * Reset bàn
     */
    public function resetBan($id)
    {
        $ban = BanAn::findOrFail($id);

        $ban->update([
            'trang_thai' => 'trong',
            'giu_ban_tu' => null,
            'giu_den' => null,
        ]);

        $datBan = DatBan::where('ban_id', $ban->id)
                        ->whereIn('trang_thai', ['da_xac_nhan', 'khach_da_den', 'da_dat'])
                        ->first();

        if ($datBan) {
            $datBan->update(['trang_thai' => 'huy']);
        }

        return redirect()->back()->with('success', "Bàn {$ban->so_ban} đã được reset về trống!");
    }
}