@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Danh sách bàn')

@section('content')
<main class="app-content">
    <div class="container-xxl px-4">
        {{-- Flash message --}}
        @if(session('success'))
        <div class="alert alert-success text-center fw-semibold rounded-3 shadow-sm mb-4" id="flashMsg">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
        @endif

        @if(session('warning'))
        <div class="alert alert-warning text-center fw-semibold rounded-3 shadow-sm mb-4" id="flashMsg">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger text-center fw-semibold rounded-3 shadow-sm mb-4" id="flashMsg">
            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
        </div>
        @endif

        {{-- TIÊU ĐỀ TRANG CHÍNH --}}
        <div class="d-flex justify-content-between align-items-center mb-5 mt-3">
            <div>
                <h4 class="page-header-title m-0">Sơ đồ bàn ăn</h4>
                <p class="text-muted small ms-3 mb-0 mt-1">Theo dõi trạng thái thực tế</p>
            </div>
        </div>

        {{-- Phân khu vực --}}
        @foreach($khuVucs as $khu)
        <div class="card-zone mb-5">
            {{-- Header Khu Vực --}}
            <div class="card-zone-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building me-2"></i> {{ $khu->ten_khu_vuc }}</span>
                <span class="badge bg-white text-dark shadow-sm" style="opacity: 0.9;">Tầng {{ $khu->tang }}</span>
            </div>

            {{-- Body Khu Vực --}}
            <div class="card-body p-4 bg-light-subtle">
                <div class="row g-3">
                    @foreach($bans->where('khu_vuc_id', $khu->id) as $ban)
                    @php
                        $order = $orders->has($ban->id) ? $orders[$ban->id] : null;
                        $datBanMoiNhat = \App\Models\DatBan::where('ban_id', $ban->id)->latest()->first();
                        
                        // Xác định trạng thái để tô màu
                        $cardClass = '';
                        $statusBadgeClass = '';
                        $statusText = '';
                        $iconClass = '';

                        if($ban->trang_thai == 'khong_su_dung') {
                            $cardClass = 'card-maintenance'; // Xám
                            $statusBadgeClass = 'badge-maintenance';
                            $statusText = 'Bảo trì';
                            $iconClass = 'bi-slash-circle';
                        } elseif(isset($datBanMoiNhat) && $datBanMoiNhat->trang_thai == 'da_xac_nhan') {
                            $cardClass = 'card-reserved'; // Vàng nhạt
                            $statusBadgeClass = 'badge-reserved';
                            $statusText = 'Đã đặt';
                            $iconClass = 'bi-clock-history';
                        } elseif(isset($datBanMoiNhat) && $datBanMoiNhat->trang_thai == 'khach_da_den') {
                            $cardClass = 'card-active'; // Cam nhạt (đang phục vụ)
                            $statusBadgeClass = 'badge-active';
                            $statusText = $order ? 'Đang phục vụ' : 'Khách đã đến';
                            $iconClass = 'bi-people-fill';
                        } else {
                            $cardClass = ''; // Trắng (Mặc định)
                            $statusBadgeClass = 'badge-free';
                            $statusText = 'Bàn trống';
                            $iconClass = 'bi-check-circle';
                        }
                    @endphp

                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 d-flex">
                        <div class="table-card {{ $cardClass }} flex-fill d-flex flex-column">
                            {{-- Icon nền trang trí --}}
                            <i class="bi bi-disc table-icon-bg"></i>

                            <div class="p-3 h-100 d-flex flex-column justify-content-between">
                                {{-- Phần trên: Số bàn + Số ghế --}}
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-2 position-relative" style="z-index: 2;">
                                        <div class="table-number">{{ $ban->so_ban }}</div>
                                        <div class="text-secondary fw-bold small">
                                            <i class="bi bi-grid-3x3-gap-fill text-primary"></i> 
                                            {{-- Bạn có thể thay bằng số ghế nếu có biến $ban->so_ghe --}}
                                        </div>
                                    </div>

                                    {{-- Badge Trạng thái --}}
                                    <div class="mb-3" style="position: relative; z-index: 2;">
                                        <span class="badge-custom {{ $statusBadgeClass }}">
                                            <i class="bi {{ $iconClass }} me-1"></i> {{ $statusText }}
                                        </span>
                                    </div>

                                    {{-- Thông tin Order / Khách --}}
                                    <div class="info-box" style="position: relative; z-index: 2;">
                                        @if($order)
                                            @if($order->datBan)
                                                <div class="fw-bold text-dark text-truncate mb-1">{{ $order->datBan->ten_khach }}</div>
                                            @endif
                                            <div class="small text-muted"><i class="bi bi-receipt"></i> Order #{{ $order->id }}</div>
                                            <div class="small text-muted"><i class="bi bi-basket3"></i> {{ $order->tong_mon }} món</div>
                                        @elseif(isset($datBanMoiNhat) && $datBanMoiNhat->trang_thai == 'da_xac_nhan')
                                            <div class="small fw-bold text-primary mb-1">SẮP ĐẾN:</div>
                                            <div class="fw-bold text-dark text-truncate">{{ $datBanMoiNhat->ten_khach }}</div>
                                            <div class="small text-danger mt-1">
                                                <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($datBanMoiNhat->gio_den)->format('H:i') }}
                                            </div>
                                        @else
                                            <div class="text-muted small fst-italic">Sẵn sàng đón khách</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="mt-3 text-center" style="position: relative; z-index: 2;">
                                    @if($order)
                                        <a href="{{ route('nhanVien.order.page', $order->id) }}" class="btn-ocean">
                                            <i class="bi bi-card-checklist"></i> Xem Order
                                        </a>
                                    @else
                                        @if($ban->trang_thai != 'khong_su_dung' && (!isset($datBanMoiNhat) || $datBanMoiNhat->trang_thai != 'da_xac_nhan'))
                                            <form action="{{ route('nhanVien.order.mo-order') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="ban_id" value="{{ $ban->id }}">
                                                <button type="submit" class="btn-ocean-outline">
                                                    <i class="bi bi-plus-lg"></i> Mở bàn
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</main>

