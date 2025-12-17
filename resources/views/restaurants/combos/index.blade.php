@extends('layouts.page')

@section('title', 'Thực Đơn Combo Buffet')

@section('content')

{{-- =========================================================== --}}
{{-- 1. CSS TÙY CHỈNH (Search, Cart, Animation) --}}
{{-- =========================================================== --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    /* --- THANH TÌM KIẾM --- */
    #comboSearchInput:focus {
        box-shadow: none;
        border-color: #FEA116;
    }

    .search-tag {
        cursor: pointer;
        background: #f8f9fa;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #6c757d;
        border: 1px solid #dee2e6;
        transition: all 0.2s;
    }

    .search-tag:hover,
    .search-tag.active {
        background: #FEA116;
        color: white;
        border-color: #FEA116;
    }

    /* --- CARD SẢN PHẨM --- */
    .product-card-trigger {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .product-card-trigger:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        border-color: #FEA116;
    }

    .badge-status {
        z-index: 5;
        font-size: 0.75rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* --- GIỎ HÀNG NỔI (FLOATING CART) --- */
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
        background: linear-gradient(135deg, #FEA116, #FF8E53);
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        box-shadow: 0 10px 25px rgba(254, 161, 22, 0.5);
    }

    .count-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #d63031;
        color: white;
        font-size: 13px;
        font-weight: 800;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fff;
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

<div class="container py-5">

    {{-- TITLE --}}
    <div class="text-center wow fadeInUp" data-wow-delay="0.1s">
        <h5 class="section-title ff-secondary text-center text-primary fw-normal">Thực Đơn Vé</h5>
        <h1 class="mb-3 text-uppercase">Bảng Giá Vé Buffet</h1>
    </div>

    {{-- =========================================================== --}}
    {{-- 2. THANH TÌM KIẾM LIVE SEARCH --}}
    {{-- =========================================================== --}}
    <div class="row justify-content-center mb-5 wow fadeInUp" data-wow-delay="0.2s">
        <div class="col-md-8 col-lg-6">
            <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white border">
                <span class="input-group-text bg-white border-0 ps-4">
                    <i class="fa fa-search text-muted"></i>
                </span>
                <input type="text" id="comboSearchInput" class="form-control border-0 py-3 ps-2"
                    placeholder="Tìm gói Buffet... (VD: VIP, 199k, Gia đình)" autocomplete="off">
                <button class="btn btn-white border-0 pe-4" type="button" id="btnClearSearch" style="display: none;">
                    <i class="fa fa-times-circle text-secondary"></i>
                </button>
            </div>
            {{-- Tags gợi ý --}}
            <div class="mt-3 text-center d-flex justify-content-center flex-wrap gap-2">
                <span class="text-muted small align-self-center me-1">Gợi ý:</span>
                <span class="search-tag" data-tag="VIP">#VIP</span>
                <span class="search-tag" data-tag="Gia đình">#GiaDinh</span>
                <span class="search-tag" data-tag="Trẻ em">#TreEm</span>
                <span class="search-tag" data-tag="199">#199k</span>
            </div>
        </div>
    </div>

    {{-- Thông báo không tìm thấy --}}
    <div id="noResultMsg" class="text-center py-5 d-none animate__animated animate__fadeIn">
        <div class="opacity-50 mb-3">
            <i class="fa fa-search fa-4x text-muted"></i>
        </div>
        <p class="text-muted fw-bold">Không tìm thấy Combo nào phù hợp.</p>
        <button class="btn btn-sm btn-outline-primary rounded-pill px-4"
            onclick="document.getElementById('btnClearSearch').click()">
            Xem tất cả
        </button>
    </div>

    {{-- =========================================================== --}}
    {{-- 3. DANH SÁCH COMBO (GRID VIEW) --}}
    {{-- =========================================================== --}}
    <div class="row g-4" id="comboListContainer">
        @forelse($combos as $combo)
        @php
        // Xử lý ảnh
        $imgSrc = $combo->anh ? asset($combo->anh) : asset('assets/img/menu-1.jpg');
        // Chuỗi danh sách món cho JS (Modal chi tiết)
        $danhSachMonJS = $combo->monAn->pluck('ten_mon')->implode(', ');
        @endphp

        <div class="col-lg-4 col-md-6 combo-item-col wow fadeInUp" data-wow-delay="0.1s">
            {{-- CARD CHÍNH --}}
            <div class="card shadow-sm h-100 border-0 rounded-3 product-card-trigger position-relative overflow-hidden"
                style="cursor: pointer;" {{-- DATA ATTRIBUTES CHO JS --}} data-id="{{ $combo->id }}"
                data-key="combo_{{ $combo->id }}" data-type="combo" data-name="{{ $combo->ten_combo }}"
                data-price="{{ $combo->gia_co_ban }}" data-desc="{{ $combo->mo_ta ?? 'Thưởng thức không giới hạn...' }}"
                data-img="{{ $imgSrc }}" data-menu="{{ $danhSachMonJS }}">

                {{-- Badge Trạng Thái --}}
                <span
                    class="badge badge-status position-absolute bg-success text-white top-0 start-0 m-3 py-2 px-3 rounded-pill">
                    {{ $combo->trang_thai == 'dang_ban' ? 'Đang bán' : 'Hết vé' }}
                </span>

                {{-- Badge Loại Combo --}}
                <span class="badge position-absolute top-0 end-0 m-3 py-2 px-3 rounded-pill shadow-sm"
                    style="background: rgba(255,255,255,0.9); color: #FEA116; font-weight: bold;">
                    @if($combo->loai_combo == 'nguoi_lon') <i class="fa fa-user"></i> Người lớn
                    @elseif($combo->loai_combo == 'tre_em') <i class="fa fa-child"></i> Trẻ em
                    @else <i class="fa fa-star"></i> VIP @endif
                </span>

                {{-- Ảnh --}}
                <div class="overflow-hidden position-relative" style="height: 240px;">
                    <img class="img-fluid w-100 h-100" src="{{ $imgSrc }}" alt="{{ $combo->ten_combo }}"
                        style="object-fit: cover;">
                    {{-- Nút Thêm Nhanh (Góc ảnh) --}}
                    <button
                        class="btn btn-primary position-absolute bottom-0 end-0 m-3 btn-quick-add rounded-circle shadow"
                        style="width: 45px; height: 45px; z-index: 10;" title="Chọn vé này">
                        <i class="fa fa-plus text-white"></i>
                    </button>
                </div>

                <div class="card-body d-flex flex-column p-4">
                    <h4 class="card-title fw-bold text-dark mb-2">{{ $combo->ten_combo }}</h4>

                    {{-- Hiển thị tóm tắt 3 món --}}
                    <div class="mb-3">
                        @foreach($combo->monAn->take(3) as $mon)
                        <span
                            class="badge bg-light text-secondary border fw-normal me-1 mb-1">{{ $mon->ten_mon }}</span>
                        @endforeach
                        @if($combo->monAn->count() > 3)
                        <span class="badge bg-secondary text-white fw-normal">+{{ $combo->monAn->count() - 3 }}
                            món</span>
                        @endif
                    </div>

                    <div class="mt-auto d-flex align-items-end justify-content-between">
                        <div>
                            <small class="text-muted text-decoration-line-through fst-italic">
                                {{ number_format($combo->gia_co_ban * 1.2, 0, ',', '.') }}đ
                            </small>
                            <h4 class="text-danger fw-bold mb-0">
                                {{ number_format($combo->gia_co_ban, 0, ',', '.') }} <span
                                    class="fs-6 text-dark">VNĐ</span>
                            </h4>
                        </div>
                        <button class="btn btn-outline-primary rounded-pill px-3 fw-bold btn-view-detail">
                            Chi tiết <i class="fa fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="alert alert-warning d-inline-block px-5">
                <i class="fa fa-exclamation-circle me-2"></i> Hiện chưa có gói Combo nào được mở bán.
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- =========================================================== --}}
{{-- 4. MODALS & FLOATING ICON --}}
{{-- =========================================================== --}}

