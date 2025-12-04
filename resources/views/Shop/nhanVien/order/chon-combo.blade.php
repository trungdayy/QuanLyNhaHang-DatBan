@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Chọn Combo Buffet')

{{-- 1. IMPORT FONTS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

{{-- 2. CSS STYLING (Design System) --}}
<style>
    :root {
        --primary: #fea116;
        /* Cam vàng */
        --primary-dark: #d98a12;
        /* Cam đậm */
        --dark: #0f172b;
        /* Xanh đen */
        --white: #ffffff;
        --text-main: #1e293b;
        --text-sub: #64748b;
        --bg-light: #f8f9fa;

        --shadow-card: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        --shadow-hover: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
        --radius: 8px;
        --anim-fast: 0.2s ease;
    }

    body {
        font-family: 'Nunito', sans-serif;
        background-color: var(--bg-light);
        color: var(--text-main);
    }

    h2,
    h3,
    h4,
    h5,
    strong,
    .font-heading {
        font-family: 'Heebo', sans-serif;
    }

    /* --- HEADER SECTION --- */
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: nowrap;
        margin-bottom: 20px;
        gap: 10px;
        background: linear-gradient(135deg, #020617, #000000) !important;
        box-shadow: 0 15px 40px rgba(0, 0, 0, .8);
    }

    .page-header span.text-muted {
        flex-shrink: 0;
    }

    .header-title {
        color: var(--primary) !important;
        font-weight: 800;
        font-size: 1.8rem;
        text-transform: uppercase;
        flex-shrink: 1;
    }


    /* --- INFO BOX (Context Order) --- */
    .context-box {
        background-color: black;
        color: var(--text-main);
        box-shadow: inset 0 0 20px rgba(251, 191, 36, .2), 0 20px 40px rgba(0, 0, 0, .8);
        border-radius: var(--radius);
        padding: 15px 20px;
        border-left: 4px solid var(--primary);
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .context-item {
        display: flex;
        flex-direction: column;
    }

    .context-label {
        font-size: 0.75rem;
        color: var(--text-sub);
        font-weight: 700;
        text-transform: uppercase;
    }

    .context-value {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--dark);
        font-family: 'Heebo';
    }

    /* --- COMBO CARD --- */
    .combo-card {
        background: var(--white);
        border-radius: var(--radius);
        overflow: hidden;
        border: 1px solid #f1f5f9;
        box-shadow: var(--shadow-card);
        transition: var(--anim-fast);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .combo-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(254, 161, 22, 0.4);
    }

    /* ẩn món */
    .combo-card .combo-desc-label,
    .combo-card .menu-list {
        display: none !important;
    }


    /* Image Area */
    .img-wrapper {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .combo-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .combo-card:hover .combo-img {
        transform: scale(1.05);
    }

    .price-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(15, 23, 43, 0.9);
        /* Dark background */
        color: var(--primary);
        padding: 5px 12px;
        border-radius: 4px;
        font-family: 'Heebo';
        font-weight: 800;
        font-size: 1.1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    /* Card Body */
    .card-body-custom {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .combo-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 10px;
        font-family: 'Heebo';
        line-height: 1.2;
    }

    .combo-desc-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-sub);
        margin-bottom: 8px;
        display: block;
    }

    /* List Items */
    .menu-list {
        list-style: none;
        padding: 0;
        margin: 0 0 20px 0;
        flex: 1;
    }

    .menu-item {
        display: flex;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px dashed #f1f5f9;
    }

    .menu-item:last-child {
        border-bottom: none;
    }

    .item-thumb {
        width: 40px;
        height: 40px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        margin-right: 10px;
        flex-shrink: 0;
    }

    .item-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #ffffff !important;
        flex: 1;
    }

    .item-qty {
        background: #f1f5f9;
        color: var(--text-sub);
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    /* Button */
    .btn-select {
        width: 100%;
        border: none;
        padding: 12px;
        border-radius: 6px;
        font-weight: 800;
        text-transform: uppercase;
        font-family: 'Heebo', sans-serif;
        font-size: 0.9rem;
        cursor: pointer;
        transition: var(--anim-fast);
        background: var(--primary);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(254, 161, 22, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-select:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    .btn-back {
        flex-shrink: 0;
        background: #e2e8f0;
        color: var(--text-sub);
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: 0.2s;
    }

    .btn-back:hover {
        background: #cbd5e1;
        color: var(--dark);
    }

    /* Button ± tròn */
    .btn-increase,
    .btn-decrease {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        background-color: #f8f9fa;
        font-weight: 800;
        font-size: 1.2rem;
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s ease;
    }

    .btn-increase:hover,
    .btn-decrease:hover {
        background-color: var(--primary);
        color: var(--white);
        transform: scale(1.1);
    }

    /* Input số lượng */
    .combo-qty {
        text-align: center;
        font-weight: 700;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        transition: transform 0.15s ease;
    }

    /* Animation khi thay đổi số lượng */
    .combo-qty.animate {
        transform: scale(1.2);
    }

    .filter-menu {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .filter-menu a {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid var(--primary);
        color: var(--primary);
        background: var(--white);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .filter-menu a:hover {
        background: var(--primary);
        color: var(--white);
        transform: translateY(-2px);
    }

    .filter-menu a.active {
        background: var(--primary);
        color: var(--white);
        box-shadow: 0 4px 12px rgba(254, 161, 22, 0.3);
    }

    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-content {
        background: #ffffff;
        border-radius: 12px;
        width: 90%;
        max-width: 520px;

        max-height: calc(100vh - 40px);
        /* luôn nằm trong khung nhìn */
        display: flex;
        flex-direction: column;

        position: relative;
        overflow: hidden;
    }

    .modal-body {
        overflow-y: auto;
        padding-right: 10px;
    }

    /* ===== FIX NÚT ĐÓNG MODAL (X) MÀU TRẮNG ===== */
    .modal-close {
        background: #000;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.4);
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 20px;
        transition: 0.2s;
        box-shadow: 0 0 10px rgba(255, 255, 255, .6);
    }

    .modal-close i {
        color: #ffffff !important;
        font-size: 18px;
    }

    .modal-close:hover {
        background: #fbbf24;
        color: black !important;
        transform: rotate(90deg) scale(1.1);
        box-shadow: 0 0 12px rgba(251, 191, 36, .8);
    }

    .modal-close:hover i {
        color: black !important;
    }

    .modal-add {
        position: sticky;
        bottom: 0;

        margin-top: 15px;
        width: 100%;
    }


    .modal-img {
        width: 100%;
        height: auto;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .modal-title {
        font-weight: 800;
        font-size: 1.3rem;
        margin-bottom: 5px;
    }

    .modal-price {
        font-weight: 700;
        color: #fea116;
        margin-bottom: 10px;
    }

    .modal-desc {
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 10px;
    }

    .modal-qty {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }

    .combo-card.disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    @media (max-width: 768px) {
        .header-title {
            font-size: 1.4rem;
        }
    }

    .modal-menu-list {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        overflow: hidden;
    }

    .modal-menu-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 12px;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 0.9rem;
    }

    .modal-menu-row:last-child {
        border-bottom: none;
    }

    .modal-menu-name {
        flex: 1;
        font-weight: 600;
        color: #0f172b;
    }

    .modal-menu-qty {
        min-width: 80px;
        text-align: right;
        font-weight: 700;
        color: #64748b;
    }

    .item-info {
        flex: 1;
    }

    .item-desc {
        display: block;
        font-size: 0.75rem;
        color: #ffffff !important;
        margin-top: 2px;
        line-height: 1.2;
        opacity: 0.9;
    }

    body {
        background: radial-gradient(circle at top, #ffffffff, #ffffffff);
        color: #eef2f5ff;
    }

    :root {
        --primary: #fbbf24;
        /* Vàng kim */
        --primary-dark: #d97706;
        --dark: #020617;
        --glass: rgba(0, 0, 0, .55);
    }

    /* ========================= */
    /*  HEADER                  */
    /* ========================= */

    .page-header {
        padding: 18px 22px;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.8));
        border-radius: 14px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, .6);
    }

    .header-title {
        color: #fbbf24;
        letter-spacing: 1px;
        text-shadow: 0 2px 8px rgba(251, 191, 36, .4);
    }

    .page-header span {
        color: #fde68a !important;
    }

    /* ========================= */
    /*  CONTEXT BOX              */
    /* ========================= */

    .context-label {
        color: #facc15;
    }

    .context-value {
        color: #fff;
    }

    /* ========================= */
    /*  FILTER MENU              */
    /* ========================= */

    .filter-menu a {
        background: linear-gradient(135deg, #020617, #111827);
        border: 1px solid #fbbf24;
        color: #fde68a;
        box-shadow: 0 0 5px rgba(251, 191, 36, .4);
    }

    .filter-menu a:hover,
    .filter-menu a.active {
        background: linear-gradient(135deg, #fbbf24, #d97706);
        color: black;
        box-shadow: 0 0 18px rgba(251, 191, 36, .9);
        transform: translateY(-3px) scale(1.05);
    }


    /* ========================= */
    /* COMBO CARD (GOLD GLASS)   */
    /* ========================= */

    .combo-card {
        background: linear-gradient(160deg, #020617, #111827);
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(0, 0, 0, .7);
    }

    /* viền vàng ánh sáng */
    .combo-card::before {
        content: "";
        position: absolute;
        inset: 0;
        padding: 1px;
        border-radius: 14px;
        background: linear-gradient(135deg, #fbbf24, #fff3b0, #f59e0b);
        -webkit-mask:
            linear-gradient(#000 0 0) content-box,
            linear-gradient(#000 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: .5;
        pointer-events: none;
        /* ✅ KHÔNG CHẶN CLICK */
    }

    .combo-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 60px rgba(251, 191, 36, .35);
    }

    /* ========================= */
    /* IMAGE EFFECT              */
    /* ========================= */

    .img-wrapper::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, .7), transparent);
        pointer-events: none;
    }

    .price-badge {
        background: black;
        border: 1px solid #fbbf24;
        color: #fbbf24;
        box-shadow: 0 0 12px rgba(251, 191, 36, .9);
    }


    /* ========================= */
    /* TITLE & TEXT              */
    /* ========================= */

    .combo-title {
        color: #fbbf24;
    }

    .combo-desc-label {
        color: #fcd34d;
    }

    .menu-item {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 6px;
        margin-bottom: 4px;
    }

    .item-qty {
        background: rgba(251, 191, 36, .2);
        color: #ffffff !important;
    }

    .menu-item:hover .item-name,
    .menu-item:hover .item-desc,
    .menu-item:hover .item-qty {
        color: #ffffff !important;
    }

    .item-name,
    .item-desc,
    .item-qty {
        color: #ffffff !important;
        text-shadow: 0 0 4px rgba(255, 255, 255, 0.4);
    }

    /* ========================= */
    /* BUTTONS                   */
    /* ========================= */

    .btn-select {
        background: linear-gradient(135deg, #fbbf24, #d97706);
        color: black;
        text-shadow: 0 1px 1px rgba(255, 255, 255, .5);
        box-shadow: 0 0 20px rgba(251, 191, 36, .9);
    }

    .btn-select:hover {
        box-shadow: 0 0 30px rgba(251, 191, 36, 1);
        transform: translateY(-3px) scale(1.03);
    }

    .btn-increase,
    .btn-decrease {
        background: black;
        border: 1px solid #fbbf24;
        box-shadow: 0 0 8px rgba(251, 191, 36, .5);
    }

    .btn-increase:hover,
    .btn-decrease:hover {
        background: #fbbf24;
        color: black;
        transform: scale(1.15);
    }

    /* ========================= */
    /* DISABLED COMBO             */
    /* ========================= */

    .combo-card.disabled {
        opacity: .4;
        filter: grayscale(100%);
        box-shadow: none;
    }

    /* ========================= */
    /* MODAL                      */
    /* ========================= */

    .modal-content {
        background: linear-gradient(160deg, #020617, #111827);
        box-shadow: 0 25px 80px rgba(0, 0, 0, .8);
    }

    .modal-title {
        color: #fbbf24;
    }

    .modal-price {
        color: #fde047;
    }

    .modal-add {
        background: linear-gradient(135deg, #fbbf24, #d97706);
        color: black;
        box-shadow: 0 0 20px rgba(251, 191, 36, .9);
    }

    .modal-add:hover {
        box-shadow: 0 0 30px rgba(251, 191, 36, 1);
    }

    /* ========================= */
    /* FIX CLICK SAFETY           */
    /* ========================= */

    .combo-card::after,
    .img-wrapper::before,
    .img-wrapper::after,
    .modal-content::before,
    .modal-content::after {
        pointer-events: none;
        /* ✅ CHẮN TUYỆT ĐỐI LỚP CHẶN CLICK */
    }

    .card-body-custom,
    .card-body-custom * {
        position: relative;
        z-index: 3;
    }

    /* ========================= */
    /* EFFECT CLICK (nhẹ)         */
    /* ========================= */

    .btn-select:active,
    .btn-increase:active,
    .btn-decrease:active {
        transform: scale(.9);
    }

    /* ===== RIÊNG GIAO DIỆN CHI TIẾT MODAL ===== */
    #comboModal .modal-title {
        font-size: 1.5rem;
        font-weight: 900;
        letter-spacing: 0.5px;
    }

    #comboModal .modal-price {
        font-size: 1.2rem;
        font-weight: 800;
        color: #fde047;
    }

    #comboModal .modal-menu-title {
        font-weight: 800;
        color: #fbbf24;
        margin-bottom: 8px;
        margin-top: 10px;
    }

    #comboModal .modal-menu-row {
        background: rgba(255, 255, 255, .04);
        border-radius: 6px;
        transition: 0.2s;
    }

    #comboModal .modal-menu-row:hover {
        background: rgba(251, 191, 36, .15);
        transform: translateX(3px);
    }

    /* ===== INPUT & NÚT TRONG MODAL ===== */
    #comboModal .combo-qty {
        background: black;
        border: 1px solid #fbbf24;
        color: #fbbf24;
        font-weight: 800;
    }

    #comboModal .btn-increase,
    #comboModal .btn-decrease {
        background: #020617;
        border: 1px solid #fbbf24;
        color: #fbbf24;
    }

    #comboModal .btn-increase:hover,
    #comboModal .btn-decrease:hover {
        background: #fbbf24;
        color: black;
    }

    /* ===== BUTTON THÊM COMBO ===== */
    #comboModal .modal-add {
        margin-top: 10px;
        font-weight: 900;
        letter-spacing: .5px;
    }

    #comboModal .modal-menu-row div {
        color: #ffffff !important;
    }

    #comboModal .modal-menu-row div:last-child {
        color: #fde68a !important;
        /* số lượng vàng mềm */
    }

    /* Hiệu ứng nổi */
    #comboModal .modal-menu-row {
        text-shadow: 0 0 4px rgba(255, 255, 255, 0.4);
    }

    .combo-row {
        display: none;
    }
