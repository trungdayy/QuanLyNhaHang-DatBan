@extends('layouts.Shop.layout-oderqr')

@section('title', 'Chọn Combo - ' . ($tenBan ?? 'Ocean Buffet'))

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- 1. CẤU HÌNH CƠ BẢN --- */
        :root {
            --primary: #fea116; 
            --primary-dark: #d98a12;
            --dark: #0f172b;
            --white: #ffffff;
            --text-main: #1e293b;
            --radius: 12px;
            --shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
        }

        body { font-family: 'Nunito', sans-serif; background-color: #f2f4f8; color: var(--text-main); padding-bottom: 100px; overflow-x: hidden; }
        .app-content { min-height: 100vh; padding: 20px 10px; display: flex; justify-content: center; }
        
        .form-container { 
            width: 100%; max-width: 850px; background: var(--white); 
            border-radius: 16px; box-shadow: var(--shadow); 
            overflow: hidden; margin-top: 10px;
            transition: all 0.5s ease-in-out;
        }

        /* Header */
        .form-header { background: var(--dark); padding: 30px 20px; text-align: center; color: var(--white); border-bottom: 4px solid var(--primary); }
        .form-header h1 { font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 1.8rem; margin: 0 0 5px 0; color: var(--primary); text-transform: uppercase; }
        .form-header p { margin: 0; opacity: 0.9; font-size: 0.9rem; color: #e2e8f0; }

        .form-body { padding: 20px; }
        .section-label { font-weight: 700; font-size: 1.1rem; color: var(--dark); margin-bottom: 15px; display: flex; align-items: center; gap: 10px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
        .section-label i { color: var(--primary); }

        /* Input & Error */
        .input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .form-group label { display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 5px; color: #64748b; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 1rem; background: #fcfcfc; transition: 0.2s; box-sizing: border-box; }
        .form-control:focus { border-color: var(--primary); outline: none; background: #fff; }
        .alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-error ul { margin: 5px 0 0 20px; padding: 0; }

        /* --- 2. LAYOUT NÚT CHỌN COMBO (3 TRÊN - 2 DƯỚI) --- */
        .category-nav-wrapper {
            background: #ffffff;
            padding: 10px 0 20px 0;
            margin: 0 -10px;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .category-nav {
            display: flex;
            justify-content: center; 
            flex-wrap: wrap; 
            gap: 15px; 
            padding: 0 15px;
            transition: all 0.6s cubic-bezier(0.25, 1, 0.5, 1);
        }

        /* STYLE CÁC Ô GIÁ */
        .cat-item {
            padding: 25px 10px;
            font-weight: 800; 
            font-size: 1.3rem; 
            text-align: center;
            color: #475569; 
            background-color: #fff; 
            border: 2px solid #e2e8f0;
            border-radius: 20px; 
            cursor: pointer;
            flex: 0 0 calc(33.333% - 15px); 
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            transition: all 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);
            max-width: 500px; 
            max-height: 150px;
            opacity: 1;
            transform: scale(1);
            margin: 0;
            overflow: hidden;
            position: relative; /* Để xác định vị trí z-index */
        }

        .cat-item:not(.active):hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(254, 161, 22, 0.15);
            z-index: 2;
        }
        .cat-item:not(.active):active { transform: scale(0.98); }

        .cat-item.active {
            background-color: var(--primary); 
            color: var(--white); 
            border-color: var(--primary);
            box-shadow: 0 10px 25px rgba(254, 161, 22, 0.4); 
            flex: 0 0 auto; 
            width: auto;
            min-width: 200px; 
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            z-index: 10;
        }

        .cat-item.active.is-removable:hover {
            background-color: #ef4444; border-color: #ef4444;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
        }
        
        .cat-item .reset-icon { margin-left: 10px; display: inline-block; font-size: 1rem; }

        .cat-item.hidden-filter {
            flex: 0 0 0; 
            max-width: 0;
            max-height: 0;
            padding: 0 !important;
            margin: 0 !important;
            border-width: 0;
            opacity: 0;
            transform: scale(0.8);
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .cat-item {
                flex: 0 0 calc(50% - 10px); 
                padding: 20px 5px;
                font-size: 1.1rem;
            }
            .cat-item.hidden-filter { flex: 0 0 0; }
        }

        /* --- 3. GRID COMBO --- */
        .combo-grid { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 15px; 
            position: relative;
        }
        @media (min-width: 768px) { .combo-grid { grid-template-columns: repeat(3, 1fr); } }

        .combo-card {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
            overflow: hidden; position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            cursor: pointer;
            transform-origin: center center;
            opacity: 1;
            will-change: transform, opacity;
        }
        
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

        /* WIDGET & MODAL */
        .mini-cart-widget { position: fixed; bottom: 30px; right: 30px; background: var(--dark); color: #fff; border: 2px solid var(--primary); padding: 16px 35px; border-radius: 60px; display: none; align-items: center; gap: 15px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3); z-index: 1000; cursor: pointer; transition: all 0.3s; }
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
        
        .custom-confirm-modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.7); z-index: 3000; display: none; align-items: center; justify-content: center; }
        .custom-confirm-modal.show { display: flex; }
        .confirm-box { background: var(--white); border-radius: var(--radius); width: 90%; max-width: 350px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); overflow: hidden; animation: zoomIn 0.2s ease-out; }
        .confirm-header { background: var(--dark); color: var(--white); padding: 15px 20px; font-size: 1.1rem; font-weight: 700; border-bottom: 3px solid var(--primary); display: flex; align-items: center; gap: 10px; }
        .confirm-body { padding: 25px 20px; color: var(--text-main); font-size: 0.95rem; line-height: 1.4; }
        .confirm-body strong { color: var(--primary-dark); }
        .confirm-footer { padding: 15px 20px; display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; }
        .confirm-btn { padding: 10px 20px; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; transition: background 0.2s, transform 0.1s; }
        .confirm-btn-ok { background: #10b981; color: var(--white); }
        .confirm-btn-ok:hover { background: #059669; }
        .confirm-btn-ok:active { transform: scale(0.98); }
        .confirm-btn-cancel { background: #f1f5f9; color: var(--dark); border: 1px solid #cbd5e1; }
        .confirm-btn-cancel:hover { background: #e2e8f0; }
        .confirm-btn-cancel:active { transform: scale(0.98); }
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
                    @if ($errors->any())
                        <div class="alert-error">
                            <strong><i class="fa-solid fa-triangle-exclamation"></i> Vui lòng kiểm tra lại:</strong>
                            <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
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
                    
                    {{-- 2. CẤU TRÚC NAV --}}
                    <div class="category-nav-wrapper" id="categoryNavWrapper">
                        <div class="category-nav" id="categoryScrollBox">
                            <div class="cat-item" data-filter-category="199k" onclick="filterCombos('199k', this)">
                                Combo 199K <span class="reset-icon"></span>
                            </div>
                            <div class="cat-item" data-filter-category="299k" onclick="filterCombos('299k', this)">
                                Combo 299K <span class="reset-icon"></span>
                            </div>
                            <div class="cat-item" data-filter-category="399k" onclick="filterCombos('399k', this)">
                                Combo 399K <span class="reset-icon"></span>
                            </div>
                            <div class="cat-item" data-filter-category="499k" onclick="filterCombos('499k', this)">
                                Combo 499K <span class="reset-icon"></span>
                            </div>
                            <div class="cat-item" data-filter-category="99k" onclick="filterCombos('99k', this)">
                                Combo 99K <span class="reset-icon"></span>
                            </div>
                        </div>
                    </div>

                    <div class="combo-grid">
                        @foreach ($combos as $index => $combo)
                            {{-- Mặc định ẩn --}}
                            <div class="combo-card hidden" 
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

                            <input type="hidden" name="combos[{{ $index }}][id]" id="input-id-{{ $combo->id }}" value="{{ $combo->id }}" disabled>
                            <input type="hidden" name="combos[{{ $index }}][so_luong]" id="input-qty-{{ $combo->id }}" value="0" disabled>

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

            {{-- MINI CART WIDGET --}}
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

            {{-- CART MODAL --}}
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
            
            {{-- CUSTOM CONFIRM MODAL --}}
            <div id="custom-confirm" class="custom-confirm-modal" onclick="closeCustomConfirm()">
                <div class="confirm-box" onclick="event.stopPropagation()">
                    <div class="confirm-header">
                        <i class="fa-solid fa-triangle-exclamation" style="color:var(--primary);"></i> Xác Nhận Thao Tác
                    </div>
                    <div class="confirm-body" id="confirm-message">
                        </div>
                    <div class="confirm-footer">
                        <button type="button" class="confirm-btn confirm-btn-cancel" onclick="resolveCustomConfirm(false)">Hủy</button>
                        <button type="button" class="confirm-btn confirm-btn-ok" onclick="resolveCustomConfirm(true)">Đồng Ý</button>
                    </div>
                </div>
            </div>
        </form>
    </main>

<script>
    // Global state
    let selectedCategory = null; 
    let combosInCart = {}; 
    let confirmResolver = null; 

    const comboData = {
        @foreach($combos as $combo)
            {{ $combo->id }}: {
                name: "{{ $combo->ten_combo }}",
                price: {{ $combo->gia_co_ban }},
                img: "{{ $combo->anh ? url('uploads/' . $combo->anh) : '' }}",
                category: "{{ $combo->loai_combo ?? 'khac' }}" 
            },
        @endforeach
    };
    const formatter = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });
    
    // --- CUSTOM CONFIRM HANDLERS ---
    function customConfirm(message) {
        return new Promise((resolve) => {
            document.getElementById('confirm-message').innerHTML = message;
            document.getElementById('custom-confirm').classList.add('show');
            confirmResolver = resolve;
        });
    }
    function resolveCustomConfirm(result) {
        document.getElementById('custom-confirm').classList.remove('show');
        if (confirmResolver) { confirmResolver(result); confirmResolver = null; }
    }
    function closeCustomConfirm() { if(confirmResolver) resolveCustomConfirm(false); }

    // --- LOGIC CHÍNH ---
    async function handleActivePillClick(category, element) {
        const currentTotal = Object.values(combosInCart).reduce((sum, qty) => sum + qty, 0);
        
        // NẾU CÓ MÓN -> HỎI XÓA
        if (currentTotal > 0) {
            const message = `Bạn có chắc chắn muốn xóa toàn bộ <strong>Combo ${category}</strong> đã chọn và trở về màn hình lựa chọn mức giá không?`;
            const result = await customConfirm(message);
            if (result) resetAllCombosInCart();
            return;
        }
        
        // NẾU CHƯA CÓ MÓN -> TỰ ĐỘNG RESET VỀ BAN ĐẦU
        resetAllCombosInCart();
    }
    
    function resetAllCombosInCart() {
        for (let id in comboData) updateSystem(id, 0);
        unlockMenu(); // Mở lại menu khi xóa hết
        selectedCategory = null;
    }

    function filterCombos(category, element) {
        if (element.classList.contains('active')) {
             handleActivePillClick(category, element);
             return;
        }

        // Reset active cũ
        document.querySelectorAll('.cat-item').forEach(el => el.classList.remove('active'));
        
        // Chặn nếu đang chọn combo khác
        if (selectedCategory && category !== selectedCategory) {
            alert(`Đã chọn Combo ${selectedCategory}. Vui lòng xóa hết Combo đã chọn để chuyển sang mức giá khác.`);
            document.querySelector('.cat-item[data-filter-category="' + selectedCategory + '"]').classList.add('active');
            return;
        }

        // Active nút mới
        element.classList.add('active');

        setTimeout(() => {
            selectedCategory = category;
            lockMenu(selectedCategory); 
        }, 50);

        // --- NEW: LOGIC ANIMATION BAY RA (FLY OUT) ---
        const allCards = document.querySelectorAll('.combo-card');
        const activeCards = [];
        const btnRect = element.getBoundingClientRect(); // Lấy tọa độ nút bấm

        // 1. Chuẩn bị: Ẩn tất cả card không khớp, lấy card khớp
        allCards.forEach(card => {
            const cardCat = card.getAttribute('data-category');
            if (category === cardCat) {
                card.classList.remove('hidden'); // Hiện ra DOM để tính toán
                card.style.transition = 'none'; // Tắt animation để set vị trí đầu
                
                // Đặt vị trí ban đầu: Nằm ngay tại vị trí nút bấm
                const cardRect = card.getBoundingClientRect();
                const deltaX = btnRect.left + (btnRect.width/2) - (cardRect.left + (cardRect.width/2));
                const deltaY = btnRect.top + (btnRect.height/2) - (cardRect.top + (cardRect.height/2));
                
                card.style.transform = `translate(${deltaX}px, ${deltaY}px) scale(0)`;
                card.style.opacity = '0';
                
                activeCards.push(card);
            } else {
                card.classList.add('hidden');
            }
        });

        // 2. Kích hoạt Animation: Bay về vị trí lưới
        requestAnimationFrame(() => {
            // Force reflow
            document.body.offsetHeight; 
            
            activeCards.forEach((card, i) => {
                // Thêm delay nhẹ cho từng thẻ để tạo hiệu ứng lần lượt
                setTimeout(() => {
                    card.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)'; // Hiệu ứng nảy (spring)
                    card.style.transform = 'translate(0, 0) scale(1)';
                    card.style.opacity = '1';
                }, i * 50);
            });
        });
    }
    
    function lockMenu(category) {
        document.querySelectorAll('.cat-item').forEach(el => {
            const filterCat = el.getAttribute('data-filter-category');
            if (filterCat !== category) {
                el.classList.add('hidden-filter');
            } else {
                el.classList.remove('hidden-filter');
            }
        });
    }

    function unlockMenu() {
        // Hiện lại tất cả nút
        document.querySelectorAll('.cat-item').forEach(el => el.classList.remove('hidden-filter'));
        
        // --- NEW: LOGIC ANIMATION BAY VỀ (FLY BACK) ---
        // Tìm nút đang active (hoặc vừa active) để làm điểm đích
        let targetBtn = document.querySelector(`.cat-item[data-filter-category="${selectedCategory}"]`);
        
        // Nếu không tìm thấy (trường hợp hiếm), lấy đại nút đầu tiên
        if (!targetBtn) targetBtn = document.querySelector('.cat-item'); 
        
        const btnRect = targetBtn.getBoundingClientRect();
        const visibleCards = [];

        // Lấy tất cả card đang hiện
        document.querySelectorAll('.combo-card:not(.hidden)').forEach(card => {
            visibleCards.push(card);
        });

        // Thực hiện bay về
        visibleCards.forEach((card, i) => {
             // Tính toán đích đến (là vị trí của nút giá)
             const cardRect = card.getBoundingClientRect();
             const deltaX = btnRect.left + (btnRect.width/2) - (cardRect.left + (cardRect.width/2));
             const deltaY = btnRect.top + (btnRect.height/2) - (cardRect.top + (cardRect.height/2));

             card.style.transition = 'all 0.5s ease-in';
             card.style.transform = `translate(${deltaX}px, ${deltaY}px) scale(0)`;
             card.style.opacity = '0';
        });

        // Sau khi bay xong thì mới ẩn display:none và reset style
        setTimeout(() => {
            visibleCards.forEach(card => {
                card.classList.add('hidden');
                card.style.transform = '';
                card.style.opacity = '';
                card.style.transition = '';
            });
        }, 500); // Khớp với thời gian transition ở trên

        
        // Reset trạng thái pill
        document.querySelectorAll('.cat-item').forEach(el => {
            el.classList.remove('active');
            el.classList.remove('is-removable');
            const resetIcon = el.querySelector('.reset-icon');
            if(resetIcon) resetIcon.innerHTML = '';
        });
    }

    function updatePillState() {
        let currentTotal = 0;
        for (let id in combosInCart) currentTotal += combosInCart[id];
        
        const activePill = document.querySelector('.cat-item.active');
        if (activePill) {
            const resetIcon = activePill.querySelector('.reset-icon');
            if (currentTotal > 0) {
                activePill.classList.add('is-removable');
                if(resetIcon) resetIcon.innerHTML = `<i class="fa-solid fa-trash-can"></i>`;
            } else {
                activePill.classList.remove('is-removable');
                if(resetIcon) resetIcon.innerHTML = ``;
            }
        }
    }

    function toggleInputs(id, qty) {
        const inputQty = document.getElementById(`input-qty-${id}`);
        const inputId = document.getElementById(`input-id-${id}`);
        inputQty.value = qty;
        const enable = qty > 0;
        inputQty.disabled = !enable;
        inputId.disabled = !enable;
    }

    function resetOtherCategories(currentComboId) {
        const currentCategory = comboData[currentComboId].category;
        for (let id in comboData) {
            if (comboData[id].category !== currentCategory) {
                const input = document.getElementById(`input-qty-${id}`);
                if ((parseInt(input.value) || 0) > 0) updateSystem(id, 0); 
            }
        }
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

    function quickAdd(id, event) {
        if(event) event.stopPropagation();
        const input = document.getElementById(`input-qty-${id}`);
        const currentQty = parseInt(input.value) || 0;
        
        if (currentQty === 0) { 
            resetOtherCategories(id);
            selectedCategory = comboData[id].category;
            const filterEl = document.querySelector(`.cat-item[data-filter-category="${selectedCategory}"]`);
            if(filterEl) {
                document.querySelectorAll('.cat-item').forEach(el => el.classList.remove('active'));
                filterEl.classList.add('active');
            }
            lockMenu(selectedCategory);
        }
        
        let qty = currentQty + 1;
        updateSystem(id, qty);
        const btn = document.getElementById(`btn-add-${id}`);
        const img = comboData[id].img;
        flyToCart(btn, img);
    }

    function updateSystem(id, qty) {
        toggleInputs(id, qty);
        combosInCart[id] = qty;
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
        updatePillState();
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
        if (count > 0) {
            widget.style.display = 'flex';
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'XÁC NHẬN ĐẶT BÀN <i class="fa-solid fa-arrow-right"></i>';
        } else {
            widget.style.display = 'none';
            submitBtn.disabled = true; 
            submitBtn.innerHTML = 'Vui lòng chọn combo';
            closeCartModal();
        }
    }

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
                        <div class="item-info"><h4>${item.name}</h4><div class="item-price">${formatter.format(item.price)}</div></div>
                        <div class="item-actions">
                            <div class="qty-btn-mini" onclick="updateCartItem(${id}, -1)">-</div>
                            <div class="qty-display-mini">${qty}</div>
                            <div class="qty-btn-mini" onclick="updateCartItem(${id}, 1)">+</div>
                            <div class="btn-remove-mini" onclick="updateCartItem(${id}, -${qty})"><i class="fa-solid fa-trash-can"></i></div>
                        </div>
                    </div>`;
            }
        }
        if(!hasItem) list.innerHTML = '<div style="text-align:center; padding:20px; color:#999;">Chưa có combo nào được chọn</div>';
    }

    function updateCartItem(id, change) {
        const input = document.getElementById(`input-qty-${id}`);
        let qty = parseInt(input.value) || 0;
        
        // Nếu đang 0 mà bấm +, tự động chọn loại combo đó
        if (change > 0 && qty === 0) {
            resetOtherCategories(id);
            selectedCategory = comboData[id].category;
            const filterEl = document.querySelector(`.cat-item[data-filter-category="${selectedCategory}"]`);
            if(filterEl) {
                document.querySelectorAll('.cat-item').forEach(el => el.classList.remove('active'));
                filterEl.classList.add('active');
            }
            lockMenu(selectedCategory);
        }
        
        qty += change;
        if(qty < 0) qty = 0;
        updateSystem(id, qty);
        renderCartList();
        
        // Nếu giảm về 0 hết tất cả -> Reset về màn hình chọn giá
        let currentTotal = 0;
        for (let k in combosInCart) currentTotal += combosInCart[k];
        if (currentTotal === 0 && selectedCategory) {
            resetAllCombosInCart();
        }
    }

    function validateForm() {
        const countBadge = document.getElementById('total-count-badge');
        if(parseInt(countBadge.innerText) <= 0) { alert('Vui lòng chọn ít nhất 1 Combo để tiếp tục!'); return false; }
        const nameInput = document.querySelector('input[name="ten_khach"]');
        if(nameInput && nameInput.value.trim() === '') { alert('Vui lòng nhập tên khách hàng!'); nameInput.focus(); return false; }
        const btn = document.getElementById('btn-submit-all');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
        btn.disabled = true;
        return true;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.combo-card').forEach(card => card.classList.add('hidden'));
        document.querySelectorAll('.cat-item').forEach(el => el.classList.remove('active'));
    });
</script>
@endsection