@extends('layouts.Shop.layout-bep')

@section('title', 'Bếp Trung Tâm')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* --- CẤU HÌNH MÀU SẮC ĐỒNG BỘ VỚI MENU --- */
        :root {
            --primary: #fea116;
            /* Cam vàng */
            --dark: #0f172b;
            /* Xanh đen */
            --bg: #f8f9fa;
            /* Nền sáng */

            --wait: #f59e0b;
            /* Cam (Chờ bếp) */
            --cook: #3b82f6;
            /* Xanh dương (Đang làm) */

            /* MÀU MỚI (Giống Menu) */
            --done: #20d489;
            /* Xanh Mint (Đã xong) */
            --cancel: #ff4d4f;
            /* Đỏ tươi (Hủy) */
        }

        .app-content {
            background-color: var(--bg);
            min-height: 100vh;
            font-family: 'Nunito', sans-serif;
            padding-top: 20px;
        }

        /* Scrollbar đẹp */
        .t-body::-webkit-scrollbar {
            width: 5px;
        }

        .t-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .t-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .kds-header {
            background: var(--dark);
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .kds-title {
            font-size: 1.5rem;
            font-weight: 800;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-box {
            background: rgba(255, 255, 255, 0.15);
            padding: 4px;
            border-radius: 50px;
            display: inline-flex;
        }

        .btn-filter {
            background: transparent;
            border: none;
            color: #cbd5e1;
            padding: 6px 16px;
            border-radius: 40px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-filter:hover,
        .btn-filter.active {
            background: var(--primary);
            color: var(--dark);
        }

        .ticket-card {
            background: white;
            border-radius: 12px;
            border: none;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-top: 5px solid var(--dark);
        }

        .t-header {
            padding: 12px 15px;
            border-bottom: 1px dashed #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }

        .badge-table {
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--dark);
            background: #f1f5f9;
            padding: 5px 12px;
            border-radius: 8px;
        }

        .t-body {
            padding: 12px;
            overflow-y: auto;
            max-height: 650px;
            flex: 1;
            background: #fff;
        }

        .dish-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            position: relative;
            transition: all 0.3s;
            border-left: 5px solid transparent;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .d-info {
            display: flex;
            gap: 12px;
            margin-bottom: 10px;
            align-items: center;
        }

        /* --- STYLE ẢNH MỚI --- */
        .d-img-wrapper {
            width: 70px;
            height: 70px;
            /* To 70px giống menu */
            flex-shrink: 0;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #eee;
            background: #f1f5f9;
            position: relative;
        }

        .d-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s;
        }

        .dish-item:hover .d-img {
            transform: scale(1.1);
        }

        .d-name {
            font-weight: 700;
            color: #334155;
            font-size: 1rem;
            line-height: 1.3;
            margin-bottom: 5px;
        }

        .d-qty {
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--dark);
            background: #f1f5f9;
            padding: 3px 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .tag-note {
            font-size: 0.9rem;
            color: var(--cancel);
            background: #fff5f5;
            padding: 8px;
            border-radius: 6px;
            border: 1px dashed #ffccc7;
            margin-bottom: 12px;
            display: flex;
            gap: 6px;
            align-items: flex-start;
            font-weight: 700;
        }

        .badge-urgent {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--cancel);
            color: white;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 4px;
            animation: pulse 1.5s infinite;
            z-index: 2;
        }

        .d-actions {
            display: flex;
            gap: 8px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-act {
            flex: 1;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            color: white;
            text-transform: uppercase;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: 0.2s;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-act:active {
            transform: scale(0.96);
        }

        .btn-sub {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .btn-sub:hover {
            background: #fff5f5;
            color: var(--cancel);
            border-color: #ffccc7;
        }

        /* MÀU NỀN TRẠNG THÁI */
        .btn-start {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        /* Xanh dương */
        .btn-done {
            background: linear-gradient(135deg, #20d489, #16a34a);
        }

        /* Xanh Mint */

        .st-cho_bep {
            border-left-color: var(--wait);
            background: #fffbeb;
        }

        .st-dang_che_bien {
            border-left-color: var(--cook);
            background: #eff6ff;
        }

        /* Trạng thái đã xong: Màu xanh mint nhạt, nhìn tươi */
        .st-da_len_mon {
            border-left-color: var(--done);
            background: #f0fdf4;
            opacity: 0.8;
        }

        .st-huy_mon {
            border-left-color: var(--cancel);
            opacity: 0.6;
            background: #fff5f5;
        }

        .txt-done {
            width: 100%;
            text-align: center;
            padding: 10px;
            background: rgba(32, 212, 137, 0.15);
            color: #15803d;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            font-weight: 700;
        }

        .txt-cancel {
            width: 100%;
            text-align: center;
            padding: 10px;
            background: #fff5f5;
            color: var(--cancel);
            border: 1px solid #ffccc7;
            border-radius: 8px;
            font-weight: 700;
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
    </style>

    <div class="container-fluid">
        <div class="kds-header">
            <div class="kds-title"><i class="fa-solid fa-fire-burner text-warning"></i> Bếp Trung Tâm</div>
            {{-- Lọc theo khu bếp --}}
            <form method="GET" action="{{ route('bep.dashboard') }}">
                <div class="filter-box">
                    <button type="submit" name="khu_bep" value=""
                        class="btn-filter {{ request('khu_bep') == '' ? 'active' : '' }}">Tất cả</button>
                    <button type="submit" name="khu_bep" value="nong"
                        class="btn-filter {{ request('khu_bep') == 'nong' ? 'active' : '' }}">Bếp Nóng</button>
                    <button type="submit" name="khu_bep" value="lanh"
                        class="btn-filter {{ request('khu_bep') == 'lanh' ? 'active' : '' }}">Bếp Lạnh</button>
                    <button type="submit" name="khu_bep" value="bar"
                        class="btn-filter {{ request('khu_bep') == 'bar' ? 'active' : '' }}">Bar</button>
                </div>
            </form>
        </div>

        <div class="row g-4" id="order-grid">
            @forelse ($theoBan as $soBan => $danhSachMon)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="ticket-card">
                        <div class="t-header">
                            <div class="badge-table">Bàn {{ $soBan }}</div>
                            <div style="font-size:0.9rem; color:#64748b; font-weight: 600;">
                                <i class="fa-regular fa-clock"></i>
                                {{-- Lấy thời gian gọi của món đầu tiên trong danh sách (vì đã được sắp xếp theo created_at) --}}
                                {{ $danhSachMon->first()->created_at->format('H:i') }}
                            </div>
                        </div>

                        <div class="t-body" data-table="{{ $soBan }}">
                            @foreach ($danhSachMon as $mon)
                                @php
                                    $monAn = $mon->monAn;
                                    $tenMon = optional($monAn)->ten_mon ?? 'Món không tồn tại';
                                    $imgUrl = optional($monAn)->hinh_anh ? asset(optional($monAn)->hinh_anh) : null;
                                    $firstChar = mb_substr($tenMon, 0, 1, 'UTF-8');
                                    $fallback = 'https://placehold.co/100x100/png?text=' . urlencode($firstChar);
                                    $isNuoc = optional($monAn)->danh_muc_id == 14;
                                @endphp
                                
                                {{-- BỔ SUNG DATA-ATTRIBUTES cho JS tối ưu refresh --}}
                                <div class="dish-item st-{{ $mon->trang_thai }}" id="dish-{{ $mon->id }}" 
                                    data-mon-id="{{ $mon->mon_an_id }}"
                                    data-is-nuoc="{{ $isNuoc ? 'true' : 'false' }}">

                                    @if ($mon->uu_tien)
                                        <div class="badge-urgent">GẤP</div>
                                    @endif

                                    <div class="d-info">
                                        {{-- === CODE HIỂN THỊ ẢNH === --}}
                                        <div class="d-img-wrapper">
                                            @if ($imgUrl)
                                                <img src="{{ $imgUrl }}" class="d-img" loading="lazy"
                                                    alt="{{ $tenMon }}"
                                                    onerror="this.onerror=null; this.src='{{ $fallback }}';">
                                            @else
                                                <img src="{{ $fallback }}" class="d-img" alt="No Image">
                                            @endif
                                        </div>
                                        {{-- === KẾT THÚC CODE ẢNH === --}}

                                        <div style="flex:1;">
                                            <div class="d-name">{{ $tenMon }}</div>
                                            <div class="d-qty">SL: {{ $mon->so_luong }}</div>
                                            {{-- Hiển thị thời gian chế biến dự kiến --}}
                                            @if($monAn->thoi_gian_che_bien && !$isNuoc)
                                                <small class="text-secondary ms-2" title="Thời gian dự kiến">
                                                    <i class="fa-solid fa-hourglass-half"></i> {{ $monAn->thoi_gian_che_bien }} phút
                                                </small>
                                            @endif
                                            @if($isNuoc)
                                                <small class="text-info ms-2" title="Đồ uống"><i class="fa-solid fa-glass-water"></i> Đồ Uống</small>
                                            @endif
                                        </div>
                                    </div>

                                    @if ($mon->ghi_chu)
                                        <div class="tag-note"><i class="fa-solid fa-pen-to-square mt-1"></i>
                                            <span>{{ $mon->ghi_chu }}</span></div>
                                    @endif

                                    <div class="d-actions" id="actions-{{ $mon->id }}">
                                        @if ($mon->trang_thai == 'cho_bep')
                                            <button class="btn-sub" onclick="updateStatus({{ $mon->id }}, 'huy_mon')"
                                                title="Hủy"><i class="fa-solid fa-xmark"></i></button>
                                            <button class="btn-act btn-start"
                                                onclick="updateStatus({{ $mon->id }}, 'dang_che_bien')"><i
                                                    class="fa-solid fa-fire-burner"></i> Chế biến</button>
                                        @elseif ($mon->trang_thai == 'dang_che_bien')
                                            <button class="btn-sub" onclick="updateStatus({{ $mon->id }}, 'cho_bep')"
                                                title="Quay lại"><i class="fa-solid fa-rotate-left"></i></button>
                                            <button class="btn-act btn-done"
                                                onclick="updateStatus({{ $mon->id }}, 'da_len_mon')"><i
                                                    class="fa-solid fa-check"></i> Hoàn tất</button>
                                        @elseif ($mon->trang_thai == 'da_len_mon')
                                            <div class="txt-done"><i class="fa-solid fa-check-circle"></i> Đã xong</div>
                                            <button class="btn-sub"
                                                onclick="updateStatus({{ $mon->id }}, 'dang_che_bien')"
                                                title="Hoàn tác"><i class="fa-solid fa-rotate-left"></i></button>
                                        @elseif ($mon->trang_thai == 'huy_mon')
                                            <div class="txt-cancel"><i class="fa-solid fa-ban"></i> Đã hủy</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-utensils text-muted" style="font-size: 3rem; margin-bottom: 15px;"></i>
                    <h3 class="text-muted">Bếp đang rảnh!</h3>
                    <p class="text-secondary">Chưa có order nào cần xử lý.</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
        function updateStatus(id, status) {
            if (status === 'huy_mon' && !confirm('Bạn chắc chắn muốn hủy món này?')) return;
            const dishEl = document.getElementById(`dish-${id}`);
            const actionEl = document.getElementById(`actions-${id}`);
            if (!dishEl || !actionEl) return;

            dishEl.style.opacity = '0.6';
            dishEl.style.pointerEvents = 'none';

            fetch('{{ route('bep.update-status') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id,
                        trang_thai: status
                    })
                })
                .then(async res => {
                    const isJson = res.headers.get('content-type')?.includes('application/json');
                    const data = isJson ? await res.json() : null;
                    if (!res.ok) throw new Error((data && data.message) || res.statusText);
                    return data;
                })
                .then(data => {
                    // Cập nhật trạng thái ngay lập tức mà không cần tải lại toàn bộ DOM
                    renderNewState(id, status, dishEl, actionEl);
                    // Lưu ý: Vị trí món sẽ được sắp xếp lại đúng trong lần Auto Refresh tiếp theo (3s)
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi: ' + err.message);
                    dishEl.style.opacity = '1';
                    dishEl.style.pointerEvents = 'auto';
                });
        }

        function renderNewState(id, status, dishEl, actionEl) {
            dishEl.className = `dish-item st-${status}`;
            dishEl.style.opacity = '1';
            dishEl.style.pointerEvents = 'auto';
            let html = '';
            if (status === 'cho_bep') {
                html =
                    `<button class="btn-sub" onclick="updateStatus(${id}, 'huy_mon')"><i class="fa-solid fa-xmark"></i></button><button class="btn-act btn-start" onclick="updateStatus(${id}, 'dang_che_bien')"><i class="fa-solid fa-fire-burner"></i> Chế biến</button>`;
            } else if (status === 'dang_che_bien') {
                html =
                    `<button class="btn-sub" onclick="updateStatus(${id}, 'cho_bep')"><i class="fa-solid fa-rotate-left"></i></button><button class="btn-act btn-done" onclick="updateStatus(${id}, 'da_len_mon')"><i class="fa-solid fa-check"></i> Hoàn tất</button>`;
            } else if (status === 'da_len_mon') {
                html =
                    `<div class="txt-done"><i class="fa-solid fa-check-circle"></i> Đã xong</div><button class="btn-sub" onclick="updateStatus(${id}, 'dang_che_bien')"><i class="fa-solid fa-rotate-left"></i></button>`;
            } else if (status === 'huy_mon') {
                html = `<div class="txt-cancel"><i class="fa-solid fa-ban"></i> Đã hủy</div>`;
            }
            actionEl.innerHTML = html;
        }

        // --- AUTO REFRESH 3S (GIỮ VỊ TRÍ CUỘN) ---
        let isInteracting = false;
        document.addEventListener('mousedown', () => isInteracting = true);
        document.addEventListener('mouseup', () => isInteracting = false);

        setInterval(() => {
            if (isInteracting) return;
            fetch(window.location.href).then(response => response.text()).then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('order-grid').innerHTML;

                let scrollPositions = {};
                document.querySelectorAll('.t-body').forEach(el => {
                    const tableId = el.getAttribute('data-table');
                    if (tableId) scrollPositions[tableId] = el.scrollTop;
                });

                document.getElementById('order-grid').innerHTML = newContent;

                document.querySelectorAll('.t-body').forEach(el => {
                    const tableId = el.getAttribute('data-table');
                    if (tableId && scrollPositions[tableId] !== undefined) el.scrollTop =
                        scrollPositions[tableId];
                });
            }).catch(err => console.error('Auto refresh error:', err));
        }, 3000);
    </script>
@endsection