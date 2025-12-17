@extends('layouts.shop.layout-nhanvien')

@section('title', 'Chọn Combo Buffet')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;700;800&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        :root { --primary: #fea116; --primary-hover: #db8a10; --dark: #0f172b; --light: #F1F8FF; --text-main: #1e293b; --text-sub: #64748b; --white: #ffffff; --radius: 8px; --shadow-card: 0 4px 20px rgba(0, 0, 0, 0.05); }
        body { font-family: 'Nunito', sans-serif; background-color: var(--light); color: var(--text-main); }
        .page-header-title { font-weight: 800; color: var(--dark); text-transform: uppercase; position: relative; padding-left: 15px; font-size: 1.5rem; }
        .page-header-title::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); height: 70%; width: 5px; background-color: var(--primary); border-radius: 2px; }
        .btn-back { background: #fff; border: 1px solid #e2e8f0; color: var(--text-sub); padding: 8px 16px; border-radius: 6px; font-weight: 700; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: 0.2s; }
        .btn-back:hover { background: #f1f5f9; color: var(--dark); border-color: #cbd5e1; }
        
        /* Filter Menu */
        .filter-menu { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
        .filter-btn { background: var(--white); border: 1px solid #e2e8f0; color: var(--text-sub); padding: 8px 20px; border-radius: 50px; font-weight: 700; font-size: 0.95rem; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; }
        .filter-btn:hover { background: #fff8e6; border-color: var(--primary); color: var(--primary-hover); transform: translateY(-2px); }
        .filter-btn.active { background: var(--primary); color: var(--white); border-color: var(--primary); box-shadow: 0 4px 10px rgba(254, 161, 22, 0.4); }

        /* Combo Card */
        .combo-card { background: var(--white); border-radius: var(--radius); overflow: hidden; border: 1px solid #e2e8f0; box-shadow: var(--shadow-card); transition: 0.3s; display: flex; flex-direction: column; height: 100%; cursor: pointer; }
        .combo-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1); border-color: var(--primary); }
        .combo-card.disabled { opacity: 0.5; pointer-events: none; filter: grayscale(80%); }
        .img-wrapper { position: relative; height: 200px; overflow: hidden; }
        .combo-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .combo-card:hover .combo-img { transform: scale(1.05); }
        .price-badge { position: absolute; bottom: 10px; right: 10px; background: rgba(15, 23, 43, 0.9); color: var(--primary); padding: 5px 12px; border-radius: 6px; font-family: 'Heebo', sans-serif; font-weight: 800; font-size: 1rem; box-shadow: 0 2px 10px rgba(0,0,0,0.2); backdrop-filter: blur(4px); }
        .card-body-custom { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        .combo-title { font-size: 1.2rem; font-weight: 800; color: var(--dark); margin-bottom: 5px; line-height: 1.3; }
        
        /* Input Qty */
        .input-group-custom { background: #f8fafc; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 4px; }
        .btn-qty { width: 36px; height: 36px; background: transparent; border: none; border-radius: 6px; font-weight: 800; color: var(--primary); font-size: 1.2rem; transition: 0.2s; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .btn-qty:hover { background: var(--primary); color: var(--white); }
        .combo-qty { text-align: center; border: none; background: transparent; font-weight: 700; color: var(--dark); width: 50px; font-size: 1.1rem; }
        .combo-qty:focus { outline: none; }
        
        /* Footer Button */
        .btn-confirm { width: 100%; padding: 14px; border: none; border-radius: var(--radius); background: var(--primary); color: var(--white); font-weight: 800; font-size: 1rem; text-transform: uppercase; box-shadow: 0 4px 15px rgba(254, 161, 22, 0.4); transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-confirm:hover { background: var(--primary-hover); transform: translateY(-2px); }

        /* Modal */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 43, 0.6); backdrop-filter: blur(4px); z-index: 1050; display: none; justify-content: center; align-items: center; }
        .modal-overlay.show { display: flex; }
        .modal-content-custom { background: var(--white); width: 90%; max-width: 500px; max-height: 90vh; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .modal-header-custom { padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
        .modal-body-custom { padding: 20px; overflow-y: auto; }
        .menu-list-item { display: flex; gap: 12px; padding: 10px; border: 1px solid #f1f5f9; border-radius: 8px; margin-bottom: 8px; align-items: center; }
        
        /* Warning Message */
        #warning-message { 
            border-left: 4px solid #f59e0b; 
            background-color: #fef3c7; 
            color: #92400e;
            animation: slideDown 0.3s ease-out;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <main class="app-content container-xxl py-4 px-4">
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('nhanVien.order.index') }}" class="btn-back mb-2">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
                <h4 class="page-header-title m-0">Chọn Combo Buffet</h4>
            </div>
            <div class="text-end">
                <span class="text-muted fw-bold small d-block">Ngày tạo</span>
                <span class="fw-bold text-dark">{{ date('d/m/Y') }}</span>
            </div>
        </div>

        {{-- CONTEXT INFO --}}
        <div class="context-box mb-4">
            <div class="context-item">
                <span class="context-label">Mã Order</span>
                <span class="context-value">#{{ $order->id }}</span>
            </div>
            <div style="width: 1px; height: 40px; background: #e2e8f0;"></div>
            <div class="context-item">
                <span class="context-label">Bàn Phục Vụ</span>
                <span class="context-value">
                    @if($order->banAn) {{ $order->banAn->so_ban }} @else <span class="text-danger">--</span> @endif
                </span>
            </div>
            <div style="width: 1px; height: 40px; background: #e2e8f0;"></div>
            <div class="context-item flex-grow-1 text-end">
                <span class="context-label">Tổng tạm tính</span>
                <span class="context-value text-primary" id="cart-total-display">0 đ</span>
            </div>
        </div>

        {{-- FILTER PRICE --}}
        <div class="mb-4">
            <div class="filter-menu">
                <a href="#" class="filter-btn active" data-price="all">Tất cả</a>
                @foreach ([99000, 199000, 299000, 399000, 499000] as $price)
                    <a href="#" class="filter-btn" data-price="{{ $price }}">
                        {{ number_format($price/1000, 0) }}k
                    </a>
                @endforeach
            </div>
        </div>

        {{-- FORM COMBO --}}
        <form method="POST" action="{{ route('nhanVien.order.luu-combo', $order->id) }}">
            @csrf
            <div class="row g-4 combo-row">
                @foreach ($combos as $combo)
                {{-- [QUAN TRỌNG] Ép kiểu (int) cho data-price để lọc chính xác --}}
                <div class="col-lg-4 col-md-6 combo-col" data-price="{{ (int)$combo->gia_co_ban }}">
                    <div class="combo-card">
                        <div class="img-wrapper">
                            @php
                                $imgSrc = ($combo->anh && file_exists(public_path('uploads/'.$combo->anh))) 
                                    ? asset('uploads/'.$combo->anh) 
                                    : 'https://placehold.co/600x400?text=Combo';
                            @endphp
                            <img src="{{ $imgSrc }}" class="combo-img" alt="{{ $combo->ten_combo }}">
                            
                            <div class="price-badge">
                                <span class="badge-amount" data-base-price="{{ (int)$combo->gia_co_ban }}">{{ number_format($combo->gia_co_ban) }}</span> 
                                <span style="font-size:0.7em;">đ</span>
                            </div>
                        </div>

                        <div class="card-body-custom">
                            <div class="mb-auto">
                                <h5 class="combo-title">{{ $combo->ten_combo }}</h5>
                                <div class="text-muted small mb-2" style="font-size: 0.85rem;">
                                    @foreach($combo->monTrongCombo->take(3) as $mon)
                                        @if($mon->monAn) • {{ $mon->monAn->ten_mon }} @endif
                                    @endforeach
                                    @if($combo->monTrongCombo->count() > 3)... @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 fw-bold mb-3 view-detail-btn" 
                                    style="color: var(--primary); font-size: 0.85rem;"
                                    data-title="{{ $combo->ten_combo }}"
                                    data-price="{{ (int)$combo->gia_co_ban }}"
                                    data-menu="{{ htmlspecialchars(json_encode($combo->monTrongCombo->map(function($item){ 
                                        return [
                                            "name" => $item->monAn->ten_mon ?? "", 
                                            "desc" => $item->monAn->mo_ta ?? "", 
                                            "img" => $item->monAn->hinh_anh ?? "", 
                                            "limit" => $item->gioi_han_so_luong
                                        ]; 
                                    })), ENT_QUOTES, 'UTF-8') }}">
                                    <i class="fa-solid fa-circle-info me-1"></i> Xem chi tiết menu
                                </button>
                            </div>

                            @php
                                $qtyInDb = $order->datBan->combos->where('id', $combo->id)->first()?->pivot->so_luong ?? 0;
                            @endphp
                            <div class="input-group-custom">
                                <button type="button" class="btn-qty btn-decrease"><i class="fa-solid fa-minus"></i></button>
                                <input type="number" min="0" value="{{ $qtyInDb }}" class="combo-qty"
                                    name="combos[{{ $combo->id }}]"
                                    data-price="{{ (int)$combo->gia_co_ban }}">
                                <button type="button" class="btn-qty btn-increase"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4 p-3 bg-white rounded shadow-sm border border-warning bg-opacity-10 d-none" id="cart-preview-box">
                <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-cart-shopping text-primary me-2"></i>Các combo đã chọn:</h6>
                <div id="cart-summary-text" class="text-muted small"></div>
            </div>

            {{-- Thông báo cảnh báo --}}
            <div class="mt-3 alert alert-warning d-none" id="warning-message" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <strong>Cảnh báo:</strong> Số lượng combo hiện tại <span id="current-combo-count">0</span> ít hơn số khách (<span id="min-combo-count">{{ $order->datBan ? ($order->datBan->nguoi_lon + $order->datBan->tre_em) : 1 }}</span> người). Vui lòng chọn thêm combo!
            </div>

            <button type="submit" class="btn-confirm mt-4">
                <i class="fa-solid fa-check-circle"></i> Xác nhận & Lưu Combo
            </button>
        </form>
    </main>

    {{-- MODAL CHI TIẾT --}}
    <div id="detailModal" class="modal-overlay">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h5 class="modal-title-custom">Chi tiết Combo</h5>
                <button type="button" class="modal-close-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body-custom">
                <h3 class="modal-title fw-bold mb-1" id="modal-combo-title"></h3>
                <span class="modal-price-tag" style="font-size:1.5rem; color:var(--primary); font-weight:800;" id="modal-combo-price"></span>
                <div class="mt-3" id="modal-menu-list"></div>
            </div>
            <div class="modal-footer-custom text-end" style="padding:15px; border-top:1px solid #eee;">
                <button type="button" class="btn btn-secondary btn-sm fw-bold px-4 modal-close-btn-action">Đóng</button>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const orderMinCombo = {{ $order->datBan ? ($order->datBan->nguoi_lon + $order->datBan->tre_em) : 1 }};
            const cartTotalDisplay = document.getElementById('cart-total-display');
            const cartPreviewBox = document.getElementById('cart-preview-box');
            const cartSummaryText = document.getElementById('cart-summary-text');
            const modal = document.getElementById('detailModal');

            // --- 1. FILTER GIÁ (SỬA LỖI) ---
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Active style
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const price = this.dataset.price;
                    
                    document.querySelectorAll('.combo-col').forEach(col => {
                        // So sánh chuỗi hoặc số đều được vì data-price giờ là int
                        if (price === 'all' || col.dataset.price == price) {
                            col.style.display = 'block';
                        } else {
                            col.style.display = 'none';
                        }
                    });
                });
            });

            // --- 2. XỬ LÝ SỐ LƯỢNG (+/-) ---
            function updateQuantity(input, change) {
                let currentVal = parseInt(input.value) || 0;
                let newVal = Math.max(0, currentVal + change);
                input.value = newVal;
                calculateTotal();
            }

            document.querySelectorAll('.btn-increase').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('.combo-qty');
                    updateQuantity(input, 1);
                });
            });

            document.querySelectorAll('.btn-decrease').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('.combo-qty');
                    updateQuantity(input, -1);
                });
            });

            document.querySelectorAll('.combo-qty').forEach(input => {
                input.addEventListener('input', calculateTotal);
            });

            // --- 3. TÍNH TỔNG TIỀN & LOGIC KHÓA ---
            const warningMessage = document.getElementById('warning-message');
            const currentComboCount = document.getElementById('current-combo-count');

            function calculateTotal() {
                let total = 0;
                let selectedPrice = null;
                let items = [];
                let totalQty = 0;

                document.querySelectorAll('.combo-qty').forEach(input => {
                    const qty = parseInt(input.value) || 0;
                    const price = parseInt(input.dataset.price);
                    const card = input.closest('.combo-card');
                    const badgeAmount = card.querySelector('.badge-amount');
                    const basePrice = parseInt(badgeAmount.dataset.basePrice);

                    totalQty += qty; // Tổng số lượng combo

                    if (qty > 0) {
                        total += qty * price;
                        selectedPrice = price; 
                        const title = card.querySelector('.combo-title').innerText;
                        items.push(`<b>${title}</b> (x${qty})`);
                        badgeAmount.innerText = (qty * price).toLocaleString();
                        card.style.borderColor = 'var(--primary)';
                        card.style.backgroundColor = '#fffbeb';
                    } else {
                        badgeAmount.innerText = basePrice.toLocaleString();
                        card.style.borderColor = '#e2e8f0';
                        card.style.backgroundColor = '#fff';
                    }
                });

                cartTotalDisplay.innerText = total.toLocaleString() + ' đ';

                if (items.length > 0) {
                    cartPreviewBox.classList.remove('d-none');
                    cartSummaryText.innerHTML = items.join(', ');
                } else {
                    cartPreviewBox.classList.add('d-none');
                }

                // Kiểm tra và hiển thị cảnh báo nếu số lượng combo < số người
                if (totalQty < orderMinCombo) {
                    warningMessage.classList.remove('d-none');
                    currentComboCount.textContent = totalQty;
                } else {
                    warningMessage.classList.add('d-none');
                }

                // Khóa các combo khác giá
                document.querySelectorAll('.combo-card').forEach(card => {
                    const input = card.querySelector('.combo-qty');
                    const price = parseInt(input.dataset.price);
                    if (selectedPrice !== null && price !== selectedPrice && input.value == 0) {
                        card.classList.add('disabled');
                        input.disabled = true;
                    } else {
                        card.classList.remove('disabled');
                        input.disabled = false;
                    }
                });
            }

            // --- 4. MODAL CHI TIẾT ---
            document.querySelectorAll('.view-detail-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const title = this.dataset.title;
                    const price = parseInt(this.dataset.price).toLocaleString();
                    const menu = JSON.parse(this.dataset.menu);

                    document.getElementById('modal-combo-title').innerText = title;
                    document.getElementById('modal-combo-price').innerText = price + ' đ';
                    
                    const listEl = document.getElementById('modal-menu-list');
                    listEl.innerHTML = '';

                    if(menu.length === 0) {
                        listEl.innerHTML = '<p class="text-muted fst-italic">Đang cập nhật...</p>';
                    } else {
                        menu.forEach(item => {
                            const imgSrc = item.img ? `{{ asset('uploads') }}/${item.img}` : 'https://placehold.co/50';
                            const limitText = item.limit ? `Giới hạn: ${item.limit}` : 'Không giới hạn';
                            const html = `
                                <div class="menu-list-item">
                                    <img src="${imgSrc}" class="item-thumb" style="width:50px;height:50px;border-radius:6px;object-fit:cover;">
                                    <div class="item-details" style="flex:1;">
                                        <div style="font-weight:700;">${item.name}</div>
                                        <div class="text-muted small">${item.desc || ''}</div>
                                    </div>
                                    <span class="badge bg-light text-dark border">${limitText}</span>
                                </div>`;
                            listEl.insertAdjacentHTML('beforeend', html);
                        });
                    }
                    modal.classList.add('show');
                });
            });

            function closeModal() { modal.classList.remove('show'); }
            document.querySelector('.modal-close-btn').onclick = closeModal;
            document.querySelector('.modal-close-btn-action').onclick = closeModal;
            modal.onclick = function(e) { if(e.target === modal) closeModal(); }

            // --- 5. VALIDATION ---
            document.querySelector('form').addEventListener('submit', function(e) {
                let totalQty = 0;
                document.querySelectorAll('.combo-qty').forEach(input => totalQty += parseInt(input.value) || 0);
                if (totalQty < orderMinCombo) {
                    e.preventDefault();
                    alert(`⚠️ Tổng số lượng Combo phải tối thiểu bằng số khách (${orderMinCombo} khách).`);
                }
            });

            calculateTotal(); // Chạy lần đầu
        });
    </script>
@endsection