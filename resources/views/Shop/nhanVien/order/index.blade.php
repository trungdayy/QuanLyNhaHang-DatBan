@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Danh sách bàn')

@section('content')
<main class="app-content">
    <div class="container-xxl px-4">
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

        {{-- Phân khu vực --}}
        @foreach($khuVucs as $khu)
        <div class="mb-4">
            <h3 class="fw-bold mb-3">
                <i class="bi bi-building me-2"></i> {{ $khu->ten_khu_vuc }} (Tầng {{ $khu->tang }})
            </h3>
            <div class="row g-3 justify-content-start">
                @foreach($bans->where('khu_vuc_id', $khu->id) as $ban)
                @php
                $order = $orders->has($ban->id) ? $orders[$ban->id] : null;
                $datBanMoiNhat = \App\Models\DatBan::where('ban_id', $ban->id)->latest()->first();
                if($ban->trang_thai == 'trong') {
                $bgHeader = 'linear-gradient(135deg, #28a745, #7be495)';
                $icon='bi-person-check';
                } elseif($ban->trang_thai == 'dang_phuc_vu') {
                $bgHeader='linear-gradient(135deg, #dc3545, #ff6b6b)';
                $icon='bi-people-fill';
                } elseif($ban->trang_thai == 'khong_su_dung') {
                $bgHeader='linear-gradient(135deg, #6c757d, #adb5bd)'; // xám
                $icon='bi-slash-circle';
                } else {
                $bgHeader='linear-gradient(135deg, #ffc107, #ffe58a)';
                $icon='bi-tools';
                }

                @endphp

                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 d-flex">
                    <div class="card table-card shadow-sm rounded-4 border-0 position-relative overflow-hidden flex-fill d-flex flex-column">
                        <div class="table-card-header text-center text-white fw-bold py-2 rounded-top"
                            style="background: {{ $bgHeader }};">
                            <h5 class="mb-1"><i class="bi {{ $icon }}"></i> {{ $ban->so_ban }}</h5>
                            <div class="mt-2 w-100 text-center">
                                @php
                                if($ban->trang_thai == 'khong_su_dung') {
                                $trangThaiText = 'Bảo trì';
                                $trangThaiClass = 'bg-dark text-white';
                                } elseif(isset($datBanMoiNhat)) {
                                switch($datBanMoiNhat->trang_thai) {
                                case 'da_xac_nhan':
                                $trangThaiText='Đã đặt';
                                $trangThaiClass='bg-warning text-dark';
                                break;
                                case 'khach_da_den':
                                $trangThaiText = $order ? 'Đang phục vụ':'Khách đã đến';
                                $trangThaiClass = $order ? 'bg-success text-white':'bg-info text-white';
                                break;
                                default:
                                $trangThaiText = 'Trống';
                                $trangThaiClass = 'bg-secondary';
                                break;
                                }
                                } else {
                                $trangThaiText = 'Trống';
                                $trangThaiClass = 'bg-secondary';
                                }
                                @endphp
                                <span class="badge {{ $trangThaiClass }}">{{ $trangThaiText }}</span>

                            </div>
                        </div>

                        <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                            @if($order)
                            <p class="mb-1 text-truncate"><i class="bi bi-receipt"></i> <b>Order: {{ $order->id }}</b></p>
                            @if($order->datBan)
                            <p class="mb-1"><i class="bi bi-person-fill"></i> {{ $order->datBan->ten_khach }}</p>
                            <p class="mb-1"><i class="bi bi-telephone-fill"></i> {{ $order->datBan->sdt_khach }}</p>
                            @endif
                            <p class="mb-1"><i class="bi bi-basket3"></i> {{ $order->tong_mon }} món</p>

                            <a href="{{ route('nhanVien.order.page', $order->id) }}"
                                class="btn btn-warning btn-lg rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                style="width:50px; height:50px; padding:0;">
                                <i class="bi bi-card-checklist fs-5"></i>
                            </a>
                            @else
                            <form action="{{ route('nhanVien.order.mo-order') }}" method="POST" class="w-100 d-flex justify-content-center">
                                @csrf
                                <input type="hidden" name="ban_id" value="{{ $ban->id }}">
                                <button type="submit"
                                    class="btn btn-success btn-lg rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                    style="width:50px; height:50px;">
                                    <i class="bi bi-plus-circle fs-5"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