</style>

@section('content')
<main class="app-content">
    <div class="container-xxl py-4">
        <div class="page-header">
            <div>
                <a href="{{ route('nhanVien.order.index') }}" class="btn-back mb-2">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
                <h2 class="header-title">Chọn Combo Buffet</h2>
            </div>
            <span class="text-muted fw-bold">{{ date('d/m/Y') }}</span>
        </div>

        {{-- CONTEXT INFO --}}
        <div class="context-box mb-4">
            <div class="context-item">
                <span class="context-label">Mã Order</span>
                <span class="context-value">Số: {{ $order->id }}</span>
            </div>
            <div style="width: 1px; height: 30px; background: #e2e8f0;"></div>
            <div class="context-item">
                <span class="context-label">Bàn Phục Vụ</span>
                <span class="context-value">
                    @if($order->banAn)
                    {{ $order->banAn->so_ban }}
                    @else
                    <span class="text-danger">Chưa xếp</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="mb-2"></h5>
            <div class="filter-menu">
                @foreach ([99000, 199000, 299000, 399000, 499000] as $price)
                <a href="{{ route('nhanVien.order.chon-combo', ['orderId' => $order->id, 'price' => $price]) }}"
                    class="{{ request('price') == $price ? 'active' : '' }}">
                    {{ number_format($price/1000, 0) }}k
                </a>
                @endforeach
            </div>

        </div>

        {{-- COMBO GRID --}}
        <form method="POST" action="{{ route('nhanVien.order.luu-combo', $order->id) }}">
            @csrf
            <div class="row combo-row">
                @foreach ($combos as $combo)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="combo-card" data-price="{{ $combo->gia_co_ban }}">
                        {{-- Hình ảnh & Giá --}}
                        <div class="img-wrapper">
                            @php
                            $images = [];
                            // Ảnh chính
                            if($combo->anh && file_exists(public_path('uploads/'.$combo->anh))) {
                            $images[] = asset('uploads/'.$combo->anh);
                            } else {
                            $images[] = 'https://placehold.co/600x400?text=No+Image';
                            }

                            // Ảnh phụ (nếu có)
                            if(!empty($combo->anh_phu)) {
                            foreach($combo->anh_phu as $img) {
                            if(file_exists(public_path('uploads/'.$img))) {
                            $images[] = asset('uploads/'.$img);
                            }
                            }
                            }
                            @endphp

                            @if(count($images) > 1)
                            <div class="combo-img-gallery" style="display:flex; overflow-x:auto; gap:5px; padding:5px;">
                                @foreach($images as $img)
                                <img src="{{ $img }}" class="combo-img" style="width:100px; height:80px; object-fit:cover; border-radius:6px;" alt="{{ $combo->ten_combo }}">
                                @endforeach
                            </div>
                            @else
                            <img src="{{ $images[0] }}" class="combo-img" alt="{{ $combo->ten_combo }}">
                            @endif

                            <div class="price-badge" id="price-badge-{{ $combo->id }}">
                                Tạm tính: <span class="badge-amount">{{ number_format($combo->gia_co_ban) }}</span> <span style="font-size:0.7em;font-weight:600;">đ</span>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="card-body-custom">
                            <h5 class="combo-title">{{ $combo->ten_combo }}</h5>
                            <span class="combo-desc-label"><i class="fa-solid fa-list-ul"></i> Menu bao gồm:</span>

                            <ul class="menu-list">
                                @foreach ($combo->monTrongCombo as $ct)
                                @if($ct->monan)
                                @php $monImgPath = $ct->monan->hinh_anh; @endphp
                                <li class="menu-item">
                                    <img src="{{ file_exists(public_path($monImgPath)) ? asset($monImgPath) : 'https://placehold.co/100?text=Mon' }}"
                                        class="item-thumb" alt="{{ $ct->monAn->ten_mon }}">
                                    <div class="item-info">
                                        <span class="item-name">{{ $ct->monAn->ten_mon }}</span>
                                        <small class="item-desc">
                                            <i class="fa-solid fa-circle-info"></i> {{ $ct->monAn->mo_ta }}
                                        </small>
                                    </div>
                                    <span class="item-qty">
                                        {{ $ct->gioi_han_so_luong !== null ? $ct->gioi_han_so_luong : 'Không giới hạn' }}
                                    </span>
                                </li>
                                @endif
                                @endforeach
                                @if($combo->monTrongCombo->isEmpty())
                                <li class="text-muted small fst-italic">Đang cập nhật món...</li>
                                @endif
                            </ul>

                            {{-- Số lượng combo --}}
                            @php
                            $qtyInDb = $order->datBan->combos->where('id', $combo->id)->first()?->pivot->so_luong ?? 0;
                            @endphp
                            <div class="input-group mb-2" style="max-width: 130px;">
                                <button type="button" class="btn-decrease">-</button>
                                <input type="number" min="0" value="{{ $qtyInDb }}" class="form-control combo-qty"
                                    name="combos[{{ $combo->id }}]"
                                    data-price="{{ $combo->gia_co_ban }}">
                                <button type="button" class="btn-increase">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Giỏ hàng tạm tính --}}
            <div class="context-box mt-4">
                <div class="context-item flex-grow-1">
                    <span class="context-label">Giỏ hàng tạm tính</span>
                    <span class="context-value" id="cart-summary">Chưa chọn combo nào</span>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-select mt-3"><i class="fa-solid fa-check"></i> Xác nhận chọn combo</button>
        </form>
    </div>
    <div id="comboModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <button class="modal-close"><i class="fa-solid fa-xmark"></i></button>
            <div class="modal-body">
                <div class="modal-img-gallery" style="display:flex; gap:10px; overflow-x:auto; padding:5px 0;">
                    <!-- ảnh sẽ được JS thêm vào -->
                </div>
                <h3 class="modal-title"></h3>
                <p class="modal-price"></p>
                <div class="modal-menu-title">Menu bao gồm:</div>
                <div class="modal-desc modal-menu-list"></div>
                <div class="modal-qty">
                    <button class="btn-decrease">-</button>
                    <input type="number" value="0" min="0" class="combo-qty">
                    <button class="btn-increase">+</button>
                </div>
                <button class="btn-select modal-add">THÊM COMBO NGAY</button>
            </div>
        </div>
    </div>
