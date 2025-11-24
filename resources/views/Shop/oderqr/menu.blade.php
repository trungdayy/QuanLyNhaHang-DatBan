@extends('layouts.Shop.layout-oderqr')

@section('title', 'Thực Đơn - ' . ($tenBan ?? 'Ocean Buffet'))

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap"
        rel="stylesheet">

    <style>
        /* --- CẤU HÌNH MÀU SẮC & BIẾN --- */
        :root {
            --primary: #fea116;
            /* Cam vàng */
            --primary-dark: #d98a12;
            /* Cam đậm */
            --dark: #0f172b;
            /* Xanh đen */
            --light: #f1f8ff;
            --white: #ffffff;

            --success: #20d489;
            /* Xanh mint */
            --danger: #ff4d4f;
            /* Đỏ */

            --text-main: #1e293b;
            --text-sub: #64748b;

            --shadow-card: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
            --radius: 8px;

            --anim-fast: 0.2s ease;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
            color: var(--text-main);
            margin: 0;
        }

        .app-content {
            padding: 30px 0 100px 0;
            min-height: 100vh;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            gap: 30px;
            position: relative;
        }

        /* Scrollbar đẹp */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* =========================================
               CỘT TRÁI
               ========================================= */
        #main-content {
            flex: 1;
            min-width: 0;
        }

        /* --- INFO CARD --- */
        .info-card {
            background: var(--dark);
            color: var(--white);
            border-radius: var(--radius);
            padding: 25px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-card);
            margin-bottom: 30px;
        }

        .info-card h1 {
            font-family: 'Heebo', sans-serif;
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--primary);
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .info-pill {
            background: rgba(255, 255, 255, 0.1);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-pill i {
            color: var(--primary);
        }

        #countdown-timer {
            color: #ff8787;
        }

        .combo-box {
            margin-top: 20px;
            padding: 12px 15px;
            background: rgba(254, 161, 22, 0.1);
            border: 1px dashed var(--primary);
            border-radius: 8px;
            color: #fff;
        }

        /* --- SECTION TITLE (ĐÃ FIX LỖI GẠCH NGANG) --- */
        .section-header {
            margin: 35px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            /* Dùng border dưới thay vì pseudo-element */
        }

        .section-title {
            font-family: 'Heebo', sans-serif;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary);
        }

        /* Xóa bỏ hoàn toàn các gạch trang trí cũ gây lỗi */
        .section-title::before,
        .section-title::after {
            display: none !important;
            content: none !important;
        }

        /* --- TRẠNG THÁI GỌI MÓN (FIX LỖI DÀI NGOẰNG) --- */
        /* Bọc lưới trong một khung cuộn riêng */
        #status-scroll-wrapper {
            max-height: 280px;
            /* Giới hạn chiều cao */
            overflow-y: auto;
            /* Hiện thanh cuộn */
            padding: 5px;
            border: 1px solid #eee;
            border-radius: var(--radius);
            background: #fff;
        }

        #status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 10px;
        }

        .status-card {
            background: var(--white);
            padding: 10px 15px;
            border-radius: 6px;
            border-left: 4px solid #ccc;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-card.pending {
            border-color: var(--primary);
            background: #fffdf5;
        }

        .status-card.completed {
            border-color: var(--success);
            background: #f0fdf4;
        }

        .status-card.cancelled {
            border-color: var(--danger);
            background: #fff5f5;
            opacity: 0.7;
        }

        .status-card.waiting {
            border-color: #64748b;
            background: #f8fafc;
        }

        .stt-text {
            font-weight: 800;
            font-size: 0.7rem;
            text-transform: uppercase;
            padding: 4px 8px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .pending .stt-text {
            color: #b45309;
            background: rgba(254, 161, 22, 0.15);
        }

        .completed .stt-text {
            color: #15803d;
            background: rgba(32, 212, 137, 0.15);
        }

        .waiting .stt-text {
            color: #475569;
            background: #e2e8f0;
        }

        .cancelled .stt-text {
            color: #b91c1c;
            background: rgba(255, 77, 79, 0.15);
        }

        /* --- MENU LIST --- */
        .danh-muc-wrapper {
            margin-bottom: 30px;
        }

        .danh-muc-sticky {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(248, 249, 250, 0.95);
            backdrop-filter: blur(5px);
            padding: 10px 0;
            margin-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .danh-muc-name {
            font-family: 'Heebo', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--dark);
            border-left: 4px solid var(--primary);
            padding-left: 10px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
        }

        .dish-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 12px;
            box-shadow: var(--shadow-card);
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 15px;
            position: relative;
            transition: var(--anim-fast);
        }

        .dish-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(254, 161, 22, 0.3);
        }

        /* Ảnh nhỏ gọn 70px */
        .dish-thumb-wrapper {
            width: 70px;
            height: 70px;
            flex-shrink: 0;
            border-radius: 8px;
            overflow: hidden;
        }

        .dish-thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .dish-card:hover .dish-thumb {
            transform: scale(1.1);
        }

        /* Badge nhỏ gọn */
        .dish-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            padding: 2px 6px;
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
            border: 1px solid #eee;
        }

        .dish-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 0;
        }

        .dish-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .dish-desc {
            font-size: 0.8rem;
            color: var(--text-sub);
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .dish-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dish-price {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--primary);
            font-family: 'Heebo', sans-serif;
        }

        .btn-add-cart {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--light);
            color: var(--primary);
            border: 1px solid var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.2s;
            font-size: 0.9rem;
        }

        .btn-add-cart:hover {
            background: var(--primary);
            color: var(--white);
            transform: rotate(90deg);
        }

        /* =========================================
               CỘT PHẢI: GIỎ HÀNG
               ========================================= */
        #cart-sidebar {
            width: 360px;
            flex-shrink: 0;
        }

        .cart-panel {
            position: sticky;
            top: 20px;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 40px);
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .cart-header {
            background: var(--dark);
            color: var(--white);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.1) 1px, transparent 0);
            background-size: 20px 20px;
        }

        .cart-title {
            font-family: 'Heebo', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        #cart-count {
            background: var(--primary);
            color: var(--dark);
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.8rem;
        }

        .cart-body {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background: #fff;
        }

        .cart-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px dashed #e2e8f0;
            animation: slideInLeft 0.3s ease;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .c-row-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .c-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: var(--dark);
            line-height: 1.3;
        }

        .c-qty {
            background: var(--primary);
            color: #fff;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 800;
            margin-left: 5px;
        }

        .btn-remove {
            color: #cbd5e1;
            cursor: pointer;
            padding: 2px;
            transition: 0.2s;
        }

        .btn-remove:hover {
            color: var(--danger);
        }

        .c-note {
            font-size: 0.75rem;
            color: var(--text-sub);
            background: #f8fafc;
            padding: 4px 8px;
            border-radius: 4px;
            display: block;
            border-left: 3px solid #cbd5e1;
        }

        .c-action {
            font-size: 0.75rem;
            color: var(--primary);
            cursor: pointer;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }

        .cart-footer {
            padding: 15px;
            background: #fff;
            border-top: 1px solid #f1f5f9;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: var(--dark);
            border: none;
            border-radius: 8px;
            font-family: 'Heebo', sans-serif;
            font-weight: 800;
            font-size: 1rem;
            text-transform: uppercase;
            cursor: pointer;
            transition: var(--anim-fast);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-submit:hover:not(:disabled) {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(254, 161, 22, 0.3);
        }

        .btn-submit:disabled {
            background: #e2e8f0;
            color: #94a3b8;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .empty-cart {
            text-align: center;
            padding: 40px 0;
            color: #cbd5e1;
        }

        .empty-cart i {
            font-size: 3rem;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        /* Toast & Spinner */
        #toast-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            pointer-events: none;
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


        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .spinner {
            width: 24px;
            height: 24px;
            border: 3px solid #e2e8f0;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s infinite linear;
            margin: 20px auto;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
            }

            #cart-sidebar {
                width: 100%;
            }

            .cart-panel {
                height: auto;
                max-height: 500px;
            }
        }

        @media (max-width: 768px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }

            /* 1 cột trên mobile */
            #cart-sidebar {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                z-index: 1000;
                pointer-events: none;
            }

            .cart-panel {
                display: none;
            }

            .cart-footer {
                pointer-events: auto;
                padding: 15px;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(5px);
                border-top: 1px solid #eee;
                box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.1);
            }

            .mobile-sticky-btn {
                display: block !important;
            }

            #cart-sidebar {
                position: static;
            }

            .cart-panel {
                display: block;
                height: auto;
                border: none;
                box-shadow: none;
                background: transparent;
            }

            .cart-sticky {
                position: static;
            }

            .cart-header,
            .cart-body {
                display: none;
            }

            .cart-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 999;
            }
        }
    </style>

    <main class="app-content">
        <div class="container">

            <div id="main-content">
                <div class="info-card">
                    <h1><i class="fa-solid fa-utensils"></i> {{ $tenBan ?? 'Bàn...' }}</h1>
                    <div class="info-stats">
                        <div class="info-pill"><i class="fa-solid fa-user"></i> <span id="ten-khach">...</span></div>
                        <div class="info-pill"><i class="fa-solid fa-users"></i> <span id="so-nguoi">0</span> khách</div>
                        <div class="info-pill"><i class="fa-solid fa-stopwatch"></i> <span id="countdown-timer">...</span>
                        </div>
                    </div>

                    <div id="combo-display" class="combo-box" style="display: none;">
                        <strong style="color: var(--primary); font-size: 1.1rem;"><i class="fa-solid fa-crown"></i> <span
                                id="combo-name">...</span></strong>
                        <div id="combo-details" style="font-size: 0.9rem; margin-left: auto;"></div>
                    </div>
                </div>

                <div class="section-header">
                    <div class="section-title"><i class="fa-solid fa-fire-burner"></i> TRẠNG THÁI GỌI MÓN</div>
                </div>
                <div id="status-scroll-wrapper">
                    <div id="status-grid">
                        <div class="spinner"></div>
                    </div>
                </div>

                <div class="section-header">
                    <div class="section-title"><i class="fa-solid fa-book-open"></i> THỰC ĐƠN HÔM NAY</div>
                </div>
                <div id="menu-container">
                    <div class="spinner"></div>
                </div>
            </div>

            <aside id="cart-sidebar">
                <div class="cart-panel">
                    <div class="cart-header">
                        <div class="cart-title"><i class="fa-solid fa-receipt"></i> Giỏ hàng</div>
                        <div id="cart-count" style="background: var(--white); color: var(--dark);">0</div>
                    </div>

                    <div id="cart-items" class="cart-body">
                        <div class="empty-cart">
                            <i class="fa-solid fa-basket-shopping"></i>
                            <p>Chưa có món</p>
                        </div>
                    </div>

                    <div class="cart-footer mobile-sticky-btn">
                        <button id="submit-order-btn" disabled class="btn-submit">
                            <span>GỌI MÓN NGAY</span> <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </aside>

        </div>
    </main>

    <div id="toast-container"></div>

    <script>
        const STORAGE_URL = '{{ url('/') }}';
        const QR_KEY = '{{ $qrKey ?? '' }}';
        let DAT_BAN_ID = null;
        let cart = [];
        let bookingStartTime = null;
        let bookingDuration = 0;

        const formatMoney = (a) => new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(a);

        // --- HÀM HIỂN THỊ THÔNG BÁO (SỬA LẠI) ---
 function showToast(msg, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    const iconClass = type === 'success' ? 'circle-check' : 'circle-exclamation';
    const iconColor = type === 'success' ? '#20d489' : '#ff4d4f';

    toast.innerHTML = `
        <div style="display:flex; align-items:center; gap:12px; flex:1;">
            <i class="fa-solid fa-${iconClass}" style="color:${iconColor}; font-size:1.2rem;"></i>
            <span style="font-weight:600; line-height:1.4;">${msg}</span>
        </div>
        <div onclick="this.parentElement.remove()" style="cursor:pointer; padding:0 5px; opacity:0.6;">
            <i class="fa-solid fa-xmark"></i>
        </div>
    `;

    container.appendChild(toast);

    // Show animation
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    });

    // Tự động tắt sau 10 giây
    let autoRemove = setTimeout(() => hideToast(toast), 3000);

    toast.onmouseenter = () => clearTimeout(autoRemove);
    toast.onmouseleave = () => { autoRemove = setTimeout(() => hideToast(toast), 5000); };

    function hideToast(t) {
        t.style.opacity = '0';
        t.style.transform = 'translateX(50px)';
        setTimeout(() => t.remove(), 300);
    }
}


        async function loadSessionInfo() {
            try {
                const res = await fetch(`/oderqr/session/table/${QR_KEY}`);
                if (!res.ok) throw new Error('Lỗi tải bàn');
                const data = await res.json();

                DAT_BAN_ID = data.dat_ban_info.id;
                document.getElementById('ten-khach').innerText = data.dat_ban_info.ten_khach || 'Khách';
                document.getElementById('so-nguoi').innerText = data.dat_ban_info.so_khach;
                bookingStartTime = data.dat_ban_info.gio_den;
                bookingDuration = parseInt(data.dat_ban_info.thoi_luong_phut) || 0;
                startCountdown();

                const combo = data.dat_ban_info.combo_buffet;
                if (combo) {
                    document.getElementById('combo-display').style.display = 'flex';
                    document.getElementById('combo-name').innerText = combo.ten_combo;
                    document.getElementById('combo-details').innerText = `${formatMoney(combo.gia_co_ban)}`;
                }

                renderMenu(data.menu);
                renderCart();
                loadOrderStatus();
            } catch (e) {
                console.error(e);
            }
        }

        function renderMenu(menuData) {
            const c = document.getElementById('menu-container');
            c.innerHTML = '';
            menuData.forEach(cat => {
                if (!cat.mon_an || cat.mon_an.length === 0) return;
                let h =
                    `<div class="danh-muc-wrapper"><div class="danh-muc-sticky"><div class="danh-muc-name">${cat.ten_danh_muc}</div></div><div class="menu-grid">`;
                cat.mon_an.forEach(i => {
                    const isCombo = i.is_in_combo;
                    const badgeCls = isCombo ? 'badge-combo' : 'badge-le';
                    const badgeTxt = isCombo ? 'Trong gói' : 'Gọi thêm';
                    const img = i.hinh_anh ? `${STORAGE_URL}/${i.hinh_anh}` :
                        'https://placehold.co/100?text=IMG';
                    h += `
                    <div class="dish-card">
                        <div class="dish-thumb-wrapper">
                            <img src="${img}" class="dish-thumb" loading="lazy">
                        </div>
                        <span class="dish-badge ${badgeCls}">${badgeTxt}</span>
                        <div class="dish-body">
                            <div class="dish-name">${i.ten_mon}</div>
                            <div class="dish-desc">${i.mo_ta || 'Món ngon.'}</div>
                            <div class="dish-footer">
                                <div class="dish-price">${formatMoney(i.gia)}</div>
                                <div class="btn-add-cart" onclick="addToCart(${i.id}, '${i.ten_mon}', '${isCombo?'combo':'goi_them'}')"><i class="fa-solid fa-plus"></i></div>
                            </div>
                        </div>
                    </div>`;
                });
                h += `</div></div>`;
                c.innerHTML += h;
            });
        }

        function addToCart(id, name, type) {
            const ex = cart.find(i => i.mon_an_id === id && !i.ghi_chu);
            if (ex) ex.so_luong++;
            else cart.push({
                mon_an_id: id,
                ten_mon: name,
                so_luong: 1,
                ghi_chu: '',
                loai_mon: type
            });
            renderCart();
            showToast(`Đã thêm ${name}`);
        }

        function renderCart() {
            const list = document.getElementById('cart-items');
            const btn = document.getElementById('submit-order-btn');
            const cnt = document.getElementById('cart-count');

            if (cart.length === 0) {
                list.innerHTML =
                    `<div class="empty-cart"><i class="fa-solid fa-basket-shopping"></i><p>Chưa chọn món nào</p></div>`;
                btn.disabled = true;
                cnt.innerText = '0';
                return;
            }
            btn.disabled = false;
            cnt.innerText = cart.length;
            list.innerHTML = '';

            cart.forEach((i, idx) => {
                list.innerHTML += `
                <div class="cart-item">
                    <div class="c-row-top">
                        <div class="c-name">${i.ten_mon} <span class="c-qty">x${i.so_luong}</span></div>
                        <div class="btn-remove" onclick="removeCart(${idx})"><i class="fa-solid fa-trash-can"></i></div>
                    </div>
                    ${i.ghi_chu ? `<span class="c-note">${i.ghi_chu}</span>` : ''}
                    <div class="c-action" onclick="editNote(${idx})"><i class="fa-regular fa-pen-to-square"></i> ${i.ghi_chu ? 'Sửa note' : 'Ghi chú'}</div>
                </div>`;
            });
        }

        function removeCart(i) {
            if (confirm('Xóa món?')) {
                cart.splice(i, 1);
                renderCart();
            }
        }

        function editNote(i) {
            const n = prompt('Ghi chú:', cart[i].ghi_chu);
            if (n !== null) {
                cart[i].ghi_chu = n;
                renderCart();
            }
        }

        document.getElementById('submit-order-btn').addEventListener('click', async function() {
            if (cart.length === 0) return;
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ĐANG GỬI...';
            try {
                const res = await fetch('/oderqr/order/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        dat_ban_id: DAT_BAN_ID,
                        items: cart
                    })
                });
                if (!res.ok) throw new Error('Lỗi gửi');
                showToast('Gửi thành công!');
                cart = [];
                renderCart();
                loadOrderStatus();
            } catch (e) {
                showToast('Lỗi: ' + e.message, 'error');
            } finally {
                this.disabled = cart.length === 0;
                this.innerHTML = '<span>GỌI MÓN NGAY</span> <i class="fa-solid fa-arrow-right"></i>';
            }
        });

        async function loadOrderStatus() {
            if (!DAT_BAN_ID) return;
            try {
                const res = await fetch(`/oderqr/order/status/${DAT_BAN_ID}`);
                if (res.status !== 200) return;
                const data = await res.json();

                // LƯU Ý: Render vào grid nằm trong scroll-wrapper
                const c = document.getElementById('status-grid');

                if (!data.items || data.items.length === 0) {
                    c.innerHTML =
                        `<p style="color:#aaa; text-align:center; grid-column:1/-1; padding:20px;">Chưa gọi món nào</p>`;
                    return;
                }

                c.innerHTML = '';
                data.items.forEach(i => {
                    let s = {
                        cls: 'pending',
                        txt: 'Đang làm',
                        ic: 'fire-burner'
                    };
                    if (i.trang_thai === 'da_len_mon') s = {
                        cls: 'completed',
                        txt: 'Đã lên',
                        ic: 'check'
                    };
                    else if (i.trang_thai === 'huy_mon') s = {
                        cls: 'cancelled',
                        txt: 'Đã hủy',
                        ic: 'xmark'
                    };
                    else if (i.trang_thai === 'cho_bep') s = {
                        cls: 'waiting',
                        txt: 'Chờ bếp',
                        ic: 'clock'
                    };

                    c.innerHTML += `
                    <div class="status-card ${s.cls}">
                        <div>
                            <div style="font-weight:700; font-size:0.9rem; margin-bottom:2px;">${i.mon_an.ten_mon} <span style="color:var(--primary); margin-left:4px;">x${i.so_luong}</span></div>
                            ${i.ghi_chu ? `<div style="font-size:0.75rem; color:#888; font-style:italic;">${i.ghi_chu}</div>` : ''}
                        </div>
                        <div class="stt-text"><i class="fa-solid fa-${s.ic}"></i> ${s.txt}</div>
                    </div>`;
                });
            } catch (e) {}
        }

        function startCountdown() {
            if (!bookingStartTime || bookingDuration <= 0) {
                document.getElementById('countdown-timer').innerText = "∞";
                return;
            }
            const end = new Date(new Date(bookingStartTime.replace(' ', 'T')).getTime() + bookingDuration * 60000);
            setInterval(() => {
                const d = end - new Date();
                if (d < 0) return document.getElementById('countdown-timer').innerText = "Hết giờ";
                document.getElementById('countdown-timer').innerText =
                    `${Math.floor(d/3600000)}h ${Math.floor((d%3600000)/60000)}p`;
            }, 1000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadSessionInfo();
            setInterval(loadOrderStatus, 10000);
        });
    </script>
@endsection
