@extends('layouts.Shop.layout-oderqr')

@section('title', 'Thực Đơn - ' . ($tenBan ?? 'Ocean Buffet'))

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap"
        rel="stylesheet">

    <style>
        /* --- 1. GLOBAL & COLORS --- */
        :root {
            --primary: #fea116;
            --primary-dark: #d98a12;
            --dark: #0f172b;
            --white: #ffffff;
            --light: #f1f8ff;
            --success: #20d489;
            --danger: #ff4d4f;
            --text-main: #1e293b;
            --text-sub: #64748b;
            --radius: 12px;
            --shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f2f4f8;
            color: var(--text-main);
            margin: 0;
            padding-bottom: 100px;
        }

        .app-content {
            padding: 20px 10px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 100%;
            max-width: 1280px;
            display: flex;
            gap: 20px;
            position: relative;
        }

        /* --- 2. LEFT COLUMN (INFO & MENU) --- */
        #main-content {
            flex: 1;
            min-width: 0;
        }

        /* Info Card */
        .info-card {
            background: var(--dark);
            color: var(--white);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .info-card h1 {
            font-family: 'Heebo', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--primary);
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .info-pill {
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-pill i {
            color: var(--primary);
        }

        .combo-box {
            margin-top: 15px;
            padding: 10px;
            background: rgba(254, 161, 22, 0.1);
            border: 1px dashed var(--primary);
            border-radius: 8px;
            font-size: 0.9rem;
        }

        /* Filter Navigation */
        .filters-container {
            background: #fff;
            margin: 0 -10px 15px -10px;
            padding: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 900;
            border-bottom: 1px solid #eee;
        }

        .filter-wrapper-relative {
            position: relative;
            display: flex;
            align-items: center;
        }

        .filter-row {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            gap: 8px;
            scrollbar-width: none;
            padding: 5px 40px;
            align-items: center;
            scroll-behavior: smooth;
        }

        .filter-row::-webkit-scrollbar {
            display: none;
        }

        .filter-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: #94a3b8;
            margin-right: 5px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .cat-pill {
            padding: 8px 16px;
            border-radius: 50px;
            background: #f1f5f9;
            color: #64748b;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
            flex-shrink: 0;
        }

        .cat-pill.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 10px rgba(254, 161, 22, 0.3);
            transform: translateY(-1px);
        }

        .type-pill {
            padding: 6px 12px;
            border-radius: 8px;
            background: #fff;
            color: #64748b;
            font-weight: 600;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .type-pill.active {
            background: var(--dark);
            color: #fff;
            border-color: var(--dark);
        }

        .nav-arrow {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            z-index: 10;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.2s;
        }

        .nav-arrow:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
        }

        .nav-arrow.prev {
            left: 5px;
        }

        .nav-arrow.next {
            right: 5px;
        }

        /* Menu Grid & Dish Card */
        .menu-section {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: center top;
            opacity: 1;
            transform: scale(1);
            margin-bottom: 25px;
        }

        .menu-section.hiding {
            opacity: 0;
            transform: scale(0.98);
        }

        .menu-section.hidden {
            display: none;
        }

        .danh-muc-sticky {
            background: transparent;
            padding: 5px 0;
            margin-bottom: 10px;
        }

        .danh-muc-name {
            font-weight: 800;
            color: var(--dark);
            font-size: 1.1rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }

        @media (min-width: 768px) {
            .menu-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            }
        }

        .dish-card {
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s;
            position: relative;
            border: 1px solid transparent;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }

        .dish-card.hidden {
            display: none;
        }

        .dish-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .dish-thumb-wrapper {
            width: 100%;
            padding-top: 65%;
            position: relative;
            background: #f1f5f9;
        }

        .dish-thumb {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dish-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            z-index: 2;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .badge-combo {
            background: var(--success);
            color: #fff;
        }

        .badge-le {
            background: var(--white);
            color: var(--text-sub);
        }

        .dish-body {
            padding: 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .dish-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
            line-height: 1.3;
            height: 2.6em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .dish-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dish-price {
            font-weight: 800;
            color: var(--primary-dark);
            font-size: 1rem;
        }

        .btn-add-cart {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--light);
            color: var(--primary);
            border: 1px solid var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
            z-index: 5;
        }

        .btn-add-cart:active {
            transform: scale(0.9);
            background: var(--primary);
            color: #fff;
        }

        /* Right Sidebar & Status */
        #right-sidebar {
            width: 340px;
            flex-shrink: 0;
        }

        .status-panel {
            position: sticky;
            top: 20px;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 40px);
            overflow: hidden;
        }

        .panel-header {
            background: var(--dark);
            color: white;
            padding: 15px;
            font-weight: 800;
            font-size: 1rem;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--primary);
        }

        .panel-body {
            padding: 15px;
            overflow-y: auto;
            flex: 1;
            background: #f8fafc;
        }

        .status-card {
            background: #fff;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid #f1f5f9;
            position: relative;
            transition: all 0.2s;
        }

        .status-card.pending {
            border-left: 4px solid #f97316;
        }

        .status-card.cooking {
            border-left: 4px solid #3b82f6;
        }

        .status-card.completed {
            border-left: 4px solid #22c55e;
            opacity: 0.8;
        }

        .status-card.cancelled {
            border-left: 4px solid #ef4444;
            opacity: 0.6;
        }

        .stt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .stt-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--dark);
            line-height: 1.4;
            flex: 1;
        }

        .stt-qty {
            background: var(--dark);
            color: #fff;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.8rem;
            margin-left: 10px;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stt-note {
            font-size: 0.8rem;
            color: #64748b;
            font-style: italic;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stt-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            border-top: 1px dashed #e2e8f0;
        }

        .badge-status {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
            letter-spacing: 0.5px;
        }

        .pending .badge-status {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }

        .cooking .badge-status {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }

        .completed .badge-status {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #dcfce7;
        }

        .cancelled .badge-status {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }

        .timer-badge {
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
            border: 1px solid #cbd5e1;
        }

        .timer-badge.late {
            color: #b91c1c;
            background: #fef2f2;
            border-color: #fca5a5;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }

        .btn-cancel-item {
            background: #fff;
            border: 1px solid #ef4444;
            color: #ef4444;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 4px;
            text-transform: uppercase;
        }

        .btn-cancel-item:hover {
            background: #ef4444;
            color: #fff;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
        }

        /* Widget & Modal & Toast */
        .mini-cart-widget {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--dark);
            color: #fff;
            border: 2px solid var(--primary);
            padding: 16px 35px;
            border-radius: 60px;
            display: none;
            align-items: center;
            gap: 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .mini-cart-widget:active {
            transform: scale(0.95);
        }

        .mini-cart-widget.show {
            display: flex;
            animation: bounceIn 0.5s;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0);
            }

            80% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .widget-icon {
            font-size: 1.8rem;
            color: var(--primary);
            display: flex;
            align-items: center;
        }

        .widget-qty {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ff4d4f;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            font-size: 0.85rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .widget-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .widget-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #cbd5e1;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .widget-total {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #f9fafb;
            width: 95%;
            max-width: 450px;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 85vh;
            animation: zoomIn 0.2s ease-out;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .modal-header {
            background: var(--primary);
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-weight: 800;
            font-size: 1.1rem;
            margin: 0;
        }

        .btn-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .modal-body {
            padding: 15px;
            overflow-y: auto;
            flex: 1;
        }

        .cart-item {
            background: #fff;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-item-info h4 {
            margin: 0 0 4px 0;
            font-size: 0.95rem;
            color: var(--dark);
            font-weight: 700;
        }

        .cart-item-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
            color: #64748b;
        }

        .qty-num {
            font-weight: 800;
            min-width: 20px;
            text-align: center;
        }

        .btn-trash {
            color: #ef4444;
            cursor: pointer;
            margin-left: 8px;
            padding: 5px;
        }

        .modal-footer {
            padding: 20px;
            background: #fff;
            border-top: 1px solid #eee;
        }

        .btn-confirm {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-weight: 800;
            font-size: 1rem;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(254, 161, 22, 0.4);
        }

        .btn-confirm:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            box-shadow: none;
        }

        .flying-img {
            position: fixed;
            z-index: 9999;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary);
            pointer-events: none;
            transition: all 0.8s cubic-bezier(0.2, 1, 0.2, 1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .toast {
            pointer-events: auto;
            background: var(--dark);
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid var(--primary);
            opacity: 0;
            transform: translateX(50px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        /* Slideshow & Detail Modal */
        .slideshow-container {
            position: relative;
            width: 100%;
            height: 250px;
            background: #000;
            overflow: hidden;
        }

        .slide-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
            position: absolute;
            top: 0;
            left: 0;
        }

        .slide-img.active {
            opacity: 1;
            z-index: 1;
        }

        .slide-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.4);
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            transition: 0.2s;
        }

        .slide-nav:hover {
            background: rgba(0, 0, 0, 0.7);
        }

        .slide-prev {
            left: 10px;
        }

        .slide-next {
            right: 10px;
        }

        .slide-dots {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 2;
        }

        .slide-dot {
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
        }

        .slide-dot.active {
            background: var(--primary);
            transform: scale(1.2);
        }

        .detail-content {
            padding: 20px;
        }

        .detail-name {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 5px;
            line-height: 1.2;
        }

        .detail-price {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 15px;
        }

        .detail-meta {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .detail-tag {
            background: #f1f5f9;
            color: #64748b;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .detail-desc {
            color: #475569;
            line-height: 1.6;
            font-size: 0.95rem;
            margin-bottom: 20px;
        }

        /* --- [MỚI] STYLE CHO MODAL GHI CHÚ --- */
        #note-modal {
            z-index: 2100;
        }

        .form-control-note {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: 'Nunito', sans-serif;
            font-size: 1rem;
            resize: none;
            outline: none;
            transition: 0.2s;
            background: #f8fafc;
        }

        .form-control-note:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(254, 161, 22, 0.1);
        }

        /* RESPONSIVE - GIAO DIỆN ĐIỆN THOẠI */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
            }

            /* [MỚI] Đưa Sidebar (Bếp & Order) lên đầu */
            #right-sidebar {
                width: 100%;
                order: -1;
                /* Số âm giúp đẩy phần tử lên vị trí đầu tiên trong Flexbox */
                margin-bottom: 20px;
                /* Tạo khoảng cách với phần menu bên dưới */
            }

            /* Chỉnh lại panel trạng thái cho gọn trên mobile */
            .status-panel {
                height: auto;
                max-height: 300px;
                /* Giới hạn chiều cao, nếu danh sách dài quá thì cuộn trong khung này */
                position: relative;
                /* Bỏ sticky để không bị dính cứng trên mobile */
                overflow-y: auto;
            }

            /* Phần Menu chính */
            #main-content {
                width: 100%;
                order: 1;
                /* Nằm sau sidebar */
            }

            .filters-container {
                margin: 0 -10px 15px -10px;
                padding: 10px 10px 0 10px;
            }
        }
    </style>

    <main class="app-content">
        <div class="container">
            {{-- CỘT TRÁI: INFO & MENU --}}
            <div id="main-content">
                <div class="info-card">
                    <h1><i class="fa-solid fa-utensils"></i> {{ $tenBan ?? 'Bàn...' }}</h1>
                    {{-- [SỬA LẠI GIAO DIỆN DARK MODE] --}}
                    <div class="info-stats">
                        <div class="info-pill"><i class="fa-solid fa-user"></i> <span id="ten-khach">...</span></div>
                        <div class="info-pill"><i class="fa-solid fa-user-tie"></i> <span id="nguoi-lon">0</span> Lớn</div>
                        <div class="info-pill"><i class="fa-solid fa-child"></i> <span id="tre-em">0</span> Nhỏ</div>
                        <div class="info-pill"><i class="fa-solid fa-stopwatch"></i> <span id="countdown-timer">...</span>
                        </div>

                        <div class="info-pill"
                            style="border: 1px solid #3b82f6; color: #60a5fa; background: rgba(59, 130, 246, 0.15);">
                            <i class="fa-solid fa-layer-group"></i> Combo: <span id="tien-combo-badge">0đ</span>
                        </div>

                        <div class="info-pill"
                            style="border: 1px solid #22c55e; color: #4ade80; background: rgba(34, 197, 94, 0.15);">
                            <i class="fa-solid fa-utensils"></i> Thêm: <span id="tien-goi-them-badge">0đ</span>
                        </div>
                    </div>


                    <button type="button" id="btn-call-staff" class="btn btn-warning fw-bold"
                        style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                        🔔 Gọi Hỗ Trợ
                    </button>


                    <div id="combo-display" class="combo-box" style="display: none;"></div>
                </div>

                <div id="filters-wrapper" class="filters-container" style="display: none;">
                    <div id="category-filter-list" style="position: relative;"></div>
                    <div class="filter-row" id="type-filter-list"
                        style="border-top: 1px dashed #eee; padding-top: 8px; margin-top: 5px; display:none;"></div>
                </div>

                <div id="menu-container">
                    <div style="text-align: center; padding: 40px; color: #999;">
                        <i class="fa-solid fa-spinner fa-spin fa-2x"></i>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: TRẠNG THÁI GỌI MÓN --}}
            <aside id="right-sidebar">
                <div class="status-panel">
                    <div class="panel-header">
                        <span><i class="fa-solid fa-fire-burner"></i> Bếp & Order</span>
                        <div id="status-spinner" style="display:none; font-size:0.8rem;"><i
                                class="fa-solid fa-sync fa-spin"></i></div>
                    </div>
                    <div id="status-grid" class="panel-body">
                        <p style="text-align:center; color:#94a3b8; margin-top:20px;">Chưa gọi món nào</p>
                    </div>
                </div>
            </aside>
        </div>
    </main>

    {{-- WIDGET GIỎ HÀNG --}}
    <div id="mini-cart" class="mini-cart-widget" onclick="openCartModal()">
        <div class="widget-qty" id="total-count-badge">0</div>
        <div class="widget-icon"><i class="fa-solid fa-basket-shopping"></i></div>
        <div class="widget-info">
            <div class="widget-label">Tạm tính</div>
            <div class="widget-total" id="widget-total-price">0đ</div>
        </div>
    </div>

    {{-- MODAL GIỎ HÀNG --}}
    <div id="cart-modal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-title"><i class="fa-solid fa-receipt"></i> Đơn Hàng Tạm Tính</div>
                <button class="btn-close" onclick="closeCartModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div id="cart-items-list" class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" id="btn-submit-order" class="btn-confirm" onclick="submitOrder()">
                    GỌI MÓN NGAY <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL CHI TIẾT MÓN ĂN --}}
    <div id="dish-detail-modal" class="modal-overlay">
        <div class="modal-box" style="background:#fff;">
            <div class="slideshow-container" id="slideshow-box">
                <button class="slide-nav slide-prev" onclick="changeSlide(-1)"><i
                        class="fa-solid fa-chevron-left"></i></button>
                <button class="slide-nav slide-next" onclick="changeSlide(1)"><i
                        class="fa-solid fa-chevron-right"></i></button>
                <div class="slide-dots" id="slide-dots"></div>
                <button class="btn-close" onclick="closeDishDetail()"
                    style="position:absolute; top:15px; right:15px; background:rgba(0,0,0,0.3); z-index:10;"><i
                        class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body detail-content">
                <div id="detail-name" class="detail-name">Tên món ăn</div>
                <div id="detail-price" class="detail-price">0đ</div>
                <div class="detail-meta">
                    <span id="detail-cat" class="detail-tag">Danh mục</span>
                    <span id="detail-type" class="detail-tag">Loại</span>
                </div>
                <div id="detail-desc" class="detail-desc">Mô tả món ăn...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-confirm" id="btn-add-detail" onclick="">
                    <i class="fa-solid fa-plus"></i> Thêm vào giỏ
                </button>
            </div>
        </div>
    </div>

    {{-- [MỚI] MODAL NHẬP GHI CHÚ --}}
    <div id="note-modal" class="modal-overlay">
        <div class="modal-box" style="max-width: 400px;">
            <div class="modal-header">
                <div class="modal-title"><i class="fa-regular fa-pen-to-square"></i> Thêm Ghi Chú</div>
                <button class="btn-close" onclick="closeNoteModal()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <p style="margin: 0 0 10px 0; color: #64748b; font-size: 0.9rem;">
                    Ghi chú cho món: <strong id="note-item-name" style="color: var(--dark);">...</strong>
                </p>
                <textarea id="note-input" class="form-control-note" rows="4"
                    placeholder="VD: Không cay, ít đường, để riêng nước chấm..."></textarea>
            </div>
            <div class="modal-footer">
                <div style="display: flex; gap: 10px;">
                    <button type="button" class="btn-confirm" onclick="saveNote()" style="flex: 1;">Lưu</button>
                    <button type="button" class="btn-confirm" onclick="closeNoteModal()"
                        style="flex: 1; background: #e2e8f0; color: #64748b;">Hủy</button>
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container" style="position: fixed; bottom: 20px; left: 20px; z-index: 9999;"></div>

    <script>
        const STORAGE_URL = '{{ url('/') }}';
        const QR_KEY = '{{ $qrKey ?? '' }}';
        let DAT_BAN_ID = null;
        let cart = [];
        let bookingStartTime = null;
        let bookingDuration = 0;
        let lastStatusJson = '';
        let activeCategory = 'all';
        let activeType = 'all';
        let globalMenuData = {};
        let slideIndex = 0;
        let slideImages = [];
        let slideInterval;

        // [MỚI] Biến lưu vị trí món đang sửa ghi chú
        let currentNoteIdx = null;

        const formatMoney = (a) => new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(a);

        function showToast(msg, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const iconClass = type === 'success' ? 'circle-check' : 'circle-exclamation';
            const iconColor = type === 'success' ? '#20d489' : '#ff4d4f';
            toast.innerHTML =
                `<div style="display:flex; align-items:center; gap:12px; flex:1;"><i class="fa-solid fa-${iconClass}" style="color:${iconColor}; font-size:1.2rem;"></i><span style="font-weight:600; line-height:1.4;">${msg}</span></div><div onclick="this.parentElement.remove()" style="cursor:pointer; padding:0 5px; opacity:0.6;"><i class="fa-solid fa-xmark"></i></div>`;
            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateX(0)';
            });
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(50px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        async function loadSessionInfo() {
            try {
                const res = await fetch(`/oderqr/session/table/${QR_KEY}`);
                if (!res.ok) throw new Error('Lỗi tải dữ liệu');
                const data = await res.json();

                DAT_BAN_ID = data.dat_ban_info.id;
                document.getElementById('ten-khach').innerText = data.dat_ban_info.ten_khach || 'Khách';
                document.getElementById('nguoi-lon').innerText = data.dat_ban_info.nguoi_lon || 0;
                document.getElementById('tre-em').innerText = data.dat_ban_info.tre_em || 0;

                // --- [THÊM] Gán dữ liệu tạm tính vào HTML ---
                document.getElementById('tien-combo-badge').innerText = formatMoney(data.tien_combo || 0);

                // Hiển thị tiền Gọi thêm riêng
                document.getElementById('tien-goi-them-badge').innerText = formatMoney(data.tien_goi_them || 0);
                bookingStartTime = data.dat_ban_info.gio_den;
                bookingDuration = parseInt(data.dat_ban_info.thoi_luong_phut) || 0;
                startCountdown();

                const combos = data.selected_combos;
                const displayBox = document.getElementById('combo-display');
                if (combos && combos.length > 0) {
                    displayBox.style.display = 'block';
                    let html = combos.map(c =>
                        `<div style="display:flex; justify-content:space-between; margin-top:4px; font-size:0.9rem;"><span><i class="fa-solid fa-check" style="color:var(--primary)"></i> ${c.ten}</span><span style="font-weight:700;">x${c.sl}</span></div>`
                    ).join('');
                    displayBox.innerHTML =
                        `<div style="font-weight:700; color:var(--primary); margin-bottom:6px; border-bottom:1px dashed rgba(255,255,255,0.2); padding-bottom:4px;"><i class="fa-solid fa-crown"></i> GÓI ĐÃ CHỌN</div>${html}`;
                }

                renderMenu(data.menu);
                loadOrderStatus();
            } catch (e) {
                console.error(e);
            }
        }

        function renderMenu(menuData) {
            const c = document.getElementById('menu-container');
            const wrapper = document.getElementById('filters-wrapper');
            const catContainer = document.getElementById('category-filter-list');
            const typeList = document.getElementById('type-filter-list');

            c.innerHTML = '';
            wrapper.style.display = 'block';
            catContainer.innerHTML = '';

            const scrollWrapper = document.createElement('div');
            scrollWrapper.className = 'filter-wrapper-relative';
            const btnPrev =
                `<div class="nav-arrow prev" onclick="scrollFilter('left')"><i class="fa-solid fa-chevron-left"></i></div>`;
            const btnNext =
                `<div class="nav-arrow next" onclick="scrollFilter('right')"><i class="fa-solid fa-chevron-right"></i></div>`;
            const listInner = document.createElement('div');
            listInner.className = 'filter-row';
            listInner.id = 'cat-scroll-target';
            listInner.style.cssText =
                'flex: 1; display: flex; overflow-x: auto; white-space: nowrap; gap: 8px; padding: 5px 40px; align-items: center; scroll-behavior: smooth;';

            listInner.innerHTML = `<div class="cat-pill active" onclick="setCategory('all', this)">Tất cả</div>`;

            const allTypes = new Set();

            menuData.forEach(cat => {
                if (!cat.mon_an || cat.mon_an.length === 0) return;

                cat.mon_an.forEach(dish => {
                    globalMenuData[dish.id] = {
                        ...dish,
                        cat_name: cat.ten_danh_muc
                    };
                    if (dish.loai_mon) allTypes.add(dish.loai_mon);
                });

                listInner.innerHTML +=
                    `<div class="cat-pill" onclick="setCategory(${cat.id}, this)">${cat.ten_danh_muc}</div>`;

                let sectionHtml = `
                <div class="menu-section" data-cat-id="${cat.id}">
                    <div class="danh-muc-sticky"><div class="danh-muc-name">${cat.ten_danh_muc}</div></div>
                    <div class="menu-grid">`;

                cat.mon_an.forEach(i => {
                    const isCombo = i.is_in_combo;
                    const badgeCls = isCombo ? 'badge-combo' : 'badge-le';
                    const badgeTxt = isCombo ? 'Trong gói' : 'Gọi thêm';
                    const typeParam = isCombo ? 'combo' : 'goi_them';
                    const img = i.hinh_anh ? `${STORAGE_URL}/${i.hinh_anh}` :
                        'https://placehold.co/100?text=IMG';

                    sectionHtml += `
                    <div class="dish-card" data-type="${i.loai_mon || 'other'}" onclick="openDishDetail(${i.id})">
                        <div class="dish-thumb-wrapper">
                            <img src="${img}" class="dish-thumb" id="img-${i.id}" loading="lazy">
                            <span class="dish-badge ${badgeCls}">${badgeTxt}</span>
                        </div>
                        <div class="dish-body">
                            <div class="dish-name">${i.ten_mon}</div>
                            <div class="dish-desc">${i.mo_ta || ''}</div>
                            <div class="dish-footer">
                                <div class="dish-price">${isCombo ? '0đ' : formatMoney(i.gia)}</div>
                                <div class="btn-add-cart" id="btn-add-${i.id}" 
                                     onclick="event.stopPropagation(); addToCart(${i.id}, '${i.ten_mon}', '${typeParam}', ${isCombo ? 0 : i.gia}, event)">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                            </div>
                        </div>
                    </div>`;
                });
                sectionHtml += `</div></div>`;
                c.innerHTML += sectionHtml;
            });

            scrollWrapper.innerHTML = btnPrev;
            scrollWrapper.appendChild(listInner);
            scrollWrapper.innerHTML += btnNext;
            catContainer.appendChild(scrollWrapper);

            c.innerHTML += `<div style="height:20px"></div>`;

            if (allTypes.size > 0) {
                typeList.style.display = 'flex';
                typeList.innerHTML =
                    `<span class="filter-label">Lọc:</span><div class="type-pill active" onclick="setType('all', this)">Tất cả</div>`;
                allTypes.forEach(type => {
                    typeList.innerHTML += `<div class="type-pill" onclick="setType('${type}', this)">${type}</div>`;
                });
            }
        }

        function openDishDetail(id) {
            const dish = globalMenuData[id];
            if (!dish) return;
            const modal = document.getElementById('dish-detail-modal');

            slideImages = [];
            if (dish.hinh_anh) slideImages.push(`${STORAGE_URL}/${dish.hinh_anh}`);
            else slideImages.push('https://placehold.co/300x200?text=No+Image');

            if (dish.thu_vien_anh && dish.thu_vien_anh.length > 0) {
                dish.thu_vien_anh.forEach(img => {
                    slideImages.push(`${STORAGE_URL}/${img.duong_dan_anh}`);
                });
            }

            const slideContainer = document.getElementById('slideshow-box');
            const oldImgs = slideContainer.querySelectorAll('.slide-img');
            oldImgs.forEach(el => el.remove());

            const dotsContainer = document.getElementById('slide-dots');
            dotsContainer.innerHTML = '';

            slideImages.forEach((src, idx) => {
                const img = document.createElement('img');
                img.src = src;
                img.className = idx === 0 ? 'slide-img active' : 'slide-img';
                slideContainer.insertBefore(img, slideContainer.firstChild);

                const dot = document.createElement('div');
                dot.className = idx === 0 ? 'slide-dot active' : 'slide-dot';
                dot.onclick = () => showSlide(idx);
                dotsContainer.appendChild(dot);
            });

            slideIndex = 0;
            if (slideImages.length > 1) {
                startSlideInterval();
                document.querySelector('.slide-prev').style.display = 'flex';
                document.querySelector('.slide-next').style.display = 'flex';
            } else {
                document.querySelector('.slide-prev').style.display = 'none';
                document.querySelector('.slide-next').style.display = 'none';
            }

            document.getElementById('detail-name').innerText = dish.ten_mon;
            document.getElementById('detail-price').innerText = dish.is_in_combo ? '0đ (Trong gói)' : formatMoney(dish.gia);
            document.getElementById('detail-desc').innerText = dish.mo_ta || 'Chưa có mô tả cho món ăn này.';
            document.getElementById('detail-cat').innerText = dish.cat_name;
            document.getElementById('detail-type').innerText = dish.loai_mon || 'Khác';

            const btn = document.getElementById('btn-add-detail');
            const typeParam = dish.is_in_combo ? 'combo' : 'goi_them';
            const price = dish.is_in_combo ? 0 : dish.gia;
            btn.onclick = function(e) {
                addToCart(id, dish.ten_mon, typeParam, price, e);
                closeDishDetail();
            };

            modal.classList.add('active');
        }

        function closeDishDetail() {
            document.getElementById('dish-detail-modal').classList.remove('active');
            clearInterval(slideInterval);
        }

        function changeSlide(n) {
            showSlide(slideIndex += n);
            startSlideInterval();
        }

        function showSlide(n) {
            const slides = document.querySelectorAll('.slide-img');
            const dots = document.querySelectorAll('.slide-dot');
            if (n >= slides.length) slideIndex = 0;
            if (n < 0) slideIndex = slides.length - 1;
            slides.forEach(s => s.classList.remove('active'));
            dots.forEach(d => d.classList.remove('active'));
            slides[slideIndex].classList.add('active');
            dots[slideIndex].classList.add('active');
        }

        function startSlideInterval() {
            clearInterval(slideInterval);
            if (slideImages.length > 1) {
                slideInterval = setInterval(() => {
                    slideIndex++;
                    showSlide(slideIndex);
                }, 3000);
            }
        }

        // --- FILTER & SCROLL LOGIC ---
        function scrollFilter(direction) {
            const container = document.getElementById('cat-scroll-target');
            if (container) {
                const scrollAmount = 200;
                if (direction === 'left') container.scrollLeft -= scrollAmount;
                else container.scrollLeft += scrollAmount;
            }
        }

        function setCategory(id, btn) {
            document.querySelectorAll('.cat-pill').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');
            activeCategory = id;
            applyFilters();
        }

        function setType(type, btn) {
            document.querySelectorAll('.type-pill').forEach(el => el.classList.remove('active'));
            btn.classList.add('active');
            activeType = type;
            applyFilters();
        }

        function applyFilters() {
            const sections = document.querySelectorAll('.menu-section');
            sections.forEach(section => {
                const sectionCatId = section.getAttribute('data-cat-id');
                const dishes = section.querySelectorAll('.dish-card');
                let visibleCount = 0;

                dishes.forEach(dish => {
                    const dishType = dish.getAttribute('data-type');
                    const matchType = (activeType === 'all' || dishType === activeType);
                    if (matchType) {
                        dish.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        dish.classList.add('hidden');
                    }
                });

                const matchCat = (activeCategory === 'all' || sectionCatId == activeCategory);
                if (matchCat && visibleCount > 0) {
                    section.classList.remove('hidden');
                    setTimeout(() => section.classList.remove('hiding'), 50);
                } else {
                    section.classList.add('hiding');
                    setTimeout(() => section.classList.add('hidden'), 300);
                }
            });
        }

        // --- CART ACTIONS ---
        function addToCart(id, name, type, price, event) {
            const ex = cart.find(i => i.mon_an_id === id && !i.ghi_chu);
            if (ex) {
                ex.so_luong++;
            } else {
                cart.push({
                    mon_an_id: id,
                    ten_mon: name,
                    so_luong: 1,
                    ghi_chu: null,
                    loai_mon: type,
                    don_gia: price
                });
            }
            updateCartUI();
            if (event) {
                const sourceBtn = event.target.closest('.btn-add-cart') || event.target.closest('.btn-confirm');
                const img = globalMenuData[id] ? (globalMenuData[id].hinh_anh ?
                    `${STORAGE_URL}/${globalMenuData[id].hinh_anh}` : '') : '';
                if (sourceBtn) flyToCart(sourceBtn, img);
            }
            showToast(`Đã thêm ${name}`);
        }

        function updateCartUI() {
            const list = document.getElementById('cart-items-list');
            const btn = document.getElementById('btn-submit-order');
            const cnt = document.getElementById('total-count-badge');
            let totalCount = 0;
            let totalPrice = 0;
            cart.forEach(i => {
                totalCount += i.so_luong;
                totalPrice += i.so_luong * i.don_gia;
            });
            cnt.innerText = totalCount;
            document.getElementById('widget-total-price').innerText = formatMoney(totalPrice);
            const widget = document.getElementById('mini-cart');
            if (totalCount > 0) widget.classList.add('show');
            else {
                widget.classList.remove('show');
                closeCartModal();
            }
            if (cart.length === 0) {
                list.innerHTML =
                    `<div style="text-align:center; padding:20px; color:#aaa;"><i class="fa-solid fa-basket-shopping fa-2x"></i><p>Chưa chọn món nào</p></div>`;
                btn.disabled = true;
                return;
            }
            btn.disabled = false;
            list.innerHTML = '';
            cart.forEach((i, idx) => {
                list.innerHTML += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h4>${i.ten_mon}</h4>
                        <div style="color:var(--primary); font-weight:700;">${formatMoney(i.don_gia * i.so_luong)}</div>
                        ${i.ghi_chu ? `<small style="color:#888; font-style:italic;">${i.ghi_chu}</small>` : ''}
                        <div style="font-size:0.8rem; color:var(--primary); cursor:pointer;" onclick="editNote(${idx})"><i class="fa-regular fa-pen-to-square"></i> Ghi chú</div>
                    </div>
                    <div class="cart-item-actions">
                        <div class="qty-btn" onclick="updateItem(${idx}, -1)">-</div>
                        <div class="qty-num">${i.so_luong}</div>
                        <div class="qty-btn" onclick="updateItem(${idx}, 1)">+</div>
                        <i class="fa-solid fa-trash-can btn-trash" onclick="updateItem(${idx}, -999)"></i>
                    </div>
                </div>`;
            });
        }

        function updateItem(idx, change) {
            if (change === -999) cart.splice(idx, 1);
            else {
                cart[idx].so_luong += change;
                if (cart[idx].so_luong <= 0) cart.splice(idx, 1);
            }
            updateCartUI();
        }

        // --- [MỚI] LOGIC GHI CHÚ (MODAL) ---
        function editNote(idx) {
            currentNoteIdx = idx;
            const item = cart[idx];
            document.getElementById('note-item-name').innerText = item.ten_mon;
            const input = document.getElementById('note-input');
            input.value = item.ghi_chu || '';
            document.getElementById('note-modal').classList.add('active');
            setTimeout(() => input.focus(), 100);
        }

        function saveNote() {
            if (currentNoteIdx !== null) {
                const val = document.getElementById('note-input').value.trim();
                cart[currentNoteIdx].ghi_chu = val;
                updateCartUI();
            }
            closeNoteModal();
        }

        function closeNoteModal() {
            document.getElementById('note-modal').classList.remove('active');
            currentNoteIdx = null;
        }

        async function submitOrder() {
            if (cart.length === 0) return;
            const btn = document.getElementById('btn-submit-order');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ĐANG GỬI...';
            try {
                const res = await fetch('/oderqr/order/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        dat_ban_id: DAT_BAN_ID,
                        items: cart
                    })
                });
                const data = await res.json();
                if (!res.ok) {
                    if (data.errors) throw new Error(Object.values(data.errors)[0][0]);
                    throw new Error(data.message || 'Lỗi gửi đơn');
                }
                showToast('Gửi thành công!');
                cart = [];
                updateCartUI();
                lastStatusJson = '';
                loadOrderStatus();
                closeCartModal();
                loadSessionInfo();
            } catch (e) {
                showToast(e.message, 'error');
            } finally {
                btn.disabled = cart.length === 0;
                btn.innerHTML = 'GỌI MÓN NGAY <i class="fa-solid fa-arrow-right"></i>';
            }
        };

        function flyToCart(sourceBtn, imgUrl) {
            const widget = document.getElementById('mini-cart');
            const endRect = (widget.offsetParent === null) ? {
                top: window.innerHeight - 50,
                left: window.innerWidth - 50
            } : widget.getBoundingClientRect();
            const flyer = document.createElement('img');
            flyer.src = imgUrl || 'https://via.placeholder.com/50';
            flyer.classList.add('flying-img');
            const start = sourceBtn.getBoundingClientRect();
            flyer.style.top = start.top + 'px';
            flyer.style.left = start.left + 'px';
            document.body.appendChild(flyer);
            setTimeout(() => {
                flyer.style.top = (endRect.top) + 'px';
                flyer.style.left = (endRect.left) + 'px';
                flyer.style.width = '20px';
                flyer.style.height = '20px';
                flyer.style.opacity = '0.5';
            }, 10);
            setTimeout(() => {
                flyer.remove();
                widget.style.transform = 'scale(1.1)';
                setTimeout(() => widget.style.transform = 'scale(1)', 150);
            }, 800);
        }

        async function loadOrderStatus() {
            if (!DAT_BAN_ID) return;
            const spinner = document.getElementById('status-spinner');
            try {
                const res = await fetch(`/oderqr/order/status/${DAT_BAN_ID}`);
                if (res.status !== 200) return;
                const data = await res.json();
                const c = document.getElementById('status-grid');
                const currentJson = JSON.stringify(data.items);
                if (currentJson === lastStatusJson) {
                    spinner.style.display = 'none';
                    return;
                }
                lastStatusJson = currentJson;
                spinner.style.display = 'block';
                if (!data.items || data.items.length === 0) {
                    c.innerHTML = `<p style="color:#aaa; text-align:center; padding:20px;">Chưa gọi món nào</p>`;
                    return;
                }
                let htmlContent = '';
                data.items.forEach(i => {
                    let s = {
                        cls: 'pending',
                        txt: 'Chờ bếp',
                        ic: 'clock'
                    };
                    let canCancel = false;
                    if (i.trang_thai === 'dang_che_bien') {
                        s = {
                            cls: 'cooking',
                            txt: 'Đang nấu',
                            ic: 'fire-burner'
                        };
                    } else if (i.trang_thai === 'da_len_mon') {
                        s = {
                            cls: 'completed',
                            txt: 'Đã lên',
                            ic: 'check'
                        };
                    } else if (i.trang_thai === 'huy_mon') {
                        s = {
                            cls: 'cancelled',
                            txt: 'Đã hủy',
                            ic: 'xmark'
                        };
                    } else {
                        canCancel = true;
                    }
                    const prepTimeMin = i.mon_an.thoi_gian_che_bien || 15;
                    const createdTime = new Date(i.created_at).getTime();
                    const targetTime = createdTime + (prepTimeMin * 60000);
                    const btnCancelHtml = canCancel ?
                        `<div class="btn-cancel-item" onclick="cancelOrderItem(${i.id})"><i class="fa-regular fa-trash-can"></i> Hủy</div>` :
                        '';
                    const showTimer = (i.trang_thai !== 'huy_mon' && i.trang_thai !== 'da_len_mon');
                    const timerHtml = showTimer ?
                        `<div class="timer-badge" data-target="${targetTime}" id="timer-${i.id}"><i class="fa-solid fa-hourglass-half"></i> <span>--:--</span></div>` :
                        '';

                    htmlContent += `
                    <div class="status-card ${s.cls}">
                        <div class="stt-header"><div class="stt-name">${i.mon_an.ten_mon} <span class="stt-qty">x${i.so_luong}</span></div><div class="badge-status"><i class="fa-solid fa-${s.ic}"></i> ${s.txt}</div></div>
                        ${i.ghi_chu ? `<div class="stt-note"><i class="fa-regular fa-comment-dots"></i> ${i.ghi_chu}</div>` : ''}
                        ${ (showTimer || canCancel) ? `<div class="stt-footer">${timerHtml}${btnCancelHtml}</div>` : '' }
                    </div>`;
                });
                c.innerHTML = htmlContent;
                updateTimers();
                setTimeout(() => spinner.style.display = 'none', 500);
            } catch (e) {
                console.error(e);
            }
        }

        function updateTimers() {
            const timers = document.querySelectorAll('.timer-badge');
            const now = new Date().getTime();
            timers.forEach(el => {
                const target = parseInt(el.getAttribute('data-target'));
                const diff = target - now;
                if (diff > 0) {
                    const m = Math.floor(diff / 60000);
                    const s = Math.floor((diff % 60000) / 1000);
                    el.querySelector('span').innerText = `${m}:${s < 10 ? '0'+s : s}`;
                    el.classList.remove('late');
                } else {
                    const lateDiff = Math.abs(diff);
                    const m = Math.floor(lateDiff / 60000);
                    const s = Math.floor((lateDiff % 60000) / 1000);
                    el.querySelector('span').innerText = `-${m}:${s < 10 ? '0'+s : s}`;
                    el.classList.add('late');
                }
            });
        }

        async function cancelOrderItem(id) {
            if (!confirm('Bạn có chắc muốn hủy món này không?')) return;
            try {
                const res = await fetch('/oderqr/order/cancel-item', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id: id
                    })
                });
                const data = await res.json();
                if (res.ok) {
                    showToast('Đã hủy món thành công');
                    lastStatusJson = '';
                    loadOrderStatus();
                } else {
                    showToast(data.message, 'error');
                }
            } catch (e) {
                showToast('Lỗi kết nối', 'error');
            }
        }

        function startCountdown() {
            if (!bookingStartTime || bookingDuration <= 0) return document.getElementById('countdown-timer').innerText =
                "∞";
            const end = new Date(new Date(bookingStartTime.replace(' ', 'T')).getTime() + bookingDuration * 60000);
            setInterval(() => {
                const d = end - new Date();
                if (d < 0) return document.getElementById('countdown-timer').innerText = "Hết giờ";
                document.getElementById('countdown-timer').innerText =
                    `${Math.floor(d/3600000)}h ${Math.floor((d%3600000)/60000)}p`;
            }, 1000);
        }

        function openCartModal() {
            updateCartUI();
            document.getElementById('cart-modal').classList.add('active');
        }

        function closeCartModal() {
            document.getElementById('cart-modal').classList.remove('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadSessionInfo();
            setInterval(loadOrderStatus, 5000);
            setInterval(updateTimers, 1000);
        });
        $(document).ready(function() {
            $('#btn-call-staff').click(function() {
                var btn = $(this);

                // Hỏi lại cho chắc để tránh bấm nhầm
                if (!confirm("Bạn cần nhân viên hỗ trợ tại bàn?")) return;

                btn.prop('disabled', true).text('Đang gọi...');

                $.ajax({
                    url: "{{ route('oderqr.call_staff') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        ban_id: "{{ $banId }}" // Biến $banId đã có sẵn trong controller showGoiMonPage của ông
                    },
                    success: function(response) {
                        alert(response.message);
                        btn.text('🔔 Đã gọi (Chờ xíu...)');
                        // Sau 30s cho bấm lại
                        setTimeout(function() {
                            btn.prop('disabled', false).text('🔔 Gọi Hỗ Trợ');
                        }, 30000);
                    },
                    error: function() {
                        alert('Lỗi kết nối! Vui lòng gọi trực tiếp.');
                        btn.prop('disabled', false).text('🔔 Gọi Hỗ Trợ');
                    }
                });
            });
        });
    </script>
@endsection