{{-- Nút Giỏ Hàng Nổi --}}
<div id="floatingCartIcon">
    <div class="icon-wrapper">
        <i class="fa fa-ticket-alt"></i>
        <span id="cartCountBadge" class="count-badge">0</span>
    </div>
</div>

{{-- Modal Giỏ Hàng --}}
<div class="modal fade" id="cartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-primary text-uppercase"><i class="fa fa-receipt me-2"></i>Vé Đã Chọn
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3">
                    <ul id="cartItemsList" class="list-unstyled mb-0"></ul>
                    <div id="emptyCartMsg" class="text-center py-5 text-muted" style="display: none;">
                        <i class="fa fa-ticket-alt fa-3x mb-3 opacity-25"></i>
                        <p class="mt-2">Bạn chưa chọn vé nào.</p>
                        <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-dismiss="modal">Chọn
                            ngay</button>
                    </div>
                </div>
                <div id="totalSection" class="p-3 bg-light border-top d-none">
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Tổng cộng:</span>
                        <span id="cartTotalPrint" class="text-danger"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white d-flex justify-content-between align-items-center p-3 shadow-sm">
                <div>
                    <span class="text-muted small">Tạm tính:</span>
                    <div id="cartTotalDisplay" class="fs-4 fw-bold text-danger">0 đ</div>
                </div>
                <div class="d-flex gap-2">
                    <button id="btnClearCart" class="btn btn-outline-danger"><i class="fa fa-trash"></i></button>
                    <button id="btnCheckout" class="btn btn-primary fw-bold px-4 py-2 rounded-pill">
                        ĐẶT BÀN NGAY <i class="fa fa-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Chi Tiết Sản Phẩm --}}
