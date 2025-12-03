@extends('layouts.page')

@section('title', 'Thực đơn Buffet Ocean')

@section('content')

{{-- =========================================================== --}}
{{-- PHẦN 1: CSS (TỐI ƯU DISPLAY NAME & QUANTITY CONTROLS) --}}
{{-- =========================================================== --}}
<style>
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

    /* 2. CART ITEM (Trong Modal) */
    .cart-item-row {
        padding: 15px 0;
        border-bottom: 1px dashed #eee;
    }

    .cart-item-row:last-child {
        border-bottom: none;
    }

    /* Tên món ăn hiển thị đầy đủ */
    .cart-item-name {
        font-weight: 700;
        color: #333;
        font-size: 1rem;
        line-height: 1.4;
        margin-bottom: 5px;
        white-space: normal !important;
        word-wrap: break-word;
    }

    /* Bộ điều khiển số lượng (+ -) */
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
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
{{-- PHẦN 2: DANH SÁCH MÓN ĂN THEO DANH MỤC --}}
{{-- =========================================================== --}}

<div class="container py-5">
    @foreach($danhMucs as $dm)
    <div class="mb-5">
        <h3 class="text-primary border-bottom pb-2 mb-4 fw-bold text-uppercase">{{ $dm->ten_danh_muc }}</h3>

        <div class="row g-4">
            @forelse($dm->monAn as $mon)
            <div class="col-lg-3 col-md-4 col-sm-6">
                {{-- CARD SẢN PHẨM --}}
                <div class="card mon-card h-100 position-relative overflow-hidden shadow-sm rounded-3 product-card-trigger border-0"
                    data-key="mon_{{ $mon->id }}" data-type="mon" data-name="{{ $mon->ten_mon }}"
                    data-price="{{ $mon->gia }}" data-desc="{{ $mon->mo_ta ?? 'Món ăn ngon miệng' }}"
                    {{-- SỬA LẠI: Chỉ dùng asset($mon->hinh_anh) vì DB đã có đường dẫn đầy đủ --}}
                    data-img="{{ $mon->hinh_anh ? asset($mon->hinh_anh) : '' }}">

                    <div class="position-relative">
                        @if($mon->hinh_anh)
                        {{-- SỬA LẠI: Bỏ phần nối chuỗi 'uploads/monan/' --}}
                        <img src="{{ asset($mon->hinh_anh) }}" alt="{{ $mon->ten_mon }}" class="card-img-top"
                            style="height:200px; object-fit:cover;">
                        @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center"
                            style="height:200px;">
                            <i class="fa fa-utensils fa-3x text-white"></i>
                        </div>
                        @endif

                        {{-- Nút thêm nhanh --}}
                        <button class="btn btn-primary position-absolute top-0 end-0 m-2 btn-quick-add"
                            title="Thêm ngay">
                            <i class="fa fa-plus text-white"></i>
                        </button>
                    </div>

                    <div class="card-body d-flex flex-column p-3">
                        <h5 class="card-title fw-bold mb-2 text-dark">{{ $mon->ten_mon }}</h5>
                        <p class="card-text text-danger fw-bold mb-2 fs-5">
                            {{ number_format($mon->gia,0,',','.') }} VNĐ
                        </p>
                        <p class="text-muted small flex-grow-1 mb-0 line-clamp-2">
                            {{ $mon->mo_ta ? Str::limit($mon->mo_ta,60) : 'Hương vị tuyệt hảo...' }}
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-muted fst-italic">Danh mục này chưa có món nào.</p>
            </div>
            @endforelse
        </div>
    </div>
    @endforeach
</div>

{{-- =========================================================== --}}
{{-- PHẦN 4: FLOATING UI (ICON & CART MODAL NẰM GIỮA) --}}
{{-- =========================================================== --}}

<div id="floatingCartIcon">
    <div class="icon-wrapper">
        <i class="fa fa-shopping-basket"></i>
        <span id="cartCountBadge" class="count-badge">0</span>
    </div>
</div>

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
                        </div>
                    </div>
                    <button id="modalAddToCartBtn"
                        class="btn btn-primary w-100 py-3 mt-3 fw-bold rounded-pill shadow-sm"><i
                            class="fa fa-cart-plus me-2"></i> THÊM VÀO GIỎ</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================== --}}
{{-- PHẦN 5: JAVASCRIPT (ĐÃ CẬP NHẬT LOGIC CHUYỂN TRANG) --}}
{{-- =========================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- CẤU HÌNH ---
        // 🔴 QUAN TRỌNG: Thay đổi đường dẫn trang booking tại đây
        // Nếu dùng đường dẫn cứng: "/dat-ban"
        const BOOKING_URL = "{{ route('booking.index') }}"; 

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
        // Không cần formNote nữa vì sẽ chuyển trang

        // Toast config
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 1500,
            timerProgressBar: false, didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // 1. RENDER GIỎ HÀNG (CÓ NÚT TĂNG GIẢM)
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

            // Gắn sự kiện Tăng/Giảm sau khi render
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

        // 3. SỰ KIỆN UI
        floatingCartIcon.addEventListener('click', () => { cartModal.show(); });

        // Click Sản phẩm -> Mở Modal chi tiết
        document.querySelectorAll('.product-card-trigger').forEach(card => {
            card.addEventListener('click', function(e) {
                if(e.target.closest('.btn-quick-add')) return;
                
                const d = this.dataset;
                document.getElementById('modalName').innerText = d.name;
                document.getElementById('modalPrice').innerText = parseInt(d.price).toLocaleString('vi-VN') + ' VNĐ';
                document.getElementById('modalDesc').innerText = d.desc;
                if(d.img) document.getElementById('modalImg').src = d.img;
                
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

        // Lưu ảnh bill
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

        // =======================================================
        // 4. XỬ LÝ NÚT XÁC NHẬN -> CHUYỂN TRANG BOOKING
        // =======================================================
        document.getElementById('btnCheckout').addEventListener('click', () => {
            if(cart.length === 0) {
                Toast.fire({ icon: 'warning', title: 'Giỏ hàng đang trống!' });
                return;
            }

            // Hiệu ứng nút bấm để người dùng biết đang xử lý
            const btn = document.getElementById('btnCheckout');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ĐANG CHUYỂN...';
            btn.disabled = true;

            // Đảm bảo dữ liệu đã được lưu vào LocalStorage
            localStorage.setItem("oceanCart", JSON.stringify(cart));

            // Đóng modal (cho đẹp)
            cartModal.hide();

            // Chuyển hướng sau 500ms (tạo cảm giác mượt mà)
            setTimeout(() => {
                window.location.href = BOOKING_URL;
            }, 500);
        });

        // Render lần đầu khi load trang
        renderCartUI();
    });
</script>

@endsection