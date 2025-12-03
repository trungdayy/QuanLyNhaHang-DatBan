@extends('layouts.restaurants.layout-shop')

@section('title', 'Trang chủ Buffet Ocean')

@section('content')

{{-- =========================================================== --}}
{{-- PHẦN 1: CSS (GIAO DIỆN & HIỆU ỨNG) --}}
{{-- =========================================================== --}}
<style>
    /* --- FIX LỖI TRÀN MÀN HÌNH --- */
    html,
    body {
        overflow-x: hidden !important;
        width: 100%;
        position: relative;
    }

    /* 1. ICON GIỎ HÀNG NỔI */
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
        box-shadow: 0 10px 25px rgba(255, 107, 107, 0.5);
        transition: transform 0.2s;
    }

    .icon-wrapper:active {
        transform: scale(0.9);
    }

    .count-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #fff;
        color: #d63031;
        font-size: 14px;
        font-weight: 800;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #d63031;
    }

    /* 2. CART ITEM */
    .cart-item-row {
        padding: 15px 0;
        border-bottom: 1px dashed #eee;
    }

    .cart-item-row:last-child {
        border-bottom: none;
    }

    .cart-item-name {
        font-weight: 700;
        color: #333;
        font-size: 1rem;
        line-height: 1.4;
        margin-bottom: 5px;
        white-space: normal !important;
        word-wrap: break-word;
    }

    .qty-control {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8f9fa;
        padding: 5px 10px;
        border-radius: 20px;
        border: 1px solid #eee;
    }

    .btn-qty {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-minus {
        background: #e9ecef;
        color: #333;
    }

    .btn-plus {
        background: #FEA116;
        color: white;
    }

    .qty-display {
        font-weight: bold;
        min-width: 20px;
        text-align: center;
    }

    /* 3. PRODUCT CARD */
    .product-card-trigger {
        cursor: pointer;
        transition: all 0.2s;
    }

    .product-card-trigger:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .product-card-trigger:active {
        transform: scale(0.98);
    }

    .btn-quick-add {
        z-index: 10;
        width: 40px;
        height: 40px;
        border-radius: 50% !important;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    @keyframes popUp {
        from {
            transform: scale(0);
        }

        to {
            transform: scale(1);
        }
    }
</style>

{{-- =========================================================== --}}
{{-- PHẦN 2: NỘI DUNG TRANG CHỦ --}}
{{-- =========================================================== --}}

<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="service-item rounded pt-3">
                    <div class="p-4"><i class="fa fa-3x fa-user-tie text-primary mb-4"></i>
                        <h5>Master Chefs</h5>
                        <p>Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="service-item rounded pt-3">
                    <div class="p-4"><i class="fa fa-3x fa-utensils text-primary mb-4"></i>
                        <h5>Quality Food</h5>
                        <p>Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="service-item rounded pt-3">
                    <div class="p-4"><i class="fa fa-3x fa-cart-plus text-primary mb-4"></i>
                        <h5>Online Order</h5>
                        <p>Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 wow fadeInUp" data-wow-delay="0.7s">
                <div class="service-item rounded pt-3">
                    <div class="p-4"><i class="fa fa-3x fa-headset text-primary mb-4"></i>
                        <h5>24/7 Service</h5>
                        <p>Diam elitr kasd sed at elitr sed ipsum justo dolor sed clita amet diam</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xxl py-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6 text-start"><img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.1s"
                            src="img/about-1.jpg"></div>
                    <div class="col-6 text-start"><img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.3s"
                            src="img/about-2.jpg" style="margin-top: 25%;"></div>
                    <div class="col-6 text-end"><img class="img-fluid rounded w-75 wow zoomIn" data-wow-delay="0.5s"
                            src="img/about-3.jpg"></div>
                    <div class="col-6 text-end"><img class="img-fluid rounded w-100 wow zoomIn" data-wow-delay="0.7s"
                            src="img/about-4.jpg"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <h5 class="section-title ff-secondary text-start text-primary fw-normal">About Us</h5>
                <h1 class="mb-4">Welcome to <i class="fa fa-utensils text-primary me-2"></i>Restoran</h1>
                <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos
                    erat ipsum et lorem et sit, sed stet lorem sit.</p>
                <a class="btn btn-primary py-3 px-5 mt-2" href="">Read More</a>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================== --}}
{{-- PHẦN 3: COMBO BUFFET (ĐÃ CHUẨN HÓA DATA-DISHES & PATH) --}}
{{-- =========================================================== --}}