</main>

<style>
    /* =============================== */
    /* GLOBAL & ANIMATION */
    /* =============================== */

    body {
        /* Giữ nguyên màu nền body đẹp */
        background: radial-gradient(circle at top right, #e3f2ff, #f7f9fc, #f1f5ff);
    }

    .app-content {
        animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* =============================== */
    /* MINI DASHBOARD - GIỮ NGUYÊN MÀU TỪ BLADE */
    /* =============================== */

    .row.mb-4 .col {
        flex: 1;
        min-width: 0;
    }

    .row.mb-4 .rounded-4 {
        /* Loại bỏ các lớp background bị ghi đè để dùng màu từ style="" */
        color: inherit;
        /* Đảm bảo màu chữ tự động đổi tùy theo background */
        border-radius: 18px !important;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
    }

    /* Thêm màu chữ tương phản cho từng thẻ dashboard nếu màu nền quá sáng */
    .row.mb-4 .col:nth-child(1) .rounded-4,
    /* Tổng số bàn (Xanh lá) */
    .row.mb-4 .col:nth-child(2) .rounded-4 {
        /* Bàn trống (Xám) */
        color: #333 !important;
        /* Đặt màu chữ đậm để nổi bật trên nền nhạt */
    }

    .row.mb-4 .rounded-4:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }

    .row.mb-4 h5 {
        font-size: 1.4rem;
        letter-spacing: 0.3px;
    }

    .row.mb-4 small {
        opacity: 0.8;
        font-size: 0.8rem;
    }

    /* =============================== */
    /* TABLE CARD */
    /* =============================== */

    .table-card {
        border-radius: 18px !important;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: all 0.25s ease;
        position: relative;
        z-index: 1;
        cursor: pointer;
    }

    .table-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 14px 35px rgba(0, 0, 0, 0.18);
    }

    /* Loại bỏ border gradient để không ghi đè lên hover glow */
    /* .table-card::before { ... } */

    /* =============================== */
    /* TABLE CARD HEADER - GIỮ NGUYÊN MÀU TỪ BLADE */
    /* =============================== */

    .table-card-header {
        position: relative;
        border-bottom-left-radius: 18px;
        border-bottom-right-radius: 18px;
        padding: 14px 10px 10px;
        z-index: 2;
    }

    /* Thêm lớp phủ mờ nhẹ cho header để hiệu ứng gradient đẹp hơn */
    .table-card-header::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.1);
        mix-blend-mode: overlay;
    }

    .table-card-header h5 {
        letter-spacing: 0.5px;
    }

    /* Hiệu ứng ánh sáng hover trên header */
    .table-card-header::before {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.45), transparent);
        transform: rotate(20deg);
        opacity: 0;
        transition: .6s;
        pointer-events: none;
    }

    .table-card:hover .table-card-header::before {
        opacity: 1;
        top: 50%;
        left: 50%;
    }

    /* =============================== */
    /* BADGE */
    /* =============================== */

    .table-card-header .badge {
        border-radius: 50px;
        font-weight: 600;
        padding: 5px 14px;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.15);
        font-size: 0.75rem;
        /* Đặt lại font-size cho badge */
    }

    /* =============================== */
    /* CARD BODY */
    /* =============================== */

    .card-body {
        text-align: center;
        padding: 1.5rem 1rem !important;
        /* Căn chỉnh lại padding cho đẹp */
        position: relative;
        z-index: 5;
    }

    .card-body p {
        font-size: 0.85rem;
        opacity: 0.85;
        margin-bottom: 0.25rem;
    }

    .card-body p i {
        color: #0d6efd;
    }

    .card-body p b {
        font-size: 0.9rem;
    }

    /* =============================== */
    /* BUTTON (ACTION) */
    /* =============================== */

    .card-body .btn {
        border-radius: 50px !important;
        transition: all 0.25s cubic-bezier(.4, 1.8, .6, .9);
        width: 50px;
        height: 50px;
        padding: 0;
        border: none;
        /* Loại bỏ border cho nút */

        /* Đặt lại màu sắc nút để nó nổi bật, không dùng màu gradient bị ghi đè */
        /* Nút mở Order (màu xanh lá) */
        &.btn-success {
            background: linear-gradient(135deg, #28a745, #157347) !important;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.6) !important;
            color: white;
        }

        /* Nút xem Order (màu vàng) */
        &.btn-warning {
            background: linear-gradient(135deg, #ffc107, #d49500) !important;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.6) !important;
            color: #333;
        }
    }

    .card-body .btn:hover {
        transform: scale(1.15) rotate(3deg);
    }

    .card-body .btn:active {
        transform: scale(0.9);
    }

    /* =============================== */
    /* GLOW BY STATUS (Hover Shadow) */
    /* =============================== */

    .table-card:has(.badge.bg-success):hover {
        box-shadow: 0 14px 35px rgba(40, 167, 69, 0.3) !important;
    }

    .table-card:has(.badge.bg-warning):hover {
        box-shadow: 0 14px 35px rgba(255, 193, 7, 0.4) !important;
    }

    .table-card:has(.badge.bg-info):hover {
        box-shadow: 0 14px 35px rgba(23, 162, 184, 0.4) !important;
    }

    .table-card:has(.badge.bg-dark) {
        opacity: 0.75;
        filter: grayscale(50%);
        /* Giảm nhẹ hiệu ứng xám */
    }

    /* =============================== */
    /* NEW ORDER ANIMATION */
    /* =============================== */

    .table-card.new-order {
        animation: pulseGlow 1.2s infinite alternate;
    }

    @keyframes pulseGlow {
        from {
            box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
        }

        to {
            box-shadow: 0 0 30px rgba(255, 193, 7, 0.9);
        }
    }

    /* =============================== */
    /* SECTION TITLE (H3) */
    /* =============================== */

    h3 {
        position: relative;
        display: inline-block;
        background: linear-gradient(90deg, #333333, #666666);
        /* Tông xám đen */
        color: white;
        padding: 8px 18px;
        border-radius: 50px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .2);
        margin-bottom: 1.5rem !important;
        /* Thêm khoảng cách dưới cho đẹp */
        font-size: 1.35rem;
    }

    h3 i.bi {
        color: #ffc107;
        /* Đổi màu icon thành vàng nổi bật */
    }

    h3::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 80%;
        width: 8px;
        border-radius: 50px;
        background: #ffc107;
        /* Màu trang trí ở đầu section */
    }

    /* =============================== */
    /* RESPONSIVE GRID (Giữ nguyên) */
    /* =============================== */

    /* Responsive 6 cột */
    @media (min-width: 1200px) {

        /* LỚP XL-2 CÓ SẴN TRONG HTML */
        .col-xl-2 {
            flex: 0 0 16.666667%;
            max-width: 16.666667%;
        }
    }

    @media (min-width: 992px) and (max-width: 1199.98px) {
        .col-xl-2 {
            flex: 0 0 20%;
            max-width: 20%;
        }
    }

    @media (max-width: 991.98px) {
        .col-lg-3 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }

    @media (max-width: 767.98px) {

        .col-md-4,
        .col-lg-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 575.98px) {

        .col-sm-6,
        .col-md-4,
        .col-lg-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>
@endsection