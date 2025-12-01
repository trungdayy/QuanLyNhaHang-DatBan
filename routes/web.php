<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\Admin\NhanVienController;
use App\Http\Controllers\Admin\DonHangController;
use App\Http\Controllers\Admin\MonTrongComboController;
use App\Http\Controllers\Admin\KhuVucController;
use App\Http\Controllers\Admin\BanAnController;
use App\Http\Controllers\Admin\DanhMucController;
use App\Http\Controllers\Admin\MonAnController;
use App\Http\Controllers\Admin\ComboBuffetController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\DatBanController;
use App\Http\Controllers\Admin\ChiTietOrderController;
use App\Http\Controllers\Admin\OrderMonController;
use App\Http\Controllers\Admin\HoaDonController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Shop\BookingController;
use App\Http\Controllers\Shop\OtpController;
use App\Http\Controllers\Shop\MomoController;
use App\Http\Controllers\Shop\ComboClientController;
use App\Http\Controllers\Shop\NhanVien\KhuVuc\NhanVienBanAnController;
use App\Http\Controllers\Shop\NhanVien\NhanVienOrderMonController;
use App\Http\Controllers\Shop\Bep\BepController;
use App\Http\Controllers\Shop\NhanVien\NVDatBanController;
use App\Http\Controllers\Shop\NhanVien\ThanhToanController;


// ===== PHẦN THÊM MỚI 1: KHAI BÁO CONTROLLER =====
use App\Http\Controllers\Shop\Oderqr\OrderController;
// ===============================================


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== CLIENT SITE ====================
Route::prefix('/')->group(function () {

    // Trang chủ
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Combos
    Route::get('/combos', [ComboClientController::class, 'index'])->name('combos.index');
    Route::get('/combos/{id}', [ComboClientController::class, 'show'])->name('combos.show');

    // Booking: resource (trừ show)
    Route::resource('booking', BookingController::class)->except(['show']);

    // Trang đặt bàn thành công
    Route::get('booking/success', [BookingController::class, 'success'])->name('booking.success');

    // AJAX: lấy bàn theo khu vực
    Route::get('booking/bans-by-khuvuc/{khu_vuc_id}', [BookingController::class, 'getBansByKhuVuc']);

    // ==== OTP cho booking ====
    Route::prefix('otp')->group(function () {
        Route::get('verify', [OtpController::class, 'showOtpForm'])->name('otp.form');
        Route::post('send', [OtpController::class, 'sendOtp'])->name('otp.send');
        Route::post('verify', [OtpController::class, 'verifyOtp'])->name('otp.verify');
    });

    // ==== Chọn phương thức thanh toán sau khi xác thực OTP ====
    Route::get('booking/{booking_id}/payment-method', [BookingController::class, 'paymentMethod'])
        ->name('booking.payment_method');

    // ==== Các phương thức thanh toán ====
    Route::get('booking/{booking_id}/pay-cash', [BookingController::class, 'payCash'])->name('booking.pay_cash');
    Route::get('booking/{booking_id}/pay-os', [BookingController::class, 'payOS'])->name('booking.pay_os'); // đúng với controller
    Route::get('booking/{booking_id}/pay-vnpay', [BookingController::class, 'payVNPay'])->name('booking.pay_vnpay');
    Route::get('booking/{booking_id}/pay-vietqr', [BookingController::class, 'payVietQR'])->name('booking.pay_vietqr');

    // Callback và cancel PayOS
    Route::get('payment/cancel', [BookingController::class, 'cancel'])->name('booking.pay-os.cancel');
    Route::get('payment/success', [BookingController::class, 'success'])->name('booking.pay-os.success');
});