</main>
{{-- JS --}}
<script>
    const cartSummary = document.getElementById('cart-summary');
    const orderMinCombo = {{ ($order->datBan->nguoi_lon ?? 0) + ($order->datBan->tre_em ?? 0) }}; // tổng số khách
    // lỗi thì thay thành : const orderMinCombo = {{ ($order->datBan->nguoi_lon ?? 0) + ($order->datBan->tre_em ?? 0) }}; // tổng số khách
    const form = document.querySelector('form');

    form.addEventListener('submit', function(e) {
        let totalCombo = 0;
        document.querySelectorAll('.combo-qty').forEach(input => {
            totalCombo += parseInt(input.value) || 0;
        });

        if (totalCombo < orderMinCombo) {
            e.preventDefault();
            alert(`Số lượng combo phải ít nhất bằng số khách đặt bàn : Số khách hàng ${orderMinCombo}`);
        }
    });

    function updateCart() {
        let total = 0;
        let lines = [];

        document.querySelectorAll('.combo-card > .card-body-custom > .input-group .combo-qty').forEach(input => {
            const qty = parseInt(input.value) || 0;
            const comboCard = input.closest('.combo-card');
            const comboName = comboCard.querySelector('.combo-title').innerText;
            const price = parseInt(input.dataset.price) || 0;
            const subtotal = qty * price;

            const priceBadge = comboCard.querySelector('.price-badge');
            priceBadge.innerHTML = `${subtotal > 0 ? subtotal.toLocaleString() : price.toLocaleString()} <span style="font-size:0.7em;font-weight:600;">đ</span>`;

            if (qty > 0) {
                total += subtotal;
                lines.push(`${comboName} x${qty} = ${subtotal.toLocaleString()} đ`);
            }
        });

        cartSummary.innerText = lines.length ? lines.join(' | ') + ' | Tổng: ' + total.toLocaleString() + ' đ' : 'Chưa chọn combo nào';
    }

    function animateInput(input) {
        input.classList.add('animate');
        setTimeout(() => input.classList.remove('animate'), 150);
    }

    // Nút +
    document.querySelectorAll('.btn-increase').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.input-group').querySelector('.combo-qty');
            input.value = parseInt(input.value || 0) + 1;
            animateInput(input);
            updateCart();
        });
    });

    // Nút -
    document.querySelectorAll('.btn-decrease').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.input-group').querySelector('.combo-qty');
            input.value = Math.max(0, parseInt(input.value || 0) - 1);
            animateInput(input);
            updateCart();
        });
    });

    // Input trực tiếp
    document.querySelectorAll('.combo-qty').forEach(input => {
        input.addEventListener('input', () => {
            animateInput(input);
            updateCart();
        });
    });

    // --- Filter combo theo giá ---
    function filterCombos(price) {
        const comboRow = document.querySelector('.combo-row');
        if (!price) {
            comboRow.style.display = 'none'; // ẩn tất cả khi chưa chọn giá
            return;
        } else {
            comboRow.style.display = 'flex'; // hiện row combo khi chọn giá
        }

        document.querySelectorAll('.col-lg-4').forEach(col => {
            const comboCard = col.querySelector('.combo-card');
            const comboPrice = parseInt(comboCard.dataset.price);

            if (comboPrice === price) {
                col.style.display = 'block';
            } else {
                col.style.display = 'none';
            }
        });
    }

    // --- Modal ---
    const modal = document.getElementById('comboModal');
    const modalImg = modal.querySelector('.modal-img');
    const modalTitle = modal.querySelector('.modal-title');
    const modalPrice = modal.querySelector('.modal-price');
    const modalDesc = modal.querySelector('.modal-desc');
    const modalQty = modal.querySelector('.combo-qty');
    const modalClose = modal.querySelector('.modal-close');
    const modalIncrease = modal.querySelector('.btn-increase');
    const modalDecrease = modal.querySelector('.btn-decrease');
    const modalAddBtn = modal.querySelector('.modal-add');

    // Mở modal khi click vào combo card
    document.querySelectorAll('.combo-card').forEach(card => {
        card.addEventListener('click', e => {
            if (e.target.closest('input') || e.target.closest('button')) return;

            // Lấy tất cả ảnh trong card
            const galleryImgs = card.querySelectorAll('.combo-img-gallery img, .combo-img');
            const images = Array.from(galleryImgs).map(img => img.src);

            const modalGallery = modal.querySelector('.modal-img-gallery');
            modalGallery.innerHTML = '';
            images.forEach(src => {
                const imgEl = document.createElement('img');
                imgEl.src = src;
                imgEl.className = 'modal-img';
                modalGallery.appendChild(imgEl);
            });

            // Title, Price
            const title = card.querySelector('.combo-title').innerText;
            const price = card.dataset.price;

            // Menu
            const descItems = Array.from(card.querySelectorAll('.menu-item')).map(li => {
                const name = li.querySelector('.item-name').innerText;
                const qty = li.querySelector('.item-qty').innerText;
                const imgSrc = li.querySelector('.item-thumb')?.src || 'https://placehold.co/60?text=Mon';
                return `
                <div class="modal-menu-row">
                    <div style="flex-shrink:0;">
                        <img src="${imgSrc}" alt="${name}" style="width:40px; height:40px; border-radius:6px; object-fit:cover; border:1px solid #e2e8f0;">
                    </div>
                    <div style="flex:1; font-weight:600; color:#0f172b;">${name}</div>
                    <div style="min-width:80px; text-align:right; font-weight:700; color:#64748b;">${qty}</div>
                </div>
            `;
            }).join('');

            modalTitle.innerText = title;
            modalPrice.innerText = parseInt(price).toLocaleString() + ' đ';
            modalDesc.innerHTML = descItems || '<div class="text-muted">Đang cập nhật món...</div>';

            // Đồng bộ số lượng
            const mainInput = card.querySelector('.combo-qty');
            modalQty.value = mainInput.value || 0;

            modal.style.display = 'flex';

            // Nút + / - trong modal đồng bộ với card
            modalIncrease.onclick = () => {
                modalQty.value = parseInt(modalQty.value) + 1;
                mainInput.value = modalQty.value;
                animateInput(modalQty);
                animateInput(mainInput);
                updateCart();
            };
            modalDecrease.onclick = () => {
                modalQty.value = Math.max(0, parseInt(modalQty.value) - 1);
                mainInput.value = modalQty.value;
                animateInput(modalQty);
                animateInput(mainInput);
                updateCart();
            };
        });
    });

    // Nút thêm combo ngay
    modalAddBtn.addEventListener('click', () => {
        const comboTitleText = modalTitle.innerText;
        const comboCard = Array.from(document.querySelectorAll('.combo-card')).find(card => card.querySelector('.combo-title').innerText === comboTitleText);
        if (comboCard) {
            const mainInput = comboCard.querySelector('.combo-qty');
            mainInput.value = parseInt(modalQty.value) || 0;
            animateInput(mainInput);
            updateCart();
            modal.style.display = 'none';
        }
    });

    // Đóng modal
    modalClose.addEventListener('click', () => modal.style.display = 'none');
    modal.addEventListener('click', e => {
        if (e.target === modal) modal.style.display = 'none';
    });

    // Lấy giá filter từ request
    const selectedPrice = parseInt("{{ request('price') ?? 0 }}");
    filterCombos(selectedPrice);
    // Cập nhật cart lần đầu
    updateCart();

    function updateCart() {
        let total = 0;
        let lines = [];
        let selectedComboPrice = null;

        // Lặp qua tất cả input combo
        document.querySelectorAll('.combo-card > .card-body-custom > .input-group .combo-qty').forEach(input => {
            const qty = parseInt(input.value) || 0;
            const comboCard = input.closest('.combo-card');
            const comboName = comboCard.querySelector('.combo-title').innerText;
            const price = parseInt(input.dataset.price) || 0;
            const subtotal = qty * price;

            // Cập nhật giá trên card
            const priceBadge = comboCard.querySelector('.price-badge');
            priceBadge.innerHTML = `${subtotal > 0 ? subtotal.toLocaleString() : price.toLocaleString()} <span style="font-size:0.7em;font-weight:600;">đ</span>`;

            if (qty > 0) {
                total += subtotal;
                lines.push(`${comboName} x${qty} = ${subtotal.toLocaleString()} đ`);
                // Lấy giá combo đã chọn
                selectedComboPrice = price;
            }
        });

        // Cập nhật giỏ hàng
        cartSummary.innerText = lines.length ? lines.join(' | ') + ' | Tổng: ' + total.toLocaleString() + ' đ' : 'Chưa chọn combo nào';

        // --- Khóa combo khác giá ---
        document.querySelectorAll('.combo-card > .card-body-custom > .input-group .combo-qty').forEach(input => {
            const price = parseInt(input.dataset.price) || 0;
            if (selectedComboPrice && price !== selectedComboPrice) {
                input.disabled = true;
                input.closest('.combo-card').classList.add('disabled'); // bạn có thể style thẻ này mờ đi
            } else {
                input.disabled = false;
                input.closest('.combo-card').classList.remove('disabled');
            }
        });
    } // Hiển thị combo theo giá
    function showCombosByPrice(price) {
        document.querySelector('.combo-row').style.display = 'flex'; // hiện row
        document.querySelectorAll('.combo-row .col-lg-4').forEach(col => {
            const comboCard = col.querySelector('.combo-card');
            const comboPrice = parseInt(comboCard.dataset.price);
            if (comboPrice === price) {
                col.style.display = 'block';
            } else {
                col.style.display = 'none';
            }
        });
    }

    // Lắng nghe click filter menu
    document.querySelectorAll('.filter-menu a').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const price = parseInt(link.innerText.replace('k', '')) * 1000;
            showCombosByPrice(price);

            // Cập nhật active
            document.querySelectorAll('.filter-menu a').forEach(a => a.classList.remove('active'));
            link.classList.add('active');
        });
    });

    // Nếu load trang đã có giá được request, show ngay
    const initialPrice = parseInt("{{ request('price') ?? 0 }}");
    if (initialPrice) {
        showCombosByPrice(initialPrice);
    }
</script>

@endsection