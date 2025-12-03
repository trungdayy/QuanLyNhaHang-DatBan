<?php

namespace App\Http\Controllers\Shop\Booking;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\ChiTietDatBan; // Import model mới
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB để dùng Transaction
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Str;

class BookingController extends Controller
{
    // Trong BookingController.php

    public function index(Request $request)
    {
        $sdt = $request->sdt;
        $datBans = collect([]);

        if ($sdt) {
            // Lấy đơn hàng trong 10 ngày gần nhất
            $datBans = DatBan::where('sdt_khach', $sdt)
                ->where('created_at', '>=', now()->subDays(10))
                ->with(['chiTietDatBan.comboBuffet']) // Eager load để tối ưu
                ->orderByDesc('created_at')
                ->get();
        }

        // [MỚI] Nếu là AJAX request -> Chỉ trả về view danh sách (Partial View)
        if ($request->ajax()) {
            // Tạo một file view mới tên là: resources/views/restaurants/booking/_history_list.blade.php
            // File này chỉ chứa vòng lặp foreach hiển thị danh sách
            return view('restaurants.booking._history_list', compact('datBans', 'sdt'))->render();
        }

        return view('restaurants.booking.index', compact('datBans', 'sdt'));
    }

    public function create()
    {
        return view('restaurants.booking.create');
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'ten_khach'   => 'required|string|max:255',
            'sdt_khach'   => 'required|string|max:20',
            'gio_den'     => 'required|date',
            'nguoi_lon'   => 'required|integer|min:1',
            'tre_em'      => 'nullable|integer|min:0',
            'cart_data'   => 'nullable|string', // Dữ liệu giỏ hàng dạng JSON string
        ]);

        // Sử dụng Transaction để đảm bảo cả 2 bảng đều lưu thành công
        DB::beginTransaction();

        try {
            // 2. Tạo đơn đặt bàn (Bảng dat_ban)
            $datBan = DatBan::create([
                'ma_dat_ban'      => 'DB-' . strtoupper(Str::random(6)), // Tự sinh mã
                'ten_khach'       => $request->ten_khach,
                'sdt_khach'       => $request->sdt_khach,
                'gio_den'         => $request->gio_den,
                'nguoi_lon'       => $request->nguoi_lon,
                'tre_em'          => $request->tre_em ?? 0,
                'trang_thai'      => 'cho_xac_nhan', // Mặc định
                'la_dat_online'   => true,
                'ghi_chu'         => $request->ghi_chu,
            ]);

            // 3. Xử lý lưu Combo (Bảng dat_ban_combo)
            if ($request->has('cart_data') && !empty($request->cart_data)) {
                $cartItems = json_decode($request->cart_data, true); // Decode JSON

                if (is_array($cartItems)) {
                    foreach ($cartItems as $item) {
                        // KIỂM TRA QUAN TRỌNG:
                        // Chỉ lưu nếu item có key bắt đầu bằng "combo_"
                        if (isset($item['key']) && strpos($item['key'], 'combo_') === 0) {

                            // Lấy ID thật của Combo (bỏ prefix 'combo_')
                            $realComboId = str_replace('combo_', '', $item['key']);

                            // Đảm bảo ID tồn tại mới lưu
                            if ($realComboId) {
                                ChiTietDatBan::create([
                                    'dat_ban_id' => $datBan->id,
                                    'combo_id'   => $realComboId, // ID này buộc phải có trong bảng combo_buffet
                                    'so_luong'   => $item['quantity'] ?? 1,
                                ]);
                            }
                        }
                        // Nếu là 'mon_...' thì bỏ qua, không lưu vào bảng dat_ban_combo
                        // để tránh lỗi Foreign Key Constraint (1452).
                    }
                }
            }

            DB::commit(); // Lưu tất cả nếu không lỗi

            return redirect()->route('booking.index', ['sdt' => $request->sdt_khach])
                ->with('success', 'Đặt bàn thành công! Mã đơn: ' . $datBan->ma_dat_ban);
        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác nếu có lỗi
            // Log lỗi ra để debug nếu cần: Log::error($e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $datBan = DatBan::with('chiTietDatBan')->findOrFail($id);
        return view('restaurants.booking.edit', compact('datBan'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validate
        $request->validate([
            'ten_khach'   => 'required|string|max:255',
            'sdt_khach'   => 'required|string|max:20',
            'gio_den'     => 'required|date',
            'nguoi_lon'   => 'required|integer|min:1',
            'tre_em'      => 'nullable|integer|min:0',
            'cart_data'   => 'nullable|string', // Chấp nhận chuỗi JSON
        ]);

        DB::beginTransaction();
        try {
            $datBan = DatBan::findOrFail($id);

            // 2. Cập nhật thông tin chung
            $datBan->update([
                'ten_khach'   => $request->ten_khach,
                'sdt_khach'   => $request->sdt_khach,
                'gio_den'     => $request->gio_den,
                'nguoi_lon'   => $request->nguoi_lon,
                'tre_em'      => $request->tre_em,
            ]);

            // 3. Xử lý cập nhật món (QUAN TRỌNG)
            // Kiểm tra xem form có gửi dữ liệu giỏ hàng lên không
            if ($request->has('cart_data')) {

                // BƯỚC 1: Xóa sạch chi tiết cũ của đơn này trước
                // (Để đảm bảo nếu khách xóa bớt món trên giao diện thì trong DB cũng mất)
                ChiTietDatBan::where('dat_ban_id', $datBan->id)->delete();

                // BƯỚC 2: Thêm lại danh sách mới (nếu có)
                $cartItems = json_decode($request->cart_data, true);

                if (is_array($cartItems) && count($cartItems) > 0) {
                    foreach ($cartItems as $item) {
                        // Logic phải giống hệt hàm store: Chỉ xử lý nếu là COMBO
                        if (isset($item['key']) && strpos($item['key'], 'combo_') === 0) {

                            // Lấy ID chuẩn (Xóa chữ 'combo_' đi)
                            $realComboId = str_replace('combo_', '', $item['key']);

                            ChiTietDatBan::create([
                                'dat_ban_id' => $datBan->id,
                                'combo_id'   => $realComboId,
                                'so_luong'   => $item['quantity'] ?? 1,
                            ]);
                        }
                        // Nếu bạn có bảng DatBanMonAn (cho món lẻ), thì thêm elseif check 'mon_' ở đây
                    }
                }
            }

            DB::commit();

            // Redirect về trang danh sách kèm thông báo
            return redirect()->route('booking.index')
                ->with('success', 'Cập nhật đơn hàng thành công!');
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
