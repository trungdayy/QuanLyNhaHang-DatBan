@extends('layouts.master')
@section('title', 'Home')

@section('content')

{{-- =========================================================== --}}
{{-- 1. CSS TÙY CHỈNH --}}
{{-- =========================================================== --}}
<style>
    /* FIX LỖI TRÀN MÀN HÌNH & CUỘN MƯỢT */
    html, body { 
        overflow-x: hidden !important; 
        width: 100%; 
        position: relative; 
        scroll-behavior: smooth; 
    }

    /* ICON GIỎ HÀNG NỔI */
    #floatingCartIcon {
        position: fixed !important; 
        bottom: 30px; 
        right: 30px; 
        z-index: 2147483647 !important; 
        cursor: pointer; 
        display: none; 
        animation: popUp 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28);
    }

    .icon-wrapper {
        width: 65px; 
        height: 65px; 
        background: linear-gradient(135deg, #FF6B6B, #FF8E53);
        color: #fff; 
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        font-size: 28px; 
        box-shadow: 0 5px 15px rgba(255, 107, 107, 0.5); 
        transition: transform 0.2s;
        border: 3px solid #fff; 
    }

    .icon-wrapper:active { transform: scale(0.9); }
    .icon-wrapper:hover { transform: scale(1.05); }

    .count-badge {
        position: absolute; 
        top: -5px; 
        right: -5px; 
        background: #fff; 
        color: #d63031;
        font-size: 14px; 
        font-weight: 800; 
        width: 28px; 
        height: 28px; 
        border-radius: 50%;
        display: flex; 
        align-items: center; 
        justify-content: center; 
        border: 2px solid #d63031;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    /* CSS CHO MODAL & ITEM */
    .cart-item-row { padding: 15px 0; border-bottom: 1px dashed #eee; }
    .cart-item-row:last-child { border-bottom: none; }
    .cart-item-name { font-weight: 700; color: #333; font-size: 1rem; line-height: 1.4; margin-bottom: 5px; }
    .qty-control { display: flex; align-items: center; gap: 10px; background: #f8f9fa; padding: 5px 10px; border-radius: 20px; border: 1px solid #eee; }
    .btn-qty { width: 28px; height: 28px; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; transition: all 0.2s; }
    .btn-minus { background: #e9ecef; color: #333; }
    .btn-plus { background: #FEA116; color: white; }
    .qty-display { font-weight: bold; min-width: 20px; text-align: center; }

    /* HIỆU ỨNG CARD SẢN PHẨM */
    .product-card-trigger { cursor: pointer; transition: all 0.3s; }
    .product-card-trigger:hover { 
        background-color: #fff; 
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important; 
    }
    
    @keyframes popUp { from { transform: scale(0); } to { transform: scale(1); } }

    div.swal2-container { z-index: 20000000 !important; }

    /* --- TÙY CHỈNH LỊCH FLATPICKR MÀU CAM --- */
    .flatpickr-calendar.dark {
        background: #0F172B; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        border: 1px solid #FEA116;
    }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, 
    .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, 
    .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, 
    .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, 
    .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, 
    .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
        background: #FEA116 !important; 
        border-color: #FEA116 !important;
        color: #fff;
    }
    .flatpickr-months .flatpickr-month { background: #0F172B !important; color: #fff !important; fill: #fff !important; }
    .flatpickr-current-month .flatpickr-monthDropdown-months, .flatpickr-current-month input.cur-year { color: #fff !important; }
    span.flatpickr-weekday { color: #FEA116 !important; }
    .flatpickr-months .flatpickr-prev-month svg, .flatpickr-months .flatpickr-next-month svg { fill: #FEA116 !important; }
    
    /* Ẩn icon mặc định của trình duyệt */
    input[type="datetime-local"]::-webkit-calendar-picker-indicator { display: none; }
    
    /* Input nền trắng chữ đen */
    input.flatpickr-input { background-color: #fff !important; color: #333 !important; }
    
    /* --- [MỚI] TÙY CHỈNH DROPDOWN GIỜ --- */
    .time-dropdown-menu {
        max-height: 200px !important; /* Giới hạn chiều dài danh sách */
        overflow-y: auto !important;  /* Hiện thanh cuộn */
        border: 1px solid #ced4da;
        border-radius: 5px;
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    .time-dropdown-menu .dropdown-item {
        padding: 8px 16px;
        cursor: pointer;
    }
    .time-dropdown-menu .dropdown-item:hover {
        background-color: #FEA116; /* Màu cam khi hover */
        color: #fff;
    }
    /* Style cho nút chọn giờ */
    #dropdownTimeBtn {
        border: 1px solid #ced4da;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0.75rem; /* Căn chỉnh cho khớp với ô input ngày */
    }
    #dropdownTimeBtn::after {
        margin-left: 0.5em;
    }
</style>

{{-- =========================================================== --}}
{{-- 2. HERO SECTION --}}
{{-- =========================================================== --}}
<div class="container-xxl py-5 bg-dark hero-header mb-5">
    <div class="container my-5 py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 text-center text-lg-start">
                <h1 class="display-3 text-white animated slideInLeft">Thưởng Thức<br>Tiệc Buffet Đẳng Cấp</h1>
                <p class="text-white animated slideInLeft mb-4 pb-2">Trải nghiệm ẩm thực tuyệt vời với các gói Combo đa dạng từ 99k đến 499k.</p>
                <a href="#booking-section" class="btn btn-primary py-sm-3 px-sm-5 me-3 animated slideInLeft">Đặt Bàn Ngay</a>
            </div>
            <div class="col-lg-6 text-center text-lg-end overflow-hidden">
                <img class="img-fluid" src="{{ asset('assets/img/hero.png') }}" alt="">
            </div>
        </div>
    </div>
</div>

{{-- =========================================================== --}}
{{-- 3. NỘI DUNG CHÍNH --}}
{{-- =========================================================== --}}

{{-- About Start --}}
<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s" src="{{ asset('assets/img/about-1.jpg') }}">
                    </div>
                    <div class="col-6 text-start">
                        <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.3s" src="{{ asset('assets/img/about-2.jpg') }}" style="margin-top: 25%;">
                    </div>
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.5s" src="{{ asset('assets/img/about-3.jpg') }}">
                    </div>
                    <div class="col-6 text-end">
                        <img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.7s" src="{{ asset('assets/img/about-4.jpg') }}">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <h5 class="section-title ff-secondary text-start text-primary fw-normal">Về Chúng Tôi</h5>
                <h1 class="mb-4">Chào mừng đến với <i class="fa fa-utensils text-primary me-2"></i>Buffet Ocean</h1>
                <p class="mb-4">Nhà hàng Buffet Ocean tự hào mang đến trải nghiệm ẩm thực đẳng cấp với thực đơn phong phú.</p>
                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                            <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">15</h1>
                            <div class="ps-4">
                                <p class="mb-0">Năm</p>
                                <h6 class="text-uppercase mb-0">Kinh Nghiệm</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center border-start border-5 border-primary px-3">
                            <h1 class="flex-shrink-0 display-5 text-primary mb-0" data-toggle="counter-up">50</h1>
                            <div class="ps-4">
                                <p class="mb-0">Đầu Bếp</p>
                                <h6 class="text-uppercase mb-0">Hàng Đầu</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <a class="btn btn-primary py-3 px-5 mt-2" href="/gioi-thieu">Xem Thêm</a>
            </div>
        </div>
    </div>
</div>
{{-- About End --}}

{{-- MENU COMBO --}}
@if (isset($combos) && $combos->count() > 0)
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Thực Đơn Combo</h5>
            <h1 class="mb-5">Các Gói Buffet Đặc Biệt</h1>
        </div>
        
        <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.1s">
            @php $groupedCombos = $combos->groupBy('loai_combo'); @endphp

            {{-- TABS HEADER --}}
            <ul class="nav nav-pills d-inline-flex justify-content-center border-bottom mb-5">
                @foreach ($groupedCombos as $type => $typeCombos)
                    <li class="nav-item">
                        <a class="d-flex align-items-center text-start mx-3 ms-0 pb-3 {{ $loop->first ? 'active' : '' }}"
                           data-bs-toggle="pill" href="#tab-combo-{{ $type }}">
                            <i class="fa fa-utensils fa-2x text-primary"></i>
                            <div class="ps-3">
                                <small class="text-body">Gói</small>
                                <h6 class="mt-n1 mb-0">{{ strtoupper($type) }}</h6>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- TABS CONTENT --}}
            <div class="tab-content">
                @foreach ($groupedCombos as $type => $typeCombos)
                    <div id="tab-combo-{{ $type }}" class="tab-pane fade show p-0 {{ $loop->first ? 'active' : '' }}">
                        <div class="row g-4">
                            @foreach($typeCombos as $combo)
                                @php
                                    $imagePath = $combo->anh;
                                    if ($imagePath && !str_starts_with($imagePath, 'uploads/')) {
                                        $imagePath = 'uploads/' . $imagePath;
                                    }
                                    $imageUrl = $combo->anh ? asset($imagePath) : asset('assets/img/menu-1.jpg');
                                @endphp

                                <div class="col-lg-6">
                                    {{-- ITEM CARD --}}
                                    <div class="d-flex align-items-center product-card-trigger rounded p-3 bg-white shadow-sm h-100"
                                         data-key="combo_{{ $combo->id }}" 
                                         data-type="combo" 
                                         data-name="{{ $combo->ten_combo }}"
                                         data-price="{{ $combo->gia_co_ban }}" 
                                         data-desc="{{ $combo->mo_ta }}"
                                         data-img="{{ $imageUrl }}"
                                         data-dishes="{{ json_encode($combo->danhSachMon ? $combo->danhSachMon->pluck('ten_mon') : []) }}">
                                    
                                        {{-- Ảnh --}}
                                        <img class="flex-shrink-0 img-fluid rounded" src="{{ $imageUrl }}" alt="{{ $combo->ten_combo }}" style="width: 100px; height: 100px; object-fit: cover;">
                                        
                                        {{-- Thông tin --}}
                                        <div class="w-100 d-flex flex-column text-start ps-4">
                                            <h5 class="d-flex justify-content-between border-bottom pb-2">
                                                <span>{{ $combo->ten_combo }}</span>
                                                <span class="text-primary">{{ number_format($combo->gia_co_ban, 0, ',', '.') }} đ</span>
                                            </h5>
                                            <small class="fst-italic text-muted mb-2"><i class="fa fa-clock me-1"></i> Không giới hạn</small>
                                            <small class="fst-italic text-secondary line-clamp-2">{{ \Illuminate\Support\Str::limit($combo->mo_ta, 60) }}</small>
                                            
                                            <div class="mt-auto pt-2">
                                                <button class="btn btn-sm btn-outline-warning fw-bold rounded-pill px-3">
                                                    <i class="fa fa-eye me-1"></i> Xem chi tiết
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- Reservation Start --}}
<div class="container-xxl py-5 px-0 wow fadeInUp" id="booking-section" data-wow-delay="0.1s">
    <div class="row g-0">
        <div class="col-md-6">
            <div class="video">
                <button type="button" class="btn-play" data-bs-toggle="modal" data-src="https://www.youtube.com/embed/DWRcNpR6Kdc" data-bs-target="#videoModal">
                    <span></span>
                </button>
            </div>
        </div>
        <div class="col-md-6 bg-dark d-flex align-items-center">
            <div class="p-5 wow fadeInUp" data-wow-delay="0.2s">
                <h5 class="section-title ff-secondary text-start text-primary fw-normal">Đặt Bàn</h5>
                <h1 class="text-white mb-4">Đặt Bàn Trực Tuyến</h1>

                @if (session('success')) 
                    <div class="alert alert-success alert-dismissible fade show"> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> 
                @endif
                @if (session('error')) 
                    <div class="alert alert-danger alert-dismissible fade show"> {{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> 
                @endif
                
                {{-- Form Đặt Bàn --}}
                <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="cart_data" id="cart_data_input">

                    <div class="row g-3">
                        <div class="col-md-6"><div class="form-floating"><input type="text" class="form-control" id="ten_khach" name="ten_khach" placeholder="Họ và Tên" value="{{ old('ten_khach') }}" required><label for="ten_khach">Họ và Tên</label></div></div>
                        <div class="col-md-6"><div class="form-floating"><input type="email" class="form-control" id="email_khach" name="email_khach" placeholder="Email" value="{{ old('email_khach') }}"><label for="email_khach">Email</label></div></div>
                        <div class="col-md-6"><div class="form-floating"><input type="text" class="form-control" id="sdt_khach" name="sdt_khach" placeholder="Số Điện Thoại" value="{{ old('sdt_khach') }}" required><label for="sdt_khach">Số Điện Thoại</label></div></div>
                        
                        {{-- ========================================================== --}}
                        {{-- [SỬA ĐỔI] NGÀY LÀ FLATPICKR, GIỜ LÀ DROPDOWN CÓ SCROLL --}}
                        {{-- ========================================================== --}}
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-floating">
                                        {{-- Ô Chọn Ngày --}}
                                        <input type="text" class="form-control bg-white" id="booking_date" placeholder="Ngày" required style="cursor: pointer;">
                                        <label for="booking_date">Ngày đến</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating">
                                        {{-- Ô Chọn Giờ (Dùng Bootstrap Dropdown để có Scrollbar) --}}
                                        <div class="dropdown h-100">
                                            <button class="form-control bg-white text-start h-100" type="button" id="dropdownTimeBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                                Chọn giờ
                                            </button>
                                            <ul class="dropdown-menu w-100 time-dropdown-menu" id="timeList">
                                                {{-- JS sẽ điền giờ vào đây --}}
                                            </ul>
                                        </div>
                                        <label style="z-index: 1001; pointer-events: none;"></label>
                                    </div>
                                </div>
                            </div>
                            {{-- Input ẨN để gộp dữ liệu gửi về Server --}}
                            <input type="hidden" name="gio_den" id="gio_den">
                        </div>
                        {{-- ========================================================== --}}

                        <div class="col-md-6"><div class="form-floating"><input type="number" class="form-control" id="nguoi_lon" name="nguoi_lon" placeholder="Người lớn" value="{{ old('nguoi_lon', 1) }}" min="1" required><label for="nguoi_lon">Số Người Lớn</label></div></div>
                        <div class="col-md-6"><div class="form-floating"><input type="number" class="form-control" id="tre_em" name="tre_em" placeholder="Trẻ em" value="{{ old('tre_em', 0) }}" min="0"><label for="tre_em">Số Trẻ Em</label></div></div>

                        <div class="col-12"><div class="form-floating"><textarea class="form-control" placeholder="Ghi chú" id="ghi_chu" name="ghi_chu" style="height: 100px">{{ old('ghi_chu') }}</textarea><label for="ghi_chu">Ghi chú</label></div></div>
                        <div class="col-12"><button class="btn btn-primary w-100 py-3" type="submit">Xác Nhận Đặt Bàn</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Video Modal --}}
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Video Giới Thiệu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <iframe class="embed-responsive-item" src="" id="video" allowfullscreen allowscriptaccess="always" allow="autoplay"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Team Section --}}
{{-- <div class="container-xxl pt-5 pb-3">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Đội Ngũ</h5>
            <h1 class="mb-5">Đầu Bếp Của Chúng Tôi</h1>
        </div>
        <div class="row g-4">
             <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="team-item text-center rounded overflow-hidden">
                    <div class="rounded-circle overflow-hidden m-4">
                        <img class="img-fluid" src="{{ asset('assets/img/team-1.jpg') }}" alt="">
                    </div>
                    <h5 class="mb-0">Nguyễn Văn A</h5>
                    <small>Bếp Trưởng</small>
                    <div class="d-flex justify-content-center mt-3">
                        <a class="btn btn-square btn-primary mx-1" href=""><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container">
        <div class="text-center">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Phản hồi</h5>
            <h1 class="mb-5">Khách Hàng Nói Gì Về Chúng Tôi</h1>
        </div>
        <div class="owl-carousel testimonial-carousel">
            @if(isset($danhGias) && $danhGias->count() > 0)
                @foreach($danhGias as $item)
                <div class="testimonial-item bg-transparent border rounded p-4">
                    <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                    <p>{{ $item->noi_dung }}</p>
                    <div class="d-flex align-items-center">
                        <img class="img-fluid flex-shrink-0 rounded-circle" 
                             src="{{ asset('assets/img/testimonial-1.jpg') }}" 
                             style="width: 50px; height: 50px; object-fit: cover;">
                        <div class="ps-3">
                            <h5 class="mb-1">{{ $item->ten_khach }}</h5>
                            <small>{{ $item->nghe_nghiep ?? 'Thực khách' }}</small>
                            <div class="small text-warning mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $item->so_sao) <i class="fas fa-star"></i>
                                    @else <i class="far fa-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="testimonial-item bg-transparent border rounded p-4">
                    <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                    <p>Dịch vụ tuyệt vời, đồ ăn ngon. Chắc chắn sẽ quay lại!</p>
                    <div class="d-flex align-items-center">
                        <img class="img-fluid flex-shrink-0 rounded-circle" src="{{ asset('assets/img/testimonial-1.jpg') }}" style="width: 50px; height: 50px;">
                        <div class="ps-3">
                            <h5 class="mb-1">Khách hàng mẫu</h5>
                            <small>Thực khách</small>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- =========================================================== --}}
{{-- 4. UI ẨN & JAVASCRIPT --}}
{{-- =========================================================== --}}

{{-- NÚT GIỎ HÀNG BAY --}}
<div id="floatingCartIcon">
    <div class="icon-wrapper">
        <i class="fa fa-shopping-basket"></i>
        <span id="cartCountBadge" class="count-badge">0</span>
    </div>
</div>

{{-- MODAL GIỎ HÀNG --}}
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true" style="z-index: 9999999;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary"><i class="fa fa-receipt me-2"></i>GIỎ HÀNG CỦA BẠN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="billContent">
                <div class="p-3">
                    <ul id="cartItemsList" class="list-unstyled mb-0"></ul>
                    <div id="emptyCartMsg" class="text-center py-5 text-muted" style="display: none;">
                        <i class="fa fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                        <p>Chưa có món nào được chọn</p>
                    </div>
                </div>
                <div id="totalSection" class="p-3 bg-light border-top d-none">
                    <div class="d-flex justify-content-between fw-bold fs-5"><span>Tổng:</span><span id="cartTotalPrint" class="text-danger"></span></div>
                </div>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between align-items-center p-3 shadow-sm">
                <div class="d-flex flex-column">
                    <span class="text-muted small">Tổng tạm tính:</span>
                    <span id="cartTotalDisplay" class="fs-4 fw-bold text-danger">0 đ</span>
                </div>
                <div class="d-flex gap-2">
                    <button id="btnClearCart" class="btn btn-outline-danger"><i class="fa fa-trash"></i></button>
                    <button id="btnSaveBill" class="btn btn-outline-success"><i class="fa fa-download"></i></button>
                    <button id="btnCheckout" class="btn btn-primary fw-bold px-4">XÁC NHẬN <i class="fa fa-arrow-down ms-1"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CHI TIẾT SẢN PHẨM --}}
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true" style="z-index: 99998;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 15px;">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" style="z-index: 10; background-color: white; border-radius: 50%; padding: 8px;"></button>
            <div class="row g-0">
                <div class="col-md-6 bg-light d-flex align-items-center justify-content-center p-0">
                    <img id="modalImg" src="" class="img-fluid" style="width: 100%; height: 100%; min-height: 350px; object-fit: cover;">
                </div>
                <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                    <div class="mb-auto mt-2">
                        <span id="modalType" class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill">Món ăn</span>
                        <h3 id="modalName" class="fw-bold mb-2"></h3>
                        <h4 id="modalPrice" class="text-danger fw-bold mb-4"></h4>
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <h6 class="text-dark fw-bold mb-2"><i class="fa fa-info-circle me-2"></i>Mô tả:</h6>
                            <p id="modalDesc" class="text-muted small mb-0" style="line-height: 1.6;"></p>
                            
                            <div id="modalComboItems" class="mt-3 pt-3 border-top" style="display: none;">
                                <h6 class="text-dark fw-bold mb-2 text-primary"><i class="fa fa-utensils me-2"></i>Món trong Combo:</h6>
                                <ul id="modalComboList" class="list-group list-group-flush small bg-transparent"></ul>
                            </div>
                        </div>
                    </div>
                    <button id="modalAddToCartBtn" class="btn btn-primary w-100 py-3 mt-3 fw-bold rounded-pill shadow-sm">
                        <i class="fa fa-cart-plus me-2"></i> THÊM VÀO GIỎ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        console.log("App script loaded."); 

        // Elements
        let cart = JSON.parse(localStorage.getItem("oceanCart")) || [];
        const floatingCartIcon = document.getElementById("floatingCartIcon");
        const cartModalElement = document.getElementById("cartModal");
        const cartModal = new bootstrap.Modal(cartModalElement);
        const cartItemsList = document.getElementById("cartItemsList");
        const cartCountBadge = document.getElementById("cartCountBadge");
        const emptyCartMsg = document.getElementById("emptyCartMsg");
        const cartTotalDisplay = document.getElementById("cartTotalDisplay");
        const detailModal = new bootstrap.Modal(document.getElementById('productDetailModal'));

        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 1500,
            timerProgressBar: false, didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // 1. RENDER GIỎ HÀNG
        function renderCartUI() {
            const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            if (cartCountBadge) cartCountBadge.innerText = totalCount;

            if (floatingCartIcon) {
                if(totalCount > 0) {
                    floatingCartIcon.style.display = 'flex';
                } else {
                    floatingCartIcon.style.display = 'none';
                    if(cartModalElement.classList.contains('show')) cartModal.hide();
                }
            }

            // Tính tổng tiền
            if (document.getElementById('totalSection')) {
                if(totalCount > 0) document.getElementById('totalSection').classList.remove('d-none');
                else document.getElementById('totalSection').classList.add('d-none');
            }

            if (cartItemsList) {
                cartItemsList.innerHTML = '';
                let totalPrice = 0;

                if (cart.length === 0) {
                    if(emptyCartMsg) emptyCartMsg.style.display = 'block';
                } else {
                    if(emptyCartMsg) emptyCartMsg.style.display = 'none';
                    cart.forEach((item, index) => {
                        totalPrice += item.price * item.quantity;
                        const li = document.createElement('li');
                        li.className = "cart-item-row d-flex justify-content-between align-items-center";
                        li.innerHTML = `
                            <div class="d-flex align-items-center" style="width: 60%;">
                                <img src="${item.img || ''}" class="rounded me-3 d-none d-sm-block" style="width: 50px; height: 50px; object-fit: cover;">
                                <div>
                                    <div class="cart-item-name">${item.name}</div>
                                    <div class="small text-danger fw-bold">${parseInt(item.price).toLocaleString('vi-VN')} đ</div>
                                </div>
                            </div>
                            <div class="qty-control">
                                <button class="btn-qty btn-minus" onclick="updateItem(${index}, -1)">-</button>
                                <span class="qty-display">${item.quantity}</span>
                                <button class="btn-qty btn-plus" onclick="updateItem(${index}, 1)">+</button>
                            </div>
                        `;
                        cartItemsList.appendChild(li);
                    });
                }
                
                const formattedTotal = totalPrice.toLocaleString('vi-VN') + ' đ';
                if(cartTotalDisplay) cartTotalDisplay.innerText = formattedTotal;
                if(document.getElementById('cartTotalPrint')) document.getElementById('cartTotalPrint').innerText = formattedTotal;
            }
        }

        // --- GLOBAL FUNCTIONS CHO HTML ONCLICK ---
        window.updateItem = function(index, change) {
            cart[index].quantity += change;
            if(cart[index].quantity <= 0) cart.splice(index, 1);
            saveCart();
        };

        function saveCart() {
            localStorage.setItem("oceanCart", JSON.stringify(cart));
            renderCartUI();
        }

        // --- HÀM THÊM VÀO GIỎ HÀNG (Logic 1 giá Combo) ---
        function addToCart(newItem) {
            // 1. Logic chỉ cho phép 1 loại giá Combo
            if (newItem.type === 'combo') {
                const existingCombo = cart.find(item => item.type === 'combo');
                if (existingCombo) {
                    const oldPrice = parseInt(existingCombo.price);
                    const newPrice = parseInt(newItem.price);

                    if (oldPrice !== newPrice) {
                        // HIỆN CẢNH BÁO
                        Swal.fire({
                            title: 'Chỉ được chọn 1 mức giá!',
                            text: `Bạn đang chọn gói ${oldPrice.toLocaleString('vi-VN')}đ. Mỗi bàn chỉ được phục vụ một mức giá Buffet. Bạn có muốn xóa giỏ hàng để chọn mức giá mới không?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Xóa giỏ & Chọn món này',
                            cancelButtonText: 'Giữ lại giỏ cũ'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                cart = []; // Xóa sạch giỏ
                                cart.push({ ...newItem, quantity: 1 });
                                saveCart();
                                Toast.fire({ icon: 'success', title: 'Đã đổi sang mức giá mới!' });
                            }
                        });
                        return; // Dừng hàm lại
                    }
                }
            }

            // 2. Logic thêm bình thường
            const existingItem = cart.find(item => item.key === newItem.key);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ ...newItem, quantity: 1 });
            }
            saveCart();
            Toast.fire({ icon: 'success', title: 'Đã thêm món!' });
        }

        if(floatingCartIcon) floatingCartIcon.addEventListener('click', () => { cartModal.show(); });

        // EVENT: CLICK CARD SẢN PHẨM (Mở Modal chi tiết)
        document.querySelectorAll('.product-card-trigger').forEach(card => {
            card.addEventListener('click', function(e) {
                const d = this.dataset;
                
                document.getElementById('modalName').innerText = d.name;
                document.getElementById('modalPrice').innerText = parseInt(d.price).toLocaleString('vi-VN') + ' VNĐ';
                document.getElementById('modalDesc').innerText = d.desc;
                if(d.img) document.getElementById('modalImg').src = d.img;
                
                const badge = document.getElementById('modalType');
                if(d.type === 'combo') { 
                    badge.className='badge bg-danger mb-3 px-3 py-2 rounded-pill'; badge.innerText='Combo Hot'; 
                } else { 
                    badge.className='badge bg-success mb-3 px-3 py-2 rounded-pill'; badge.innerText='Món Ngon'; 
                }

                const comboSection = document.getElementById('modalComboItems');
                const comboList = document.getElementById('modalComboList');
                comboList.innerHTML = ''; 

                if (d.type === 'combo' && d.dishes && d.dishes !== '[]') {
                    try {
                        const dishesArray = JSON.parse(d.dishes);
                        if (dishesArray.length > 0) {
                            comboSection.style.display = 'block'; 
                            dishesArray.forEach(dishName => {
                                const li = document.createElement('li');
                                li.className = 'list-group-item bg-transparent px-0 py-1';
                                li.innerHTML = `<i class="fa fa-check text-success me-2"></i> ${dishName}`;
                                comboList.appendChild(li);
                            });
                        } else { comboSection.style.display = 'none'; }
                    } catch (error) { console.error(error); comboSection.style.display = 'none'; }
                } else { comboSection.style.display = 'none'; }

                // Reset nút thêm để tránh gán nhiều sự kiện
                const oldBtn = document.getElementById('modalAddToCartBtn');
                const newBtn = oldBtn.cloneNode(true);
                oldBtn.parentNode.replaceChild(newBtn, oldBtn);

                newBtn.onclick = function() {
                    addToCart({ key: d.key, name: d.name, price: parseInt(d.price), img: d.img, type: d.type });
                    detailModal.hide();
                };
                detailModal.show();
            });
        });

        // Nút Xóa Hết
        const btnClear = document.getElementById('btnClearCart');
        if(btnClear) {
            btnClear.addEventListener('click', () => {
                if(cart.length === 0) return;
                Swal.fire({
                    title: 'Xóa hết giỏ hàng?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Xóa ngay'
                }).then((result) => {
                    if (result.isConfirmed) {
                        cart = []; saveCart();
                        Toast.fire({icon: 'success', title: 'Đã xóa giỏ hàng'});
                    }
                });
            });
        }

        // Nút Lưu Bill (Ảnh)
        const btnSave = document.getElementById('btnSaveBill');
        if(btnSave) {
            btnSave.addEventListener('click', () => {
                if(cart.length === 0) return;
                document.getElementById('totalSection').classList.remove('d-none');
                html2canvas(document.getElementById("billContent"), { backgroundColor: "#ffffff", scale: 2 }).then(canvas => {
                    const link = document.createElement('a');
                    link.download = 'Bill_ThucDon.png';
                    link.href = canvas.toDataURL();
                    link.click();
                    Toast.fire({ icon: 'success', title: 'Đã tải ảnh hóa đơn' });
                });
            });
        }

        // Nút Xác Nhận (Cuộn xuống form)
        const btnCheckout = document.getElementById('btnCheckout');
        if(btnCheckout) {
            btnCheckout.addEventListener('click', () => {
                if(cart.length === 0) {
                    Toast.fire({ icon: 'warning', title: 'Giỏ hàng đang trống!' });
                    return;
                }
                cartModal.hide(); 
                
                Swal.fire({
                    title: 'Xác nhận đơn hàng?',
                    text: 'Vui lòng điền thông tin vào Form Đặt Bàn bên dưới để hoàn tất.',
                    icon: 'info',
                    showCancelButton: true, 
                    confirmButtonText: 'Điền thông tin', 
                    cancelButtonText: 'Xem lại',
                    confirmButtonColor: '#FEA116', 
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const bookingFormSection = document.getElementById('booking-section');
                        if(bookingFormSection) {
                            bookingFormSection.scrollIntoView({ behavior: 'smooth' });
                            setTimeout(() => {
                                // const inputName = document.getElementById('ten_khach');
                                // if(inputName) inputName.focus();
                                window.location.href = '/booking';
                            }, 800);
                        }
                    } else {
                        cartModal.show();
                    }
                });
            });
        }

        // --- SUBMIT FORM (Gửi kèm Cart Data) ---
        const bookingForm = document.getElementById('bookingForm');
        if(bookingForm) {
            bookingForm.addEventListener('submit', function(e) {
                const cartData = localStorage.getItem("oceanCart");
                const cartInput = document.getElementById('cart_data_input');
                if(cartInput) {
                    cartInput.value = cartData;
                }
                // Sau đó form sẽ submit bình thường lên server
            });
        }

        // --- [MỚI] TẠO DANH SÁCH GIỜ CHO DROPDOWN ---
        function generateTimeSlots() {
            const list = document.getElementById('timeList');
            const btn = document.getElementById('dropdownTimeBtn');
            if(!list || !btn) return;

            const startHour = 10; 
            const endHour = 22;   
            const interval = 30;  

            for (let hour = startHour; hour < endHour; hour++) {
                for (let min = 0; min < 60; min += interval) {
                    const h = hour.toString().padStart(2, '0');
                    const m = min.toString().padStart(2, '0');
                    const time = `${h}:${m}`;
                    
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.className = 'dropdown-item';
                    a.href = '#';
                    a.innerText = time;
                    a.onclick = function(e) {
                        e.preventDefault();
                        btn.innerText = this.innerText; // Update text nút
                        btn.dataset.value = this.innerText; // Lưu giá trị
                        updateHiddenDateTime(); // Gọi hàm update
                    }
                    li.appendChild(a);
                    list.appendChild(li);
                }
            }
            // Giờ cuối
            const endLi = document.createElement('li');
            const endA = document.createElement('a');
            endA.className = 'dropdown-item';
            endA.href = '#';
            endA.innerText = endHour + ":00";
            endA.onclick = function(e) {
                e.preventDefault();
                btn.innerText = this.innerText;
                btn.dataset.value = this.innerText;
                updateHiddenDateTime();
            }
            endLi.appendChild(endA);
            list.appendChild(endLi);
        }
        generateTimeSlots();

        // --- LOGIC GỘP NGÀY + GIỜ ---
        const dateInput = document.getElementById('booking_date');
        const btnTime = document.getElementById('dropdownTimeBtn');
        const hiddenInput = document.getElementById('gio_den');

        function updateHiddenDateTime() {
            // Lấy giá trị ngày thực (Y-m-d) từ Flatpickr
            const dateVal = dateInput._flatpickr ? dateInput._flatpickr.input.value : ''; 
            const timeVal = btnTime.dataset.value;

            if (dateVal && timeVal) {
                hiddenInput.value = dateVal + ' ' + timeVal; // Kết quả: 2025-12-25 09:30
                console.log("Gửi đi:", hiddenInput.value);
            }
        }

        // 1. Kích hoạt Lịch chọn ngày (Flatpickr)
        flatpickr("#booking_date", {
            dateFormat: "Y-m-d", // Định dạng value
            altInput: true,
            altFormat: "d/m/Y",  // Định dạng hiển thị đẹp
            locale: "vn",
            minDate: "today",
            disableMobile: "true",
            onChange: function() {
                updateHiddenDateTime();
            }
        });

        // Render lần đầu
        renderCartUI();
    });
</script>
@endsection