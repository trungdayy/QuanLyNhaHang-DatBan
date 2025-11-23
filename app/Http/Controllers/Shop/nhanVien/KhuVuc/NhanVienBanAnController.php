<?php

namespace App\Http\Controllers\Shop\NhanVien\KhuVuc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BanAn;
use App\Models\DatBan;
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
    
        // Huỷ các đơn quá hạn
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
    
        // Lấy khu vực + bàn
        $khuVucs = \App\Models\KhuVuc::with('banAns')->get();
    
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
    
        // Lấy các đơn trong ngày + khách đã check-in
        $datBansQuery = DatBan::whereBetween('gio_den', [$today, $tomorrow])
            ->whereIn('trang_thai', ['da_xac_nhan', 'khach_da_den'])
            ->with('banAn')
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
    
        // Gắn thông tin khách đang ngồi vào bàn
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
    
        return view('shop.nhanVien.ban_khuvuc', compact('khuVucs', 'datBans'));
    }
    

    /**
     * Walk-in: khách tới trực tiếp, bàn trống
     */
    public function checkInWalkIn(Request $request)
    {
        $request->validate([
            'ban_id' => 'required|exists:ban_an,id',
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'so_khach' => 'required|integer|min:1',
        ]);

        $ban = BanAn::findOrFail($request->ban_id);

        if ($ban->trang_thai === 'khong_su_dung') {
            return redirect()->back()->with('error', 'Bàn không sử dụng.');
        }

        $ban->update([
            'trang_thai' => 'dang_phuc_vu'
        ]);

        DatBan::create([
            'ban_id' => $ban->id,
            'ten_khach' => $request->ten_khach,
            'sdt_khach' => $request->sdt_khach,
            'so_khach' => $request->so_khach,
            'gio_den' => now(),
            'trang_thai' => 'da_xac_nhan', // Walk-in đã xác nhận
            'nhan_vien_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', "Check-in Walk-in khách {$request->ten_khach} thành công!");
    }

    /**
     * Check-in khách đặt trước
     */
    public function checkInDatTruoc(Request $request)
    {
        $request->validate([
            'dat_ban_id' => 'required|exists:dat_ban,id',
        ]);

        $datBan = DatBan::findOrFail($request->dat_ban_id);

        $datBan->trang_thai = 'khach_da_den';
        $datBan->nhan_vien_id = Auth::id();
        $datBan->save();

        // Cập nhật trạng thái bàn
        $ban = $datBan->banAn;
        if ($ban) {
            $ban->trang_thai = 'dang_phuc_vu';
            $ban->save();
        }

        return redirect()->back()->with('success', "Check-in khách đặt trước {$datBan->ten_khach} thành công!");
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
