<!DOCTYPE html>
<html lang="vi"> {{-- Đổi ngôn ngữ thành tiếng Việt --}}

<head>
    <meta charset="utf-8">
    {{-- Title động, mặc định là Nhân viên... --}}
    <title>@yield('title', 'Nhân viên - Restoran')</title> 
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Buffet Ocean, nhà hàng hải sản, đặt bàn online" name="keywords"> {{-- Việt hóa Keywords --}}
    <meta content="Trang quản lý dành cho nhân viên của nhà hàng Buffet Ocean." name="description"> {{-- Việt hóa Description --}}

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
</head>

<body>
    <div class="container-xxl bg-white p-0">
        {{-- SPINNER --}}
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Đang tải...</span> {{-- Việt hóa "Loading..." --}}
            </div>
        </div>
        
        {{-- NAVBAR START --}}
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
                <a href="#" class="navbar-brand p-0">
                    <h1 class="text-primary m-0"><i class="fa fa-utensils me-3"></i>Buffet Ocean</h1>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav ms-auto py-0 pe-4">
                        {{-- Các route chức năng của nhân viên --}}
                        <a href="{{ route('nhanVien.ban-an.index') }}"
                            class="nav-item nav-link {{ request()->routeIs('nhanVien.ban-an.*') ? 'active' : '' }}">Bàn ăn</a>
                        <a href="{{ route('nhanVien.order.index') }}"
                            class="nav-item nav-link {{ request()->routeIs('nhanVien.order.*') ? 'active' : '' }}">Tạo Order</a> {{-- Rút gọn và Việt hóa --}}
                        <a href="{{ route('nhanVien.datban.index') }}"
                            class="nav-item nav-link {{ request()->routeIs('nhanVien.datban.*') ? 'active' : '' }}">Quản lý Đặt bàn</a> {{-- Rút gọn và Việt hóa --}}
                    </div>
                    
                    {{-- Nút Logout --}}
                    <a href="#" class="btn btn-primary py-2 px-4"
                        onclick="event.preventDefault(); document.getElementById('logout-form-nhanvien').submit();">
                        <i class="fa fa-sign-out-alt me-2"></i> Đăng xuất
                    </a>

                    {{-- Form ẩn để thực hiện POST request --}}
                    <form id="logout-form-nhanvien" action="{{ route('logout') }}" method="POST" style="display: none;">
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
        <div class="container-fluid bg-dark text-light footer pt-5 mt-5 wow fadeIn" data-wow-delay="0.1s">
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
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-youtube"></i></a>
                            <a class="btn btn-outline-light btn-social" href=""><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Giờ mở cửa</h4>
                        <h5 class="text-light fw-normal">Thứ Hai - Thứ Bảy</h5>
                        <p>09AM - 09PM</p>
                        <h5 class="text-light fw-normal">Chủ Nhật</h5>
                        <p>10AM - 08PM</p>
                    </div>
                    {{-- Đã thay thế phần Newsletter/Đăng ký bằng Giới thiệu tóm tắt (theo yêu cầu trước đó) --}}
                    <div class="col-lg-3 col-md-6">
                        <h4 class="section-title ff-secondary text-start text-primary fw-normal mb-4">Buffet Ocean</h4>
                        <p>Nhà hàng hải sản buffet hàng đầu, mang đến cho thực khách trải nghiệm ẩm thực tươi ngon và đa dạng. Cam kết chất lượng sản phẩm và dịch vụ tốt nhất.</p>

                    </div>
                </div>
            </div>
            <div class="container">
                <div class="copyright">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                            &copy; <a class="border-bottom" href="#">Tên Trang Web Của Bạn</a>, Mọi quyền được bảo lưu. {{-- Việt hóa --}}
                            Thiết kế bởi <a class="border-bottom" href="https://htmlcodex.com">HTML Codex</a><br><br> {{-- Giữ nguyên tên tác giả --}}
                            Phân phối bởi <a class="border-bottom" href="https://themewagon.com"
                                target="_blank">ThemeWagon</a> {{-- Giữ nguyên tên tác giả --}}
                        </div>
                        <div class="col-md-6 text-center text-md-end">
                            <div class="footer-menu">
                                <a href="">Trang chủ</a> {{-- Việt hóa --}}
                                <a href="">Cookies</a>
                                <a href="">Trợ giúp</a> {{-- Việt hóa --}}
                                <a href="">FAQs</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a> {{-- Nút quay lại đầu trang --}}
        {{-- FOOTER END --}}
    </div>

    {{-- SCRIPT LIBS --}}
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('restaurant/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('restaurant/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <script src="{{ asset('restaurant/js/main.js') }}"></script>

    {{-- Chỗ này để các trang con push thêm JS riêng nếu cần --}}
    @stack('scripts')
</body>

</html>