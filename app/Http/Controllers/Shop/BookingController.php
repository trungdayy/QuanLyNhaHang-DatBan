<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ComboBuffet;
use App\Models\KhuVuc;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    /** TRANG CHÍNH: Hiển thị danh sách booking của khách và form chọn phương thức đặt bàn */
    public function index()
    {
        $sdt = session('sdt_khach', null);
        $datBans = $sdt ? DatBan::where('sdt_khach', $sdt)->orderByDesc('created_at')->get() : collect();

        $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
        // Chỉ loại bàn đang bận hoặc đã đặt
        $banAns = BanAn::whereNotIn('trang_thai', ['dang_su_dung', 'da_dat'])->get();
        $khuVucs = KhuVuc::all();

        return view('restaurants.booking.index', compact('datBans', 'sdt', 'combos', 'banAns', 'khuVucs'));
    }

    /** FORM ĐẶT BÀN ONLINE */
    public function create()
    {
        $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
        $banAns = BanAn::whereNotIn('trang_thai', ['dang_su_dung', 'da_dat'])->get();
        $khuVucs = KhuVuc::all();

        return view('restaurants.booking.create', compact('combos', 'banAns', 'khuVucs'));
    }

    /** LƯU ĐẶT BÀN MỚI */
    public function store(Request $request)
    {
        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'so_khach' => 'required|integer|min:1',
            'combo_id' => 'nullable|exists:combo_buffet,id',
            'ban_id' => 'nullable|exists:ban_an,id',
            'gio_den' => 'required|date',
            'ghi_chu' => 'nullable|string',
        ]);

        // Lưu session số điện thoại
        session(['sdt_khach' => $request->sdt_khach]);

        $combo_id = $request->combo_id ?: null;
        $ban_id = $request->ban_id ?: null;

        // Kiểm tra trùng bàn nếu có chọn bàn
        if ($ban_id) {
            $duration = 120; // phút
            $gioDen = Carbon::parse($request->gio_den);

            $conflict = DatBan::where('ban_id', $ban_id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                ->whereBetween('gio_den', [
                    $gioDen->copy()->subMinutes($duration - 1),
                    $gioDen->copy()->addMinutes($duration - 1)
                ])
                ->first();

            if ($conflict) {
                $gioBiTrung = Carbon::parse($conflict->gio_den)->format('H:i d/m/Y');
                return back()->withInput()->with('error', "Bàn đã được đặt lúc $gioBiTrung.");
            }
        }

        $maDatBan = 'DB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        DatBan::create([
            'ma_dat_ban' => $maDatBan,
            'ten_khach' => $request->ten_khach,
            'sdt_khach' => $request->sdt_khach,
            'so_khach' => $request->so_khach,
            'combo_id' => $combo_id,
            'ban_id' => $ban_id,
            'gio_den' => $request->gio_den,
            'thoi_luong_phut' => 120,
            'ghi_chu' => $request->ghi_chu,
            'tien_coc' => 0,
            'la_dat_online' => 1,
            'trang_thai' => 'cho_xac_nhan',
        ]);

        return redirect()->route('booking.success')->with('success', 'Đặt bàn thành công! Chờ nhà hàng xác nhận.');
    }

    /** FORM SỬA ĐƠN */
    public function edit($id)
    {
        $datBan = DatBan::findOrFail($id);
        $sdt = session('sdt_khach', null);

        if (!in_array($datBan->trang_thai, ['cho_xac_nhan']) || $datBan->sdt_khach !== $sdt) {
            return redirect()->route('booking')->with('error', 'Không thể sửa đơn này.');
        }

        $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
        $banAns = BanAn::whereNotIn('trang_thai', ['dang_su_dung', 'da_dat'])->get();
        $khuVucs = KhuVuc::all();

        return view('restaurants.booking.edit', compact('datBan', 'combos', 'banAns', 'khuVucs'));
    }

    /** CẬP NHẬT ĐƠN */
    public function update(Request $request, $id)
    {
        $datBan = DatBan::findOrFail($id);
        $sdt = session('sdt_khach', null);

        if (!in_array($datBan->trang_thai, ['cho_xac_nhan']) || $datBan->sdt_khach !== $sdt) {
            return back()->with('error', 'Không thể sửa đơn này.');
        }

        $request->validate([
            'ten_khach' => 'required|string|max:255',
            'sdt_khach' => 'required|string|max:20',
            'so_khach' => 'required|integer|min:1',
            'combo_id' => 'nullable|exists:combo_buffet,id',
            'ban_id' => 'nullable|exists:ban_an,id',
            'gio_den' => 'required|date',
            'ghi_chu' => 'nullable|string',
        ]);

        $combo_id = $request->combo_id ?: null;
        $ban_id = $request->ban_id ?: null;

        // Kiểm tra trùng bàn
        if ($ban_id) {
            $duration = 120;
            $gioDen = Carbon::parse($request->gio_den);

            $conflict = DatBan::where('ban_id', $ban_id)
                ->where('id', '!=', $datBan->id)
                ->whereNotIn('trang_thai', ['huy', 'hoan_tat'])
                ->whereBetween('gio_den', [
                    $gioDen->copy()->subMinutes($duration - 1),
                    $gioDen->copy()->addMinutes($duration - 1)
                ])
                ->first();

            if ($conflict) {
                $gioBiTrung = Carbon::parse($conflict->gio_den)->format('H:i d/m/Y');
                return back()->withInput()->with('error', "Bàn đã được đặt lúc $gioBiTrung.");
            }
        }

        $datBan->update([
            'ten_khach' => $request->ten_khach,
            'sdt_khach' => $request->sdt_khach,
            'so_khach' => $request->so_khach,
            'combo_id' => $combo_id,
            'ban_id' => $ban_id,
            'gio_den' => $request->gio_den,
            'ghi_chu' => $request->ghi_chu,
        ]);

        return redirect()->route('booking')->with('success', 'Cập nhật đặt bàn thành công.');
    }

    /** HỦY ĐƠN */
    public function destroy($id)
    {
        $datBan = DatBan::findOrFail($id);
        $sdt = session('sdt_khach', null);

        if (!in_array($datBan->trang_thai, ['cho_xac_nhan']) || $datBan->sdt_khach !== $sdt) {
            return back()->with('error', 'Không thể hủy đơn này.');
        }

        $datBan->update(['trang_thai' => 'huy']);
        return redirect()->route('booking')->with('success', 'Đơn đã được hủy.');
    }

    /** TRANG THÀNH CÔNG */
    public function success()
    {
        $sdt = session('sdt_khach', null);
        $datBans = $sdt ? DatBan::where('sdt_khach', $sdt)->orderByDesc('created_at')->get() : collect();
        $combos = ComboBuffet::where('trang_thai', 'dang_ban')->get();
        $banAns = BanAn::whereNotIn('trang_thai', ['dang_su_dung', 'da_dat'])->get();
        $khuVucs = KhuVuc::all();

        return view('restaurants.booking.index', compact('datBans', 'sdt', 'combos', 'banAns', 'khuVucs'))
            ->with('success', 'Đặt bàn thành công! Chờ nhà hàng xác nhận.');
    }
}
