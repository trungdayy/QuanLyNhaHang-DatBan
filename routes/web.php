<?php

use Illuminate\Support\Facades\Route;

// Controllers (Đảm bảo tất cả các Controller cần thiết được import)
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SanPhamController;
use App\Http\Controllers\Admin\NhanVienController;
use App\Http\Controllers\Admin\DonHangController;
use App\Http\Controllers\Admin\MonTrongComboController;
use App\Http\Controllers\Admin\KhuVucController;
use App\Http\Controllers\Admin\BanAnController;
use App\Http\Controllers\Admin\DanhMucController;
use App\Http\Controllers\Admin\ComboBuffetController;
use App\Http\Controllers\Auth\LoginController; // Controller Auth
use App\Http\Controllers\Admin\DatBanController;
use App\Http\Controllers\Admin\ChiTietOrderController;
use App\Http\Controllers\Admin\OrderMonController;
use App\Http\Controllers\Admin\HoaDonController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Shop\OtpController;
use App\Http\Controllers\Shop\ComboClientController;
use App\Http\Controllers\Shop\NhanVien\KhuVuc\NhanVienBanAnController;
use App\Http\Controllers\Shop\NhanVien\NhanVienOrderMonController;
use App\Http\Controllers\Shop\Bep\BepController;
use App\Http\Controllers\Shop\NhanVien\NVDatBanController;
use App\Http\Controllers\Shop\NhanVien\ThanhToanController;
use App\Http\Controllers\Shop\Oderqr\OrderController;
use App\Http\Controllers\Shop\Booking\BookingController;
use App\Http\Controllers\Admin\DanhGiaController;


// ==========================================================
// ===== 1. PUBLIC ROUTES (CLIENT WEB & BOOKING) =====
// Không yêu cầu đăng nhập
// ==========================================================
Route::prefix('/')->group(function () {

    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('home');
        Route::get('/gioi-thieu', 'about')->name('about');
        Route::get('/dich-vu', 'service')->name('service');
        Route::get('/thuc-don', 'menu')->name('menu');
        Route::get('/lien-he', 'contact')->name('contact');
        Route::post('/lien-he', 'sendContact')->name('contact.send'); // Route GỬI form
        Route::get('/doi-ngu', 'team')->name('team');
        Route::get('/danh-gia', 'testimonial')->name('testimonial');
    });

    // Combos
    Route::get('/combos', [ComboClientController::class, 'index'])->name('combos.index');
    Route::get('/combos/{id}', [ComboClientController::class, 'show'])->name('combos.show');

    // Booking & Thanh toán
    Route::resource('booking', BookingController::class)->except(['show']);
    Route::get('booking/success', [BookingController::class, 'success'])->name('booking.success');
    Route::get('booking/bans-by-khuvuc/{khu_vuc_id}', [BookingController::class, 'getBansByKhuVuc']);

    // OTP
    Route::prefix('otp')->group(function () {
        Route::get('verify', [OtpController::class, 'showOtpForm'])->name('otp.form');
        Route::post('send', [OtpController::class, 'sendOtp'])->name('otp.send');
        Route::post('verify', [OtpController::class, 'verifyOtp'])->name('otp.verify');
    });

    // Thanh toán trực tuyến (Sau khi xác thực OTP)
    Route::get('booking/{booking_id}/payment-method', [BookingController::class, 'paymentMethod'])->name('booking.payment_method');
    Route::get('booking/{booking_id}/pay-cash', [BookingController::class, 'payCash'])->name('booking.pay_cash');
    Route::get('booking/{booking_id}/pay-os', [BookingController::class, 'payOS'])->name('booking.pay_os');
    Route::get('booking/{booking_id}/pay-vnpay', [BookingController::class, 'payVNPay'])->name('booking.pay_vnpay');
    Route::get('booking/{booking_id}/pay-vietqr', [BookingController::class, 'payVietQR'])->name('booking.pay_vietqr');

    // Callback PayOS
    Route::get('payment/cancel', [BookingController::class, 'cancel'])->name('booking.pay-os.cancel');
    Route::get('payment/success', [BookingController::class, 'success'])->name('booking.pay-os.success');
});


