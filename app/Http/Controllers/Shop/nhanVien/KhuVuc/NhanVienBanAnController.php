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

        // 3. Lấy danh sách đơn
        $now = Carbon::now();
        $timeStart = $now->copy()->subMinutes(30); 
        $timeEnd = $now->copy()->addMinutes(30);   

        $datBansQuery = DatBan::with(['banAn', 'nhanVien']) 
            ->where(function ($q) use ($timeStart, $timeEnd, $now) {
                $q->where('trang_thai', 'da_xac_nhan')
                    ->whereBetween('gio_den', [$timeStart, $timeEnd]);
            })
            ->orWhere(function ($q) use ($now) {
                $q->where('trang_thai', 'khach_da_den')
                    ->where('gio_den', '<=', $now);
            })
            ->orderBy('gio_den', 'asc');

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

        // 5. LOGIC CHO MODAL CHECK-IN (Tái sử dụng logic hiển thị)
        $checkInBooking = null;
        $banTrongsSorted = collect(); 
        $tongKhachCheckIn = 0;

        if ($request->has('checkin_id')) {
            $checkInBooking = DatBan::find($request->checkin_id);
            if ($checkInBooking) {
                // Gọi hàm nội bộ để lấy danh sách bàn phù hợp
                $result = $this->getSuitableTables($checkInBooking);
                $banTrongsSorted = $result['tables'];
                $tongKhachCheckIn = $result['tongKhach'];
            }
        }

        return view('shop.nhanVien.ban_khuvuc', compact('khuVucs', 'datBans', 'checkInBooking', 'banTrongsSorted', 'tongKhachCheckIn'));
    }
 
    /**
     * Hiển thị form chọn bàn (Trang riêng)
     */
    public function showCheckInForm($id)
    {
        $datBan = DatBan::findOrFail($id);
        
        // Gọi hàm logic tìm bàn
        $result = $this->getSuitableTables($datBan);
        
        $banTrongsSorted = $result['tables'];
        $tongKhach = $result['tongKhach'];

        return view('shop.nhanVien.checkin_confirm', compact('datBan', 'banTrongsSorted', 'tongKhach'));
    }

    /**
     * [MỚI] Hàm tách riêng logic tìm bàn "Best Fit" để dùng chung
     */
    private function getSuitableTables($datBan)
    {
        $tongKhach = ($datBan->nguoi_lon ?? 0) + ($datBan->tre_em ?? 0);
        if ($tongKhach == 0 && isset($datBan->so_khach)) $tongKhach = $datBan->so_khach;
        if ($tongKhach == 0) $tongKhach = 1;

        // 1. Lấy tất cả bàn đang TRỐNG (vật lý)
        $allFreeTables = BanAn::with('khuVuc')->where('trang_thai', 'trong')->get();

        // 2. Phân loại bàn
        // - Bàn Thường: Không thuộc khu 5 (Kho) và 9 (SOS)
        // - Bàn Dự Phòng: Thuộc khu 5 hoặc 9
        $standardTables = $allFreeTables->whereNotIn('khu_vuc_id', [5, 9]);
        $backupTables   = $allFreeTables->whereIn('khu_vuc_id', [5, 9]);

        $banTrongsSorted = collect();

        // --- CHIẾN THUẬT: TÌM BÀN PHÙ HỢP NHẤT (BEST FIT HIERARCHY) ---
        
        // Bước A: Kiểm tra Bàn Thường trước
        if ($standardTables->isNotEmpty()) {
            // Lấy danh sách các loại số ghế (VD: [2, 4, 6]) sắp xếp từ bé đến lớn
            $seatTypes = $standardTables->pluck('so_ghe')->unique()->sort();
            
            foreach ($seatTypes as $seats) {
                // Chỉ quan tâm nếu bàn đủ chỗ ngồi
                if ($seats >= $tongKhach) {
                    $tablesOfThisSize = $standardTables->where('so_ghe', $seats);
                    
                    if ($tablesOfThisSize->isNotEmpty()) {
                        // TÌM THẤY! 
                        // Trả về NGAY các bàn loại này (VD: chỉ trả về bàn 4 ghế nếu bàn 2 ghế đã hết)
                        // và DỪNG LẠI (không hiển thị bàn to hơn nữa để tránh lãng phí)
                        $banTrongsSorted = $tablesOfThisSize;
                        goto finish; // Nhảy xuống bước trả về
                    }
                }
            }
        }

        // Bước B: Nếu Bàn Thường hết sạch (hoặc ko có bàn nào đủ to), mới check Bàn Dự Phòng
        if ($banTrongsSorted->isEmpty() && $backupTables->isNotEmpty()) {
            $seatTypes = $backupTables->pluck('so_ghe')->unique()->sort();
            
            foreach ($seatTypes as $seats) {
                if ($seats >= $tongKhach) {
                    $tablesOfThisSize = $backupTables->where('so_ghe', $seats);
                    if ($tablesOfThisSize->isNotEmpty()) {
                        $banTrongsSorted = $tablesOfThisSize;
                        goto finish;
                    }
                }
            }
        }

        // Bước C: Nếu vẫn chưa tìm thấy (VD: Khách 20 người mà bàn to nhất chỉ 10 ghế)
        // -> Hiển thị tất cả các bàn còn lại (Bàn to lên đầu) để nhân viên tự ghép bàn
        if ($banTrongsSorted->isEmpty()) {
             $banTrongsSorted = $allFreeTables->sortByDesc('so_ghe');
        }

        finish:
        return ['tables' => $banTrongsSorted, 'tongKhach' => $tongKhach];
    }

    /**
     * Xử lý Check-in: Phân công ngẫu nhiên NV ít việc nhất
     */
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
        
        $phucVuList = \App\Models\NhanVien::where('vai_tro', 'phuc_vu')
            ->where('trang_thai', 1)
            ->withCount(['datBans' => function($q) {
                $q->where('trang_thai', 'khach_da_den');
            }])
            ->get();

        $assignedNhanVien = null;
        $messsageBonus = "";

        if ($phucVuList->isEmpty()) {
            $datBan->nhan_vien_id = Auth::id();
            $messsageBonus = "Không tìm thấy NV Phục vụ. Gán cho bạn.";
        } else {
            $minBan = $phucVuList->min('dat_bans_count');
            $ungVienTiemNang = $phucVuList->where('dat_bans_count', $minBan);

            if ($ungVienTiemNang->isNotEmpty()) {
                $assignedNhanVien = $ungVienTiemNang->random();
                $datBan->nhan_vien_id = $assignedNhanVien->id;
                $messsageBonus = "Đã phân công: " . $assignedNhanVien->ho_ten . " (Đang phục vụ: " . $minBan . " bàn)";
            } else {
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

    /**
     * Cập nhật trạng thái hàng loạt cho TẤT CẢ các bàn
     * Điều kiện: Chỉ update được những bàn đang 'trong' hoặc 'khong_su_dung'
     */
public function updateBatchStatus(Request $request)
{
    // Nhận dữ liệu là mảng các bàn cần đổi: [{id: 1, status: 'trong'}, {id: 2, status: 'khong_su_dung'}]
    $request->validate([
        'changes' => 'required|array',
        'changes.*.id' => 'required|integer',
        'changes.*.status' => 'required|in:trong,khong_su_dung',
    ]);

    try {
        $count = 0;
        foreach ($request->changes as $change) {
            // Chỉ update những bàn đang KHÔNG có khách (để an toàn)
            $updated = BanAn::where('id', $change['id'])
                ->whereIn('trang_thai', ['trong', 'khong_su_dung']) // Chỉ cho phép đổi nếu đang Trống hoặc Bảo trì
                ->update(['trang_thai' => $change['status']]);
            
            if ($updated) $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật trạng thái cho {$count} bàn."
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
}