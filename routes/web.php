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
    Route::get('/', [HomeController::class, 'index'])->name('home');
    // Route::get('/about', [AboutController::class, 'index'])->name('about');
    // Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    // Route::get('/booking', [BookingController::class, 'index'])->name('booking');
    // Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    // Route::get('/service', [ServiceController::class, 'index'])->name('service');
    // Route::get('/team', [TeamController::class, 'index'])->name('team');
    // Route::get('/testimonial', [TestimonialController::class, 'index'])->name('testimonial');
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
        Route::post('/{id}/update', 'update')->name('update');
        Route::post('/{id}/delete', 'destroy')->name('destroy');
        Route::post('/{id}/update-status', 'updateStatus')->name('updateStatus');
    });
});
// <-- ** KẾT THÚC NHÓM ADMIN **




    

// ==========================================================
// ===== MÀN HÌNH NHÂN VIÊN (CHỈ ROUTE CHÍNH) =====
// ==========================================================
Route::prefix('nhan-vien')->name('nhan-vien.')->controller(NhanVienController::class)->group(function () {
    // Route chính: Trang quản lý nhân viên (danh sách, thao tác chính)
    Route::get('/', 'index')->name('index');
});

// ==========================================================
// ===== MÀN HÌNH BẾP (CHỈ ROUTE CHÍNH) =====
// ==========================================================
Route::prefix('bep')->name('bep.')->controller(OrderController::class)->group(function () {
    // Route chính: Trang hiển thị các món cần chế biến / trạng thái order
    Route::get('/', 'showKitchenDashboard')->name('dashboard');
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


    

});

