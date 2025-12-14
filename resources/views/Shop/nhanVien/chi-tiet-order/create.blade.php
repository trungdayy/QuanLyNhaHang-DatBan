@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Thêm món vào Order')

{{-- 1. IMPORT FONTS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

{{-- 2. CSS STYLING (Design System) --}}
<style>
    :root {
        --primary: #fea116;
        --primary-dark: #d98a12;
        --dark: #0f172b;
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

    h3,
    h5,
    h6,
    strong,
    .font-heading {
        font-family: 'Heebo', sans-serif;
    }

    /* --- HEADER CONTEXT --- */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header-title {
        color: var(--dark);
        font-weight: 800;
        font-size: 1.8rem;
        text-transform: uppercase;
    }

    /* --- CONTEXT BOX --- */
    .context-box {
        background: var(--white);
        border-radius: var(--radius);
        padding: 15px 20px;
        border-left: 4px solid var(--primary);
        box-shadow: var(--shadow-card);
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

    /* --- MENU GRID --- */
    .dish-card {
        background: var(--white);
        border-radius: var(--radius);
        padding: 10px;
        border: 1px solid #f1f5f9;
        box-shadow: var(--shadow-card);
        transition: var(--anim-fast);
        display: flex;
        align-items: center;
        gap: 15px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .dish-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-hover);
        border-color: rgba(254, 161, 22, 0.4);
    }

    .dish-thumb {
        width: 70px;
        height: 70px;
        border-radius: 6px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .dish-info {
        flex: 1;
    }

    .dish-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 2px;
    }

    .dish-meta {
        font-size: 0.75rem;
        color: var(--text-sub);
        margin-bottom: 4px;
    }

    .dish-price {
        color: var(--primary);
        font-weight: 800;
        font-family: 'Heebo';
        font-size: 0.9rem;
    }

    .btn-add-quick {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #f1f8ff;
        color: var(--primary);
        border: 1px solid var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--anim-fast);
        font-size: 1.1rem;
    }

    .dish-card:hover .btn-add-quick {
        background: var(--primary);
        color: var(--white);
    }

    /* --- CART SIDEBAR --- */
    .cart-panel {
        position: sticky;
        top: 20px;
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow-card);
        border: 1px solid #f1f5f9;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: calc(100vh - 40px);
    }

    .cart-header {
        background: var(--dark);
        color: var(--white);
        padding: 15px;
        font-family: 'Heebo';
        font-weight: 700;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 8px;
        background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.1) 1px, transparent 0);
        background-size: 20px 20px;
    }

    .cart-body {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 10px;
        margin-bottom: 10px;
        border-bottom: 1px dashed #e2e8f0;
        animation: slideIn 0.2s ease;
    }

    .cart-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .item-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--dark);
        line-height: 1.2;
    }

    .item-qty {
        color: var(--primary);
        font-weight: 800;
        font-family: 'Heebo';
        margin-left: 4px;
    }

    .item-note {
        font-size: 0.75rem;
        color: var(--text-sub);
        font-style: italic;
        display: block;
        margin-top: 2px;
    }

    .btn-icon {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-edit-note {
        background: #e2e8f0;
        color: var(--text-sub);
        margin-right: 4px;
    }

    .btn-edit-note:hover {
        background: #cbd5e1;
        color: var(--dark);
    }

    .btn-remove {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-remove:hover {
        background: #ef4444;
        color: white;
    }

    .cart-footer {
        padding: 15px;
        border-top: 1px solid #f1f5f9;
        background: #f8fafc;
    }

    .btn-submit {
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

    .btn-submit:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    .btn-back {
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

    .filter-menu {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 20px;
    }

    .filter-row {
        display: flex;
        gap: 12px;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 6px;
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }

    .filter-row::-webkit-scrollbar {
        height: 6px;
    }

    .filter-row::-webkit-scrollbar-thumb {
        background-color: rgba(254, 161, 22, 0.6);
        border-radius: 3px;
    }

    .filter-row::-webkit-scrollbar-track {
        background-color: transparent;
    }


    .filter-btn {
        flex: 0 0 auto;
        white-space: nowrap;
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        background-color: var(--white);
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-main);
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .filter-btn:hover {
        background: var(--primary);
        color: var(--white);
        transform: translateY(-2px) scale(1.05);
        box-shadow: var(--shadow-hover);
    }

    .filter-btn.active {
        background: var(--primary);
        color: var(--white);
        border-color: var(--primary-dark);
        box-shadow: 0 4px 12px rgba(254, 161, 22, 0.25);
    }

    .filter-btn i {
        margin-right: 6px;
        font-size: 0.9rem;
        vertical-align: middle;
    }

    /* Responsive: thu nhỏ trên mobile */
    @media (max-width: 768px) {
        .filter-btn {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
    }
</style>


@section('content')
<main class="app-content">{{-- Flash message --}}
    @if(session('success'))
    <div class="alert alert-success text-center fw-semibold rounded-3 shadow-sm mb-4" id="flashMsg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning text-center fw-semibold rounded-3 shadow-sm mb-4" id="flashMsg">
        {{ session('warning') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger text-center fw-semibold rounded-3 shadow-sm mb-4" id="flashMsg">
        {{ session('error') }}
    </div>
    @endif

    <div class="container py-4">
        <h3 class="fw-bold mb-3">Thêm món vào Order số:{{ $order->id }}</h3>
        <p class="mb-3"><b>Bàn:</b> {{ $order->banAn->so_ban ?? 'Không xác định' }}</p>
        <hr>
        {{-- Flash Messages --}}
        @foreach(['success', 'warning', 'error'] as $msg)
        @if(session($msg))
        <div class="alert alert-{{ $msg == 'error' ? 'danger' : ($msg == 'warning' ? 'warning' : 'success') }} mb-4 shadow-sm border-0 fw-bold">
            {{ session($msg) }}
        </div>
        @endif
        @endforeach

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <a href="{{ route('nhanVien.order.page', $order->id) }}" class="btn-back mb-2">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại Order
                </a>
                <h2 class="header-title">Thêm món</h2>
            </div>
            <span class="text-muted fw-bold">{{ date('d/m/Y') }}</span>
        </div>

        {{-- CONTEXT INFO --}}
        <div class="context-box">
            <div class="context-item">
                <span class="context-label">Order ID</span>
                <span class="context-value">#{{ $order->id }}</span>
            </div>
            <div style="width: 1px; height: 30px; background: #e2e8f0;"></div>
            <div class="context-item">
                <span class="context-label">Bàn Phục Vụ</span>
                <span class="context-value">
                    @if($order->banAn)
                    Bàn {{ $order->banAn->so_ban }}
                    @else
                    <span class="text-danger">--</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mb-4">

                {{-- Tabs --}}
                <ul class="nav nav-tabs mb-3" id="dishTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="combo-tab" data-bs-toggle="tab" data-bs-target="#combo-dishes" type="button" role="tab">Món trong Combo</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-dishes" type="button" role="tab">Tất cả món ăn</button>
                    </li>
                </ul>{{-- Search + Filter --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    {{-- Search Input --}}
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="search-dish" class="form-control" placeholder="Tìm theo món ăn...">
                    </div>

                    {{-- Filter toggle --}}
                    <button id="toggle-filter" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-filter"></i> Lọc món
                    </button>
                </div>

                {{-- Filter Row (ẩn mặc định) --}}
                <div id="filter-row" class="filter-row mb-3" style="display:none;">
                    @php
                    $loaiMons = $monAns->pluck('loai_mon')->unique();
                    @endphp
                    @foreach($loaiMons as $loai)
                    <button class="filter-btn" data-type="loai" data-value="{{ $loai }}">
                        <i class="fa-solid fa-utensils"></i> {{ $loai }}
                    </button>
                    @endforeach
                </div>


                <div class="tab-content" id="dishTabsContent">

                    {{-- 1. Món trong Combo --}}
                    <div class="tab-pane fade show active" id="combo-dishes" role="tabpanel">
                        @php
                        $comboMons = [];
                        foreach($order->datBan->combos as $combo){
                        foreach($combo->monTrongCombo as $item){
                        $mon = $item->monAn;
                        $comboMons[$mon->danh_muc_id][] = $mon;
                        }
                        }
                        @endphp

                        @foreach($comboMons as $danhMucId => $mons)
                        <h5 class="mb-2 mt-3">{{ $mons[0]->danhMuc->ten_danh_muc ?? 'Danh mục không xác định' }}</h5>
                        <div class="row g-3">
                            @foreach($mons as $mon)
                            @php $soLuong = $soLuongMonTrongCombo[$mon->id] ?? 1; @endphp
                            <div class="col-lg-6">
                                <div class="dish-card mon-card combo-item"
                                    data-is-combo="1"
                                    data-id="{{ $mon->id }}"
                                    data-ten="{{ $mon->ten_mon }}"
                                    data-gia="0"
                                    data-loai="{{ $mon->loai_mon }}"
                                    data-category="{{ $mon->danh_muc_id }}">
                                    <img src="{{ asset($mon->hinh_anh ?? 'https://placehold.co/70x70?text=IMG') }}" class="dish-thumb" alt="{{ $mon->ten_mon }}">
                                    <div class="dish-info">
                                        <div class="dish-name">{{ $mon->ten_mon }}</div>
                                        <div class="dish-meta">{{ $mon->loai_mon }}</div>
                                        <div class="dish-price">0 <small>đ</small></div>
                                    </div>
                                    <div class="btn-add-quick add-to-cart"><i class="fa-solid fa-plus"></i></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>

                    {{-- 2. Tất cả món ăn --}}
                    <div class="tab-pane fade" id="all-dishes" role="tabpanel">
                        @php
                        $allMonsByCategory = $monAns->groupBy('danh_muc_id'); // collection groupBy
                        @endphp

                        @foreach($allMonsByCategory as $danhMucId => $mons)
                        <h5 class="mb-2 mt-3">{{ $mons[0]->danhMuc->ten_danh_muc ?? 'Danh mục không xác định' }}</h5>
                        <div class="row g-3">
                            @foreach($mons as $mon)
                            <div class="col-lg-6">
                                <div class="dish-card mon-card"
                                    data-is-combo="0"
                                    data-id="{{ $mon->id }}"
                                    data-ten="{{ $mon->ten_mon }}"
                                    data-gia="{{ $mon->gia }}"
                                    data-loai="{{ $mon->loai_mon }}"
                                    data-category="{{ $mon->danh_muc_id }}">
                                    <img src="{{ asset($mon->hinh_anh ?? 'https://placehold.co/70x70?text=IMG') }}" class="dish-thumb" alt="{{ $mon->ten_mon }}">
                                    <div class="dish-info">
                                        <div class="dish-name">{{ $mon->ten_mon }}</div>
                                        <div class="dish-meta">{{ $mon->loai_mon }}</div>
                                        <div class="dish-price">{{ number_format($mon->gia,0,',','.') }} <small>đ</small></div>
                                    </div>
                                    <div class="btn-add-quick add-to-cart"><i class="fa-solid fa-plus"></i></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT: CART SIDEBAR --}}
            <div class="col-md-4">
                <div class="cart-panel">
                    <div class="cart-header">
                        <i class="fa-solid fa-basket-shopping"></i> Giỏ hàng thêm
                    </div>

                    <div class="cart-body">
                        <ul id="cart-items" class="list-unstyled mb-0">
                            {{-- Items will be rendered here --}}
                            <li class="text-center text-muted py-4 empty-cart-msg">
                                <small>Chưa chọn món nào</small>
                            </li>
                        </ul>
                    </div>

                    <div class="cart-footer">
                        <button id="submit-order-btn" class="btn-submit">
                            <i class="fa-solid fa-paper-plane"></i> THÊM MÓN ORDER
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        const orderId = {{ $order->id }};
        // chạy lỗi thì thay thành : const orderId = {{ $order->id }};
        let cart = [];

        // Render Cart UI
        function renderCart() {
            const ul = document.getElementById('cart-items');
            ul.innerHTML = '';

            if (cart.length === 0) {
                ul.innerHTML = `<li class="text-center text-muted py-4 empty-cart-msg"><small>Chưa chọn món nào</small></li>`;
                return;
            }

            cart.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = "cart-item";
                li.innerHTML = `
                    <div style="flex: 1;">
                        <div class="item-name">
                            ${item.ten_mon}
                            <span class="item-qty">x${item.so_luong}</span>
                            ${item.is_combo == 1 ? '<small style="color: #22c55e;">(Combo)</small>' : '<small style="color:#f97316;">(Gọi thêm)</small>'}
                            </div>
                        ${item.ghi_chu ? `<span class="item-note"><i class="fa-regular fa-comment"></i> ${item.ghi_chu}</span>` : ''}
                    </div>
                    <div style="flex-shrink: 0; display: flex;">
                        <button class="btn-icon btn-edit-note" onclick="editNote(${index})" title="Ghi chú"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn-icon btn-remove" onclick="deleteItem(${index})" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                    </div>
                `;
                ul.appendChild(li);
            });
        }

        // Logic Add Cart
        function addToCart(monId, tenMon, gia, loaiMon, isCombo) {
            const existing = cart.find(i => i.mon_an_id == monId);
            if (existing) {
                existing.so_luong++;
            } else {
                cart.push({
                    mon_an_id: monId,
                    ten_mon: tenMon,
                    so_luong: 1,
                    ghi_chu: null,
                    loai_mon: loaiMon,
                    is_combo: isCombo
                });
            }
            renderCart();
        }

        // Logic Delete
        function deleteItem(index) {
            // Không confirm để thao tác nhanh hơn, hoặc confirm nhẹ
            cart.splice(index, 1);
            renderCart();
        }

        // Logic Note
        function editNote(index) {
            const note = prompt(`Ghi chú cho món ${cart[index].ten_mon}:`, cart[index].ghi_chu || '');
            if (note !== null) {
                cart[index].ghi_chu = note.trim();
                renderCart();
            }
        }

        // Event Listener: Click Card to Add
        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', e => {
                // Prevent bubbling if user clicks button inside card, or card itself
                e.stopPropagation();
                const card = e.target.closest('.mon-card');

                // Animation effect (optional)
                card.style.transform = "scale(0.98)";
                setTimeout(() => card.style.transform = "", 100);

                addToCart(
                    card.dataset.id,
                    card.dataset.ten,
                    card.dataset.gia,
                    card.dataset.loai,
                    card.dataset.isCombo
                );
            });
        });

        // Also allow clicking the whole card
        document.querySelectorAll('.mon-card').forEach(card => {
            card.addEventListener('click', () => {
                // Trigger the button click logic
                card.querySelector('.add-to-cart').click();
            });
        });

        // Submit Order
        document.getElementById('submit-order-btn').addEventListener('click', async () => {
            if (cart.length === 0) return alert('Vui lòng chọn món trước khi gửi!');

            const btn = document.getElementById('submit-order-btn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ĐANG GỬI...';
            btn.disabled = true;

            try {
                const res = await fetch("{{ route('nhanVien.chi-tiet-order.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        items: cart
                    })
                });

                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Lỗi khi gửi order');

                // Redirect back
                window.location.href = "{{ route('nhanVien.order.page', $order->id) }}";
            } catch (err) {
                alert(err.message);
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> GỬI BẾP';
                btn.disabled = false;
            }
        });

        // Gán data-category cho card từ controller nếu chưa có
        document.querySelectorAll('.mon-card').forEach(card => {
            if (!card.dataset.category) {
                card.dataset.category = card.dataset.danhmuc || ''; // fallback
            }
        });

        document.querySelectorAll('.filter-row').forEach(row => {
            row.addEventListener('wheel', function(e) {
                e.preventDefault(); // ngăn cuộn dọc mặc định
                row.scrollLeft += e.deltaY; // cuộn ngang thay vì dọc
            });
        });
        const searchInput = document.getElementById('search-dish');

        searchInput.addEventListener('input', () => {
            const keyword = searchInput.value.toLowerCase();

            document.querySelectorAll('.mon-card').forEach(card => {
                const matchesType = Array.from(document.querySelectorAll('#filter-type .filter-btn.active'))
                    .map(b => b.dataset.value)
                    .includes(card.dataset.loai) ||
                    document.querySelectorAll('#filter-type .filter-btn.active').length === 0;

                const matchesName = card.dataset.ten.toLowerCase().includes(keyword);

                card.parentElement.style.display = (matchesType && matchesName) ? 'block' : 'none';
            });
        });
        document.getElementById('toggle-filter').addEventListener('click', () => {
            const row = document.getElementById('filter-row');
            row.style.display = row.style.display === 'none' ? 'flex' : 'none';
        });

        // Filter theo loại món
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.classList.toggle('active');
                applyFilters();
            });
        });

        // Search theo tên món
        document.getElementById('search-dish').addEventListener('input', applyFilters);

        // Hàm áp dụng cả filter và search
        function applyFilters() {
            const keyword = document.getElementById('search-dish').value.toLowerCase();
            const activeTypes = Array.from(document.querySelectorAll('.filter-btn.active')).map(b => b.dataset.value);

            document.querySelectorAll('.mon-card').forEach(card => {
                const matchesName = card.dataset.ten.toLowerCase().includes(keyword);
                const matchesType = activeTypes.length === 0 || activeTypes.includes(card.dataset.loai);

                card.parentElement.style.display = (matchesName && matchesType) ? 'block' : 'none';
            });
        }

        // Scroll ngang filter row
        document.querySelectorAll('.filter-row').forEach(row => {
            row.addEventListener('wheel', function(e) {
                e.preventDefault();
                row.scrollLeft += e.deltaY;
            });
        });
    </script>
    @endsection