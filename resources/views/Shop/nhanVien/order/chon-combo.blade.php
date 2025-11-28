@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Chọn Combo Buffet')

@section('content')
    {{-- 1. IMPORT FONTS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    {{-- 2. CSS STYLING (Design System) --}}
    <style>
        :root {
            --primary: #fea116;       /* Cam vàng */
            --primary-dark: #d98a12;  /* Cam đậm */
            --dark: #0f172b;          /* Xanh đen */
            --white: #ffffff;
            --text-main: #1e293b;
            --text-sub: #64748b;
            --bg-light: #f8f9fa;
            
            --shadow-card: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
            --radius: 8px;
            --anim-fast: 0.2s ease;
        }

        body { font-family: 'Nunito', sans-serif; background-color: var(--bg-light); color: var(--text-main); }
        h2, h3, h4, h5, strong, .font-heading { font-family: 'Heebo', sans-serif; }

        /* --- HEADER SECTION --- */
        .page-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .header-title { color: var(--dark); font-weight: 800; font-size: 1.8rem; text-transform: uppercase; }
        
        /* --- INFO BOX (Context Order) --- */
        .context-box {
            background: var(--white); border-radius: var(--radius); padding: 15px 20px;
            border-left: 4px solid var(--primary); box-shadow: var(--shadow-card);
            margin-bottom: 30px; display: flex; align-items: center; gap: 20px;
        }
        .context-item { display: flex; flex-direction: column; }
        .context-label { font-size: 0.75rem; color: var(--text-sub); font-weight: 700; text-transform: uppercase; }
        .context-value { font-size: 1.1rem; font-weight: 800; color: var(--dark); font-family: 'Heebo'; }

        /* --- COMBO CARD --- */
        .combo-card {
            background: var(--white); border-radius: var(--radius); overflow: hidden;
            border: 1px solid #f1f5f9; box-shadow: var(--shadow-card);
            transition: var(--anim-fast); height: 100%; display: flex; flex-direction: column;
        }
        .combo-card:hover {
            transform: translateY(-5px); box-shadow: var(--shadow-hover);
            border-color: rgba(254, 161, 22, 0.4);
        }

        /* Image Area */
        .img-wrapper { position: relative; height: 200px; overflow: hidden; }
        .combo-img {
            width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;
        }
        .combo-card:hover .combo-img { transform: scale(1.05); }
        
        .price-badge {
            position: absolute; bottom: 10px; right: 10px;
            background: rgba(15, 23, 43, 0.9); /* Dark background */
            color: var(--primary); padding: 5px 12px; border-radius: 4px;
            font-family: 'Heebo'; font-weight: 800; font-size: 1.1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        /* Card Body */
        .card-body-custom { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        
        .combo-title {
            font-size: 1.25rem; font-weight: 800; color: var(--dark); margin-bottom: 10px;
            font-family: 'Heebo'; line-height: 1.2;
        }

        .combo-desc-label {
            font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
            color: var(--text-sub); margin-bottom: 8px; display: block;
        }

        /* List Items */
        .menu-list { list-style: none; padding: 0; margin: 0 0 20px 0; flex: 1; }
        .menu-item {
            display: flex; align-items: center; padding: 8px 0;
            border-bottom: 1px dashed #f1f5f9;
        }
        .menu-item:last-child { border-bottom: none; }
        
        .item-thumb {
            width: 40px; height: 40px; border-radius: 6px; object-fit: cover;
            border: 1px solid #e2e8f0; margin-right: 10px; flex-shrink: 0;
        }
        .item-name { font-size: 0.9rem; font-weight: 600; color: var(--text-main); flex: 1; }
        .item-qty {
            background: #f1f5f9; color: var(--text-sub); padding: 2px 8px;
            border-radius: 4px; font-size: 0.75rem; font-weight: 700;
        }

        /* Button */
        .btn-select {
            width: 100%; border: none; padding: 12px; border-radius: 6px;
            font-weight: 800; text-transform: uppercase; font-family: 'Heebo', sans-serif;
            font-size: 0.9rem; cursor: pointer; transition: var(--anim-fast);
            background: var(--primary); color: var(--white);
            box-shadow: 0 4px 10px rgba(254, 161, 22, 0.3);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-select:hover {
            background: var(--primary-dark); transform: translateY(-2px);
        }

        .btn-back {
            background: #e2e8f0; color: var(--text-sub); text-decoration: none;
            padding: 8px 16px; border-radius: 6px; font-weight: 700; font-size: 0.85rem;
            display: inline-flex; align-items: center; gap: 5px; transition: 0.2s;
        }
        .btn-back:hover { background: #cbd5e1; color: var(--dark); }

    </style>

    <div class="container py-4">
        
        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <a href="{{ route('nhanVien.order.index') }}" class="btn-back mb-2">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
                <h2 class="header-title">Chọn Combo Buffet</h2>
            </div>
            <span class="text-muted fw-bold">{{ date('d/m/Y') }}</span>
        </div>

        {{-- CONTEXT INFO (Bàn nào, Order nào) --}}
        <div class="context-box">
            <div class="context-item">
                <span class="context-label">Mã Order</span>
                <span class="context-value">#{{ $order->id }}</span>
            </div>
            <div style="width: 1px; height: 30px; background: #e2e8f0;"></div>
            <div class="context-item">
                <span class="context-label">Bàn Phục Vụ</span>
                <span class="context-value">
                    @if($order->banAn)
                        Bàn {{ $order->banAn->so_ban }}
                    @else
                        <span class="text-danger">Chưa xếp</span>
                    @endif
                </span>
            </div>
        </div>

        {{-- COMBO GRID --}}
        <div class="row">
            @foreach ($combos as $combo)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="combo-card">
                    {{-- Hình ảnh & Giá --}}
                    <div class="img-wrapper">
                        @php $imgPath = 'uploads/combo_buffet/' . $combo->anh; @endphp
                        <img src="{{ file_exists(public_path($imgPath)) ? asset($imgPath) : 'https://placehold.co/600x400?text=No+Image' }}" 
                             class="combo-img" alt="{{ $combo->ten_combo }}">
                        
                        <div class="price-badge">
                            {{ number_format($combo->gia_co_ban) }} <span style="font-size: 0.7em; font-weight: 600;">đ</span>
                        </div>
                    </div>

                    <div class="card-body-custom">
                        <h5 class="combo-title">{{ $combo->ten_combo }}</h5>
                        
                        <span class="combo-desc-label"><i class="fa-solid fa-list-ul"></i> Menu bao gồm:</span>
                        
                        <ul class="menu-list">
                            @foreach ($combo->monTrongCombo as $ct)
                                @php $monImgPath = 'uploads/mon_an/' . $ct->monAn->anh; @endphp
                                <li class="menu-item">
                                    <img src="{{ file_exists(public_path($monImgPath)) ? asset($monImgPath) : 'https://placehold.co/100?text=Mon' }}" 
                                         class="item-thumb" alt="mon">
                                    <span class="item-name">{{ $ct->monAn->ten_mon }}</span>
                                    <span class="item-qty">x{{ $ct->gioi_han_so_luong }}</span>
                                </li>
                            @endforeach
                            @if($combo->monTrongCombo->isEmpty())
                                <li class="text-muted small fst-italic">Đang cập nhật món...</li>
                            @endif
                        </ul>

                        {{-- Form Submit --}}
                        <form method="POST" action="{{ route('nhanVien.order.luu-combo', $order->id) }}">
                            @csrf
                            <input type="hidden" name="combo_id" value="{{ $combo->id }}">
                            <button type="submit" class="btn-select">
                                <i class="fa-solid fa-check-circle"></i> CHỌN COMBO NÀY
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
@endsection