// ==================== ADMIN SITE ====================
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 💡 === ĐÃ THÊM: ROUTE AJAX CHO BIỂU ĐỒ ===
    // (Sửa lỗi biểu đồ trống)
    Route::get('/dashboard/data', [DashboardController::class, 'getChartData'])->name('dashboard.data');

    // SẢN PHẨM & MÓN ĂN
    Route::resource('danh-muc', DanhMucController::class);
    Route::resource('san-pham', SanPhamController::class); // Tên này có thể nên là 'mon-an'
    Route::resource('mon-trong-combo', MonTrongComboController::class);
    Route::resource('combo-buffet', ComboBuffetController::class);

    // CHI TIẾT ORDER
    Route::resource('chi-tiet-order', ChiTietOrderController::class);
    Route::resource('order-mon', OrderMonController::class);

    //hoa don
    Route::resource('hoa-don', HoaDonController::class);

    //voucher
    Route::resource('voucher', VoucherController::class)->except(['show']);
    // AJAX ROUTE CHO BÀN ĂN
    Route::get('ban-an/ajax-list', [App\Http\Controllers\Admin\BanAnController::class, 'ajaxList'])
        ->name('ban-an.ajax');


    // NHÂN VIÊN 
    Route::prefix('nhan-vien')->name('nhan-vien.')->controller(NhanVienController::class)->group(function () {

        // Hiển thị danh sách
        Route::get('/', 'index')->name('index');

        // Thêm mới
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store'); // POST /nhan-vien

        // Sửa
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update'); // PUT /nhan-vien/{id}

        // Xóa
        Route::delete('/{id}', 'destroy')->name('destroy'); // DELETE /nhan-vien/{id}

        // Cập nhật trạng thái (AJAX hoặc patch)
        Route::patch('/{id}/trang-thai', 'capNhatTrangThai')->name('cap-nhat-trang-thai');

        // Reset mật khẩu
        Route::post('/{id}/reset-mat-khau', 'resetMatKhau')->name('reset-mat-khau');
    });

    Route::get('/don-hang', [DonHangController::class, 'index'])->name('don-hang');

    // KHU VỰC & BÀN ĂN
    Route::get('/khu-vuc-ban-an', [KhuVucController::class, 'showManagementPage'])
        ->name('khu-vuc-ban-an');

    Route::prefix('khu-vuc')->name('khu-vuc.')->controller(KhuVucController::class)->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::post('/{id}/update', 'update')->name('update');
        Route::post('/{id}/delete', 'destroy')->name('destroy');
        Route::patch('/{id}/trang-thai', 'capNhatTrangThai')->name('cap-nhat-trang-thai');
    });

    Route::prefix('ban-an')->name('ban-an.')->controller(BanAnController::class)->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::post('/{id}/update', 'update')->name('update');
        Route::post('/{id}/delete', 'destroy')->name('destroy');
        Route::post('/{id}/regenerate-qr', 'regenerateQr')->name('qr');
        Route::patch('/{id}/trang-thai', 'capNhatTrangThai')->name('cap-nhat-trang-thai');
        Route::get('/qr-tool', 'showQrGeneratorPage')->name('qr_tool');                 // Trang công cụ tạo QR hàng loạt
    });

    // AJAX ROUTE (Lấy bàn trống theo giờ)
    Route::get('/ajax/get-available-tables', [BanAnController::class, 'ajaxGetAvailableTables'])
        ->name('ajax.get-available-tables');


    // CRUD CHO ĐẶT BÀN
    Route::prefix('dat-ban')->name('dat-ban.')->controller(DatBanController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::post('/{id}/delete', 'destroy')->name('destroy');
        Route::post('/{id}/update-status', 'updateStatus')->name('updateStatus');
    });
});
// <-- ** KẾT THÚC NHÓM ADMIN **






// ==========================================================
// ===== MÀN Nhân Viên =====
// ==========================================================

Route::prefix('nhanVien')->name('nhanVien.')->group(function () {
 Route::prefix('ban-an')->name('ban-an.')->group(function () {
    Route::get('/', [NhanVienBanAnController::class, 'index'])->name('index');

    // Check-in Walk-in (Khách vãng lai) - Giữ nguyên
    Route::post('/check-in-walkin', [NhanVienBanAnController::class, 'checkInWalkIn'])->name('check-in-walkin');

    // --- SỬA ĐOẠN NÀY ---
    // 1. Route hiển thị Form chọn bàn cho khách đặt trước (GET)
    Route::get('/check-in-dattruoc/{id}', [NhanVienBanAnController::class, 'showCheckInForm'])->name('show-checkin-dattruoc');

    // 2. Route Xử lý Check-in sau khi chọn bàn (POST)
    Route::post('/process-check-in', [NhanVienBanAnController::class, 'processCheckIn'])->name('process-checkin');
    // --------------------
    Route::post('/reset/{id}', [NhanVienBanAnController::class, 'resetBan'])->name('reset-ban');
});


    // DatBan NhanVien
    Route::get('/dat-ban', [NVDatBanController::class, 'index'])->name('datban.index');
    Route::get('/dat-ban/create', [NVDatBanController::class, 'create'])->name('datban.create');
    Route::post('/dat-ban/store', [NVDatBanController::class, 'store'])->name('datban.store');

    // Đã sửa: Đồng bộ với tên phương thức Controller (thayDoiTrangThai) và sử dụng Model Binding ({datBan})
    Route::post('/dat-ban/{datBan}/thay-doi-trang-thai', [NVDatBanController::class, 'thayDoiTrangThai'])->name('datban.thaydoitrangthai');

    Route::get('/dat-ban/check-ban-trong', [NVDatBanController::class, 'ajaxCheckBanTrong'])->name('datban.check_ban');



    //gọi món tạo order
    Route::get('/order', [NhanVienOrderMonController::class, 'index'])->name('order.index');
    Route::post('/order/mo-order', [NhanVienOrderMonController::class, 'moOrder'])->name('order.mo-order');

    // 👉 NEW: Bắt buộc chọn combo trước
    Route::get('/order/{orderId}/chon-combo', [NhanVienOrderMonController::class, 'chonCombo'])->name('order.chon-combo');
    Route::post('/order/{orderId}/chon-combo', [NhanVienOrderMonController::class, 'luuCombo'])->name('order.luu-combo');

    Route::get('/chi-tiet-order/create', [NhanVienOrderMonController::class, 'create'])->name('chi-tiet-order.create');
    Route::get('/order/{orderId}', [NhanVienOrderMonController::class, 'orderPage'])->name('order.page');
    Route::post('/order/{orderId}/gui-bep', [NhanVienOrderMonController::class, 'guiBep'])->name('order.gui-bep');

    Route::get('/chi-tiet-order/{orderId}', [NhanVienOrderMonController::class, 'show'])->name('chi-tiet-order.show');

    Route::get('chi-tiet-order/{orderId}/edit/{ctId}', [NhanVienOrderMonController::class, 'edit'])->name('chi-tiet-order.edit');
    Route::post('/chi-tiet-order', [NhanVienOrderMonController::class, 'store'])->name('chi-tiet-order.store');
    Route::put('chi-tiet-order/{ctId}', [NhanVienOrderMonController::class, 'update'])->name('chi-tiet-order.update');
    Route::delete('/chi-tiet-order/{id}', [NhanVienOrderMonController::class, 'destroy'])->name('chi-tiet-order.destroy');

    // Thanh toán
    Route::prefix('thanh-toan')->name('thanh-toan.')->controller(ThanhToanController::class)->group(function () {
        // thanh toán từ danh sách bàn
        Route::get('/ban/{banId}', 'thanhToanTuBan')->name('ban');
        Route::post('/ban/{banId}', 'luuThanhToanTuBan')->name('luu-ban');
        // thanh toán từ bên order món
        Route::get('/order/{orderId}', 'thanhToan')->name('order');
        Route::post('/order/{orderId}', 'luuThanhToan')->name('luu');
        // hóa đơn và in
        Route::get('/hoa-don/{hoaDonId}', 'hienThiHoaDon')->name('hien-thi-hoa-don');
        Route::get('/hoa-don/{hoaDonId}/in', 'inHoaDon')->name('in-hoa-don');
        // thanh toán vnpay
        Route::get('/vnpay-payment/{banId}', 'vnpayPayment')->name('vnpay.payment');
        Route::get('/vnpay/callback/{banId}', 'vnpayCallback')->name('vnpay.callback');
    });
});

