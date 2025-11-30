<?php

namespace App\Http\Controllers\Shop\Booking;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    /** ============================
     *  HIỂN THỊ DANH SÁCH ĐẶT BÀN
     *  ============================ */
    /** ============================
     *  HIỂN THỊ DANH SÁCH ĐẶT BÀN THEO SĐT
     *  ============================ */
    public function index(Request $request)
    {
        // Lấy số điện thoại khách từ query string hoặc session
        $sdt = $request->query('sdt') ?? session('sdt_khach', null);

        $datBans = collect();

        if ($sdt) {
            // Lưu session để khách không phải nhập lại
            session(['sdt_khach' => $sdt]);

            // Lấy booking trong 10 ngày gần đây
            $datBans = DatBan::where('sdt_khach', $sdt)
                ->where('gio_den', '>=', now()->subDays(10))
                ->orderByDesc('gio_den')
                ->get();
        }

        return view('restaurants.booking.index', compact('datBans', 'sdt'));
    }


    /** ============================
     *  TRANG TẠO MỚI
     *  ============================ */
    public function create()
    {
        return view('restaurants.booking.create');
    }

    /** ============================
     *  LƯU ĐẶT BÀN
     *  ============================ */
    public function store(Request $request)
    {
        $request->validate([
            'ten_khach'   => 'required|string|max:255',
            'sdt_khach'   => 'required|string|max:20',
            'gio_den'     => 'required|date',
            'nguoi_lon'   => 'required|integer|min:0',
            'tre_em'      => 'required|integer|min:0',
        ]);

        DatBan::create([
            'ten_khach'   => $request->ten_khach,
            'sdt_khach'   => $request->sdt_khach,
            'gio_den'     => $request->gio_den,
            'nguoi_lon'   => $request->nguoi_lon,
            'tre_em'      => $request->tre_em,
        ]);

        return redirect()->route('booking.index')
            ->with('success', 'Tạo đặt bàn thành công!');
    }

    /** ============================
     *  TRANG CHỈNH SỬA
     *  ============================ */
    public function edit($id)
    {
        $datBan = DatBan::findOrFail($id);
        return view('restaurants.booking.edit', compact('datBan'));
    }

    /** ============================
     *  CẬP NHẬT ĐẶT BÀN
     *  ============================ */
    public function update(Request $request, $id)
    {
        $request->validate([
            'ten_khach'   => 'required|string|max:255',
            'sdt_khach'   => 'required|string|max:20',
            'gio_den'     => 'required|date',
            'nguoi_lon'   => 'required|integer|min:0',
            'tre_em'      => 'required|integer|min:0',
        ]);

        $datBan = DatBan::findOrFail($id);

        $datBan->update([
            'ten_khach'   => $request->ten_khach,
            'sdt_khach'   => $request->sdt_khach,
            'gio_den'     => $request->gio_den,
            'nguoi_lon'   => $request->nguoi_lon,
            'tre_em'      => $request->tre_em,
        ]);

        return redirect()->route('booking.index')
            ->with('success', 'Cập nhật đặt bàn thành công!');
    }

    /** ============================
     *  XÓA ĐẶT BÀN
     *  ============================ */
    public function destroy($id)
    {
        DatBan::destroy($id);

        return redirect()->route('booking.index')
            ->with('success', 'Xoá đặt bàn thành công!');
    }
}
