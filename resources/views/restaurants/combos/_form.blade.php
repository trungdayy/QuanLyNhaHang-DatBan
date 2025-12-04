@extends('layouts.page')

@section('title', 'Chọn Vé Buffet Ocean')

@section('content')

{{-- =========================================================== --}}
{{-- PHẦN 1: CSS --}}
{{-- =========================================================== --}}
<style>
    /* CSS Icon Giỏ hàng */
    #floatingCartIcon {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
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

    /* CSS Card */
    .product-card-trigger {
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid transparent;
    }

    .product-card-trigger:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        border-color: #FEA116;
    }

    /* CSS Thanh tìm kiếm */
    .search-input:focus {
        box-shadow: none;
        border-color: #FEA116;
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
{{-- PHẦN 2: NỘI DUNG CHÍNH --}}
{{-- =========================================================== --}}

@if (isset($combos) && $combos->count() > 0)
<div class="container-xxl py-5">
    <div class="container">

        {{-- TIÊU ĐỀ --}}
        <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
            <h5 class="section-title ff-secondary text-center text-primary fw-normal">Thực Đơn Vé</h5>
            <h1 class="mb-4">Chọn Gói Buffet Của Bạn</h1>
        </div>

        {{-- [MỚI] THANH TÌM KIẾM --}}
        <div class="row justify-content-center mb-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="col-md-6">
                <div class="input-group shadow-sm rounded-pill overflow-hidden border">
                    <span class="input-group-text bg-white border-0 ps-3"><i class="fa fa-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-0 search-input py-3"
                        placeholder="Tìm tên combo, giá vé (VD: 99k, VIP)...">
                    <button class="btn btn-white border-0 pe-3" id="clearSearch" style="display:none;"><i
                            class="fa fa-times text-secondary"></i></button>
                </div>
            </div>
        </div>

        <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.1s">
            {{-- Tabs Header --}}
            @php $groupedCombos = $combos->groupBy('loai_combo'); @endphp
            <ul class="nav nav-pills d-inline-flex justify-content-center border-bottom mb-5" id="comboTabs">
                @foreach ($groupedCombos as $type => $typeCombos)
                <li class="nav-item">
                    <a class="d-flex align-items-center text-start mx-3 ms-0 pb-3 {{ $loop->first ? 'active' : '' }}"
                        data-bs-toggle="pill" href="#tab-combo-{{ $type }}">
                        <i class="fa fa-utensils fa-2x text-primary"></i>
                        <div class="ps-3">
                            <small class="text-body text-uppercase">Gói</small>
                            <h6 class="mt-n1 mb-0 text-uppercase">
                                @if($type == 'nguoi_lon') Người Lớn
                                @elseif($type == 'tre_em') Trẻ Em
                                @else {{ $type }} @endif
                            </h6>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>

            {{-- Tabs Content --}}
            <div class="tab-content">
                @foreach ($groupedCombos as $type => $typeCombos)
                <div id="tab-combo-{{ $type }}" class="tab-pane fade show p-0 {{ $loop->first ? 'active' : '' }}">
                    <div class="row g-4" id="comboContainer-{{ $type }}">
                        @foreach($typeCombos as $combo)
                        @php
                        $imageUrl = $combo->anh ? asset($combo->anh) : asset('assets/img/menu-1.jpg');
                        $danhSachMonJS = $combo->monAn->pluck('ten_mon')->implode(', ');
                        @endphp

                        {{-- THÊM CLASS 'combo-item' ĐỂ JS TÌM KIẾM HOẠT ĐỘNG --}}
                        <div class="col-lg-6 combo-item">
                            {{-- ITEM CARD --}}
                            <div class="d-flex align-items-start product-card-trigger rounded p-3 bg-white shadow-sm h-100"
                                data-id="{{ $combo->id }}" data-key="combo_{{ $combo->id }}"
                                data-name="{{ $combo->ten_combo }}" data-price="{{ $combo->gia_co_ban }}"
                                data-desc="{{ $combo->mo_ta }}" data-img="{{ $imageUrl }}"
                                data-menu="{{ $danhSachMonJS }}">

                                {{-- Ảnh --}}
                                <img class="flex-shrink-0 img-fluid rounded" src="{{ $imageUrl }}"
                                    alt="{{ $combo->ten_combo }}"
                                    style="width: 100px; height: 100px; object-fit: cover;">

                                {{-- Thông tin --}}
                                <div class="w-100 d-flex flex-column text-start ps-4 h-100">
                                    <h5 class="d-flex justify-content-between border-bottom pb-2">
                                        <span>{{ $combo->ten_combo }}</span>
                                        <span
                                            class="text-primary fw-bold">{{ number_format($combo->gia_co_ban, 0, ',', '.') }}
                                            đ</span>
                                    </h5>

                                    {{-- Danh sách món tóm tắt --}}
                                    <div class="mb-2">
                                        <small class="text-muted fw-bold"><i class="fa fa-list-ul me-1"></i>Menu
                                            gồm:</small>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            @forelse($combo->monAn->take(4) as $mon)
                                            <span class="badge bg-light text-dark border"
                                                style="font-weight: normal;">{{ $mon->ten_mon }}</span>
                                            @empty
                                            <span class="small text-muted fst-italic">Đang cập nhật món...</span>
                                            @endforelse

                                            @if($combo->monAn->count() > 4)
                                            <span
                                                class="badge bg-secondary text-white">+{{ $combo->monAn->count() - 4 }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-auto pt-2 text-end w-100">
                                        <button class="btn btn-sm btn-primary fw-bold rounded-pill px-3 shadow-sm">
                                            Chọn Vé <i class="fa fa-plus-circle ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    {{-- Thông báo không tìm thấy trong Tab này --}}
                    <div class="no-result text-center py-4 d-none">
                        <p class="text-muted fst-italic">Không tìm thấy combo nào phù hợp trong mục này.</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@else
<div class="text-center py-5">
    <h3 class="text-muted">Chưa có gói Combo nào được mở bán.</h3>
</div>
@endif

{{-- =========================================================== --}}
{{-- PHẦN 3: MODAL & UI GIỎ HÀNG --}}
{{-- =========================================================== --}}

<div id="floatingCartIcon">
    <div class="icon-wrapper"><i class="fa fa-ticket-alt"></i><span id="cartCountBadge" class="count-badge">0</span>
    </div>
</div>

<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary">VÉ ĐÃ CHỌN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3">
                    <ul id="cartItemsList" class="list-unstyled mb-0"></ul>
                    <div id="emptyCartMsg" class="text-center py-5 text-muted" style="display: none;">
                        <i class="fa fa-ticket-alt fa-3x mb-3 opacity-25"></i>
                        <p>Giỏ hàng trống</p>
                    </div>
                </div>
                <div id="totalSection" class="p-3 bg-light border-top d-none">
                    <div class="d-flex justify-content-between fw-bold fs-5"><span>Tổng cộng:</span><span
                            id="cartTotalPrint" class="text-danger"></span></div>
                </div>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between align-items-center p-3">
                <div>Tạm tính: <span id="cartTotalDisplay" class="fs-4 fw-bold text-danger">0 đ</span></div>
                <button id="btnCheckout" class="btn btn-primary fw-bold px-4">ĐẶT BÀN NGAY <i
                        class="fa fa-arrow-right"></i></button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CHI TIẾT --}}
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 15px;">
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                style="z-index: 10; background-color: white; border-radius: 50%; padding: 8px;"></button>
            <div class="row g-0">
                <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-0">
                    <img id="modalImg" src="" class="img-fluid"
                        style="width: 100%; height: 100%; min-height: 400px; object-fit: cover;">
                </div>
                <div class="col-md-7 p-4 d-flex flex-column">
                    <div class="mb-auto">
                        <span class="badge bg-warning text-dark mb-2 px-3 py-1 rounded-pill">Vé Buffet</span>
                        <h3 id="modalName" class="fw-bold mb-1"></h3>
                        <h4 id="modalPrice" class="text-danger fw-bold mb-3"></h4>
                        <div class="mb-3">
                            <h6 class="text-dark fw-bold border-bottom pb-1">Mô tả:</h6>
                            <p id="modalDesc" class="text-muted small mb-0"></p>
                        </div>
                        <div class="bg-light p-3 rounded">
                            <h6 class="text-dark fw-bold border-bottom pb-1 mb-2"><i
                                    class="fa fa-utensils me-2"></i>Thực đơn bao gồm:</h6>
                            <p id="modalMenu" class="text-dark small mb-0 fw-bold" style="line-height: 1.8;"></p>
                        </div>
                    </div>
                    <button id="modalAddToCartBtn"
                        class="btn btn-primary w-100 py-3 mt-3 fw-bold rounded-pill shadow-sm">
                        <i class="fa fa-check-circle me-2"></i> THÊM VÀO GIỎ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================== --}}
{{-- PHẦN 4: JAVASCRIPT (ĐÃ SỬA LOGIC CART & SEARCH) --}}
{{-- =========================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const BOOKING_URL = "{{ route('booking.index') }}"; 
        let cart = JSON.parse(localStorage.getItem("oceanCart")) || [];
        
        // Elements
        const floatingCartIcon = document.getElementById("floatingCartIcon");
        const cartModalElement = document.getElementById("cartModal");
        const cartModal = new bootstrap.Modal(cartModalElement);
        const detailModal = new bootstrap.Modal(document.getElementById('productDetailModal'));
        const cartItemsList = document.getElementById("cartItemsList");
        const cartCountBadge = document.getElementById("cartCountBadge");
        const emptyCartMsg = document.getElementById("emptyCartMsg");
        const cartTotalDisplay = document.getElementById("cartTotalDisplay");
        
        // Search Elements
        const searchInput = document.getElementById("searchInput");
        const clearSearchBtn = document.getElementById("clearSearch");

        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 1500,
            timerProgressBar: false, didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // =========================================================
        // 1. LOGIC TÌM KIẾM (LIVE SEARCH)
        // =========================================================
        searchInput.addEventListener('keyup', function() {
            const keyword = this.value.toLowerCase().trim();
            
            // Hiện nút X nếu có chữ
            clearSearchBtn.style.display = keyword.length > 0 ? 'block' : 'none';

            // Lấy tất cả các item
            const items = document.querySelectorAll('.combo-item');
            
            items.forEach(item => {
                // Lấy data từ thẻ con .product-card-trigger
                const card = item.querySelector('.product-card-trigger');
                const name = card.dataset.name.toLowerCase();
                const price = card.dataset.price.toLowerCase();
                const desc = (card.dataset.desc || '').toLowerCase();
                
                // Logic so sánh: Tên OR Giá OR Mô tả chứa từ khóa
                const isMatch = name.includes(keyword) || price.includes(keyword) || desc.includes(keyword);
                
                item.style.display = isMatch ? 'block' : 'none';
            });

            // Xử lý thông báo "Không tìm thấy" trong từng tab
            document.querySelectorAll('.tab-pane').forEach(tab => {
                const visibleItems = tab.querySelectorAll('.combo-item[style="display: block;"]');
                const noResultDiv = tab.querySelector('.no-result');
                if(noResultDiv) {
                    noResultDiv.classList.toggle('d-none', visibleItems.length > 0);
                }
            });
        });

        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('keyup')); // Trigger lại sự kiện keyup để hiện lại hết
        });

        // =========================================================
        // 2. RENDER UI
        // =========================================================
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
                    li.className = "d-flex justify-content-between align-items-center py-3 border-bottom";
                    li.innerHTML = `
                        <div class="d-flex align-items-center" style="width: 60%;">
                            <img src="${item.img || ''}" class="rounded me-3 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <div class="fw-bold text-dark">${item.name}</div>
                                <div class="small text-danger fw-bold">${parseInt(item.price).toLocaleString('vi-VN')} đ</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center bg-light rounded-pill px-2 py-1 border">
                            <button class="btn btn-sm text-secondary border-0 btn-minus p-0 px-2 fw-bold" data-index="${index}">-</button>
                            <span class="fw-bold px-2 text-dark" style="min-width:20px; text-align:center;">${item.quantity}</span>
                            <button class="btn btn-sm text-primary border-0 btn-plus p-0 px-2 fw-bold" data-index="${index}">+</button>
                        </div>
                    `;
                    cartItemsList.appendChild(li);
                });
            }
            
            const formattedTotal = totalPrice.toLocaleString('vi-VN') + ' đ';
            cartTotalDisplay.innerText = formattedTotal;
            document.getElementById('cartTotalPrint').innerText = formattedTotal;

            attachQtyEvents();
        }

        function attachQtyEvents() {
            document.querySelectorAll('.btn-plus').forEach(btn => {
                btn.addEventListener('click', function() {
                    cart[this.dataset.index].quantity++;
                    saveCart();
                });
            });

            document.querySelectorAll('.btn-minus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idx = this.dataset.index;
                    if(cart[idx].quantity > 1) { cart[idx].quantity--; } 
                    else { cart.splice(idx, 1); }
                    saveCart();
                });
            });
        }

        function saveCart() {
            localStorage.setItem("oceanCart", JSON.stringify(cart));
            renderCartUI();
        }

        // =========================================================
        // 3. ADD TO CART LOGIC (CHECK GIÁ)
        // =========================================================
        function addToCart(newItem) {
            // Kiểm tra xem giỏ hàng có đồ chưa
            if (cart.length > 0) {
                const currentCartPrice = cart[0].price; // Lấy giá của món đầu tiên làm chuẩn
                
                // Nếu giá món mới KHÁC giá đang có trong giỏ
                if (currentCartPrice !== newItem.price) {
                    Swal.fire({
                        title: 'Khác mức giá vé!',
                        html: `Giỏ hàng đang có vé loại <b>${parseInt(currentCartPrice).toLocaleString('vi-VN')}đ</b>.<br>Bạn không thể chọn chung với vé loại <b>${parseInt(newItem.price).toLocaleString('vi-VN')}đ</b>.<br>Bạn có muốn xóa giỏ hàng cũ để chọn vé mới không?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Đồng ý xóa & chọn mới',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Xóa sạch giỏ và thêm món mới
                            cart = [{ ...newItem, quantity: 1 }];
                            saveCart();
                            Toast.fire({ icon: 'success', title: 'Đã cập nhật vé mới!' });
                        }
                    });
                    return; // Dừng lại, không thêm
                }
            }

            // Nếu cùng giá hoặc giỏ trống -> Thêm bình thường
            const existingItem = cart.find(item => item.key === newItem.key);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ ...newItem, quantity: 1 });
            }
            saveCart();
            Toast.fire({ icon: 'success', title: 'Đã thêm vào danh sách!' });
        }

        // =========================================================
        // 4. CLICK EVENTS
        // =========================================================
        floatingCartIcon.addEventListener('click', () => { cartModal.show(); });

        document.querySelectorAll('.product-card-trigger').forEach(card => {
            card.addEventListener('click', function(e) {
                const d = this.dataset;
                document.getElementById('modalName').innerText = d.name;
                document.getElementById('modalPrice').innerText = parseInt(d.price).toLocaleString('vi-VN') + ' VNĐ';
                document.getElementById('modalDesc').innerText = d.desc || 'Đang cập nhật...';
                
                const menuText = d.menu ? d.menu : 'Chưa có thông tin món ăn';
                document.getElementById('modalMenu').innerText = menuText;

                if(d.img) document.getElementById('modalImg').src = d.img;
                
                document.getElementById('modalAddToCartBtn').onclick = function() {
                    addToCart({ 
                        id: d.id, key: d.key, name: d.name, price: parseInt(d.price), img: d.img 
                    });
                    detailModal.hide();
                };
                detailModal.show();
            });
        });

        document.getElementById('btnCheckout').addEventListener('click', () => {
            if(cart.length === 0) { Toast.fire({ icon: 'warning', title: 'Vui lòng chọn vé!' }); return; }
            localStorage.setItem("oceanCart", JSON.stringify(cart));
            cartModal.hide();
            window.location.href = BOOKING_URL;
        });

        document.getElementById('btnClearCart').addEventListener('click', () => {
            if(cart.length === 0) return;
            Swal.fire({ title: 'Xóa giỏ hàng?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Xóa' })
            .then((res) => { if(res.isConfirmed) { cart = []; saveCart(); } });
        });

        renderCartUI();
    });
</script>
@endsection