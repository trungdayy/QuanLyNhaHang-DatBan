@extends('layouts.Shop.layout-oderqr')

@section('title', 'Chọn Combo - ' . ($tenBan ?? 'Ocean Buffet'))

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- 1. CẤU HÌNH MÀU SẮC & LAYOUT CHUNG --- */
        :root {
            --primary: #fea116; 
            --primary-dark: #d98a12;
            --dark: #0f172b;
            --white: #ffffff;
            --text-main: #1e293b;
            --radius: 12px;
            --filter-bg: #fea116;
            --shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
        }

        body { font-family: 'Nunito', sans-serif; background-color: #f2f4f8; color: var(--text-main); padding-bottom: 100px; overflow-x: hidden; }
        .app-content { min-height: 100vh; padding: 20px 10px; display: flex; justify-content: center; }
        .form-container { width: 100%; max-width: 850px; background: var(--white); border-radius: 16px; box-shadow: var(--shadow); overflow: hidden; margin-top: 10px; }

        /* Header */
        .form-header { background: var(--dark); padding: 30px 20px; text-align: center; color: var(--white); border-bottom: 4px solid var(--primary); }
        .form-header h1 { font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 1.8rem; margin: 0 0 5px 0; color: var(--primary); text-transform: uppercase; }
        .form-header p { margin: 0; opacity: 0.9; font-size: 0.9rem; color: #e2e8f0; }

        .form-body { padding: 20px; }
        .section-label { font-weight: 700; font-size: 1.1rem; color: var(--dark); margin-bottom: 15px; display: flex; align-items: center; gap: 10px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
        .section-label i { color: var(--primary); }

        /* Input Khách hàng */
        .input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 5px; color: #64748b; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem; background: #fcfcfc; transition: 0.2s; box-sizing: border-box; }
        .form-control:focus { border-color: var(--primary); outline: none; background: #fff; }

        /* Error Box */
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-error ul { margin: 5px 0 0 20px; padding: 0; }

        /* --- 2. FILTER NAV (THANH LỌC - KIỂU PILL MỚI) --- */
        .category-nav-wrapper {
            background: #ffffff;
            margin: 0 -10px 20px -10px;
            padding: 10px 5px;
            position: sticky;
            top: 0;
            z-index: 900;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); /* Bóng mờ nhẹ dưới thanh */
        }

        .category-nav {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            gap: 10px; /* Khoảng cách giữa các nút */
            scrollbar-width: none;
            flex: 1;
            scroll-behavior: smooth;
            padding: 0 5px;
        }
        .category-nav::-webkit-scrollbar { display: none; }

        /* Style cho từng nút (Viên thuốc) */
        .cat-item {
            padding: 10px 20px;
            font-weight: 700;
            font-size: 0.9rem;
            color: #64748b; /* Màu chữ mặc định */
            background-color: #f1f5f9; /* Màu nền xám nhạt */
            border-radius: 50px; /* Bo tròn */
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Trạng thái Active */
        .cat-item.active {
            background-color: var(--primary); /* Nền cam */
            color: #ffffff; /* Chữ trắng */
            box-shadow: 0 4px 10px rgba(254, 161, 22, 0.3);
        }
        /* Xóa gạch chân cũ */
        .cat-item.active::after { content: none; }

        /* Nút cuộn trái phải */
        .nav-scroll-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--dark);
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            flex-shrink: 0;
            z-index: 910;
        }
        .nav-scroll-btn:active { transform: scale(0.95); background: #f8fafc; }


        /* --- 3. GRID COMBO --- */
        .combo-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        @media (min-width: 768px) { .combo-grid { grid-template-columns: repeat(3, 1fr); } }

        .combo-card {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
            overflow: hidden; position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: center top;
        }
        .combo-card.hiding { opacity: 0; transform: scale(0.9) translateY(20px); pointer-events: none; }
        .combo-card.hidden { display: none; }
        .combo-card:active { transform: scale(0.98); }
        .combo-card.selected { border: 2px solid var(--primary); background: #fffbf2; }

        .card-qty-badge { position: absolute; top: 8px; right: 8px; background: var(--primary); color: #fff; width: 26px; height: 26px; border-radius: 50%; font-size: 0.85rem; font-weight: 800; display: none; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 5; }
        .card-qty-badge.show { display: flex; }

        .combo-img-wrap { width: 100%; padding-top: 60%; position: relative; background: #f1f5f9; }
        .combo-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
        .combo-info-card { padding: 12px; }
        .combo-title { font-weight: 800; font-size: 0.95rem; color: var(--dark); margin-bottom: 4px; height: 2.6em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .combo-price-tag { color: var(--primary-dark); font-weight: 800; font-size: 1rem; }

        .btn-fake-add { margin-top: 8px; width: 100%; padding: 8px; background: #f8fafc; color: var(--primary); border: 1px solid var(--primary); border-radius: 6px; font-weight: 700; font-size: 0.9rem; text-align: center; cursor: pointer; transition: 0.2s; }
        .btn-fake-add.added { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* --- 4. WIDGET & MODAL --- */
        .mini-cart-widget {
            position: fixed; bottom: 10%; right: 20px;
            background: var(--dark); border: 2px solid var(--primary); color: #fff;
            padding: 8px 20px; border-radius: 50px;
            display: none; align-items: center; gap: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            z-index: 1000; cursor: pointer; transition: transform 0.2s;
            animation: slideUpWidget 0.3s ease-out;
        }
        @keyframes slideUpWidget { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .mini-cart-widget:active { transform: scale(0.95); }

        .widget-icon { font-size: 1.5rem; color: var(--primary); position: relative; }
        .widget-qty-badge { position: absolute; top: -5px; right: -8px; background: red; color: white; font-size: 0.7rem; font-weight: bold; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .widget-info { display: flex; flex-direction: column; line-height: 1.2; }
        .widget-label { font-size: 0.75rem; color: #cbd5e1; font-weight: 600; text-transform: uppercase; }
        .widget-total { font-size: 1.1rem; font-weight: 800; color: var(--primary); }

        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 2000; display: none; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: #f9fafb; width: 95%; max-width: 450px; border-radius: 16px; overflow: hidden; display: flex; flex-direction: column; max-height: 85vh; animation: zoomIn 0.2s ease-out; box-shadow: 0 20px 50px rgba(0,0,0,0.2); }
        @keyframes zoomIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

        .modal-header-custom { background: var(--primary); padding: 18px 20px; color: white; display: flex; justify-content: space-between; align-items: center; }
        .modal-title-custom { font-weight: 800; font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 8px; }
        .btn-close-custom { background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; cursor: pointer; transition: 0.2s; }
        .modal-body-custom { padding: 20px; overflow-y: auto; flex: 1; background: #f3f4f6; }
        
        .cart-item { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px; border-radius: 12px; margin-bottom: 12px; border: 1px solid #e5e7eb; }
        .item-info h4 { margin: 0 0 6px 0; font-size: 0.95rem; color: var(--dark); font-weight: 700; }
        .item-price { color: var(--primary); font-weight: 800; font-size: 1rem; }
        .item-actions { display: flex; align-items: center; gap: 5px; }
        .qty-btn-mini { width: 32px; height: 32px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-weight: bold; font-size: 1.1rem; color: #64748b; }
        .qty-display-mini { font-weight: 800; width: 25px; text-align: center; font-size: 1.1rem; color: var(--dark); }
        .btn-remove-mini { margin-left: 8px; color: #ef4444; cursor: pointer; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }

        .modal-footer-custom { padding: 20px; background: #fff; border-top: 1px solid #eee; }
        .cart-summary { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 15px; }
        .summary-label { font-weight: 700; font-size: 1.1rem; color: var(--dark); }
        .summary-total { font-weight: 800; font-size: 1.4rem; color: var(--primary); }
        
        .btn-confirm-order { width: 100%; padding: 15px; background: linear-gradient(to right, #fea116, #f59e0b); color: #fff; border: none; border-radius: 50px; font-weight: 800; font-size: 1.1rem; text-transform: uppercase; cursor: pointer; box-shadow: 0 6px 20px rgba(254, 161, 22, 0.4); transition: transform 0.2s; }
        .btn-confirm-order:disabled { background: #cbd5e1; cursor: not-allowed; box-shadow: none; }
        .btn-confirm-order:active { transform: scale(0.98); }

        .flying-img { position: fixed; z-index: 9999; width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary); pointer-events: none; transition: all 0.8s cubic-bezier(0.2, 1, 0.2, 1); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .detail-img-wrap { position: relative; height: 180px; }
    </style>

    <main class="app-content">
        <form action="{{ route('oderqr.start_order') }}" method="POST" id="orderForm" style="width: 100%; display: flex; justify-content: center;" onsubmit="return validateForm()">
            @csrf
            <input type="hidden" name="ma_qr" value="{{ $qrKey }}">
            @if($datBan) <input type="hidden" name="dat_ban_id" value="{{ $datBan->id }}"> @endif

            <div class="form-container">
                <div class="form-header">
                    <h1>Bắt Đầu Phục Vụ</h1>
                    <p>Chọn Combo để lên đơn cho bàn</p>
                </div>

                <div class="form-body">
                    {{-- [MỚI] Hiển thị lỗi từ Controller --}}
                    @if ($errors->any())
                        <div class="alert-error">
                            <strong><i class="fa-solid fa-triangle-exclamation"></i> Vui lòng kiểm tra lại:</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="section-label"><i class="fa-solid fa-user-group"></i> Thông Tin Khách Hàng</div>
                    @if($datBan && $datBan->ten_khach && $datBan->ten_khach !== 'Khách Vãng Lai')
                        <div style="background: #fffbf0; border: 1px solid #ffeeba; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                            <strong>Khách:</strong> {{ $datBan->ten_khach }} - <strong>SĐT:</strong> {{ $datBan->sdt_khach }}
                            <br><small>Đã đặt: {{ $datBan->nguoi_lon }} Người lớn, {{ $datBan->tre_em }} Trẻ em</small>
                            <input type="hidden" name="ten_khach" value="{{ $datBan->ten_khach }}">
                            <input type="hidden" name="sdt_khach" value="{{ $datBan->sdt_khach }}">
                            <input type="hidden" name="nguoi_lon" value="{{ $datBan->nguoi_lon }}">
                            <input type="hidden" name="tre_em" value="{{ $datBan->tre_em }}">
                        </div>
                    @else
                        <div class="input-grid">
                            <div class="form-group">
                                <label>Họ Tên (*)</label>
                                <input type="text" name="ten_khach" class="form-control" placeholder="Tên khách" required value="{{ old('ten_khach') }}">
                            </div>
                            <div class="form-group">
                                <label>Số Điện Thoại (*)</label>
                                <input type="text" name="sdt_khach" class="form-control" placeholder="Số điện thoại" required value="{{ old('sdt_khach') }}">
                            </div>
                            <div class="form-group">
                                <label>Người Lớn (*)</label>
                                <input type="number" name="nguoi_lon" class="form-control" min="1" value="{{ old('nguoi_lon', 1) }}" required>
                            </div>
                            <div class="form-group">
                                <label>Trẻ Em</label>
                                <input type="number" name="tre_em" class="form-control" min="0" value="{{ old('tre_em', 0) }}">
                            </div>
                        </div>
                    @endif

                    <div class="section-label" style="margin-top: 25px;"><i class="fa-solid fa-book-open"></i> Chọn Gói Buffet</div>
                    
                    {{-- 2. CẤU TRÚC NAV MỚI: MŨI TÊN + CUỘN NGANG --}}
                    <div class="category-nav-wrapper">
                        <div class="nav-scroll-btn" onclick="scrollNav('left')">
                            <i class="fa-solid fa-chevron-left"></i>
                        </div>

                        <div class="category-nav" id="categoryScrollBox">
                            <div class="cat-item active" onclick="filterCombos('all', this)">Tất cả</div>
                            <div class="cat-item" onclick="filterCombos('nguoi_lon', this)">Người Lớn</div>
                            <div class="cat-item" onclick="filterCombos('tre_em', this)">Trẻ Em</div>
                            <div class="cat-item" onclick="filterCombos('vip', this)">VIP</div>
                        </div>

                        <div class="nav-scroll-btn" onclick="scrollNav('right')">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>
                    </div>

                    <div class="combo-grid">
                        @foreach ($combos as $index => $combo)
                            <div class="combo-card" 
                                 id="card-{{ $combo->id }}" 
                                 data-category="{{ $combo->loai_combo ?? 'khac' }}" 
                                 onclick="openDetailModal({{ $combo->id }})">
                                
                                <div class="card-qty-badge" id="badge-{{ $combo->id }}">0</div>
                                
                                <div class="combo-img-wrap">
                                    <img src="{{ $combo->anh ? url('uploads/' . $combo->anh) : 'https://placehold.co/300x200?text=Buffet' }}" class="combo-img">
                                </div>

                                <div class="combo-info-card">
                                    <div class="combo-title">{{ $combo->ten_combo }}</div>
                                    <div class="combo-price-tag">{{ number_format($combo->gia_co_ban) }}đ</div>
                                    
                                    <button type="button" class="btn-fake-add" id="btn-add-{{ $combo->id }}" onclick="quickAdd({{ $combo->id }}, event)">
                                        <i class="fa-solid fa-plus"></i> Chọn
                                    </button>
                                </div>
                            </div>

                            {{-- Input ẩn để gửi về Controller --}}
                            <input type="hidden" name="combos[{{ $index }}][id]" id="input-id-{{ $combo->id }}" value="{{ $combo->id }}" disabled>
                            <input type="hidden" name="combos[{{ $index }}][so_luong]" id="input-qty-{{ $combo->id }}" value="0" disabled>

                            {{-- Modal Chi tiết --}}
                            <div class="modal-overlay" id="detail-modal-{{ $combo->id }}">
                                <div class="modal-box" onclick="event.stopPropagation()">
                                    <div class="detail-img-wrap">
                                        <img src="{{ $combo->anh ? url('uploads/' . $combo->anh) : '' }}" style="width:100%;height:100%;object-fit:cover;">
                                        <button type="button" class="btn-close-custom" onclick="closeDetailModal({{ $combo->id }})" style="position:absolute; top:10px; right:10px;"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <div class="modal-body-custom">
                                        <h3 style="margin:0; font-size:1.2rem; color:var(--dark);">{{ $combo->ten_combo }}</h3>
                                        <div style="color:var(--primary); font-weight:800; font-size:1.1rem; margin-bottom:10px;">{{ number_format($combo->gia_co_ban) }}đ</div>
                                        <div style="font-size:0.9rem; font-weight:700; color:#64748b; margin-bottom:5px;">MENU BAO GỒM:</div>
                                        <ul style="padding-left:20px; margin:0; font-size:0.9rem; color:#334155;">
                                            @foreach ($combo->monTrongCombo as $item)
                                                @if($item->monAn) <li>{{ $item->monAn->ten_mon }}</li> @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="modal-footer-custom">
                                        <button type="button" class="btn-confirm-order" onclick="quickAdd({{ $combo->id }}, null); closeDetailModal({{ $combo->id }})">Thêm vào đơn</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. MINI CART WIDGET --}}
            <div id="mini-cart" class="mini-cart-widget" onclick="openCartModal()">
                <div class="widget-icon">
                    <i class="fa-solid fa-basket-shopping"></i>
                    <div class="widget-qty-badge" id="total-count-badge">0</div>
                </div>
                <div class="widget-info">
                    <div class="widget-label">Tạm tính</div>
                    <div class="widget-total" id="widget-total-price">0đ</div>
                </div>
            </div>

            {{-- 4. CART MODAL (GIỎ HÀNG) --}}
            <div id="cart-modal" class="modal-overlay">
                <div class="modal-box">
                    <div class="modal-header-custom">
                        <div class="modal-title-custom"><i class="fa-solid fa-receipt"></i> Đơn Hàng Tạm Tính</div>
                        <button type="button" class="btn-close-custom" onclick="closeCartModal()"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    
                    <div id="cart-list" class="modal-body-custom"></div>

                    <div class="modal-footer-custom">
                        <div class="cart-summary">
                            <span class="summary-label">Tổng cộng:</span>
                            <span id="cart-modal-total" class="summary-total">0đ</span>
                        </div>
                        <button type="submit" class="btn-confirm-order" id="btn-submit-all">
                            XÁC NHẬN ĐẶT BÀN <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

        </form>
    </main>

    <script>
        const comboData = {
            @foreach($combos as $combo)
                {{ $combo->id }}: {
                    name: "{{ $combo->ten_combo }}",
                    price: {{ $combo->gia_co_ban }},
                    img: "{{ $combo->anh ? url('uploads/' . $combo->anh) : '' }}"
                },
            @endforeach
        };
        const formatter = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });

        // --- FILTER FUNCTION ---
        function filterCombos(category, element) {
            document.querySelectorAll('.cat-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            const allCards = document.querySelectorAll('.combo-card');
            allCards.forEach(card => card.classList.add('hiding'));
            setTimeout(() => {
                allCards.forEach(card => {
                    const cardCat = card.getAttribute('data-category');
                    if (category === 'all' || cardCat === category) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
                setTimeout(() => {
                    allCards.forEach(card => {
                        if (!card.classList.contains('hidden')) card.classList.remove('hiding');
                    });
                }, 50);
            }, 300);
        }

        // --- SCROLL NAV FUNCTION (MỚI) ---
        function scrollNav(direction) {
            const container = document.getElementById('categoryScrollBox');
            const scrollAmount = 150; // Khoảng cách cuộn (px)
            
            if (direction === 'left') {
                container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else {
                container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }

        // --- CORE LOGIC ---
        function toggleInputs(id, qty) {
            const inputQty = document.getElementById(`input-qty-${id}`);
            const inputId = document.getElementById(`input-id-${id}`);
            inputQty.value = qty;
            const enable = qty > 0;
            // Chỉ enable input khi có số lượng > 0 để Controller nhận được mảng sạch
            inputQty.disabled = !enable;
            inputId.disabled = !enable;
        }

        function quickAdd(id, event) {
            if(event) event.stopPropagation();
            const input = document.getElementById(`input-qty-${id}`);
            let qty = parseInt(input.value) || 0;
            qty++;
            updateSystem(id, qty);

            // Effect
            const btn = document.getElementById(`btn-add-${id}`);
            const img = comboData[id].img;
            flyToCart(btn, img);
        }

        function flyToCart(sourceBtn, imgUrl) {
            const cart = document.getElementById('mini-cart');
            if(cart.style.display === 'none') return; 

            const flyer = document.createElement('img');
            flyer.src = imgUrl || 'https://placehold.co/50';
            flyer.classList.add('flying-img');
            
            const start = sourceBtn.getBoundingClientRect();
            flyer.style.top = start.top + 'px';
            flyer.style.left = start.left + 'px';
            document.body.appendChild(flyer);

            const end = cart.getBoundingClientRect();
            
            setTimeout(() => {
                flyer.style.top = (end.top + 10) + 'px';
                flyer.style.left = (end.left + 10) + 'px';
                flyer.style.width = '20px';
                flyer.style.height = '20px';
                flyer.style.opacity = '0.5';
            }, 10);

            setTimeout(() => {
                flyer.remove();
                cart.style.transform = 'scale(1.1)';
                setTimeout(() => cart.style.transform = 'scale(1)', 150);
            }, 800);
        }

        function updateSystem(id, qty) {
            toggleInputs(id, qty);
            
            const badge = document.getElementById(`badge-${id}`);
            const card = document.getElementById(`card-${id}`);
            const btn = document.getElementById(`btn-add-${id}`);
            badge.innerText = qty;
            if(qty > 0) {
                badge.classList.add('show');
                card.classList.add('selected');
                btn.innerHTML = `<i class="fa-solid fa-check"></i> Đã chọn (${qty})`;
                btn.classList.add('added');
            } else {
                badge.classList.remove('show');
                card.classList.remove('selected');
                btn.innerHTML = `<i class="fa-solid fa-plus"></i> Chọn`;
                btn.classList.remove('added');
            }

            recalcTotal();
        }

        function recalcTotal() {
            let total = 0;
            let count = 0;
            for(let id in comboData) {
                const val = parseInt(document.getElementById(`input-qty-${id}`).value) || 0;
                if(val > 0) {
                    total += val * comboData[id].price;
                    count += val;
                }
            }

            const totalStr = formatter.format(total);
            document.getElementById('total-count-badge').innerText = count;
            document.getElementById('widget-total-price').innerText = totalStr;
            document.getElementById('cart-modal-total').innerText = totalStr;

            const widget = document.getElementById('mini-cart');
            const submitBtn = document.getElementById('btn-submit-all');

            if(count > 0) {
                widget.style.display = 'flex';
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'XÁC NHẬN ĐẶT BÀN <i class="fa-solid fa-arrow-right"></i>';
            } else {
                widget.style.display = 'none';
                submitBtn.disabled = true; // Khóa nút gửi nếu chưa chọn gì
                submitBtn.innerHTML = 'Vui lòng chọn combo';
                closeCartModal();
            }
        }

        // --- MODAL CONTROLLERS ---
        function openDetailModal(id) { document.getElementById(`detail-modal-${id}`).classList.add('active'); }
        function closeDetailModal(id) { document.getElementById(`detail-modal-${id}`).classList.remove('active'); }
        function openCartModal() { renderCartList(); document.getElementById('cart-modal').classList.add('active'); }
        function closeCartModal() { document.getElementById('cart-modal').classList.remove('active'); }

        function renderCartList() {
            const list = document.getElementById('cart-list');
            list.innerHTML = '';
            let hasItem = false;
            for(let id in comboData) {
                const qty = parseInt(document.getElementById(`input-qty-${id}`).value) || 0;
                if(qty > 0) {
                    hasItem = true;
                    const item = comboData[id];
                    list.innerHTML += `
                        <div class="cart-item">
                            <div class="item-info">
                                <h4>${item.name}</h4>
                                <div class="item-price">${formatter.format(item.price)}</div>
                            </div>
                            <div class="item-actions">
                                <div class="qty-btn-mini" onclick="updateCartItem(${id}, -1)">-</div>
                                <div class="qty-display-mini">${qty}</div>
                                <div class="qty-btn-mini" onclick="updateCartItem(${id}, 1)">+</div>
                                <div class="btn-remove-mini" onclick="updateCartItem(${id}, -${qty})">
                                    <i class="fa-solid fa-trash-can"></i>
                                </div>
                            </div>
                        </div>
                    `;
                }
            }
            if(!hasItem) {
                list.innerHTML = '<div style="text-align:center; padding:20px; color:#999;">Chưa có combo nào được chọn</div>';
            }
        }

        function updateCartItem(id, change) {
            const input = document.getElementById(`input-qty-${id}`);
            let qty = parseInt(input.value) || 0;
            qty += change;
            if(qty < 0) qty = 0;
            updateSystem(id, qty);
            renderCartList();
        }

        // --- VALIDATION TRƯỚC KHI GỬI ---
        function validateForm() {
            const countBadge = document.getElementById('total-count-badge');
            if(parseInt(countBadge.innerText) <= 0) {
                alert('Vui lòng chọn ít nhất 1 Combo để tiếp tục!');
                return false;
            }
            
            // Check input khách hàng (nếu hiển thị)
            const nameInput = document.querySelector('input[name="ten_khach"]');
            if(nameInput && nameInput.value.trim() === '') {
                alert('Vui lòng nhập tên khách hàng!');
                nameInput.focus();
                return false;
            }

            // Hiệu ứng loading nút gửi
            const btn = document.getElementById('btn-submit-all');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
            btn.disabled = true;
            return true;
        }
    </script>
@endsection