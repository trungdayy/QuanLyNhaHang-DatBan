<?php

namespace App\Http\Controllers\Shop\NhanVien\KhuVuc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BanAn;
use App\Models\DatBan;
use App\Models\NhanVien; 
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;


class NhanVienBanAnController extends Controller
{
    /**
     * Hiển thị sơ đồ bàn theo khu vực + danh sách khách đặt trước trong ngày
     */

    public function index(Request $request)
    {
        $autoCancelAfter = 30; // phút

        // 1. Huỷ các đơn quá hạn
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

        // 3. Lấy danh sách đơn (Có thêm quan hệ 'nhanVien' để hiển thị tên PV)
        $now = Carbon::now();
        $thirtyMinutesLater = $now->copy()->addMinutes(30);

        // --- SỬA Ở ĐÂY: Thêm 'nhanVien' vào with ---
        $datBansQuery = DatBan::with(['banAn', 'nhanVien']) 
            ->where(function ($q) use ($now, $thirtyMinutesLater) {
                // Đơn đã xác nhận — trong 30 phút tới
                $q->where('trang_thai', 'da_xac_nhan')
                    ->whereBetween('gio_den', [$now, $thirtyMinutesLater]);
            })
            ->orWhere(function ($q) use ($now) {
                // Đơn khách đã đến — giờ đến <= hiện tại
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

        // 4. Gắn thông tin khách đang ngồi vào bàn (Support legacy code)
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

        // ============================================================
        // 5. LOGIC CHO MODAL CHECK-IN (Khi bấm nút Check-in Link)
        // ============================================================
        $checkInBooking = null;
        $banTrongsSorted = null;
        $tongKhachCheckIn = 0;

        if ($request->has('checkin_id')) {
            $checkInBooking = DatBan::find($request->checkin_id);
            
            if ($checkInBooking) {
                // Tính toán số khách
                $tongKhachCheckIn = ($checkInBooking->nguoi_lon ?? 0) + ($checkInBooking->tre_em ?? 0);
                if ($tongKhachCheckIn == 0 && isset($checkInBooking->so_khach)) $tongKhachCheckIn = $checkInBooking->so_khach;
                if ($tongKhachCheckIn == 0) $tongKhachCheckIn = 1;

                // Lấy list bàn trống và Sắp xếp
                $banTrongs = \App\Models\BanAn::with('khuVuc')->where('trang_thai', 'trong')->get();
                
                $banTrongsSorted = $banTrongs->sortBy(function ($ban) use ($tongKhachCheckIn) {
                    if ($ban->so_ghe >= $tongKhachCheckIn) {
                        return $ban->so_ghe; // Ngồi vừa: Ưu tiên bàn nhỏ trước
                    } else {
                        return 1000 + $ban->so_ghe; // Không vừa: Đẩy xuống cuối
                    }
                });
            }
        }

        // Trả về view kèm các biến mới
        return view('shop.nhanVien.ban_khuvuc', compact('khuVucs', 'datBans', 'checkInBooking', 'banTrongsSorted', 'tongKhachCheckIn'));
    }
 
   // --- THÊM MỚI: Hiển thị form chọn bàn ---
   public function showCheckInForm($id)
   {
       // 1. Lấy thông tin đơn đặt bàn
       $datBan = DatBan::findOrFail($id);
       
       // Tính tổng khách để gợi ý
       $tongKhach = ($datBan->nguoi_lon ?? 0) + ($datBan->tre_em ?? 0);
       if ($tongKhach == 0 && isset($datBan->so_khach)) $tongKhach = $datBan->so_khach; // Fallback nếu dùng cột so_khach
       if ($tongKhach == 0) $tongKhach = 1; // Mặc định tối thiểu 1

       // 2. Lấy danh sách bàn TRỐNG, kèm theo khu vực
       // Eager load khu vực để hiển thị tên khu vực
       $banTrongs = BanAn::with('khuVuc')
           ->where('trang_thai', 'trong') // Chỉ lấy bàn trống
           ->get();

       // 3. LOGIC NÂNG CAO: Sắp xếp gợi ý (Best fit)
       // - Ưu tiên 1: Bàn có số ghế >= tổng khách (Ngồi vừa)
       // - Ưu tiên 2: Trong các bàn ngồi vừa, chọn bàn có số ghế nhỏ nhất (Tránh lãng phí bàn to)
       // - Các bàn không ngồi vừa (quá nhỏ) vứt xuống cuối danh sách
       $banTrongsSorted = $banTrongs->sortBy(function ($ban) use ($tongKhach) {
           if ($ban->so_ghe >= $tongKhach) {
               // Nhóm ngồi vừa: Trọng số nhỏ (ưu tiên cao). 
               // Cộng thêm số ghế để bàn 4 xếp trước bàn 6 (với khách 2 người)
               return $ban->so_ghe; 
           } else {
               // Nhóm không ngồi vừa: Trọng số cực lớn để đẩy xuống cuối
               return 1000 + $ban->so_ghe;
           }
       });

       return view('shop.nhanVien.checkin_confirm', compact('datBan', 'banTrongsSorted', 'tongKhach'));
   }

   // --- THÊM MỚI: Xử lý Check-in ---
   public function processCheckIn(Request $request)
   {
       $request->validate([
           'dat_ban_id' => 'required|exists:dat_ban,id',
           'ban_id'     => 'required|exists:ban_an,id',
       ]);

       $datBan = DatBan::findOrFail($request->dat_ban_id);
       $banMoi = BanAn::with('khuVuc')->findOrFail($request->ban_id); // Eager load khu vực để lấy tầng
       $tangHienTai = $banMoi->khuVuc->tang; // Lấy tầng của bàn mới

       if ($banMoi->trang_thai !== 'trong') {
           return redirect()->route('nhanVien.ban-an.index')
               ->with('error', 'Bàn ' . $banMoi->so_ban . ' vừa có người nhận rồi!');
       }

       // 1. Xử lý bàn cũ
       if ($datBan->ban_id && $datBan->ban_id != $banMoi->id) {
           BanAn::where('id', $datBan->ban_id)->update(['trang_thai' => 'trong']);
       }

       // ====================================================
       // === LOGIC PHÂN CÔNG THÔNG MINH (CHỐT TẦNG) ===
       // ====================================================
       
       // Lấy tất cả nhân viên phục vụ đang đi làm
       $phucVuList = \App\Models\NhanVien::where('vai_tro', 'phuc_vu')
           ->where('trang_thai', 1)
           ->with(['datBans' => function($q) {
               // Chỉ lấy các đơn đang ăn để check xem nhân viên đang ở đâu
               $q->where('trang_thai', 'khach_da_den')->with('banAn.khuVuc');
           }])
           ->get();

       // Lọc nhân viên phù hợp
       $ungVien = $phucVuList->filter(function($nv) use ($tangHienTai) {
           $soBanDangPhucVu = $nv->datBans->count();

           // Điều kiện 1: Đã full 2 bàn -> Loại
           if ($soBanDangPhucVu >= 2) return false;

           // Điều kiện 2: Đang rảnh (0 bàn) -> Duyệt luôn (Có thể điều đi tầng nào cũng được)
           if ($soBanDangPhucVu == 0) return true;

           // Điều kiện 3: Đang phục vụ 1 bàn -> Check xem bàn đó có CÙNG TẦNG không?
           $banDangPhucVu = $nv->datBans->first()->banAn;
           if ($banDangPhucVu && $banDangPhucVu->khuVuc->tang == $tangHienTai) {
               return true; // Cùng tầng -> Duyệt
           }

           return false; // Khác tầng -> Loại (Không cho chạy tầng)
       });

       // Ưu tiên: Chọn người đang ở sẵn tầng đó (đang có 1 bàn) để tối ưu di chuyển,
       // nếu không có ai thì mới lấy người đang rảnh (0 bàn).
       $assignedNhanVien = $ungVien->sortByDesc(function($nv) {
           return $nv->datBans->count(); // Sắp xếp người có 1 bàn lên trước, người 0 bàn xuống sau
       })->first();

       $messsageBonus = "";
       if ($assignedNhanVien) {
           $datBan->nhan_vien_id = $assignedNhanVien->id;
           $messsageBonus = "Đã phân công: " . $assignedNhanVien->ho_ten . " (Tầng $tangHienTai)";
       } else {
           $datBan->nhan_vien_id = Auth::id(); // Không có ai thì gán tạm cho người đang thao tác
           $messsageBonus = "Full nhân sự tầng $tangHienTai. Gán tạm cho bạn.";
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

    public function checkNotifications()
{
    // 1. Lấy tất cả các bàn đang phục vụ có khách
    // Dùng with() để load luôn thông tin Đặt bàn và Nhân viên phụ trách để đỡ query nhiều lần
    $activeTables = BanAn::where('trang_thai', 'dang_phuc_vu')
        ->whereHas('datBan', function($q) {
            $q->where('trang_thai', 'khach_da_den');
        })
        ->with(['datBan' => function($q) {
            $q->where('trang_thai', 'khach_da_den')->with('nhanVien');
        }])
        ->get();

    $callingTables = [];

    foreach ($activeTables as $table) {
        // Kiểm tra Cache xem bàn này có đang "kêu" không
        if (Cache::has('goi_nhan_vien_' . $table->id)) {
            
            // Lấy thông tin nhân viên từ đơn đặt bàn (nếu có)
            $datBan = $table->datBan->first(); 
            $tenNhanVien = 'Chưa gán';
            $idNhanVien = null;

            if ($datBan && $datBan->nhanVien) {
                $tenNhanVien = $datBan->nhanVien->ho_ten;
                $idNhanVien = $datBan->nhanVien->id;
            }

            $callingTables[] = [
                'id' => $table->id,
                'so_ban' => $table->so_ban,
                'nhan_vien_phu_trach' => $tenNhanVien, // Tên nhân viên
                'nhan_vien_id' => $idNhanVien // ID nhân viên để ông dùng nếu cần so sánh
            ];
        }
    }

    return response()->json($callingTables);
}
}