// ==========================================================
// ===== MÀN HÌNH BẾP (CHỈ ROUTE CHÍNH) =====
// ==========================================================
Route::prefix('bep')->name('bep.')->group(function () {
    Route::get('/', [BepController::class, 'dashboard'])->name('dashboard');
    Route::post('/update-status', [BepController::class, 'updateMonStatus'])->name('update-status');
});






// ==========================================================
// ===== PHẦN THÊM MỚI 2: NHÓM ROUTE "oderqr" (Đã thêm Combo Selection) =====
// ==========================================================
Route::prefix('oderqr')->group(function () {

    /**
     * MỚI THÊM: TRANG CHỌN COMBO (Chuyển hướng từ /menu sang đây nếu thiếu combo)
     * VD: GET /oderqr/select-combo/3
     */
    Route::get('select-combo/{qrKey}', [OrderController::class, 'showComboSelectionPage'])
        ->name('oderqr.select_combo');

    /**
     * MỚI THÊM: API XỬ LÝ POST CHỌN COMBO VÀ TẠO SESSION
     * VD: POST /oderqr/start-order
     */
    Route::post('start-order', [OrderController::class, 'startOrder'])
        ->name('oderqr.start_order');

    /**
     * Điểm vào chính (sẽ kiểm tra và chuyển hướng)
     */
    Route::get('menu/{qrKey}', [OrderController::class, 'showGoiMonPage'])
        ->name('oderqr.menu');

    /**
     * API khi quét QR: Lấy thông tin bàn, menu và trạng thái order hiện tại
     */
    Route::get('session/table/{qrKey}', [OrderController::class, 'getSessionInfo']);

    /**
     * API gửi order (gọi thêm món)
     */
    Route::post('order/submit', [OrderController::class, 'submitOrder']);

    /**
     * API xem trạng thái các món đã gọi (cho bếp)
     * VD: GET /oderqr/order/status/21
     */
    Route::get('order/status/{datBanId}', [OrderController::class, 'getOrderStatus']);

    Route::get('list', [OrderController::class, 'showQrListPage'])->name('oderqr.list');

    /**
     * [MỚI - QUAN TRỌNG] API Hủy món (Chỉ hủy được khi bếp chưa làm)
     * URL: /oderqr/order/cancel-item
     */
    Route::post('order/cancel-item', [OrderController::class, 'cancelItem'])
        ->name('oderqr.cancel_item');
});



// ===== PHẦN THÊM MỚI 3: ROUTE TRUY CẬP NHANH (DÙNG CHO DEMO) =====
Route::get('/tong', function () {
    return view('quick-access');
});
