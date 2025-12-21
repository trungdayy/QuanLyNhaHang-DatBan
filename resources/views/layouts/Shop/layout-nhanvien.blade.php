<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    {{-- Title động, mặc định là Nhân viên... --}}
    <title>@yield('title', 'Nhân viên - Restoran')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Buffet Ocean, nhà hàng hải sản, đặt bàn online" name="keywords">
    <meta content="Trang quản lý dành cho nhân viên của nhà hàng Buffet Ocean." name="description">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="img/favicon.ico" rel="icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap"
        rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="{{ asset('restaurant/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('restaurant/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('restaurant/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('restaurant/css/bootstrap.min.css') }}" rel="stylesheet">

    <link href="{{ asset('restaurant/css/style.css') }}" rel="stylesheet">

    {{-- BỔ SUNG: CSS tùy chỉnh cho Dropdown --}}
    <style>
        .dropdown-menu-end {
            right: 0;
            left: auto;
        }

        /* Đảm bảo dropdown menu nằm trên các nội dung khác */
        .navbar .nav-item.dropdown {
            z-index: 1050;
        }
    </style>
</head>

<body>
    <div class="container-xxl bg-white p-0">
        {{-- SPINNER --}}
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Đang tải...</span>
            </div>
        </div>

        {{-- NAVBAR START --}}
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
                @php
                    $user = Auth::user();
                    $isLeTan = $user && $user->vai_tro === 'le_tan';
                    $logoLink = $isLeTan ? route('nhanVien.ban-an.index') : route('nhanVien.order.index');
                @endphp
                <a href="{{ $logoLink }}" class="navbar-brand p-0">
                    <h1 class="text-primary m-0"><i class="fa fa-utensils me-3"></i>Buffet Ocean</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        @php
                            $user = Auth::user();
                            $isLeTan = $user && $user->vai_tro === 'le_tan';
                        @endphp
                        
                        @if($isLeTan)
                            {{-- Menu cho lễ tân - Quản lý bàn ăn và Đặt bàn --}}
                            <a href="{{ route('nhanVien.ban-an.index') }}"
                                class="nav-item nav-link {{ request()->routeIs('nhanVien.ban-an.*') ? 'active' : '' }}">Bàn ăn</a>
                            
                            <a href="{{ route('nhanVien.datban.index') }}"
                                class="nav-item nav-link {{ request()->routeIs('nhanVien.datban.*') ? 'active' : '' }}">Đặt bàn</a>
                        @else
                            {{-- Menu cho nhân viên phục vụ - Order và Hàng chờ phục vụ --}}
                            <a href="{{ route('nhanVien.order.index') }}"
                                class="nav-item nav-link {{ request()->routeIs('nhanVien.order.*') ? 'active' : '' }}">Order</a>

                            <a href="{{ route('nhanVien.phuc-vu.dashboard') }}"
                                class="nav-item nav-link {{ request()->routeIs('nhanVien.phuc-vu.dashboard') ? 'active' : '' }}">Hàng Chờ Phục Vụ</a>
                        @endif
                    </div>

                    @if (Auth::check())
                        <div class="nav-item dropdown">
                            <a href="#" class="btn btn-warning py-2 px-4 dropdown-toggle shadow-sm d-flex align-items-center"
                                data-bs-toggle="dropdown" aria-expanded="false" title="Tài khoản nhân viên">
                                @php
                                    $user = Auth::user();
                                    $avatarUrl = $user->hinh_anh ? asset($user->hinh_anh) : asset('restaurant/img/default-avatar.png');
                                @endphp
                                <img src="{{ $avatarUrl }}" alt="{{ $user->ho_ten }}" 
                                     style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; margin-right: 8px;">
                                <span>{{ $user->ho_ten }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                                style="background-color: #343a40;">

                                <li class="px-3 pt-2 pb-2 text-center border-bottom border-secondary">
                                    <img src="{{ $avatarUrl }}" alt="{{ $user->ho_ten }}" 
                                         style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #ffc107;">
                                    <div class="text-white mt-2"><strong>{{ $user->ho_ten }}</strong></div>
                                </li>

                                <li><span
                                        class="dropdown-item text-muted small px-3 pt-2 pb-0 border-bottom border-secondary"
                                        style="font-size: 0.85em;">
                                        <i class="fas fa-id-badge me-2"></i> Vai trò: **{{ $user->vai_tro }}**
                                    </span></li>

                                <li><span
                                        class="dropdown-item text-white-50 px-3 pt-0 pb-2 border-bottom border-secondary"
                                        style="font-size: 0.85em;">
                                        <i class="fas fa-envelope me-2"></i> {{ $user->email }}
                                    </span></li>

                                <li>
                                    <a class="dropdown-item text-danger fw-bold" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form-nhanvien').submit();">
                                        <i class="fa fa-sign-out-alt me-2"></i> Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary py-2 px-4">Đăng nhập</a>
                    @endif
                    {{-- Form ẩn để thực hiện POST request --}}
                    <form id="logout-form-nhanvien" action="{{ route('logout') }}" method="POST"
                        style="display: none;">
                        @csrf
                    </form>

                </div>
            </nav>
        </div>
        {{-- NAVBAR END --}}

        {{-- HERO HEADER START --}}
        <div class="container-xxl py-5 bg-dark hero-header mb-5">
            <div class="container text-center my-5 pt-5 pb-4">
                {{-- Hiển thị tiêu đề trang con --}}
                <h1 class="display-3 text-white mb-3 animated slideInDown">
                    @yield('title')
                </h1>
                {{-- Breadcrumb để điều hướng --}}
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center text-uppercase">
                        <li class="breadcrumb-item"><a href="#">Nhân viên</a></li>
                        <li class="breadcrumb-item text-white active" aria-current="page">@yield('title')</li>
                    </ol>
                </nav>
            </div>
        </div>
        {{-- HERO HEADER END --}}

        {{-- CONTENT START --}}
        {{-- Bọc trong container để nội dung không bị dính sát lề --}}
        <div class="container-xxl py-5">
            <div class="container">
                @yield('content')
            </div>
        </div>
        {{-- CONTENT END --}}

        {{-- FOOTER START --}}
        <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn d-none d-md-block"
            data-wow-delay="0.1s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-lg-3 col-md-6">
                        <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Công ty</h4>
                        <a class="btn btn-link" href="">Về chúng tôi</a>
                        <a class="btn btn-link" href="">Liên hệ</a>
                        <a class="btn btn-link" href="">Đặt bàn</a>
                        <a class="btn btn-link" href="">Chính sách bảo mật</a>
                        <a class="btn btn-link" href="">Điều khoản & Điều kiện</a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Liên hệ</h4>
                        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>123 Đường, New York, USA</p>
                        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+012 345 67890</p>
                        <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@example.com</p>
                        <div class="d-flex pt-2">
                            <a class="btn btn-outline-light btn-social" href=""><i
                                    class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i
                                    class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i
                                    class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Giờ mở cửa</h4>
                        <h5 class="text-light fw-normal">Thứ Hai - Thứ Bảy</h5>
                        <p>09AM - 09PM</p>
                        <h5 class="text-light fw-normal">Chủ Nhật</h5>
                        <p>10AM - 08PM</p>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Buffet Ocean</h4>
                        <p>Nhà hàng hải sản buffet hàng đầu, mang đến cho thực khách trải nghiệm ẩm thực tươi ngon và đa
                            dạng. Cam kết chất lượng sản phẩm và dịch vụ tốt nhất.</p>

                    </div>
                </div>
            </div>
            <div class="container">
                <div class="copyright">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                            &copy; <a class="border-bottom" href="#">Tên Trang Web Của Bạn</a>, Mọi quyền được
                            bảo lưu.
                            Thiết kế bởi <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a><br><br>
                            Phân phối bởi <a class="border-bottom" href="https://themewagon.com"
                                target="_blank">ThemeWagon</a>
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <div class="footer-menu">
                                <a href="{{ route('home') }}">Trang chủ</a>
                                <a href="{{ route('login') }}">Đăng nhập</a>
                                <a href="">Cookies</a>
                                <a href="">Trợ giúp</a>
                                <a href="">FAQs</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
        {{-- FOOTER END --}}
    </div>

    {{-- SCRIPT LIBS --}}
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="{{ asset('restaurant/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <script src="{{ asset('restaurant/js/main.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lấy CSRF token từ meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            // Thiết lập cho tất cả các request Axios
            if (typeof axios !== 'undefined') {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
                axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
            }
        });
    </script>


    {{-- Chỗ này để các trang con push thêm JS riêng nếu cần --}}
    @stack('scripts')
</body>

</html>