@if(isset($combos) && $combos->count() > 0)
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Combo Buffet</h5>
            <h1 class="mb-5">Các Combo Đặc Biệt</h1>
        </div>
        <div class="row g-4">
            @foreach($combos as $combo)
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                {{-- CARD COMBO --}}
                <div class="combo-item rounded overflow-hidden product-card-trigger position-relative h-100 shadow-sm bg-white"
                    data-key="combo_{{ $combo->id }}" data-type="combo" data-name="{{ $combo->ten_combo }}"
                    data-price="{{ $combo->gia_co_ban }}" data-desc="{{ $combo->mo_ta ?? 'Không có mô tả' }}"
                    data-img="{{ $combo->anh ? asset('uploads/' . $combo->anh) : '' }}"
                    data-time="{{ $combo->thoi_luong_phut ? $combo->thoi_luong_phut . ' phút' : '' }}"
                    {{-- QUAN TRỌNG: Lấy danh sách món ăn từ quan hệ monAn --}}
                    data-dishes="{{ json_encode($combo->monAn ? $combo->monAn->pluck('ten_mon') : []) }}">

                    <div class="position-relative">
                        @if($combo->anh)
                        {{-- QUAN TRỌNG: Thêm 'uploads/' vì trong DB chưa có --}}
                        <img class="img-fluid w-100" src="{{ asset('uploads/' . $combo->anh) }}"
                            alt="{{ $combo->ten_combo }}" style="height: 250px; object-fit: cover;">
                        @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center"
                            style="height: 250px;">
                            <i class="fa fa-utensils fa-3x text-white"></i>
                        </div>
                        @endif

                        <div class="position-absolute top-0 start-0 p-3">
                            <span class="badge bg-danger text-white px-3 py-2 rounded-pill">Combo Hot</span>
                        </div>
                        <button class="btn btn-primary position-absolute top-0 end-0 m-2 btn-quick-add"
                            title="Thêm ngay">
                            <i class="fa fa-plus text-white"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <h5 class="mb-2 fw-bold text-dark">{{ $combo->ten_combo }}</h5>
                        <p class="text-primary mb-2 fw-bold fs-5">{{ number_format($combo->gia_co_ban,0,',','.') }} VNĐ
                        </p>
                        <p class="text-muted small mb-0"><i class="fa fa-clock me-1"></i> {{ $combo->thoi_luong_phut }}
                            phút</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- =========================================================== --}}
{{-- PHẦN 4: THỰC ĐƠN (DANH MỤC & MÓN ĂN) --}}
{{-- =========================================================== --}}

