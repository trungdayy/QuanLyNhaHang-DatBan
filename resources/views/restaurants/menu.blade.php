@extends('layouts.page')

@section('title', 'Thực đơn')

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

    /* Đảm bảo nội dung mô tả chỉ hiển thị 2 dòng trong thẻ card */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* HIỆU ỨNG CARD SẢN PHẨM */
    .product-card-trigger { 
        cursor: pointer; 
        transition: all 0.3s; 
    }
    .product-card-trigger:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important; 
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

    /* SIDEBAR MENU */
    .menu-sidebar {
        position: sticky;
        top: 100px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 20px 0;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }
    .menu-sidebar .menu-item {
        padding: 12px 20px;
        cursor: pointer;
        border-left: 3px solid transparent;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .menu-sidebar .menu-item:hover {
        background: #f8f9fa;
        border-left-color: #FEA116;
    }
    .menu-sidebar .menu-item.active {
        background: #fff5e6;
        border-left-color: #FEA116;
        font-weight: bold;
        color: #FEA116;
    }
    .menu-sidebar .menu-item i {
        width: 20px;
        text-align: center;
    }
    .menu-content-section {
        display: none;
    }
    .menu-content-section.active {
        display: block;
    }
    
    /* SUBMENU STYLES */
    .submenu-container {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        display: none !important;
    }
    .submenu-container.show {
        max-height: 2000px;
        transition: max-height 0.5s ease-in;
        display: block !important;
    }
    .menu-item.submenu-item {
        opacity: 0.8;
    }
    .menu-item.submenu-item:hover {
        opacity: 1;
    }
    .menu-item.submenu-item.active {
        opacity: 1;
        background: #fff5e6;
        border-left-color: #FEA116;
        font-weight: bold;
        color: #FEA116;
    }
</style>

{{-- =========================================================== --}}
{{-- 2. MENU VỚI SIDEBAR --}}
{{-- =========================================================== --}}
<div class="container-xxl py-5">
    <div class="container">
        <div class="text-center wow fadeInUp mb-5" data-wow-delay="0.1s">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Thực Đơn</h5>
            <h1 class="mb-0">Combo Buffet & Món ăn</h1>
        </div>
        
        <div class="row">
            {{-- SIDEBAR MENU --}}
            <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
                <div class="menu-sidebar">
                    <h6 class="px-3 mb-3 text-primary fw-bold">
                        <i class="fa fa-list me-2"></i>Danh Mục
                    </h6>
                    
                    {{-- Menu item: Combo Buffet --}}
                    @if(isset($combos) && $combos->count() > 0)
                        <div class="menu-item active" data-category="combo-buffet">
                            <i class="fa fa-gift"></i>
                            <span>Combo Buffet</span>
                            <small class="ms-auto text-muted">({{ $combos->count() }})</small>
                        </div>
                    @endif
                    
                    {{-- Menu item: Món gọi thêm --}}
                    @if(isset($danhMucs) && $danhMucs->count() > 0)
                        @php
                            $totalMonGoiThem = $danhMucs->sum(function($dm) { return $dm->monAn->count(); });
                        @endphp
                        <div class="menu-item" data-category="mon-goi-them" id="menu-item-mon-goi-them">
                            <i class="fa fa-utensils"></i>
                            <span>Món ăn</span>
                            <small class="ms-auto text-muted">({{ $totalMonGoiThem }})</small>
                        </div>
                        
                        {{-- Submenu: Các danh mục con --}}
                        <div class="submenu-container" id="submenu-mon-goi-them">
                            @foreach($danhMucs as $index => $danhMuc)
                                @if($danhMuc->monAn && $danhMuc->monAn->count() > 0)
                                    <div class="menu-item submenu-item" 
                                         data-category="mon-goi-them-{{ $danhMuc->id }}"
                                         data-parent="mon-goi-them"
                                         style="padding-left: 40px; font-size: 0.9rem;">
                                        <i class="fa fa-circle" style="font-size: 0.5rem;"></i>
                                        <span>{{ $danhMuc->ten_danh_muc }}</span>
                                        <small class="ms-auto text-muted">({{ $danhMuc->monAn->count() }})</small>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- CONTENT AREA --}}
            <div class="col-lg-9 col-md-8">
                
                {{-- SECTION: COMBO BUFFET (ĐÃ CHỈNH SỬA: Căn giữa, to hơn nếu ít combo) --}}
{{-- SECTION: COMBO BUFFET (LAYOUT NGANG: ẢNH TRÁI - TIN PHẢI) --}}
                <div class="menu-content-section active" id="section-combo-buffet">
                    @if (isset($combos) && $combos->count() > 0)
                        @php $groupedCombos = $combos->groupBy('loai_combo'); @endphp
                        
                        @foreach ($groupedCombos as $type => $typeCombos)
                            <div class="mb-5">
                                <h3 class="text-primary mb-4 border-bottom pb-2">
                                    <i class="fa fa-gift me-2"></i>{{ strtoupper($type) }}
                                </h3>
                                
                                <div class="d-flex flex-column gap-4">
                                    @foreach($typeCombos as $combo)
                                        @php
                                            $imagePath = $combo->anh;
                                            if ($imagePath && !str_starts_with($imagePath, 'uploads/')) {
                                                $imagePath = 'uploads/' . $imagePath;
                                            }
                                            $imageUrl = $combo->anh ? asset($imagePath) : asset('assets/img/menu-1.jpg');
                                            
                                            $monTrongCombo = $combo->monTrongCombo->pluck('monAn.ten_mon')->filter();
                                        @endphp

                                        {{-- THẺ CARD NẰM NGANG --}}
                                        <div class="card shadow-sm border-0 overflow-hidden product-card-trigger w-100"
                                            data-key="combo_{{ $combo->id }}"
                                            data-type="combo"
                                            data-name="{{ $combo->ten_combo }}"
                                            data-price="{{ $combo->gia_co_ban }}"
                                            data-desc="{{ $combo->mo_ta }}"
                                            data-img="{{ $imageUrl }}"
                                            data-dishes="{{ json_encode($monTrongCombo->toArray()) }}">
                                            
                                            <div class="row g-0 h-100">
                                                {{-- CỘT 1: ẢNH (Chiếm 4/12 trên desktop, 5/12 trên tablet) --}}
                                                <div class="col-md-5 col-lg-4 position-relative">
                                                    <img src="{{ $imageUrl }}" 
                                                         class="img-fluid h-100 w-100" 
                                                         alt="{{ $combo->ten_combo }}" 
                                                         style="object-fit: cover; min-height: 250px;">
                                                    
                                                    {{-- Badge giá nổi trên ảnh (chỉ hiện ở mobile cho đẹp) --}}
                                                    <div class="position-absolute top-0 start-0 m-2 d-md-none">
                                                        <span class="badge bg-danger fs-6 shadow">{{ number_format($combo->gia_co_ban, 0, ',', '.') }} đ</span>
                                                    </div>
                                                </div>
                                                
                                                {{-- CỘT 2: THÔNG TIN (Chiếm phần còn lại) --}}
                                                <div class="col-md-7 col-lg-8">
                                                    <div class="card-body d-flex flex-column h-100 p-4">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h4 class="card-title fw-bold text-dark mb-0">{{ $combo->ten_combo }}</h4>
                                                            <h4 class="text-primary fw-bolder d-none d-md-block">{{ number_format($combo->gia_co_ban, 0, ',', '.') }} đ</h4>
                                                        </div>
                                                        
                                                        <p class="card-text text-secondary mb-3" style="font-size: 0.95rem; line-height: 1.6;">
                                                            {{ $combo->mo_ta }}
                                                        </p>
                                                        
                                                        {{-- Hiển thị món trong combo --}}
                                                        @if($monTrongCombo->count() > 0)
                                                            <div class="mb-3">
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    @foreach($monTrongCombo->take(6) as $tenMon)
                                                                        <span class="badge bg-light text-dark border px-2 py-1"><i class="fa fa-check text-success me-1 small"></i>{{ $tenMon }}</span>
                                                                    @endforeach
                                                                    @if($monTrongCombo->count() > 6)
                                                                        <span class="badge bg-light text-secondary border px-2 py-1">+{{ $monTrongCombo->count() - 6 }} món khác</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                        
                                                        <div class="mt-auto pt-3 border-top">
                                                            <button class="btn btn-outline-primary rounded-pill px-4 fw-bold float-end">
                                                                Xem chi tiết <i class="fa fa-arrow-right ms-2"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fa fa-utensils fa-4x text-muted opacity-25"></i>
                            </div>
                            <h5 class="text-muted">Hiện chưa có Combo nào đang mở bán.</h5>
                        </div>
                    @endif
                </div>
                
                {{-- SECTION: MÓN GỌI THÊM (GIỮ NGUYÊN) --}}
                @if(isset($danhMucs) && $danhMucs->count() > 0)
                    @foreach($danhMucs as $index => $danhMuc)
                        @if($danhMuc->monAn && $danhMuc->monAn->count() > 0)
                            <div class="menu-content-section {{ $index === 0 ? '' : '' }}" id="section-mon-goi-them-{{ $danhMuc->id }}">
                                <h3 class="text-primary mb-4">
                                    <i class="fa fa-utensils me-2"></i>{{ $danhMuc->ten_danh_muc }}
                                </h3>
                                <div class="row g-4">
                                    @foreach($danhMuc->monAn as $monAn)
                                        @php
                                            $imagePath = $monAn->hinh_anh;
                                            if ($imagePath && !str_starts_with($imagePath, 'uploads/')) {
                                                $imagePath = 'uploads/' . $imagePath;
                                            }
                                            $imageUrl = $monAn->hinh_anh ? asset($imagePath) : asset('assets/img/menu-1.jpg');
                                        @endphp
                                        
                                        <div class="col-lg-4 col-md-6 col-sm-6">
                                            <div class="card h-100 border-0 shadow-sm product-card-trigger"
                                                data-key="mon_an_{{ $monAn->id }}"
                                                data-type="mon_an"
                                                data-name="{{ $monAn->ten_mon }}"
                                                data-price="{{ $monAn->gia }}"
                                                data-desc="{{ $monAn->mo_ta ?? 'Món ăn ngon, được chế biến từ nguyên liệu tươi ngon.' }}"
                                                data-img="{{ $imageUrl }}">
                                                
                                                <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $monAn->ten_mon }}" style="height: 200px; object-fit: cover;">
                                                
                                                <div class="card-body d-flex flex-column p-3">
                                                    <h6 class="card-title fw-bold mb-2">{{ $monAn->ten_mon }}</h6>
                                                    <p class="card-text text-secondary line-clamp-2 mb-2" style="font-size: 0.85rem;">
                                                        {{ \Illuminate\Support\Str::limit($monAn->mo_ta ?? 'Món ăn ngon, được chế biến từ nguyên liệu tươi ngon.', 80) }}
                                                    </p>
                                                    <h5 class="text-primary fw-bolder mb-3">{{ number_format($monAn->gia, 0, ',', '.') }} đ</h5>
                                                    
                                                    <div class="mt-auto">
                                                        <button class="btn btn-sm btn-outline-warning fw-bold rounded-pill px-3 w-100">
                                                            <i class="fa fa-eye me-1"></i> Chi tiết
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="menu-content-section" id="section-mon-goi-them">
                        <div class="text-center py-5">
                            <p class="text-muted">Chưa có món gọi thêm nào.</p>
                        </div>
                    </div>
                @endif
            </div>
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
                    <button id="btnClearCart" class="btn btn-outline-danger"><i class="fa fa-trash"></i> Xóa hết</button>
                    <button id="btnSaveBill" class="btn btn-outline-success"><i class="fa fa-download"></i> Lưu ảnh</button>
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
                    {{-- Nút này sẽ được JS ẩn đi nếu là món thường --}}
                    <button id="modalAddToCartBtn" class="btn btn-primary w-100 py-3 mt-3 fw-bold rounded-pill shadow-sm">
                        <i class="fa fa-cart-plus me-2"></i> THÊM VÀO GIỎ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Thư viện cần thiết --}}
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

        // 1. LƯU & RENDER GIỎ HÀNG
        function saveCart() {
            localStorage.setItem("oceanCart", JSON.stringify(cart));
            renderCartUI();
        }

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

        // --- GLOBAL FUNCTIONS CHO HTML ONCLICK (Điều chỉnh số lượng) ---
        window.updateItem = function(index, change) {
            cart[index].quantity += change;
            if(cart[index].quantity <= 0) cart.splice(index, 1);
            saveCart();
        };

        // --- HÀM THÊM VÀO GIỎ HÀNG (Logic 1 giá Combo) ---
        function addToCart(newItem) {
            // 1. Logic chỉ cho phép 1 loại giá Combo (quan trọng)
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


        // EVENT: MỞ GIỎ HÀNG
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
                const btnAdd = document.getElementById('modalAddToCartBtn'); // Nút thêm giỏ

                // === LOGIC MỚI: KIỂM TRA LOẠI MÓN ===
                if(d.type === 'combo') { 
                    badge.className='badge bg-danger text-white mb-2 px-3 py-2 rounded-pill'; 
                    badge.innerText='Combo Hot'; 
                    btnAdd.style.display = 'block'; // Hiện nút nếu là Combo
                } else { 
                    badge.className='badge bg-success mb-2 px-3 py-2 rounded-pill'; 
                    badge.innerText='Món Ngon'; 
                    btnAdd.style.display = 'none'; // ẨN NÚT NẾU LÀ MÓN ĂN THƯỜNG
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

                // Reset nút thêm để tránh gán nhiều sự kiện (Chỉ cần thiết nếu nút hiển thị)
                if(d.type === 'combo') {
                    const oldBtn = document.getElementById('modalAddToCartBtn');
                    const newBtn = oldBtn.cloneNode(true);
                    oldBtn.parentNode.replaceChild(newBtn, oldBtn);

                    newBtn.onclick = function() {
                        addToCart({ key: d.key, name: d.name, price: parseInt(d.price), img: d.img, type: d.type });
                        detailModal.hide();
                    };
                }
                
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

        // Nút Xác Nhận (Chuyển hướng đến trang /booking)
        const btnCheckout = document.getElementById('btnCheckout');
        if(btnCheckout) {
            btnCheckout.addEventListener('click', () => {
                if(cart.length === 0) {
                    Toast.fire({ icon: 'warning', title: 'Giỏ hàng đang trống!' });
                    return;
                }
                cartModal.hide();
                window.location.href = '/booking';
            });
        }

        // --- LOGIC CHO PHẦN ĐẶT BÀN ---
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
                        btn.innerText = this.innerText; 
                        btn.dataset.value = this.innerText; 
                        updateHiddenDateTime();
                    }
                    li.appendChild(a);
                    list.appendChild(li);
                }
            }
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

        const dateInput = document.getElementById('booking_date');
        const btnTime = document.getElementById('dropdownTimeBtn');
        const hiddenInput = document.getElementById('gio_den');

        function updateHiddenDateTime() {
            const dateVal = dateInput && dateInput._flatpickr ? dateInput._flatpickr.input.value : ''; 
            const timeVal = btnTime ? btnTime.dataset.value : '';

            if (dateVal && timeVal && hiddenInput) {
                hiddenInput.value = dateVal + ' ' + timeVal;
                console.log("Gửi đi:", hiddenInput.value);
            }
        }

        if (dateInput) {
            flatpickr("#booking_date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d/m/Y",
                locale: "vn",
                minDate: "today",
                disableMobile: "true",
                onChange: function() {
                    updateHiddenDateTime();
                }
            });
        }
        // Render lần đầu
        renderCartUI();

        // ===========================================================
        // SIDEBAR MENU HANDLER
        // ===========================================================
        const menuItems = document.querySelectorAll('.menu-sidebar .menu-item:not(.submenu-item)');
        const submenuItems = document.querySelectorAll('.menu-sidebar .submenu-item');
        const contentSections = document.querySelectorAll('.menu-content-section');
        const submenuContainer = document.getElementById('submenu-mon-goi-them');

        // Xử lý click vào menu item chính (Combo Buffet, Món Gọi Thêm)
        menuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const category = this.dataset.category;
                
                // Remove active class from all menu items
                document.querySelectorAll('.menu-sidebar .menu-item').forEach(mi => mi.classList.remove('active'));
                
                // Hide all content sections
                contentSections.forEach(section => section.classList.remove('active'));
                
                // Xử lý riêng cho "Món Gọi Thêm"
                if (category === 'mon-goi-them') {
                    if (submenuContainer) {
                        submenuContainer.classList.add('show');
                    }
                    
                    // Tự động chọn danh mục đầu tiên
                    const firstSubmenuItem = document.querySelector('.submenu-item');
                    if (firstSubmenuItem) {
                        firstSubmenuItem.classList.add('active');
                        const firstCategory = firstSubmenuItem.dataset.category;
                        const firstSection = document.getElementById('section-' + firstCategory);
                        if (firstSection) {
                            firstSection.classList.add('active');
                            firstSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                    
                    this.classList.add('active');
                } else {
                    // Xử lý cho Combo Buffet
                    if (submenuContainer) {
                        submenuContainer.classList.remove('show');
                    }
                    
                    this.classList.add('active');
                    
                    const targetSection = document.getElementById('section-' + category);
                    if (targetSection) {
                        targetSection.classList.add('active');
                        targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });

        // Xử lý click vào submenu item
        submenuItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                const category = this.dataset.category;
                
                submenuItems.forEach(mi => mi.classList.remove('active'));
                this.classList.add('active');
                
                const parentItem = document.getElementById('menu-item-mon-goi-them');
                if (parentItem) {
                    parentItem.classList.add('active');
                }
                
                if (submenuContainer) {
                    submenuContainer.classList.add('show');
                }
                
                contentSections.forEach(section => section.classList.remove('active'));
                
                const targetSection = document.getElementById('section-' + category);
                if (targetSection) {
                    targetSection.classList.add('active');
                    targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Tự động chọn combo buffet nếu có hash
        if (window.location.hash === '#combo-buffet') {
            const comboBuffetItem = document.querySelector('.menu-item[data-category="combo-buffet"]');
            if (comboBuffetItem) {
                comboBuffetItem.click();
                setTimeout(() => {
                    const comboSection = document.getElementById('section-combo-buffet');
                    if (comboSection) {
                        comboSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 100);
            }
        }
    });
</script>
@endsection