<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 15px;">
            <button type="button"
                class="btn-close position-absolute top-0 end-0 m-3 bg-white shadow-sm p-2 rounded-circle"
                data-bs-dismiss="modal" style="z-index: 10; opacity: 1;"></button>
            <div class="row g-0">
                <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-0 position-relative">
                    <img id="modalImg" src="" class="img-fluid w-100 h-100"
                        style="min-height: 400px; object-fit: cover;">
                </div>
                <div class="col-md-7 p-4 d-flex flex-column">
                    <div class="mb-auto">
                        <span class="badge bg-warning text-dark mb-2 px-3 py-1 rounded-pill">Vé Buffet</span>
                        <h3 id="modalName" class="fw-bold mb-1 text-dark"></h3>
                        <h4 id="modalPrice" class="text-danger fw-bold mb-3 fs-3"></h4>

                        <div class="mb-4">
                            <h6 class="text-dark fw-bold border-bottom pb-2"><i class="fa fa-info-circle me-2"></i>Mô tả
                                gói</h6>
                            <p id="modalDesc" class="text-muted small mb-0 mt-2" style="line-height: 1.6;"></p>
                        </div>

                        <div class="bg-light p-3 rounded-3 border">
                            <h6 class="text-primary fw-bold mb-2"><i class="fa fa-utensils me-2"></i>Thực đơn bao gồm:
                            </h6>
                            <p id="modalMenu" class="text-dark small mb-0 fw-bold" style="line-height: 1.8;"></p>
                        </div>
                    </div>
                    <button id="modalAddToCartBtn" class="btn btn-primary w-100 py-3 mt-3 fw-bold rounded-pill shadow">
                        <i class="fa fa-check-circle me-2"></i> XÁC NHẬN CHỌN VÉ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- =========================================================== --}}
{{-- 5. JAVASCRIPT LOGIC (Full Features) --}}
{{-- =========================================================== --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- CẤU HÌNH ---
        const BOOKING_URL = "{{ route('booking.index') }}"; 
        const CART_KEY = "oceanCart";
        let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];

        // --- ELEMENTS ---
        const searchInput = document.getElementById('comboSearchInput');
        const btnClearSearch = document.getElementById('btnClearSearch');
        const noResultMsg = document.getElementById('noResultMsg');
        
        const floatingCartIcon = document.getElementById("floatingCartIcon");
        const cartModalElement = document.getElementById("cartModal");
        const cartModal = new bootstrap.Modal(cartModalElement);
        const detailModal = new bootstrap.Modal(document.getElementById('productDetailModal'));
        
        // --- TOAST CONFIG ---
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
        function performSearch(keyword) {
            keyword = keyword.toLowerCase().trim();
            btnClearSearch.style.display = keyword.length > 0 ? 'block' : 'none';
            
            const cols = document.querySelectorAll('.combo-item-col');
            let visibleCount = 0;

            cols.forEach(col => {
                const card = col.querySelector('.product-card-trigger');
                const name = card.dataset.name.toLowerCase();
                const price = card.dataset.price.toString();
                const type = card.dataset.type.toLowerCase();
                const desc = card.dataset.desc.toLowerCase();

                // Logic tìm kiếm: Tên OR Giá OR Loại OR Mô tả
                if (name.includes(keyword) || price.includes(keyword) || type.includes(keyword) || desc.includes(keyword)) {
                    col.style.display = '';
                    col.classList.add('animate__animated', 'animate__fadeIn');
                    visibleCount++;
                } else {
                    col.style.display = 'none';
                    col.classList.remove('animate__animated', 'animate__fadeIn');
                }
            });

            noResultMsg.classList.toggle('d-none', visibleCount > 0);
        }

        searchInput.addEventListener('keyup', (e) => performSearch(e.target.value));
        
        btnClearSearch.addEventListener('click', () => {
            searchInput.value = '';
            performSearch('');
            searchInput.focus();
        });

        document.querySelectorAll('.search-tag').forEach(tag => {
            tag.addEventListener('click', function() {
                const val = this.dataset.tag;
                searchInput.value = val;
                performSearch(val);
                
                // Active effect
                document.querySelectorAll('.search-tag').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // =========================================================
        // 2. LOGIC GIỎ HÀNG (CART)
        // =========================================================
        function renderCartUI() {
            const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById("cartCountBadge").innerText = totalCount;

            // Ẩn hiện icon nổi
            floatingCartIcon.style.display = totalCount > 0 ? 'flex' : 'none';
            document.getElementById('totalSection').classList.toggle('d-none', totalCount === 0);

            const list = document.getElementById("cartItemsList");
            list.innerHTML = '';
            let totalPrice = 0;

            if (cart.length === 0) {
                document.getElementById("emptyCartMsg").style.display = 'block';
            } else {
                document.getElementById("emptyCartMsg").style.display = 'none';
                cart.forEach((item, index) => {
                    totalPrice += item.price * item.quantity;
                    const li = document.createElement('li');
                    li.className = "d-flex justify-content-between align-items-center py-3 border-bottom";
                    li.innerHTML = `
                        <div class="d-flex align-items-center" style="width: 65%;">
                            <img src="${item.img || ''}" class="rounded shadow-sm me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <div class="fw-bold text-dark text-truncate">${item.name}</div>
                                <div class="small text-danger fw-bold">${parseInt(item.price).toLocaleString('vi-VN')} đ/vé</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center bg-light rounded-pill px-2 py-1 border">
                            <button class="btn btn-sm btn-link text-decoration-none text-secondary p-0 px-2 fw-bold btn-minus" data-index="${index}">-</button>
                            <span class="fw-bold px-2 text-dark" style="min-width:20px; text-align:center;">${item.quantity}</span>
                            <button class="btn btn-sm btn-link text-decoration-none text-primary p-0 px-2 fw-bold btn-plus" data-index="${index}">+</button>
                        </div>
                    `;
                    list.appendChild(li);
                });
            }

            const totalStr = totalPrice.toLocaleString('vi-VN') + ' đ';
            document.getElementById("cartTotalDisplay").innerText = totalStr;
            document.getElementById("cartTotalPrint").innerText = totalStr;

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
                    if(cart[idx].quantity > 1) cart[idx].quantity--;
                    else cart.splice(idx, 1);
                    saveCart();
                });
            });
        }

        function saveCart() {
            localStorage.setItem(CART_KEY, JSON.stringify(cart));
            renderCartUI();
        }

        // --- HÀM THÊM VÀO GIỎ (CÓ CHECK ĐỘC QUYỀN) ---
        function addToCart(newItem) {
            // Kiểm tra xem giỏ đã có combo khác loại chưa
            if (cart.length > 0) {
                const currentCombo = cart[0];
                if (currentCombo.key !== newItem.key) {
                    Swal.fire({
                        title: 'Thay đổi lựa chọn?',
                        html: `Bạn đang chọn gói <b>${currentCombo.name}</b>.<br>Bạn chỉ được chọn 1 loại gói Buffet cho mỗi lần đặt bàn.<br>Bạn có muốn đổi sang gói mới không?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#FEA116',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Đồng ý đổi',
                        cancelButtonText: 'Không'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cart = [{ ...newItem, quantity: 1 }];
                            saveCart();
                            Toast.fire({ icon: 'success', title: 'Đã đổi gói thành công!' });
                        }
                    });
                    return;
                }
            }

            // Logic thêm bình thường
            const existingItem = cart.find(item => item.key === newItem.key);
            if (existingItem) existingItem.quantity++;
            else cart.push({ ...newItem, quantity: 1 });
            
            saveCart();
            Toast.fire({ icon: 'success', title: 'Đã thêm vào danh sách!' });
        }

        // =========================================================
        // 3. SỰ KIỆN CLICK (MODAL & BUTTONS)
        // =========================================================
        
        // Mở giỏ hàng
        floatingCartIcon.addEventListener('click', () => cartModal.show());

        // Click Card sản phẩm (Mở Modal Chi Tiết)
        document.querySelectorAll('.product-card-trigger').forEach(card => {
            card.addEventListener('click', function(e) {
                // Nếu click vào nút "Thêm nhanh" thì không mở modal chi tiết
                if(e.target.closest('.btn-quick-add')) return;

                const d = this.dataset;
                document.getElementById('modalName').innerText = d.name;
                document.getElementById('modalPrice').innerText = parseInt(d.price).toLocaleString('vi-VN') + ' VNĐ';
                document.getElementById('modalDesc').innerText = d.desc;
                document.getElementById('modalMenu').innerText = d.menu ? d.menu : 'Đang cập nhật thực đơn...';
                if(d.img) document.getElementById('modalImg').src = d.img;

                // Gán sự kiện cho nút trong modal
                document.getElementById('modalAddToCartBtn').onclick = function() {
                    addToCart({ id: d.id, key: d.key, name: d.name, price: parseInt(d.price), img: d.img });
                    detailModal.hide();
                };
                detailModal.show();
            });
        });

        // Nút Thêm Nhanh (ngoài Card)
        document.querySelectorAll('.btn-quick-add').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Chặn nổi bọt để không mở modal chi tiết
                const card = this.closest('.product-card-trigger');
                const d = card.dataset;
                addToCart({ id: d.id, key: d.key, name: d.name, price: parseInt(d.price), img: d.img });
                
                // Hiệu ứng visual nút bấm
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fa fa-check text-white"></i>';
                this.classList.remove('btn-primary');
                this.classList.add('btn-success');
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.add('btn-primary');
                    this.classList.remove('btn-success');
                }, 1000);
            });
        });

        // Xóa Giỏ Hàng
        document.getElementById('btnClearCart').addEventListener('click', () => {
            if(cart.length === 0) return;
            Swal.fire({
                title: 'Xóa danh sách?', icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Xóa', cancelButtonText: 'Hủy'
            }).then((res) => {
                if(res.isConfirmed) { cart = []; saveCart(); }
            });
        });

        // Chuyển trang đặt bàn
        document.getElementById('btnCheckout').addEventListener('click', function() {
            if(cart.length === 0) {
                Toast.fire({ icon: 'warning', title: 'Vui lòng chọn ít nhất 1 vé!' });
                return;
            }
            // Hiệu ứng loading
            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ĐANG XỬ LÝ...';
            this.disabled = true;
            
            localStorage.setItem(CART_KEY, JSON.stringify(cart));
            setTimeout(() => window.location.href = BOOKING_URL, 500);
        });

        // Init
        renderCartUI();
    });
</script>
@endpush
@endsection