@if(isset($danhMucs) && $danhMucs->count() > 0)
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Food Menu</h5>
            <h1 class="mb-5">Thực Đơn Nhà Hàng</h1>
        </div>
        <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.1s">
            <ul class="nav nav-pills d-inline-flex justify-content-center border-bottom mb-5"
                style="overflow-x: auto; flex-wrap: nowrap; white-space: nowrap; -webkit-overflow-scrolling: touch;">
                @foreach($danhMucs as $index => $danhMuc)
                <li class="nav-item">
                    <a class="d-flex align-items-center text-start mx-3 {{ $index === 0 ? 'ms-0 active' : '' }} {{ $index === $danhMucs->count() - 1 ? 'me-0' : '' }} pb-3"
                        data-bs-toggle="pill" href="#tab-{{ $danhMuc->id }}">
                        <i class="fa fa-utensils fa-2x text-primary"></i>
                        <div class="ps-3"><small class="text-body">{{ $danhMuc->monAn->count() }} món</small>
                            <h6 class="mt-n1 mb-0">{{ $danhMuc->ten_danh_muc }}</h6>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach($danhMucs as $index => $danhMuc)
                <div id="tab-{{ $danhMuc->id }}" class="tab-pane fade show p-0 {{ $index === 0 ? 'active' : '' }}">
                    <div class="row g-4">
                        @forelse($danhMuc->monAn as $mon)
                        <div class="col-lg-6">
                            {{-- CARD MÓN ĂN --}}
                            <div class="d-flex align-items-center menu-item product-card-trigger position-relative bg-white rounded shadow-sm p-3 h-100"
                                data-key="mon_{{ $mon->id }}" data-type="mon" data-name="{{ $mon->ten_mon }}"
                                data-price="{{ $mon->gia }}" data-desc="{{ $mon->mo_ta ?? 'Không có mô tả' }}"
                                {{-- QUAN TRỌNG: Món ăn đã có sẵn đường dẫn trong DB, không thêm 'uploads/' --}}
                                data-img="{{ $mon->hinh_anh ? asset($mon->hinh_anh) : '' }}"
                                data-time="{{ $mon->loai_mon }}">

                                <div class="flex-shrink-0 position-relative">
                                    @if($mon->hinh_anh)
                                    <img class="img-fluid rounded" src="{{ asset($mon->hinh_anh) }}"
                                        alt="{{ $mon->ten_mon }}"
                                        style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                        style="width: 100px; height: 100px;"><i
                                            class="fa fa-utensils text-white fa-2x"></i></div>
                                    @endif
                                </div>
                                <div class="w-100 d-flex flex-column text-start ps-3">
                                    <h5 class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                        <span class="text-dark fw-bold"
                                            style="font-size: 1rem;">{{ $mon->ten_mon }}</span>
                                        <span class="text-primary fw-bold">{{ number_format($mon->gia,0,',','.') }}
                                            đ</span>
                                    </h5>
                                    <small
                                        class="fst-italic text-muted line-clamp-2">{{ Str::limit($mon->mo_ta, 60) }}</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary ms-2 btn-quick-add position-relative">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-muted">Danh mục này chưa có món ăn.</div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<div class="container-xxl pt-5 pb-3">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Team Members</h5>
            <h1 class="mb-5">Our Master Chefs</h1>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="team-item text-center rounded overflow-hidden">
                    <div class="rounded-circle overflow-hidden m-4"><img class="img-fluid" src="img/team-1.jpg" alt="">
                    </div>
                    <h5 class="mb-0">Full Name</h5><small>Designation</small>
                    <div class="d-flex justify-content-center mt-3"><a class="btn btn-square btn-primary mx-1"
                            href=""><i class="fab fa-facebook-f"></i></a></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="team-item text-center rounded overflow-hidden">
                    <div class="rounded-circle overflow-hidden m-4"><img class="img-fluid" src="img/team-2.jpg" alt="">
                    </div>
                    <h5 class="mb-0">Full Name</h5><small>Designation</small>
                    <div class="d-flex justify-content-center mt-3"><a class="btn btn-square btn-primary mx-1"
                            href=""><i class="fab fa-facebook-f"></i></a></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="team-item text-center rounded overflow-hidden">
                    <div class="rounded-circle overflow-hidden m-4"><img class="img-fluid" src="img/team-3.jpg" alt="">
                    </div>
                    <h5 class="mb-0">Full Name</h5><small>Designation</small>
                    <div class="d-flex justify-content-center mt-3"><a class="btn btn-square btn-primary mx-1"
                            href=""><i class="fab fa-facebook-f"></i></a></div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="0.7s">
                <div class="team-item text-center rounded overflow-hidden">
                    <div class="rounded-circle overflow-hidden m-4"><img class="img-fluid" src="img/team-4.jpg" alt="">
                    </div>
                    <h5 class="mb-0">Full Name</h5><small>Designation</small>
                    <div class="d-flex justify-content-center mt-3"><a class="btn btn-square btn-primary mx-1"
                            href=""><i class="fab fa-facebook-f"></i></a></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
    <div class="container">
        <div class="text-center">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Testimonial</h5>
            <h1 class="mb-5">Our Clients Say!!!</h1>
        </div>
        <div class="owl-carousel testimonial-carousel">
            <div class="testimonial-item bg-transparent border rounded p-4">
                <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                <p>Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="img/testimonial-1.jpg"
                        style="width: 50px; height: 50px;">
                    <div class="ps-3">
                        <h5 class="mb-1">Client Name</h5><small>Profession</small>
                    </div>
                </div>
            </div>
            <div class="testimonial-item bg-transparent border rounded p-4">
                <i class="fa fa-quote-left fa-2x text-primary mb-3"></i>
                <p>Dolor et eos labore, stet justo sed est sed. Diam sed sed dolor stet amet eirmod eos labore diam</p>
                <div class="d-flex align-items-center">
                    <img class="img-fluid flex-shrink-0 rounded-circle" src="img/testimonial-2.jpg"
                        style="width: 50px; height: 50px;">
                    <div class="ps-3">
                        <h5 class="mb-1">Client Name</h5><small>Profession</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================== --}}
{{-- PHẦN 5: FLOATING UI & MODALS (ĐÃ CẬP NHẬT ĐẦY ĐỦ) --}}
{{-- =========================================================== --}}

