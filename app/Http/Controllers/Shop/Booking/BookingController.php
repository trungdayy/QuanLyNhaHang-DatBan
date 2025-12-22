<?php

namespace App\Http\Controllers\Shop\Booking;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\ChiTietDatBan; // Đảm bảo Model này map đúng với bảng trong DB
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\BookingAvailabilityService;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingAvailabilityService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        // 1. Tự động dọn dẹp đơn quá hạn (Tạm chấp nhận để ở đây)
        DatBan::whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan'])
            ->where('gio_den', '<', now()->subMinutes(15))
            ->update([
                'trang_thai' => 'huy',
                'ghi_chu' => DB::raw("CONCAT(COALESCE(ghi_chu, ''), ' | [System] Hủy do khách trễ 15p')")
            ]);

        // 2. Lấy thông tin đơn vừa đặt
        $newBooking = null;
        if (session('new_booking_id')) {
            $newBooking = DatBan::with(['chiTietDatBan.comboBuffet', 'chiTietDatBan.monAn'])
                ->find(session('new_booking_id'));
        }

        // 3. Lịch sử đặt bàn
        $sdt = $request->sdt;
        $datBans = collect([]);

        if ($sdt) {
            $datBans = DatBan::where('sdt_khach', $sdt)
                ->where('created_at', '>=', now()->subDays(10))
                ->with(['chiTietDatBan.comboBuffet'])
                ->orderByDesc('created_at')
                ->get();
        }

        if ($request->ajax()) {
            return view('restaurants.booking._history_list', compact('datBans', 'sdt'))->render();
        }

        return view('restaurants.booking.index', compact('datBans', 'sdt', 'newBooking'));
    }

    public function create()
    {
        return view('restaurants.booking.create');
    }

    public function store(Request $request)
    {
        $this->mergeDateTime($request); // Gộp ngày giờ
        $this->validateBooking($request); // Validate chung

        // 1. Kiểm tra bàn trống
        // Trong hàm store() và update()
        $tongKhach = $request->nguoi_lon + ($request->tre_em ?? 0);
        $check = $this->bookingService->checkAvailability($tongKhach, $request->gio_den);
        if (!$check['status']) {
            return back()->with('error', $check['message'])->withInput();
        }

        DB::beginTransaction();
        try {
            // 2. Tạo đơn
            $datBan = DatBan::create([
                'ma_dat_ban'      => 'DB-' . strtoupper(Str::random(6)),
                'ten_khach'       => $request->ten_khach,
                'sdt_khach'       => $request->sdt_khach,
                'gio_den'         => $request->gio_den,
                'nguoi_lon'       => $request->nguoi_lon,
                'tre_em'          => $request->tre_em ?? 0,
                'trang_thai'      => 'cho_xac_nhan',
                'la_dat_online'   => true,
                'ghi_chu'         => $request->ghi_chu,
                'thoi_luong_phut' => 120,
            ]);

            // 3. Lưu món/combo (Dùng hàm riêng để không lặp code)
            $this->syncBookingDetails($datBan, $request->cart_data);

            DB::commit();

            return redirect()->route('booking.index', ['sdt' => $request->sdt_khach])
                ->with('new_booking_id', $datBan->id)
                ->with('clear_cart', true);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $datBan = DatBan::with('chiTietDatBan')->findOrFail($id);
        return view('restaurants.booking.edit', compact('datBan'));
    }

    public function update(Request $request, $id)
    {
        $this->mergeDateTime($request);
        $this->validateBooking($request);

        $datBan = DatBan::findOrFail($id);

        // [QUAN TRỌNG] Kiểm tra bàn trống lại khi Update
        // Chỉ kiểm tra nếu khách thay đổi số người hoặc giờ đến
        $isChanged = ($datBan->nguoi_lon != $request->nguoi_lon) || ($datBan->gio_den != $request->gio_den);

        if ($isChanged) {
            $check = $this->bookingService->checkAvailability($request->nguoi_lon, $request->gio_den);
            if (!$check['status']) {
                return back()->with('error', $check['message'])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $datBan->update([
                'ten_khach'   => $request->ten_khach,
                'sdt_khach'   => $request->sdt_khach,
                'gio_den'     => $request->gio_den,
                'nguoi_lon'   => $request->nguoi_lon,
                'tre_em'      => $request->tre_em,
            ]);

            // Xóa chi tiết cũ và thêm mới (Dùng lại hàm sync)
            if ($request->has('cart_data')) {
                // Lưu ý: Đảm bảo model ChiTietDatBan đúng quan hệ
                ChiTietDatBan::where('dat_ban_id', $datBan->id)->delete();
                $this->syncBookingDetails($datBan, $request->cart_data);
            }

            DB::commit();
            return redirect()->route('booking.index')->with('success', 'Cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi cập nhật: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        DatBan::destroy($id);
        return redirect()->route('booking.index')->with('success', 'Xoá đặt bàn thành công!');
    }

    // ==========================================================
    // CÁC HÀM PHỤ TRỢ (HELPER FUNCTIONS) - GIÚP CODE GỌN HƠN
    // ==========================================================

    private function mergeDateTime(Request $request)
    {
        if ($request->has(['booking_date', 'booking_time'])) {
            $request->merge([
                'gio_den' => $request->booking_date . ' ' . $request->booking_time
            ]);
        }
    }

    private function validateBooking(Request $request)
    {
        $rules = [
            'ten_khach'    => 'required|string|max:255',
            'sdt_khach'    => ['required', 'string', 'min:10', 'max:10', 'regex:/^(03|05|07|08|09)+([0-9]{8})$/'],
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'gio_den'      => 'required|date|after:now',
            'nguoi_lon'    => 'required|integer|min:1',
            'tre_em'       => 'nullable|integer|min:0',
        ];
        $request->validate($rules, [
            'sdt_khach.regex' => 'Số điện thoại không hợp lệ.',
            'gio_den.after'   => 'Thời gian đặt phải ở tương lai.',
        ]);

        // Validate Combo: Số lượng combo >= số khách (người lớn + trẻ em), hoặc không chọn combo
        $tongKhach = $request->nguoi_lon + ($request->tre_em ?? 0);
        $totalCombos = 0;
        
        if ($request->filled('cart_data')) {
            $cartItems = json_decode($request->cart_data, true);
            if (is_array($cartItems)) {
                foreach ($cartItems as $item) {
                    if (isset($item['key']) && str_starts_with($item['key'], 'combo_')) {
                        $totalCombos += ($item['quantity'] ?? 0);
                    }
                }
            }
        }
        
        // Nếu có chọn combo thì phải >= số khách, nếu không chọn combo thì OK
        if ($totalCombos > 0 && $totalCombos < $tongKhach) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'cart_data' => "Số suất Combo ($totalCombos) phải >= số khách ($tongKhach người). Hoặc không chọn combo."
            ]);
        }
    }

    private function syncBookingDetails($datBan, $cartJson)
    {
        if (empty($cartJson)) return;

        $cartItems = json_decode($cartJson, true);
        if (!is_array($cartItems)) return;

        foreach ($cartItems as $item) {
            $qty = $item['quantity'] ?? 1;
            $key = $item['key'] ?? '';

            // Tách Combo và Món
            if (str_starts_with($key, 'combo_')) {
                ChiTietDatBan::create([
                    'dat_ban_id' => $datBan->id,
                    'combo_id'   => str_replace('combo_', '', $key),
                    'so_luong'   => $qty,
                ]);
            } elseif (str_starts_with($key, 'mon_')) {
                ChiTietDatBan::create([
                    'dat_ban_id' => $datBan->id,
                    'mon_an_id'  => str_replace('mon_', '', $key),
                    'so_luong'   => $qty,
                ]);
            }
        }
    }
}
