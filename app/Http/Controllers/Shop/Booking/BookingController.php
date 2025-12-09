<?php

namespace App\Http\Controllers\Shop\Booking;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\ChiTietDatBan;
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
        // 1. Tự động dọn dẹp đơn quá hạn 15p (Lazy Loading)
        DatBan::whereIn('trang_thai', ['cho_xac_nhan', 'da_xac_nhan'])
            ->where('gio_den', '<', now()->subMinutes(15))
            ->update([
                'trang_thai' => 'huy',
                'ghi_chu' => DB::raw("CONCAT(COALESCE(ghi_chu, ''), ' | [System] Hủy do khách trễ 15p')")
            ]);

        // 2. Lấy thông tin đơn vừa đặt (để hiện Popup Vé trên View)
        $newBooking = null;
        if (session('new_booking_id')) {
            $newBooking = DatBan::with(['chiTietDatBan.comboBuffet', 'chiTietDatBan.monAn'])
                ->find(session('new_booking_id'));
        }

        // 3. Tra cứu lịch sử đặt bàn (cho cột bên phải)
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
        // 1. Gộp Ngày + Giờ thành 'gio_den'
        if ($request->has(['booking_date', 'booking_time'])) {
            $request->merge([
                'gio_den' => $request->booking_date . ' ' . $request->booking_time
            ]);
        }

        // 2. Validate dữ liệu đầu vào (SĐT kỹ càng)
        $rules = [
            'ten_khach'    => 'required|string|max:255',
            'sdt_khach'    => [
                'required',
                'string',
                'min:10',
                'max:10',
                'regex:/^(03|05|07|08|09)+([0-9]{8})$/' // Regex chuẩn đầu số VN
            ],
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'gio_den'      => 'required|date|after:now', // Chặn quá khứ
            'nguoi_lon'    => 'required|integer|min:1',
            'tre_em'       => 'nullable|integer|min:0',
            'cart_data'    => 'nullable|string',
        ];

        $messages = [
            'sdt_khach.required' => 'Vui lòng nhập số điện thoại.',
            'sdt_khach.min'      => 'Số điện thoại phải đủ 10 số.',
            'sdt_khach.max'      => 'Số điện thoại phải đủ 10 số.',
            'sdt_khach.regex'    => 'Số điện thoại không hợp lệ (Phải bắt đầu bằng 03, 05, 07, 08, 09).',
            'gio_den.after'      => 'Thời gian đặt bàn phải lớn hơn thời gian hiện tại.',
        ];

        $request->validate($rules, $messages);

        // 3. Validate Logic: Số lượng Combo >= Số người lớn
        if ($request->has('cart_data') && !empty($request->cart_data)) {
            $cartItems = json_decode($request->cart_data, true);
            $totalCombos = 0;

            if (is_array($cartItems)) {
                foreach ($cartItems as $item) {
                    // Kiểm tra key bắt đầu bằng combo_
                    if (isset($item['key']) && strpos($item['key'], 'combo_') === 0) {
                        $totalCombos += ($item['quantity'] ?? 0);
                    }
                }
            }

            // Nếu có chọn combo nhưng không đủ suất cho người lớn
            if ($totalCombos > 0 && $totalCombos < $request->nguoi_lon) {
                return back()
                    ->with('error', "Quy định: Số suất Combo ($totalCombos) phải đủ cho số người lớn ($request->nguoi_lon người).")
                    ->withInput();
            }
        }

        // 4. Kiểm tra bàn trống (Service)
        $check = $this->bookingService->checkAvailability($request->nguoi_lon, $request->gio_den);
        if (!$check['status']) {
            return back()->with('error', $check['message'])->withInput();
        }

        // 5. Lưu vào Database
        DB::beginTransaction();
        try {
            // Tạo bảng DatBan
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

            // Tạo bảng ChiTietDatBan
            if ($request->has('cart_data') && !empty($request->cart_data)) {
                $cartItems = json_decode($request->cart_data, true);
                if (is_array($cartItems)) {
                    foreach ($cartItems as $item) {
                        // Xử lý Combo
                        if (isset($item['key']) && strpos($item['key'], 'combo_') === 0) {
                            $realComboId = str_replace('combo_', '', $item['key']);
                            if ($realComboId) {
                                ChiTietDatBan::create([
                                    'dat_ban_id' => $datBan->id,
                                    'combo_id'   => $realComboId,
                                    'so_luong'   => $item['quantity'] ?? 1,
                                ]);
                            }
                        } 
                        // Xử lý Món lẻ
                        else if (isset($item['key']) && strpos($item['key'], 'mon_') === 0) {
                            $realMonId = str_replace('mon_', '', $item['key']);
                            if ($realMonId) {
                                ChiTietDatBan::create([
                                    'dat_ban_id' => $datBan->id,
                                    'mon_an_id'  => $realMonId,
                                    'so_luong'   => $item['quantity'] ?? 1,
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            // [QUAN TRỌNG] Redirect với các tín hiệu cần thiết cho View
            return redirect()->route('booking.index', ['sdt' => $request->sdt_khach])
                ->with('new_booking_id', $datBan->id) // Để hiện Modal Vé
                ->with('clear_cart', true);           // Để Javascript xóa giỏ hàng

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
        // 1. Gộp ngày giờ
        if ($request->has(['booking_date', 'booking_time'])) {
            $request->merge([
                'gio_den' => $request->booking_date . ' ' . $request->booking_time
            ]);
        }

        // 2. Validate (Giống store)
        $rules = [
            'ten_khach'    => 'required|string|max:255',
            'sdt_khach'    => [
                'required', 'string', 'min:10', 'max:10',
                'regex:/^(03|05|07|08|09)+([0-9]{8})$/'
            ],
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'gio_den'      => 'required|date|after:now',
            'nguoi_lon'    => 'required|integer|min:1',
            'tre_em'       => 'nullable|integer|min:0',
        ];
        $request->validate($rules);

        // 3. Validate Combo khi Update
        if ($request->has('cart_data') && !empty($request->cart_data)) {
            $cartItems = json_decode($request->cart_data, true);
            $totalCombos = 0;
            if (is_array($cartItems)) {
                foreach ($cartItems as $item) {
                    if (isset($item['key']) && strpos($item['key'], 'combo_') === 0) {
                        $totalCombos += ($item['quantity'] ?? 0);
                    }
                }
            }
            if ($totalCombos > 0 && $totalCombos < $request->nguoi_lon) {
                return back()->with('error', "Số suất Combo ($totalCombos) phải đủ cho số người lớn ($request->nguoi_lon).")->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $datBan = DatBan::findOrFail($id);
            
            // Cập nhật thông tin chính
            $datBan->update([
                'ten_khach'   => $request->ten_khach,
                'sdt_khach'   => $request->sdt_khach,
                'gio_den'     => $request->gio_den,
                'nguoi_lon'   => $request->nguoi_lon,
                'tre_em'      => $request->tre_em,
            ]);

            // Cập nhật món (Xóa cũ thêm mới)
            if ($request->has('cart_data')) {
                ChiTietDatBan::where('dat_ban_id', $datBan->id)->delete();
                $cartItems = json_decode($request->cart_data, true);

                if (is_array($cartItems) && count($cartItems) > 0) {
                    foreach ($cartItems as $item) {
                        if (isset($item['key']) && strpos($item['key'], 'combo_') === 0) {
                            $realComboId = str_replace('combo_', '', $item['key']);
                            ChiTietDatBan::create([
                                'dat_ban_id' => $datBan->id,
                                'combo_id'   => $realComboId,
                                'so_luong'   => $item['quantity'] ?? 1,
                            ]);
                        } else if (isset($item['key']) && strpos($item['key'], 'mon_') === 0) {
                            $realMonId = str_replace('mon_', '', $item['key']);
                            ChiTietDatBan::create([
                                'dat_ban_id' => $datBan->id,
                                'mon_an_id'  => $realMonId,
                                'so_luong'   => $item['quantity'] ?? 1,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            // Update thì gửi success message bình thường (không cần Modal vé)
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
}