<div id="floatingCartIcon">
    <div class="icon-wrapper">
        <i class="fa fa-shopping-basket"></i>
        <span id="cartCountBadge" class="count-badge">0</span>
    </div>
</div>

{{-- MODAL GIỎ HÀNG --}}
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary"><i class="fa fa-receipt me-2"></i>GIỎ HÀNG CỦA BẠN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0" id="billContent">
                <div class="p-3">
                    <ul id="cartItemsList" class="list-unstyled mb-0">
                    </ul>
                    <div id="emptyCartMsg" class="text-center py-5 text-muted" style="display: none;">
                        <i class="fa fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                        <p>Chưa có món nào được chọn</p>
                    </div>
                </div>
                <div id="totalSection" class="p-3 bg-light border-top d-none">
                    <div class="d-flex justify-content-between fw-bold fs-5"><span>Tổng:</span><span id="cartTotalPrint"
                            class="text-danger"></span></div>
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
                    <button id="btnCheckout" class="btn btn-primary fw-bold px-4">XÁC NHẬN <i
                            class="fa fa-arrow-down ms-1"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CHI TIẾT SẢN PHẨM (ĐÃ SỬA ĐỂ HIỆN MÓN TRONG COMBO) --}}
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 15px;">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                style="z-index: 10; background-color: white; border-radius: 50%; padding: 8px;"></button>
            <div class="row g-0">
                <div class="col-md-6 bg-light d-flex align-items-center justify-content-center p-0">
                    <img id="modalImg" src="" class="img-fluid"
                        style="width: 100%; height: 100%; min-height: 350px; object-fit: cover;">
                </div>
                <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                    <div class="mb-auto mt-2">
                        <span id="modalType" class="badge bg-warning text-dark mb-3 px-3 py-2 rounded-pill">Món
                            ăn</span>
                        <h3 id="modalName" class="fw-bold mb-2"></h3>
                        <h4 id="modalPrice" class="text-danger fw-bold mb-4"></h4>
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <h6 class="text-dark fw-bold mb-2"><i class="fa fa-info-circle me-2"></i>Mô tả:</h6>
                            <p id="modalDesc" class="text-muted small mb-0" style="line-height: 1.6;"></p>

                            {{-- 👇👇👇 KHU VỰC HIỆN DANH SÁCH MÓN TRONG COMBO 👇👇👇 --}}
                            <div id="modalComboItems" class="mt-3 pt-3 border-top" style="display: none;">
                                <h6 class="text-dark fw-bold mb-2 text-primary"><i class="fa fa-utensils me-2"></i>Món
                                    trong Combo:</h6>
                                <ul id="modalComboList" class="list-group list-group-flush small bg-transparent">
                                    {{-- JS sẽ điền danh sách món vào đây --}}
                                </ul>
                            </div>
                            {{-- 👆👆👆 KẾT THÚC KHU VỰC THÊM 👆👆👆 --}}
                        </div>
                    </div>
                    <button id="modalAddToCartBtn"
                        class="btn btn-primary w-100 py-3 mt-3 fw-bold rounded-pill shadow-sm">
                        <i class="fa fa-cart-plus me-2"></i> THÊM VÀO GIỎ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================== --}}
{{-- PHẦN 6: JAVASCRIPT (ĐÃ CẬP NHẬT LOGIC HIỂN THỊ COMBO) --}}
{{-- =========================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- CẤU HÌNH ---
        // Đường dẫn trang booking (Sửa lại nếu cần)
        const BOOKING_URL = "/booking"; 

        // --- KHỞI TẠO ---
        let cart = JSON.parse(localStorage.getItem("oceanCart")) || [];
        
        // Elements
        const floatingCartIcon = document.getElementById("floatingCartIcon");
        const cartModalElement = document.getElementById("cartModal");
        const cartModal = new bootstrap.Modal(cartModalElement);
        const cartItemsList = document.getElementById("cartItemsList");
        const cartCountBadge = document.getElementById("cartCountBadge");
        const emptyCartMsg = document.getElementById("emptyCartMsg");
        const cartTotalDisplay = document.getElementById("cartTotalDisplay");
        const detailModal = new bootstrap.Modal(document.getElementById('productDetailModal'));

        // Toast config
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
            cartCountBadge.innerText = totalCount;

            if(totalCount > 0) {
                floatingCartIcon.style.display = 'flex';
                document.getElementById('totalSection').classList.remove('d-none');
            } else {
                floatingCartIcon.style.display = 'none';
                document.getElementById('totalSection').classList.add('d-none');
                if(cartModalElement.classList.contains('show')) cartModal.hide();
            }

            cartItemsList.innerHTML = '';
            let totalPrice = 0;

            if (cart.length === 0) {
                emptyCartMsg.style.display = 'block';
            } else {
                emptyCartMsg.style.display = 'none';
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
                            <button class="btn-qty btn-minus" data-index="${index}">-</button>
                            <span class="qty-display">${item.quantity}</span>
                            <button class="btn-qty btn-plus" data-index="${index}">+</button>
                        </div>
                    `;
                    cartItemsList.appendChild(li);
                });
            }
            
            const formattedTotal = totalPrice.toLocaleString('vi-VN') + ' đ';
            cartTotalDisplay.innerText = formattedTotal;
            document.getElementById('cartTotalPrint').innerText = formattedTotal;

            // Gắn sự kiện Tăng/Giảm
            document.querySelectorAll('.btn-plus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = this.dataset.index;
                    cart[idx].quantity++;
                    saveCart();
                });
            });

            document.querySelectorAll('.btn-minus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = this.dataset.index;
                    if(cart[idx].quantity > 1) {
                        cart[idx].quantity--;
                    } else {
                        cart.splice(idx, 1);
                    }
                    saveCart();
                });
            });
        }

        function saveCart() {
            localStorage.setItem("oceanCart", JSON.stringify(cart));
            renderCartUI();
        }

        // 2. THÊM VÀO GIỎ
        function addToCart(newItem) {
            const existingItem = cart.find(item => item.key === newItem.key);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ ...newItem, quantity: 1 });
            }
            saveCart();
            Toast.fire({ icon: 'success', title: 'Đã thêm món!' });
        }

        // 3. SỰ KIỆN UI & MODAL CHI TIẾT
        floatingCartIcon.addEventListener('click', () => { cartModal.show(); });

        // --- SỰ KIỆN CLICK SẢN PHẨM (QUAN TRỌNG: XỬ LÝ COMBO) ---
        document.querySelectorAll('.product-card-trigger').forEach(card => {
            card.addEventListener('click', function(e) {
                if(e.target.closest('.btn-quick-add')) return;
                
                const d = this.dataset;
                
                // Điền thông tin cơ bản
                document.getElementById('modalName').innerText = d.name;
                document.getElementById('modalPrice').innerText = parseInt(d.price).toLocaleString('vi-VN') + ' VNĐ';
                document.getElementById('modalDesc').innerText = d.desc;
                if(d.img) document.getElementById('modalImg').src = d.img;
                
                // Config Badge
                const badge = document.getElementById('modalType');
                if(d.type === 'combo') { 
                    badge.className='badge bg-danger mb-3 px-3 py-2 rounded-pill'; 
                    badge.innerText='Combo Hot'; 
                } else { 
                    badge.className='badge bg-success mb-3 px-3 py-2 rounded-pill'; 
                    badge.innerText='Món Ngon'; 
                }

                // 👇👇👇 XỬ LÝ HIỂN THỊ DANH SÁCH MÓN TRONG COMBO 👇👇👇
                const comboSection = document.getElementById('modalComboItems');
                const comboList = document.getElementById('modalComboList');
                comboList.innerHTML = ''; // Xóa danh sách cũ

                if (d.type === 'combo' && d.dishes) {
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
                        } else {
                            comboSection.style.display = 'none';
                        }
                    } catch (error) {
                        console.error("Lỗi parse JSON món ăn:", error);
                        comboSection.style.display = 'none';
                    }
                } else {
                    comboSection.style.display = 'none';
                }
                // 👆👆👆 KẾT THÚC XỬ LÝ COMBO 👆👆👆

                document.getElementById('modalAddToCartBtn').onclick = function() {
                    addToCart({ 
                        key: d.key, name: d.name, price: parseInt(d.price), img: d.img
                    });
                    detailModal.hide();
                };
                detailModal.show();
            });
        });

        // Click Thêm nhanh
        document.querySelectorAll('.btn-quick-add').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const card = this.closest('.product-card-trigger');
                const d = card.dataset;
                
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fa fa-check"></i>';
                setTimeout(() => this.innerHTML = originalHTML, 1000);
                
                addToCart({ 
                    key: d.key, name: d.name, price: parseInt(d.price), img: d.img
                });
            });
        });

        // Xóa hết
        document.getElementById('btnClearCart').addEventListener('click', () => {
            if(cart.length === 0) return;
            Swal.fire({
                title: 'Xóa giỏ hàng?', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Xóa', cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) { cart = []; saveCart(); }
            })
        });

        // Lưu bill
        document.getElementById('btnSaveBill').addEventListener('click', () => {
            if(cart.length === 0) return;
            document.getElementById('totalSection').classList.remove('d-none');
            html2canvas(document.getElementById("billContent"), {
                backgroundColor: "#ffffff", scale: 2
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Bill_ThucDon.png';
                link.href = canvas.toDataURL();
                link.click();
                Toast.fire({ icon: 'success', title: 'Đã tải ảnh hóa đơn' });
            });
        });

        // 4. CHUYỂN HƯỚNG BOOKING
        document.getElementById('btnCheckout').addEventListener('click', () => {
            if(cart.length === 0) {
                Toast.fire({ icon: 'warning', title: 'Giỏ hàng đang trống!' });
                return;
            }

            cartModal.hide();
            Swal.fire({
                title: 'Xác nhận đơn hàng?',
                text: 'Chúng tôi sẽ chuyển bạn đến trang Đặt Bàn để hoàn tất thông tin.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Đồng ý, Chuyển đi',
                cancelButtonText: 'Xem lại',
                confirmButtonColor: '#FEA116',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = BOOKING_URL;
                } else {
                    cartModal.show();
                }
            });
        });

        // Render lần đầu
        renderCartUI();
    });
</script>

@endsection