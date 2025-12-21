@extends('layouts.shop.layout-nhanvien')

@section('title', 'Chọn Combo Buffet')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        :root {
            --primary: #fea116;
            --primary-hover: #e58e0a;
            --dark: #0f172b;
            --light: #f4f7fa;
            --white: #ffffff;
            --text-main: #2d3748;
            --text-sub: #718096;
            --border: #e2e8f0;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.04);
            --shadow-md: 0 8px 24px rgba(0,0,0,0.08);
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        body { font-family: 'Nunito', sans-serif; background-color: var(--light); color: var(--text-main); -webkit-font-smoothing: antialiased; padding-bottom: 80px; /* Space for sticky footer */ }
        
        /* --- HEADER --- */
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .page-title { font-size: 1.5rem; font-weight: 800; color: var(--dark); margin: 0; display: flex; align-items: center; gap: 10px; }
        .page-title::before { content: ''; width: 6px; height: 24px; background: var(--primary); border-radius: 4px; display: inline-block; }
        .btn-back { width: 40px; height: 40px; border-radius: 50%; background: var(--white); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--text-sub); transition: 0.2s; box-shadow: var(--shadow-sm); text-decoration: none; }
        .btn-back:hover { background: var(--primary); color: var(--white); border-color: var(--primary); }

        /* --- CONTEXT BOX (INFO) --- */
        .context-box { background: var(--white); border-radius: var(--radius-md); padding: 16px 20px; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between; border: 1px solid var(--border); gap: 15px; flex-wrap: wrap; }
        .context-item { display: flex; flex-direction: column; }
        .context-label { font-size: 0.75rem; color: var(--text-sub); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 2px; }
        .context-value { font-size: 1.1rem; font-weight: 800; color: var(--dark); }
        .divider-vertical { width: 1px; height: 30px; background: var(--border); }

        /* --- FILTER MENU (SCROLLABLE ON MOBILE) --- */
        .filter-wrapper { position: sticky; top: 0; z-index: 99; background: var(--light); padding: 10px 0; margin: 0 -12px 15px -12px; }
        .filter-menu { display: flex; gap: 10px; padding: 0 12px; overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
        .filter-menu::-webkit-scrollbar { display: none; }
        
        .filter-btn { white-space: nowrap; background: var(--white); border: 1px solid var(--border); color: var(--text-sub); padding: 8px 24px; border-radius: 100px; font-weight: 700; font-size: 0.9rem; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.03); }
        .filter-btn:hover { transform: translateY(-2px); border-color: var(--primary); color: var(--primary); }
        .filter-btn.active { background: var(--primary); color: var(--white); border-color: var(--primary); box-shadow: 0 4px 12px rgba(254, 161, 22, 0.4); }

        /* --- COMBO CARD (DEFAULT DESKTOP) --- */
        .combo-card { background: var(--white); border-radius: var(--radius-lg); overflow: hidden; border: 1px solid transparent; box-shadow: var(--shadow-sm); transition: 0.3s; height: 100%; display: flex; flex-direction: column; position: relative; }
        .combo-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); border-color: rgba(254, 161, 22, 0.3); }
        .combo-card.active-card { border: 2px solid var(--primary); background: #fffbf2; }
        .combo-card.disabled { opacity: 0.6; filter: grayscale(90%); pointer-events: none; }

        .img-wrapper { position: relative; padding-top: 60%; /* 16:9 Aspect Ratio */ overflow: hidden; background: #eee; }
        .combo-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
        .combo-card:hover .combo-img { transform: scale(1.1); }
        
        .price-badge { position: absolute; bottom: 10px; right: 10px; background: rgba(15, 23, 43, 0.95); color: var(--white); padding: 6px 14px; border-radius: 10px; font-weight: 800; font-size: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.2); backdrop-filter: blur(4px); display: flex; align-items: baseline; gap: 2px; }
        .price-badge span { color: var(--primary); }

        .card-body-custom { padding: 16px; flex: 1; display: flex; flex-direction: column; }
        .combo-title { font-size: 1.15rem; font-weight: 800; color: var(--dark); margin-bottom: 8px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.6em; }
        .combo-desc { font-size: 0.85rem; color: var(--text-sub); margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.6em; }
        
        .btn-view-detail { color: var(--primary); font-weight: 700; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; margin-bottom: 15px; background: transparent; border: none; padding: 0; cursor: pointer; }
        
        /* --- QUANTITY INPUT --- */
        .qty-control-wrapper { margin-top: auto; background: var(--light); border-radius: 50px; padding: 4px; display: flex; align-items: center; justify-content: space-between; border: 1px solid var(--border); }
        .btn-qty { width: 34px; height: 34px; border-radius: 50%; border: none; background: var(--white); color: var(--dark); display: flex; align-items: center; justify-content: center; font-size: 1rem; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .btn-qty:hover { background: var(--primary); color: var(--white); }
        .btn-qty.btn-increase { background: var(--dark); color: var(--white); }
        .combo-qty { width: 40px; text-align: center; border: none; background: transparent; font-weight: 800; color: var(--dark); font-size: 1.1rem; padding: 0; }
        .combo-qty:focus { outline: none; }

        /* --- STICKY BOTTOM BAR --- */
        .bottom-action-bar { position: fixed; bottom: 0; left: 0; width: 100%; background: var(--white); padding: 15px 20px; box-shadow: 0 -5px 20px rgba(0,0,0,0.1); z-index: 1000; display: flex; align-items: center; justify-content: space-between; gap: 15px; animation: slideUp 0.3s ease-out; border-top: 1px solid var(--border); }
        .cart-info { display: flex; flex-direction: column; }
        .cart-label { font-size: 0.8rem; color: var(--text-sub); }
        .cart-total { font-size: 1.25rem; font-weight: 800; color: var(--primary); }
        .btn-confirm { background: var(--dark); color: var(--white); border: none; padding: 12px 24px; border-radius: 50px; font-weight: 700; font-size: 1rem; text-transform: uppercase; letter-spacing: 0.5px; transition: 0.3s; display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        
        /* --- MODAL --- */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 1100; display: none; align-items: center; justify-content: center; padding: 15px; opacity: 0; transition: opacity 0.3s; }
        .modal-overlay.show { opacity: 1; }
        .modal-content-custom { background: var(--white); width: 100%; max-width: 480px; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); transform: translateY(20px); transition: transform 0.3s; display: flex; flex-direction: column; max-height: 85vh; }
        .modal-overlay.show .modal-content-custom { transform: translateY(0); }
        .modal-header-custom { background: var(--dark); color: var(--white); padding: 20px; position: relative; }
        .modal-close-btn { position: absolute; top: 15px; right: 15px; background: rgba(255,255,255,0.1); border: none; width: 32px; height: 32px; border-radius: 50%; color: var(--white); display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .modal-body-custom { padding: 20px; overflow-y: auto; }
        .menu-item-row { display: flex; gap: 15px; padding: 12px 0; border-bottom: 1px dashed var(--border); align-items: center; }
        .menu-item-img { width: 60px; height: 60px; border-radius: 10px; object-fit: cover; background: #eee; }

        /* --- WARNING MESSAGE --- */
        #warning-message { position: absolute; top: -50px; left: 0; width: 100%; background: #fff3cd; color: #856404; padding: 10px 15px; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; transition: 0.3s; opacity: 0; pointer-events: none; }
        #warning-message.show-warning { top: -45px; opacity: 1; }
        
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }

        /* =================================================================
           RESPONSIVE MOBILE 3 CỘT (QUAN TRỌNG) 
           ================================================================= */
        @media (max-width: 768px) {
            .app-content { padding: 10px !important; }
            .page-title { font-size: 1.25rem; }
            
            /* Context Box compact */
            .context-box { gap: 8px; padding: 10px; }
            .context-item { width: 48%; }
            .context-item:last-child { width: 100%; border-top: 1px dashed var(--border); padding-top: 8px; margin-top: 4px; flex-direction: row; justify-content: space-between; align-items: center; }
            .divider-vertical { display: none; }

            /* Sticky Footer compact */
            .bottom-action-bar { padding: 10px 15px; }
            .btn-confirm { padding: 8px 16px; font-size: 0.85rem; }
            .cart-total { font-size: 1.1rem; }

            /* CARD 3 CỘT - THU NHỎ MỌI THỨ */
            .combo-col { padding-left: 4px; padding-right: 4px; } /* Giảm khoảng cách cột */
            
            .card-body-custom { padding: 8px 5px !important; } /* Padding thẻ bé lại */

            .img-wrapper { padding-top: 80%; } /* Ảnh vuông hơn */
            
            .price-badge { 
                right: 4px; bottom: 4px; 
                padding: 2px 6px; 
                border-radius: 4px; 
                font-size: 0.75rem; 
            }
            .currency-symbol { display: none; } /* Ẩn chữ đ */

            .combo-title { 
                font-size: 0.75rem; /* ~12px */
                margin-bottom: 4px; 
                height: 2.5em; 
                line-height: 1.2;
            }

            .btn-view-detail { font-size: 0.7rem; margin-bottom: 8px; }

            .qty-control-wrapper { padding: 2px; border-radius: 8px; }
            .btn-qty { width: 24px; height: 24px; font-size: 0.7rem; }
            .combo-qty { width: 20px; font-size: 0.9rem; }
        }
    </style>

    <main class="app-content container-xxl py-3">
        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <a href="{{ route('nhanVien.order.index') }}" class="text-decoration-none d-flex align-items-center gap-2 mb-2">
                    <span class="btn-back"><i class="fa-solid fa-arrow-left"></i></span>
                    <span class="text-muted fw-bold small">Quay lại</span>
                </a>
                <h1 class="page-title">Chọn Combo Buffet</h1>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="text-muted small d-block">Ngày tạo</span>
                <span class="fw-bold text-dark">{{ date('d/m/Y') }}</span>
            </div>
        </div>

        {{-- INFO TICKET --}}
        <div class="context-box mb-3">
            <div class="context-item">
                <span class="context-label">Mã Order</span>
                <span class="context-value">#{{ $order->id }}</span>
            </div>
            <div class="divider-vertical"></div>
            <div class="context-item">
                <span class="context-label">Bàn số</span>
                <span class="context-value text-primary">
                    @if($order->banAn) {{ $order->banAn->so_ban }} @else -- @endif
                </span>
            </div>
            <div class="divider-vertical"></div>
            <div class="context-item">
                <span class="context-label">Khách hàng</span>
                <span class="context-value">{{ $order->datBan ? ($order->datBan->nguoi_lon + $order->datBan->tre_em) . ' người' : '1 người' }}</span>
            </div>
        </div>

        {{-- FILTER HORIZONTAL --}}
        <div class="filter-wrapper">
            <div class="filter-menu">
                <a href="#" class="filter-btn active" data-price="all">Tất cả</a>
                @foreach ([99000, 199000, 299000, 399000, 499000] as $price)
                    <a href="#" class="filter-btn" data-price="{{ $price }}">
                        {{ number_format($price/1000, 0) }}k
                    </a>
                @endforeach
            </div>
        </div>

        {{-- MAIN FORM --}}
        <form method="POST" action="{{ route('nhanVien.order.luu-combo', $order->id) }}" id="combo-form">
            @csrf
            {{-- g-2: Khoảng cách giữa các cột hẹp lại để vừa 3 cái --}}
            <div class="row g-2 combo-row pb-5">
                @foreach ($combos as $combo)
                {{-- col-4: ÉP BUỘC CHIA 3 CỘT TRÊN MỌI MÀN HÌNH --}}
                <div class="col-4 col-md-4 col-lg-3 combo-col" data-price="{{ (int)$combo->gia_co_ban }}">
                    <div class="combo-card">
                        {{-- Image --}}
                        <div class="img-wrapper">
                            @php
                                $imgSrc = ($combo->anh && file_exists(public_path('uploads/'.$combo->anh))) 
                                    ? asset('uploads/'.$combo->anh) 
                                    : 'https://placehold.co/600x400/png?text=Combo';
                            @endphp
                            <img src="{{ $imgSrc }}" class="combo-img" alt="{{ $combo->ten_combo }}">
                            <div class="price-badge">
                                <span class="badge-amount" data-base-price="{{ (int)$combo->gia_co_ban }}">{{ number_format($combo->gia_co_ban) }}</span> 
                                <span class="currency-symbol">đ</span>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="card-body-custom">
                            <h5 class="combo-title" title="{{ $combo->ten_combo }}">{{ $combo->ten_combo }}</h5>
                            
                            {{-- Ẩn mô tả trên mobile --}}
                            <div class="combo-desc d-none d-md-block">
                                @foreach($combo->monTrongCombo->take(4) as $mon)
                                    @if($mon->monAn) {{ $mon->monAn->ten_mon }} • @endif
                                @endforeach
                            </div>

                            {{-- Nút xem chi tiết --}}
                            <button type="button" class="btn-view-detail view-detail-btn" 
                                data-title="{{ $combo->ten_combo }}"
                                data-price="{{ (int)$combo->gia_co_ban }}"
                                data-menu="{{ htmlspecialchars(json_encode($combo->monTrongCombo->map(function($item){ 
                                    return [
                                        "name" => $item->monAn->ten_mon ?? "Món chưa đặt tên", 
                                        "desc" => $item->monAn->mo_ta ?? "", 
                                        "img" => $item->monAn->hinh_anh ?? "", 
                                        "limit" => $item->gioi_han_so_luong
                                    ]; 
                                })), ENT_QUOTES, 'UTF-8') }}">
                                <i class="fa-solid fa-circle-info"></i> 
                                <span class="d-none d-md-inline">Xem thực đơn</span>
                                <span class="d-md-none">Chi tiết</span>
                            </button>

                            {{-- Quantity Control --}}
                            @php
                                $qtyInDb = $order->datBan->combos->where('id', $combo->id)->first()?->pivot->so_luong ?? 0;
                            @endphp
                            <div class="qty-control-wrapper">
                                <button type="button" class="btn-qty btn-decrease"><i class="fa-solid fa-minus"></i></button>
                                <input type="number" min="0" value="{{ $qtyInDb }}" class="combo-qty"
                                    name="combos[{{ $combo->id }}]"
                                    data-price="{{ (int)$combo->gia_co_ban }}" readonly>
                                <button type="button" class="btn-qty btn-increase"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- BOTTOM ACTION BAR (STICKY) --}}
            <div class="bottom-action-bar">
                <div id="warning-message">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> 
                    Chọn đủ <span id="min-combo-count" class="fw-bold">{{ $order->datBan ? ($order->datBan->nguoi_lon + $order->datBan->tre_em) : 1 }}</span> combo
                </div>

                <div class="cart-info">
                    <span class="cart-label">Tổng tạm tính</span>
                    <span class="cart-total" id="cart-total-display">0 đ</span>
                </div>
                <button type="submit" class="btn-confirm">
                    Xác nhận <i class="fa-solid fa-arrow-right d-none d-md-inline ms-2"></i>
                </button>
            </div>
        </form>
    </main>

    {{-- MODAL CHI TIẾT --}}
    <div id="detailModal" class="modal-overlay">
        <div class="modal-content-custom">
            <div class="modal-header-custom">
                <h5 class="m-0 fw-bold" id="modal-combo-title" style="padding-right: 30px;">--</h5>
                <div class="text-white opacity-75 small" id="modal-combo-price">--</div>
                <button type="button" class="modal-close-btn"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body-custom" id="modal-menu-list">
                </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const orderMinCombo = {{ $order->datBan ? ($order->datBan->nguoi_lon + $order->datBan->tre_em) : 1 }};
            const cartTotalDisplay = document.getElementById('cart-total-display');
            const modal = document.getElementById('detailModal');
            const warningMessage = document.getElementById('warning-message');

            // --- 1. FILTER ---
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Scroll to center selected item on mobile
                    this.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });

                    const price = this.dataset.price;
                    document.querySelectorAll('.combo-col').forEach(col => {
                        if (price === 'all' || col.dataset.price == price) {
                            col.style.display = 'block';
                        } else {
                            col.style.display = 'none';
                        }
                    });
                });
            });

            // --- 2. QTY LOGIC ---
            function updateQuantity(input, change) {
                let currentVal = parseInt(input.value) || 0;
                let newVal = Math.max(0, currentVal + change);
                const card = input.closest('.combo-card');
                
                // Không cho tăng nếu đang bị disable do chọn sai giá
                if(card.classList.contains('disabled') && newVal > 0) return;

                input.value = newVal;
                calculateTotal();
            }

            document.querySelectorAll('.btn-increase').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('.combo-qty');
                    if(!input.disabled) updateQuantity(input, 1);
                });
            });

            document.querySelectorAll('.btn-decrease').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('.combo-qty');
                    if(!input.disabled) updateQuantity(input, -1);
                });
            });

            // --- 3. CALC TOTAL & LOCK ---
            function calculateTotal() {
                let total = 0;
                let selectedPrice = null;
                let totalQty = 0;

                document.querySelectorAll('.combo-qty').forEach(input => {
                    const qty = parseInt(input.value) || 0;
                    const price = parseInt(input.dataset.price);
                    const card = input.closest('.combo-card');
                    
                    totalQty += qty;

                    if (qty > 0) {
                        total += qty * price;
                        selectedPrice = price; 
                        card.classList.add('active-card');
                    } else {
                        card.classList.remove('active-card');
                    }
                });

                cartTotalDisplay.innerText = total.toLocaleString('vi-VN') + ' đ';

                // Warning Logic
                if (totalQty < orderMinCombo) {
                    warningMessage.classList.add('show-warning');
                } else {
                    warningMessage.classList.remove('show-warning');
                }

                // Lock Logic
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

            // --- 4. MODAL ---
            document.querySelectorAll('.view-detail-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const title = this.dataset.title;
                    const price = parseInt(this.dataset.price).toLocaleString();
                    const menu = JSON.parse(this.dataset.menu);

                    document.getElementById('modal-combo-title').innerText = title;
                    document.getElementById('modal-combo-price').innerText = price + ' đ / người';
                    
                    const listEl = document.getElementById('modal-menu-list');
                    listEl.innerHTML = '';

                    if(menu.length === 0) {
                        listEl.innerHTML = '<div class="text-center py-4 text-muted">Đang cập nhật menu...</div>';
                    } else {
                        menu.forEach(item => {
                            const imgSrc = item.img ? `{{ asset('uploads') }}/${item.img}` : 'https://placehold.co/100';
                            const limitText = item.limit ? `<span class="badge bg-danger text-white ms-auto">Giới hạn: ${item.limit}</span>` : '';
                            const html = `
                                <div class="menu-item-row">
                                    <img src="${imgSrc}" class="menu-item-img">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark">${item.name}</div>
                                        <div class="small text-muted">${item.desc || ''}</div>
                                    </div>
                                    ${limitText}
                                </div>`;
                            listEl.insertAdjacentHTML('beforeend', html);
                        });
                    }
                    modal.style.display = 'flex';
                    setTimeout(() => modal.classList.add('show'), 10);
                });
            });

            function closeModal() {
                modal.classList.remove('show');
                setTimeout(() => modal.style.display = 'none', 300);
            }
            document.querySelector('.modal-close-btn').onclick = closeModal;
            modal.onclick = function(e) { if(e.target === modal) closeModal(); }

            // --- 5. SUBMIT CHECK ---
            document.querySelector('#combo-form').addEventListener('submit', function(e) {
                let totalQty = 0;
                document.querySelectorAll('.combo-qty').forEach(input => totalQty += parseInt(input.value) || 0);
                if (totalQty < orderMinCombo) {
                    e.preventDefault();
                    alert(`Vui lòng chọn đủ ${orderMinCombo} combo cho ${orderMinCombo} người.`);
                }
            });

            calculateTotal();
        });
    </script>
@endsection