// ==========================================================
// ===== 2. QR ORDER ROUTES (KHÁCH GỌI MÓN TẠI BÀN) =====
// Không yêu cầu đăng nhập
// ==========================================================
Route::prefix('oderqr')->group(function () {

    Route::get('select-combo/{qrKey}', [OrderController::class, 'showComboSelectionPage'])->name('oderqr.select_combo');
    Route::post('start-order', [OrderController::class, 'startOrder'])->name('oderqr.start_order');
    Route::get('menu/{qrKey}', [OrderController::class, 'showGoiMonPage'])->name('oderqr.menu');
    Route::get('session/table/{qrKey}', [OrderController::class, 'getSessionInfo']);
    Route::post('order/submit', [OrderController::class, 'submitOrder']);
    Route::get('order/status/{datBanId}', [OrderController::class, 'getOrderStatus']);
    Route::get('list', [OrderController::class, 'showQrListPage'])->name('oderqr.list');
    Route::post('order/cancel-item', [OrderController::class, 'cancelItem'])->name('oderqr.cancel_item');
    Route::post('call-staff', [OrderController::class, 'callStaff'])->name('oderqr.call_staff');
});

// Route truy cập nhanh dùng cho demo
Route::get('/tong', function () {
    return view('quick-access');
});

// Route debug cache
Route::get('/test-debug', function () {
    \Illuminate\Support\Facades\Cache::put('test_key', 'Cache Hoạt Động Ngon!', 60);
    $val = \Illuminate\Support\Facades\Cache::get('test_key');

    $bans = \App\Models\BanAn::all();
    $dangGoi = [];
    foreach ($bans as $b) {
        if (\Illuminate\Support\Facades\Cache::has('goi_nhan_vien_' . $b->id)) {
            $dangGoi[] = "Bàn " . $b->so_ban . " (ID: " . $b->id . ") đang gọi";
        }
    }

    return response()->json([
        'Trang_thai_Cache_System' => $val ? 'OK (' . $val . ')' : 'LỖI (Không lưu được)',
        'Cac_ban_dang_goi' => $dangGoi
    ]);
});


// ==========================================================
// ===== 3. AUTH ROUTES (ĐĂNG NHẬP / ĐĂNG XUẤT/ĐĂNG KÍ) =====
// ==========================================================


Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('login', [LoginController::class, 'login']);

Route::post('/register-nhanvien', [LoginController::class, 'storeNhanVien'])->name('register.store');

Route::post('logout', [LoginController::class, 'logout'])->name('logout');


// ==========================================================
// ===== 4. KHU VỰC CẦN PHÂN QUYỀN (RBAC) =====
// ==========================================================