<style>
    /* --- IMPORT FONTS & VARIABLES --- */
    @import url('https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;700;800&family=Nunito:wght@400;600;700&display=swap');

    :root {
        --primary: #fea116;
        --primary-hover: #db8a10;
        --dark: #0f172b;
        --light: #F1F8FF;
        --text-main: #1e293b;
        --white: #ffffff;
        --radius: 8px;
        --shadow-card: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    /* --- GLOBAL LAYOUT --- */
    body {
        font-family: 'Nunito', sans-serif;
        background-color: var(--light);
        color: var(--text-main);
    }

    .app-content {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* --- PAGE HEADER TITLE --- */
    .page-header-title {
        font-family: 'Heebo', sans-serif;
        font-weight: 800;
        color: var(--dark);
        text-transform: uppercase;
        position: relative;
        padding-left: 15px;
        font-size: 1.5rem;
    }
    .page-header-title::before {
        content: '';
        position: absolute;
        left: 0; top: 50%; transform: translateY(-50%);
        height: 70%; width: 5px;
        background-color: var(--primary);
        border-radius: 2px;
    }

    /* --- CARD ZONE (KHU VỰC) --- */
    .card-zone {
        background: var(--white);
        border-radius: var(--radius);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        border: none;
    }
    .card-zone-header {
        background: var(--dark);
        color: var(--primary);
        padding: 12px 20px;
        font-family: 'Heebo', sans-serif;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 3px solid var(--primary);
    }

    /* --- TABLE CARD (ITEM BÀN ĂN) --- */
    .table-card {
        background: var(--white);
        border-radius: var(--radius);
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        height: 100%;
        min-height: 180px;
    }

    .table-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: var(--primary);
    }

    /* Background Icon Trang Trí */
    .table-icon-bg {
        position: absolute;
        top: -15px; right: -15px;
        font-size: 5rem;
        color: rgba(254, 161, 22, 0.08);
        transform: rotate(20deg);
        pointer-events: none;
    }

    /* --- CARD STATUS COLORS --- */
    /* 1. Đang phục vụ (Có khách) */
    .card-active {
        background: #fff8e6; /* Vàng cam nhạt */
        border: 1px solid var(--primary);
    }
    .card-active .table-number { color: #d97706; }

    /* 2. Đã đặt trước */
    .card-reserved {
        background: #f0fdf4; /* Xanh lá rất nhạt */
        border: 1px dashed #22c55e;
    }
    
    /* 3. Bảo trì */
    .card-maintenance {
        background: #f1f5f9; /* Xám nhạt */
        border: 1px solid #cbd5e1;
        opacity: 0.8;
    }
    .card-maintenance .table-number { color: #64748b; }

    /* --- TYPOGRAPHY --- */
    .table-number {
        font-family: 'Heebo', sans-serif;
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--dark);
        line-height: 1;
    }

    .info-box {
        min-height: 40px; /* Giữ chiều cao để card đều nhau */
    }

    /* --- BADGES (Custom) --- */
    .badge-custom {
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.65rem;
        letter-spacing: 0.5px;
        display: inline-block;
    }
    .badge-free { background: #e2e8f0; color: #475569; }          /* Trống */
    .badge-active { background: var(--primary); color: #fff; }     /* Có khách */
    .badge-reserved { background: #22c55e; color: #fff; }          /* Đã đặt */
    .badge-maintenance { background: #64748b; color: #fff; }       /* Bảo trì */

    /* --- BUTTONS --- */
    .btn-ocean {
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 4px;
        font-weight: 700;
        padding: 8px;
        width: 100%;
        display: block;
        text-align: center;
        text-decoration: none;
        transition: 0.2s;
        font-size: 0.9rem;
    }
    .btn-ocean:hover {
        background: var(--primary-hover);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(254, 161, 22, 0.3);
    }

    .btn-ocean-outline {
        background: transparent;
        border: 2px solid var(--primary);
        color: var(--primary);
        border-radius: 4px;
        font-weight: 700;
        padding: 6px;
        width: 100%;
        display: block;
        text-align: center;
        text-decoration: none;
        transition: 0.2s;
        font-size: 0.9rem;
    }
    .btn-ocean-outline:hover {
        background: var(--primary);
        color: #fff;
    }

    /* --- RESPONSIVE ADJUSTMENTS --- */
    @media (max-width: 768px) {
        .table-number { font-size: 1.5rem; }
        .table-card { min-height: auto; }
    }
</style>
@endsection