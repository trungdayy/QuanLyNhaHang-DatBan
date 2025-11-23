@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Danh sách bàn')

@section('content')
<main class="app-content">
    @if(session('success'))
    <div class="alert alert-success text-center fw-semibold rounded-3 shadow-sm" id="flashMsg">
        {{ session('success') }}
    </div>
    @endif

    <div class="app-title d-flex justify-content-between align-items-center mb-4 p-3 rounded-4 shadow-sm"
        style="background: linear-gradient(135deg, #ffffff, #f0f2f5);">

        <h2 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-columns-gap me-2 text-primary"></i>
            Danh sách bàn
        </h2>
    </div>

    <div class="row g-3">
        @foreach($bans as $ban)
        @php
        $order = $orders->has($ban->id) ? $orders[$ban->id] : null;
        if($ban->trang_thai == 'trong') {
        $bgHeader = 'linear-gradient(135deg, #28a745, #7be495)'; // xanh lá
        $icon = 'bi-person-check';
        } elseif($ban->trang_thai == 'dang_phuc_vu') {
        $bgHeader = 'linear-gradient(135deg, #dc3545, #ff6b6b)'; // đỏ
        $icon = 'bi-people-fill';
        } else {
        $bgHeader = 'linear-gradient(135deg, #ffc107, #ffe58a)'; // vàng
        $icon = 'bi-tools';
        }
        @endphp

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
            <div class="card table-card shadow-sm rounded-4 border-0 position-relative overflow-hidden">

                {{-- Header trạng thái --}}
                <div class="table-card-header text-center text-white fw-bold py-3 rounded-top"
                    style="background: {{ $bgHeader }};">
                    <h5 class="mb-1"><i class="bi {{ $icon }}"></i> Bàn {{ $ban->so_ban }}</h5>
                    <small class="opacity-75">
                        @if($order)
                        Đang phục vụ
                        @else
                        Trống
                        @endif
                    </small>
                </div>

                {{-- Thân card --}}
                <div class="card-body text-center py-4">
                    @if($order)
                    <p class="mb-2 text-truncate"><i class="bi bi-receipt"></i> <b>Order #{{ $order->id }}</b></p>
                    <p class="mb-2"><i class="bi bi-basket3"></i> Tổng món: <b>{{ $order->tong_mon }}</b></p>
                    <p class="mb-3"><i class="bi bi-currency-dollar"></i> <b>{{ number_format($order->tong_tien) }} đ</b></p>

                    {{-- Icon chi tiết / thêm món --}}
                    <a href="{{ route('nhanVien.order.page', $order->id) }}"
                        class="btn btn-warning btn-lg rounded-circle shadow-sm" style="width:50px; height:50px; padding:0; display:flex; align-items:center; justify-content:center;">
                        <i class="bi bi-card-checklist fs-5"></i>
                    </a>
                    @else
                    {{-- Icon mở order --}}
                    <form action="{{ route('nhanVien.order.mo-order') }}" method="POST">
                        @csrf
                        <input type="hidden" name="ban_id" value="{{ $ban->id }}">
                        <button class="btn btn-success btn-lg rounded-circle shadow-sm" type="submit" style="width:50px; height:50px; padding:0; display:flex; align-items:center; justify-content:center;">
                            <i class="bi bi-plus-circle fs-5"></i>
                        </button>
                    </form>
                    @endif
                </div>

                {{-- Footer trạng thái nhỏ --}}
                <div class="table-card-footer position-absolute bottom-0 start-0 w-100 text-center py-1 text-white opacity-75"
                    style="font-size: 0.75rem;">
                    @if($order)
                    ID: {{ $order->id }} | {{ $order->tong_mon }} món
                    @else
                    Sẵn sàng phục vụ
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>
</main>

{{-- Hover & animation --}}
 {{-- <style>
        /* ===============================
           Spinner
        ================================= */
        #spinner {
            z-index: 1051; /* cao hơn navbar */
            background-color: rgba(255, 255, 255, 0.9);
        }

        /* ===============================
           Navbar
        ================================= */
        .navbar {
            transition: all 0.4s;
        }

        .navbar-brand h1 {
            font-family: 'Pacifico', cursive;
            font-size: 28px;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: #fff;
            margin-left: 15px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar-dark .navbar-nav .nav-link:hover,
        .navbar-dark .navbar-nav .nav-link.active {
            color: #ffc107; /* màu vàng nổi bật */
        }

        .navbar-toggler {
            border: none;
        }

        .navbar-toggler:focus {
            outline: none;
            box-shadow: none;
        }

        /* ===============================
           Hero Header
        ================================= */
        .hero-header {
            position: relative;
            background: url('../img/hero-bg.jpg') center center no-repeat;
            background-size: cover;
            min-height: 400px;
        }

        .hero-header h1 {
            font-size: 55px;
            font-weight: 700;
            line-height: 1.2;
        }

        /* ===============================
           Footer
        ================================= */
        .footer {
            font-size: 14px;
        }

        .footer .section-title {
            font-size: 18px;
            letter-spacing: 1px;
        }

        .footer a {
            color: #fff;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: #ffc107;
            text-decoration: none;
        }

        .footer .btn-social {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            padding: 0;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            transition: all 0.3s;
        }

        .footer .btn-social:hover {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #fff;
        }

        .back-to-top {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 999;
            display: none;
            border-radius: 50%;
            padding: 10px 15px;
        }

        /* ===============================
           Responsive
        ================================= */
        @media (max-width: 991px) {
            .hero-header h1 {
                font-size: 35px;
            }

            .navbar-nav .nav-link {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style> --}}
@endsection
