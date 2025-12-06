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
</style>

{{-- =========================================================== --}}
{{-- 2. MENU COMBO (ĐÃ CHUYỂN SANG DẠNG CARD & THU GỌN) --}}
{{-- =========================================================== --}}
@if (isset($combos) && $combos->count() > 0)
<div class="container-xxl py-4">
    <div class="container">
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Thực Đơn Combo</h5>
            <h1 class="mb-4">Các Gói Buffet Đặc Biệt</h1>
        </div>
        
        <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.1s">
            @php $groupedCombos = $combos->groupBy('loai_combo'); @endphp

            {{-- TABS HEADER --}}
            <ul class="nav nav-pills d-inline-flex justify-content-center border-bottom mb-4">
                @foreach ($groupedCombos as $type => $typeCombos)
                    <li class="nav-item">
                        <a class="d-flex align-items-center text-start mx-2 ms-0 pb-3 {{ $loop->first ? 'active' : '' }}"
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
                        <div class="row g-3 justify-content-center">
                            @foreach($typeCombos as $combo)
                                @php
                                    $imagePath = $combo->anh;
                                    if ($imagePath && !str_starts_with($imagePath, 'uploads/')) {
                                        $imagePath = 'uploads/' . $imagePath;
                                    }
                                    $imageUrl = $combo->anh ? asset($imagePath) : asset('assets/img/menu-1.jpg');
                                @endphp

                                {{-- THẺ COMBO (CARD) --}}
                                <div class="col-lg-3 col-md-4 col-sm-6 col-12"> 
                                    <div class="card h-100 border-0 shadow-sm product-card-trigger"
                                        style="border-radius: 8px;"
                                        data-key="combo_{{ $combo->id }}" 
                                        data-type="combo" 
                                        data-name="{{ $combo->ten_combo }}"
                                        data-price="{{ $combo->gia_co_ban }}" 
                                        data-desc="{{ $combo->mo_ta }}"
                                        data-img="{{ $imageUrl }}"
                                        data-dishes="{{ json_encode($combo->danhSachMon ? $combo->danhSachMon->pluck('ten_mon') : []) }}">
                                        
                                        {{-- Ảnh trên cùng của Card --}}
                                        <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $combo->ten_combo }}" style="height: 160px; object-fit: cover; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                                        
                                        <div class="card-body d-flex flex-column text-start p-3">
                                            {{-- Tiêu đề và Giá --}}
                                            <h6 class="card-title fw-bold mb-1">{{ $combo->ten_combo }}</h6>
                                            <h5 class="text-primary fw-bolder mb-2">{{ number_format($combo->gia_co_ban, 0, ',', '.') }} đ</h5>

                                            {{-- Mô tả/Thời gian --}}
                                            <small class="fst-italic text-muted mb-1" style="font-size: 0.75rem;"><i class="fa fa-clock me-1 text-warning"></i> Không giới hạn</small>
                                            <p class="card-text text-secondary line-clamp-2 mb-2" style="font-size: 0.8rem;">
                                                {{ \Illuminate\Support\Str::limit($combo->mo_ta, 80) }}
                                            </p>
                                            
                                            {{-- Nút chi tiết --}}
                                            <div class="mt-auto pt-1 text-center">
                                                <button class="btn btn-sm btn-outline-warning fw-bold rounded-pill px-3 py-1 w-100">
                                                    <i class="fa fa-eye me-1"></i> Chi tiết
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- END THẺ COMBO --}}
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- =========================================================== --}}
{{-- 3. CÁC MỤC THỰC ĐƠN KHÁC (NẾU CÓ) --}}
{{-- =========================================================== --}}


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

{{-- MODAL CHI TIẾT SẢN PHẨM (ĐÃ THU NHỎ VÀ CHUYỂN BỐ CỤC DỌC) --}}
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
                if(d.type === 'combo') { 
                    badge.className='badge bg-danger text-white mb-2 px-3 py-2 rounded-pill'; badge.innerText='Combo Hot'; 
                } else { 
                    badge.className='badge bg-success mb-2 px-3 py-2 rounded-pill'; badge.innerText='Món Ngon'; 
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

        // --- LOGIC CHO PHẦN ĐẶT BÀN (Dành cho trang /booking, nhưng giữ lại để hoàn chỉnh code) ---
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
    });
</script>
@endsection