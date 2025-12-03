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

        {{-- Mini Dashboard --}}
        <div class="row mb-4 g-3">
            @php
            $dashboardItems = [
            ['label'=>'Tổng số bàn', 'count'=>$bans->count(), 'bg'=>'#28a74533', 'icon'=>'bi-grid-3x3-gap'],
            ['label'=>'Bàn trống', 'count'=>$bans->where('trang_thai', 'trong')->count(), 'bg'=>'#6c757d33', 'icon'=>'bi-person-check'],
            ['label'=>'Đang phục vụ', 'count'=>$bans->where('trang_thai', 'dang_phuc_vu')->count(), 'bg'=>'#dc354533', 'icon'=>'bi-people-fill'],
            ['label'=>'Khách đến & chưa chọn combo', 'count'=>$bans->filter(function($ban) use ($orders) {
            $datBanMoiNhat = \App\Models\DatBan::where('ban_id', $ban->id)->latest()->first();
            return $datBanMoiNhat && $datBanMoiNhat->trang_thai == 'khach_da_den' && !$orders->has($ban->id);
            })->count(), 'bg'=>'#17a2b833', 'icon'=>'bi-person-plus'],
            ['label'=>'Bàn bảo trì', 'count'=>$bans->where('trang_thai', 'khong_su_dung')->count(), 'bg'=>'#6c757d88', 'icon'=>'bi-tools']
            ];
            @endphp

            @foreach($dashboardItems as $item)
            <div class="col d-flex">
                <div class="p-3 rounded-4 shadow-sm text-center flex-fill" style="background: {{ $item['bg'] }}">
                    <h5 class="fw-bold mb-1">
                        <i class="bi {{ $item['icon'] }}"></i> {{ $item['count'] }}
                    </h5>
                    <small>{{ $item['label'] }}</small>
                </div>
            </div>
            @endforeach
        </div>

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
                            <h5 class="mb-1"><i class="bi {{ $icon }}"></i> Bàn {{ $ban->so_ban }}</h5>
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
                            <p class="mb-2"><i class="bi bi-currency-dollar"></i> {{ number_format($order->tong_tien) }} đ</p>

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
    .table-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }

    .table-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .table-card-header .badge {
        font-size: 0.75rem;
        padding: 0.25em 0.6em;
        border-radius: 12px;
    }

    .card-body p {
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }

    .card-body .btn {
        transition: all 0.2s;
        background: linear-gradient(135deg, #0dcaf0, #dbbf0cff);
        color: white;
        border: none;
        box-shadow: 0 0 10px rgba(13, 202, 240, .6);
    }

    .card-body .btn:hover {
        transform: scale(1.1);
        box-shadow: 0 0 25px rgba(13, 202, 240, .9);
    }

    .row.mb-4>.col {
        flex: 1;
        min-width: 0;
    }

    /* Responsive 6 cột */
    @media (max-width: 1200px) {
        .col-xl-2 {
            flex: 0 0 16.666667%;
            max-width: 16.666667%;
        }
    }

    @media (max-width: 992px) {
        .col-lg-3 {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }

    @media (max-width: 768px) {
        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }

    @media (max-width: 576px) {
        .col-sm-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    .table-card.new-order {
        animation: blink 1s infinite;
    }

    @keyframes blink {
        0% {
            box-shadow: 0 0 0 rgba(255, 193, 7, 0.8);
        }

        50% {
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.9);
        }

        100% {
            box-shadow: 0 0 0 rgba(255, 193, 7, 0.8);
        }
    }

    body {
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
    /* MINI DASHBOARD */
    /* =============================== */

    .row.mb-4 .rounded-4 {
        border-radius: 18px !important;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
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
    }

    .table-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 14px 35px rgba(0, 0, 0, 0.18);
    }

    /* =============================== */
    /* HEADER */
    /* =============================== */

    .table-card-header {
        position: relative;
        border-bottom-left-radius: 18px;
        border-bottom-right-radius: 18px;
        padding: 14px 10px 10px;
    }

    .table-card-header::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.12);
        mix-blend-mode: overlay;
    }

    .table-card-header h5 {
        letter-spacing: 0.5px;
    }

    /* =============================== */
    /* BADGE */
    /* =============================== */

    .table-card-header .badge {
        border-radius: 50px;
        font-weight: 600;
        padding: 5px 14px;
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.15);
    }

    /* =============================== */
    /* BODY */
    /* =============================== */

    .card-body {
        text-align: center;
    }

    .card-body p {
        font-size: 0.85rem;
        opacity: 0.85;
    }

    .card-body p i {
        color: #0d6efd;
    }

    .card-body p b {
        font-size: 0.9rem;
    }

    /* =============================== */
    /* BUTTON */
    /* =============================== */

    .card-body .btn {
        border-radius: 50px !important;
        transition: all 0.25s cubic-bezier(.4, 1.8, .6, .9);
    }

    .card-body .btn:hover {
        transform: scale(1.15) rotate(3deg);
    }

    .card-body .btn:active {
        transform: scale(0.9);
    }

    /* =============================== */
    /* GLOW BY STATUS */
    /* =============================== */

    .table-card:has(.badge.bg-success) {
        box-shadow: 0 0 12px rgba(40, 167, 69, 0.4);
    }

    .table-card:has(.badge.bg-warning) {
        box-shadow: 0 0 12px rgba(255, 193, 7, 0.5);
    }

    .table-card:has(.badge.bg-info) {
        box-shadow: 0 0 12px rgba(23, 162, 184, 0.5);
    }

    .table-card:has(.badge.bg-dark) {
        opacity: 0.75;
        filter: grayscale(70%);
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
    /* SECTION TITLE */
    /* =============================== */

    h3 {
        position: relative;
        display: inline-block;
        padding-left: 10px;
        background: linear-gradient(90deg, #000000ff, #d8b00fff);
        color: white;
        padding: 8px 18px;
        border-radius: 50px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .2);
    }

    h3::before {
        content: '';
        position: absolute;
        left: 0;
        top: 6px;
        height: 60%;
        width: 4px;
        border-radius: 10px;
    }

    /* =============================== */
    /* MOBILE */
    /* =============================== */

    @media (max-width: 768px) {
        .table-card-header h5 {
            font-size: 0.95rem;
        }
    }

    .row.mb-4 .col:nth-child(1) .rounded-4 {
        background: linear-gradient(135deg, #00c853, #b2ff59) !important;
        color: #064d2a;
    }

    .row.mb-4 .col:nth-child(2) .rounded-4 {
        background: linear-gradient(135deg, #90a4ae, #eceff1) !important;
        color: #263238;
    }

    .row.mb-4 .col:nth-child(3) .rounded-4 {
        background: linear-gradient(135deg, #ff5252, #ff867c) !important;
        color: white;
    }

    .row.mb-4 .col:nth-child(4) .rounded-4 {
        background: linear-gradient(135deg, #40c4ff, #81d4fa) !important;
        color: white;
    }

    .row.mb-4 .col:nth-child(5) .rounded-4 {
        background: linear-gradient(135deg, #616161, #9e9e9e) !important;
        color: white;
    }

    .table-card::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 18px;
        padding: 1px;
        background: linear-gradient(140deg, #0dcaf0, #6610f2, #20c997);
        -webkit-mask:
            linear-gradient(#fff 0 0) content-box,
            linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0.5;
    }

    .table-card-header::before {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.35), transparent);
        transform: rotate(20deg);
        opacity: 0;
        transition: .6s;
    }

    .table-card:hover .table-card-header::before {
        opacity: 1;
        top: 50%;
        left: 50%;
    }

    .table-card::before,
    .table-card::after,
    .table-card-header::before,
    .table-card-header::after {
        pointer-events: none;
    }

    /* Đảm bảo button luôn nằm trên */
    .card-body,
    .card-body * {
        position: relative;
        z-index: 5;
    }

    /* Header vẫn đẹp nhưng không che */
    .table-card-header {
        position: relative;
        z-index: 2;
    }

    /* Card nằm đúng lớp dưới button */
    .table-card {
        position: relative;
        z-index: 1;
    }
</style>

@endsection