// 4.1. NHÓM ADMIN (Vai trò: quan_ly)
Route::middleware(['auth', 'role:quan_ly'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'getChartData'])->name('dashboard.data');

    // Quản lý menu và combo
    Route::resource('danh-muc', DanhMucController::class);
    Route::resource('san-pham', SanPhamController::class);
    Route::resource('mon-trong-combo', MonTrongComboController::class);
    Route::resource('combo-buffet', ComboBuffetController::class);

    // Quản lý Order, Hóa đơn & Voucher
    Route::resource('chi-tiet-order', ChiTietOrderController::class);
    Route::resource('order-mon', OrderMonController::class);
    Route::resource('hoa-don', HoaDonController::class);
    Route::resource('voucher', VoucherController::class)->except(['show']);

    // Quản lý Đánh giá
    Route::controller(DanhGiaController::class)->prefix('danh-gia')->name('danh-gia.')->group(function () {
        // Danh sách đánh giá: admin.danh-gia.index
        Route::get('/', 'index')->name('index');

        // Duyệt/Ẩn đánh giá: admin.danh-gia.status
        Route::get('/status/{id}/{status}', 'updateStatus')->name('status');

        // Xóa đánh giá: admin.danh-gia.destroy
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Quản lý Nhân viên
    Route::prefix('nhan-vien')->name('nhan-vien.')->controller(NhanVienController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/edit', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::patch('/{id}/trang-thai', 'capNhatTrangThai')->name('cap-nhat-trang-thai');
        Route::post('/{id}/reset-mat-khau', 'resetMatKhau')->name('reset-mat-khau');
    });

    Route::get('/don-hang', [DonHangController::class, 'index'])->name('don-hang');

    // Quản lý Khu vực & Bàn ăn
    Route::get('/khu-vuc-ban-an', [KhuVucController::class, 'showManagementPage'])->name('khu-vuc-ban-an');

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
        Route::get('/qr-tool', 'showQrGeneratorPage')->name('qr_tool');
        Route::get('/ajax/get-available-tables', 'ajaxGetAvailableTables')->name('ajax.get-available-tables');
        Route::get('ban-an/ajax-list', 'ajaxList')->name('ajax');
    });

    // Quản lý Đặt bàn (Admin)
    Route::prefix('dat-ban')->name('dat-ban.')->controller(DatBanController::class)->group(function () {
        Route::get('/ajax-get-combos-by-loai', 'ajaxGetCombosByLoai')->name('ajax-get-combos-by-loai');
        Route::get('/ajax-get-available-tables', 'ajaxGetAvailableTables')->name('ajax-get-available-tables');
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

// 4.2. NHÓM NHÂN VIÊN (Vai trò: phuc_vu, le_tan)
Route::middleware(['auth', 'role:phuc_vu,le_tan'])->prefix('nhanVien')->name('nhanVien.')->group(function () {

    // Quản lý bàn
    Route::prefix('ban-an')->name('ban-an.')->group(function () {
        Route::get('/', [NhanVienBanAnController::class, 'index'])->name('index');
        Route::post('/check-in-walkin', [NhanVienBanAnController::class, 'checkInWalkIn'])->name('check-in-walkin');
        Route::get('/check-in-dattruoc/{id}', [NhanVienBanAnController::class, 'showCheckInForm'])->name('show-checkin-dattruoc');
        Route::post('/process-check-in', [NhanVienBanAnController::class, 'processCheckIn'])->name('process-checkin');
        Route::post('/reset/{id}', [NhanVienBanAnController::class, 'resetBan'])->name('reset-ban');
        Route::get('check-notifications', [NhanVienBanAnController::class, 'checkNotifications'])->name('check_notif');
        Route::post('complete-support', [NhanVienBanAnController::class, 'completeSupport'])->name('complete_support');
    });

    // Đặt bàn cho nhân viên (Tạo booking tại quầy)
    Route::get('/dat-ban', [NVDatBanController::class, 'index'])->name('datban.index');
    Route::get('/dat-ban/create', [NVDatBanController::class, 'create'])->name('datban.create');
    Route::post('/dat-ban/store', [NVDatBanController::class, 'store'])->name('datban.store');
    Route::post('/dat-ban/{datBan}/thay-doi-trang-thai', [NVDatBanController::class, 'thayDoiTrangThai'])->name('datban.thaydoitrangthai');
    Route::get('/dat-ban/check-ban-trong', [NVDatBanController::class, 'ajaxCheckBanTrong'])->name('datban.check_ban');

    // Gọi món (Tạo order và thêm món)
    Route::get('/order', [NhanVienOrderMonController::class, 'index'])->name('order.index');
    Route::post('/order/mo-order', [NhanVienOrderMonController::class, 'moOrder'])->name('order.mo-order');
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
        Route::post('/vnpay-payment/{banId}', 'vnpayPayment')->name('vnpay.payment');
        Route::get('/vnpay/callback/{banId}', 'vnpayCallback')->name('vnpay.callback');
    });
});


// 4.3. NHÓM BẾP (Vai trò: bep)
Route::middleware(['auth', 'role:bep'])->prefix('bep')->name('bep.')->group(function () {
    Route::get('/', [BepController::class, 'dashboard'])->name('dashboard');
    Route::post('/update-status', [BepController::class, 'updateMonStatus'])->name('update-status');
});
