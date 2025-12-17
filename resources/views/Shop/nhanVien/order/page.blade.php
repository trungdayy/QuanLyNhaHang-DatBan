@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Chi tiết Order')

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
        --success: #20d489;
        --danger: #ff4d4f;
        --info: #0dcaf0;
        --text-main: #1e293b;
        --text-sub: #64748b;
        --bg-light: #f8f9fa;
        --radius: 8px;
        --shadow-card: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
    }

    body {
        font-family: 'Nunito', sans-serif;
        background-color: var(--bg-light);
        color: var(--text-main);
    }

    h2,
    h3,
    h5,
    strong,
    .font-heading {
        font-family: 'Heebo', sans-serif;
    }

    /* --- CARD STYLE --- */
    .card-box {
        border: none;
        border-radius: var(--radius);
        box-shadow: var(--shadow-card);
        background: var(--white);
        margin-bottom: 24px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
        transition: .25s ease;
    }

    .card-header-custom {
        background: var(--dark);
        color: var(--white);
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.1) 1px, transparent 0);
        background-size: 20px 20px;
    }

    .header-title {
        font-family: 'Heebo', sans-serif;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 1rem;
        letter-spacing: 0.5px;
    }

    /* --- INFO ROW --- */
    .info-row {
        display: flex;
        margin-bottom: 8px;
        align-items: center;
    }

    .info-label {
        width: 140px;
        color: var(--text-sub);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .info-value {
        color: var(--dark);
        font-weight: 700;
        font-size: 1rem;
    }

    .total-money {
        color: var(--primary);
        font-size: 1.2rem;
        font-weight: 800;
        font-family: 'Heebo';
    }

    /* --- TABLE STYLE --- */
    .custom-table thead th {
        background: #f1f5f9;
        color: var(--text-main);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px;
    }

    .custom-table tbody td {
        vertical-align: middle;
        padding: 12px;
        border-bottom: 1px dashed #f1f5f9;
        color: var(--text-main);
        font-size: 0.95rem;
        font-weight: 600;
    }

    .custom-table tbody tr:hover {
        background: #f8fafc;
    }

    /* --- STATUS BADGES (Glassmorphism) --- */
    .badge-pill {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-block;
    }

    /* Chờ bếp: Vàng/Cam nhạt */
    .st-cho-bep {
        background: rgba(254, 161, 22, 0.15);
        color: #b45309;
        border: 1px solid rgba(254, 161, 22, 0.3);
    }

    /* Đang làm: Xanh dương nhạt */
    .st-dang-lam {
        background: rgba(13, 202, 240, 0.15);
        color: #0891b2;
        border: 1px solid rgba(13, 202, 240, 0.3);
    }

    /* Đã lên: Xanh Mint nhạt */
    .st-da-len {
        background: rgba(32, 212, 137, 0.15);
        color: #059669;
        border: 1px solid rgba(32, 212, 137, 0.2);
    }

    /* Hủy: Đỏ nhạt */
    .st-huy {
        background: rgba(255, 77, 79, 0.1);
        color: #dc2626;
        border: 1px solid rgba(255, 77, 79, 0.2);
    }

    /* --- BUTTONS --- */
    .btn-custom {
        border: none;
        border-radius: 6px;
        font-weight: 700;
        text-transform: uppercase;
        font-family: 'Heebo', sans-serif;
        font-size: 0.85rem;
        padding: 8px 16px;
        transition: 0.2s;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back {
        background: #e2e8f0;
        color: var(--text-sub);
    }

    .btn-back:hover {
        background: #cbd5e1;
        color: var(--dark);
    }

    .btn-add {
        background: var(--primary);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(254, 161, 22, 0.3);
    }

    .btn-add:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    .btn-icon-small {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: 0.2s;
    }

    .btn-edit {
        background: #fff7ed;
        color: #ea580c;
        border: 1px solid #ffedd5;
    }

    .btn-edit:hover {
        background: #ffedd5;
    }

    .btn-delete {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fee2e2;
    }

    .btn-delete:hover {
        background: #fee2e2;
    }

    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .btn {
        transition: all 0.3s;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    table th,
    table td {
        vertical-align: middle !important;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
    }

    .countdown {
        font-size: 0.75rem;
        font-weight: 800;
        font-family: 'Heebo';
        color: #475569;
    }

    .countdown.warning {
        color: #f59e0b;
    }

    .countdown.danger {
        color: #dc2626;
    }

    .delay-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #dc2626;
        color: #fff;
        border-radius: 8px;
        padding: 14px 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: 14px;
        z-index: 9999;

        opacity: 0;
        transform: translateX(50px);
        pointer-events: none;
        transition: 0.4s ease;
    }

    .delay-alert.show {
        opacity: 1;
        transform: translateX(0);
    }

    tr.tre-mon {
        background: rgba(220, 38, 38, 0.08) !important;
    }

    .scrollable-table {
        max-height: 800px;
        overflow-y: auto;
        display: block;
    }

    /* Optional: scroll mượt */
    .scrollable-table::-webkit-scrollbar {
        width: 8px;
    }

    .scrollable-table::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
    }

    .scrollable-table::-webkit-scrollbar-track {
        background-color: transparent;
    }

    .table-scrollable {
        max-height: 800px;
        overflow-y: auto;
        display: block;
    }

    .table-scrollable thead {
        position: sticky;
        top: 0;
        background-color: #f1f5f9;
        z-index: 10;
    }
</style>

@section('content')
<main class="app-content">
    <div class="container py-4">
        {{-- Flash message --}}
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


        {{-- FLASH MESSAGES --}}
        @foreach (['success' => 'check-circle', 'warning' => 'exclamation-triangle', 'error' => 'times-circle'] as $msg => $icon)
        @if(session($msg))
        <div class="alert alert-{{ $msg == 'error' ? 'danger' : $msg }} mb-4 shadow-sm border-0 d-flex align-items-center gap-2"
            style="font-weight: 600;">
            <i class="fa-solid fa-{{ $icon }}"></i> {{ session($msg) }}
        </div>
        @endif
        @endforeach

        {{-- HEADER PAGE --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <a href="{{ route('nhanVien.order.index') }}" class="btn-custom btn-back mb-2" style="padding: 6px 12px; font-size: 0.75rem;">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
                <h3 class="m-0 font-heading" style="color: var(--dark); font-weight: 800; font-size: 1.8rem;">
                    ORDER BÀN {{ $order->banAn->so_ban }}
                </h3>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('nhanVien.chi-tiet-order.create', ['order_id' => $order->id]) }}" class="btn-custom btn-add">
                    <i class="fa-solid fa-plus"></i> Thêm món
                </a>
            </div>
        </div>

        <div class="row">
            {{-- 1. THÔNG TIN CHUNG (CARD) --}}
            <div class="col-md-4 order-md-2 mb-4">
                <div class="card-box">
                    <div class="card-header-custom">
                        <span class="header-title"><i class="fa-solid fa-circle-info me-2"></i> Thông tin</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="info-row">
                            <span class="info-label">Mã Order:</span>
                            <span class="info-value">Số: {{ $order->id }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Bàn:</span>
                            <span class="info-value">Số {{ $order->banAn->so_ban }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Combo:</span>
                            <span class="info-value text-primary">
                                @if($order->datBan->combos->isEmpty())
                                Gọi món lẻ
                                @else
                                @php
                                $comboText = $order->datBan->combos->map(function($combo) {
                                $qty = $combo->pivot->so_luong ?? 1;
                                return $combo->ten_combo . ($qty > 1 ? " x{$qty}" : "");
                                })->join(' , ');
                                @endphp

                                {{ $comboText }}

                                @endif
                            </span>
                        </div>
                        <hr style="border-color: #f1f5f9;">
                        @php
                        $tongMonLe = $order->chiTietOrders->sum('so_luong');

                        $tongMonCombo = $order->datBan->combos->sum('so_luong');

                        $tongMon = $tongMonLe + $tongMonCombo;
                        @endphp

                        @php
                        $tongTienCombo = $order->datBan->combos->sum(function ($combo) {
                        return $combo->gia_co_ban * $combo->pivot->so_luong;
                        });
                        @endphp

                        @php
                        $tongTienMonLe = $order->chiTietOrders
                        ->where('loai_mon', 'goi_them')
                        ->sum(function ($ct) {
                        return ($ct->monAn->gia ?? 0) * ($ct->so_luong ?? 0);
                        });
                        @endphp

                        @php
                        $tongTien = $tongTienCombo + $tongTienMonLe;
                        @endphp

                        <div class="info-row">
                            <span class="info-label">Tổng món:</span>
                            <span class="info-value">{{ $tongMon }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tạm tính:</span>
                            <span class="total-money">
                                {{ number_format($tongTien) }}đ
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. DANH SÁCH MÓN (TABLE) --}}
            <div class="col-md-8 order-md-1">
                <div class="card-box">
                    <div class="card-header-custom">
                        <span class="header-title"><i class="fa-solid fa-utensils me-2"></i> Danh sách món</span>
                    </div>

                    @if ($order->chiTietOrders->isEmpty())
                    <div class="p-5 text-center text-muted">
                        <i class="fa-solid fa-basket-shopping fa-3x mb-3 opacity-25"></i>
                        <p class="fw-bold">Chưa có món nào được gọi.</p>
                    </div>
                    @else
                    <div class="table-scrollable">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>Món ăn</th>
                                    <th class="text-center">SL</th>
                                    <th>Ghi chú</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-end">Xử lý</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->chiTietOrders as $ct)
                                <tr data-id="{{ $ct->id }}">
                                    <td>
                                        <div style="font-weight: 700; color: var(--dark);">{{ $ct->monAn->ten_mon }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($ct->so_luong_hien_thi === 'Chưa chọn')
                                        <span class="badge bg-secondary">Chưa chọn</span>
                                        @else
                                        x{{ $ct->so_luong_hien_thi }}
                                        @endif
                                    </td>

                                    <td>
                                        @if($ct->ghi_chu)
                                        <small class="text-muted fst-italic"><i class="fa-regular fa-comment-dots"></i> {{ $ct->ghi_chu }}</small>
                                        @else
                                        <span class="text-muted opacity-50">-</span>
                                        @endif
                                    </td>

                                    @php
                                    $deadline = $ct->deadline->timestamp * 1000; // JS timestamp
                                    @endphp

                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center gap-1">
                                            @switch($ct->trang_thai)

                                            @case('cho_bep')
                                            <span class="badge-pill st-cho-bep">Chờ bếp</span>
                                            @break

                                            @case('dang_che_bien')
                                            <span class="badge-pill st-dang-lam">Đang làm</span>
                                            @break

                                            @case('da_len_mon')
                                            <span class="badge-pill st-da-len">Đã lên</span>
                                            @break

                                            @case('huy_mon')
                                            <span class="badge-pill st-huy">Đã hủy</span>
                                            @break

                                            @endswitch


                                            {{-- COUNTDOWN --}}
                                            @if(in_array($ct->trang_thai, ['cho_bep','dang_che_bien']))
                                            <small
                                                class="countdown"
                                                data-deadline="{{ $deadline }}"
                                                data-ten-mon="{{ $ct->monAn->ten_mon }}">
                                                ⏳ --:--
                                            </small>

                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('nhanVien.chi-tiet-order.edit', [$order->id, $ct->id]) }}"
                                            class="btn-icon-small btn-edit" title="Sửa">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <form action="{{ route('nhanVien.chi-tiet-order.destroy', $ct->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Xóa món {{ $ct->monAn->ten_mon }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-icon-small btn-delete ms-1" title="Xóa">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            function showDelayAlert(text) {
                let box = document.getElementById("delayAlert");
                let textBox = document.getElementById("delayText");
                let sound = document.getElementById("delaySound");
                if (!box) return;

                textBox.innerHTML = text;
                box.classList.add("show");

                if (sound) {
                    sound.currentTime = 0;
                    sound.play().catch(() => {});
                }

                setTimeout(() => box.classList.remove("show"), 4000);
            }

            function updateCountdown() {
                document.querySelectorAll(".countdown").forEach(el => {

                    let deadline = parseInt(el.dataset.deadline);
                    let now = Date.now();
                    let diff = deadline - now;

                    if (diff <= 0) {

                        if (!el.dataset.alerted) {
                            showDelayAlert("⚠ Trễ món: " + el.dataset.tenMon);
                            el.dataset.alerted = "1";
                        }

                        el.innerHTML = "⚠ Trễ món";
                        el.classList.add("danger");

                        let row = el.closest("tr");
                        if (row) row.classList.add("tre-mon");

                        return;
                    }

                    let minutes = Math.floor(diff / 60000);
                    let seconds = Math.floor((diff % 60000) / 1000);

                    el.classList.remove("warning", "danger");

                    if (minutes <= 2) el.classList.add("danger");
                    else if (minutes <= 5) el.classList.add("warning");

                    el.innerHTML = `⏳ ${minutes}:${seconds.toString().padStart(2,'0')}`;
                });
            }

            // ✅ CHẠY NGAY KHI LOAD PAGE
            updateCountdown();
            setInterval(updateCountdown, 1000);

        });
    </script>


    <div id="delayAlert" class="delay-alert">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span id="delayText"></span>
    </div>
    <audio id="delaySound" preload="auto">
        <source src="/sounds/alert.mp3" type="audio/mpeg">
    </audio>
</main>
@endsection