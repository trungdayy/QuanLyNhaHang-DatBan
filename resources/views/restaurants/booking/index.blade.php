@extends('layouts.restaurants.layout-shop')

@section('title', 'Đặt Bàn & Tra Cứu')

@section('content')

{{-- =========================================================== --}}
{{-- 1. MODERN CSS --}}
{{-- =========================================================== --}}
<style>
    :root {
        --primary-color: #FEA116;
        --secondary-color: #0F172B;
        --bg-soft: #f8f9fa;
        --card-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
        --hover-shadow: 0 20px 60px -15px rgba(0, 0, 0, 0.12);
    }

    body {
        background-color: var(--bg-soft);
    }

    /* --- NÂNG CẤP HERO SECTION (PHƯƠNG ÁN 1) --- */
    .mini-hero {
        /* Ảnh nền tối tạo chiều sâu (Bạn có thể thay link ảnh khác) */
        background: linear-gradient(rgba(15, 23, 43, 0.9), rgba(15, 23, 43, 0.9)), url('https://technext.github.io/restoran/img/bg-hero.jpg');
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;

        padding: 100px 0 80px 0;
        /* Tăng chiều cao */
        margin-bottom: -60px;
        /* Đẩy phần dưới đè lên */
        color: white;
        position: relative;
        z-index: 0;
    }

    .mini-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50px;
        background: var(--bg-soft);
        clip-path: polygon(0 100%, 100% 100%, 100% 0);
        /* Cắt góc chéo hiện đại */
    }

    .hero-title {
        font-family: 'Nunito', sans-serif;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: 1px;
        text-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    /* Breadcrumb Custom */
    .breadcrumb-custom {
        background: transparent;
        padding: 0;
        justify-content: center;
        display: flex;
        list-style: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0;
    }

    .breadcrumb-custom li a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 700;
        transition: 0.3s;
    }

    .breadcrumb-custom li a:hover {
        color: white;
    }

    .breadcrumb-custom li {
        color: rgba(255, 255, 255, 0.7);
    }

    .breadcrumb-custom li+li::before {
        content: "/";
        padding: 0 10px;
        color: rgba(255, 255, 255, 0.5);
    }

    /* Modern Card */
    .glass-card {
        background: white;
        border: none;
        border-radius: 20px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    /* Floating Input Styling */
    .form-floating>.form-control {
        border-radius: 12px;
        border: 1px solid #eee;
        background-color: #fcfcfc;
    }

    .form-floating>.form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(254, 161, 22, 0.1);
        background-color: white;
    }

    /* Custom Button */
    .btn-gradient {
        background: linear-gradient(45deg, var(--primary-color), #ffb94d);
        border: none;
        color: white;
        font-weight: 700;
        border-radius: 12px;
        padding: 12px 30px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(254, 161, 22, 0.3);
        color: white;
    }

    /* Search Box Modern */
    .search-wrapper {
        position: relative;
    }

    .search-input {
        padding-left: 50px;
        height: 60px;
        border-radius: 50px;
        border: none;
        background: white;
        box-shadow: var(--card-shadow);
        font-size: 1.1rem;
    }

    .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #bbb;
        font-size: 1.2rem;
    }

    .search-btn {
        position: absolute;
        right: 5px;
        top: 5px;
        bottom: 5px;
        border-radius: 40px;
        padding: 0 25px;
    }

    /* History Cards Animation */
    .transition-hover {
        transition: all 0.3s ease;
    }

    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-shadow);
    }

    /* Loading Spinner */
    .loader-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10;
        border-radius: 20px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s;
    }

    .loader-overlay.active {
        opacity: 1;
        visibility: visible;
    }
</style>

{{-- =========================================================== --}}
{{-- 2. HTML STRUCTURE --}}
{{-- =========================================================== --}}

{{-- Mini Hero Section (ĐÃ CẬP NHẬT) --}}
<div class="mini-hero text-center">
    <div class="container">
        <h1 class="hero-title">Đặt Bàn Online</h1>

        {{-- Breadcrumb cho chuyên nghiệp --}}
        <ul class="breadcrumb-custom">
            <li><a href="/">Trang chủ</a></li>
            <li>Đặt bàn & Tra cứu</li>
        </ul>
    </div>
</div>

<div class="container pb-5" style="margin-top: 20px;">
    {{-- Alerts --}}
    @if(session('success'))
    <script>
        Swal.fire({icon: 'success', title: 'Thành công', text: "{{ session('success') }}", confirmButtonColor: '#FEA116'});
    </script>
    @endif
    @if(session('error'))
    <script>
        Swal.fire({icon: 'error', title: 'Lỗi', text: "{{ session('error') }}", confirmButtonColor: '#d33'});
    </script>
    @endif

    <div class="row g-5">
        {{-- CỘT TRÁI: FORM ĐẶT BÀN --}}
        <div class="col-lg-7">
            <div class="glass-card h-100 p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fa fa-utensils text-primary fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Thông tin đặt bàn</h4>
                        <small class="text-muted">Vui lòng điền đầy đủ thông tin bên dưới</small>
                    </div>
                </div>

                {{-- Form đặt bàn (Giữ nguyên include của bạn) --}}
                @include('restaurants.booking._form', [
                'action' => route('booking.store'),
                'method' => 'POST',
                'datBan' => null
                ])
            </div>
        </div>

        {{-- CỘT PHẢI: TRA CỨU AJAX --}}
        <div class="col-lg-5">
            {{-- Sticky Wrapper: Giữ thanh tìm kiếm khi cuộn --}}
            <div class="sticky-top" style="top: 20px; z-index: 9;">
                {{-- Box Tìm kiếm --}}
                <div class="mb-4">
                    <form id="searchForm" onsubmit="return false;"> {{-- Prevent Default Submit --}}
                        <div class="search-wrapper">
                            <i class="fa fa-search search-icon"></i>
                            <input type="tel" id="searchInput" class="form-control search-input"
                                placeholder="Nhập SĐT để tra cứu..." value="{{ $sdt ?? '' }}" autocomplete="off">
                            <button type="button" id="btnSearch" class="btn btn-primary search-btn fw-bold">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Khu vực hiển thị kết quả AJAX --}}
                <div class="position-relative">
                    {{-- Loader --}}
                    <div id="searchLoader" class="loader-overlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    {{-- Kết quả sẽ được load vào đây --}}
                    <div id="historyResults">
                        {{-- Include lần đầu để hiển thị nếu có biến sdt từ server --}}
                        @include('restaurants.booking._history_list', ['datBans' => $datBans ?? collect([]), 'sdt' =>
                        $sdt ?? null])
                    </div>
                </div>

                {{-- Support Box --}}
                <div class="mt-4 text-center">
                    <p class="small text-muted mb-1">Cần hỗ trợ gấp?</p>
                    <a href="tel:0999999999" class="fw-bold text-dark text-decoration-none fs-5 hover-primary">
                        <i class="fa fa-phone-alt text-primary me-2"></i> 0999.999.999
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================== --}}
{{-- 3. JAVASCRIPT AJAX LOGIC --}}
{{-- =========================================================== --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById('searchInput');
        const btnSearch = document.getElementById('btnSearch');
        const historyResults = document.getElementById('historyResults');
        const loader = document.getElementById('searchLoader');
        let timeout = null;

        // Hàm thực hiện tìm kiếm AJAX
        function performSearch() {
            const phone = searchInput.value.trim();
            
            // Hiệu ứng Loading
            loader.classList.add('active');
            historyResults.style.opacity = '0.5';

            // Gọi AJAX
            fetch(`{{ route('booking.index') }}?sdt=${phone}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Đánh dấu là AJAX request cho Laravel nhận biết
                }
            })
            .then(response => response.text())
            .then(html => {
                // Cập nhật HTML
                historyResults.innerHTML = html;
            })
            .catch(error => {
                console.error('Lỗi tìm kiếm:', error);
                historyResults.innerHTML = '<div class="text-center text-danger py-3">Có lỗi xảy ra, vui lòng thử lại.</div>';
            })
            .finally(() => {
                // Tắt loading
                loader.classList.remove('active');
                historyResults.style.opacity = '1';
            });
        }

        // Sự kiện Click nút tìm kiếm
        btnSearch.addEventListener('click', performSearch);

        // Sự kiện gõ phím (Debounce - chờ người dùng gõ xong mới tìm)
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                performSearch();
                return;
            }
            
            // Xóa timeout cũ
            clearTimeout(timeout);
            // Đặt timeout mới (tự động tìm sau 800ms ngừng gõ)
            timeout = setTimeout(() => {
                if(this.value.length >= 3 || this.value.length === 0) { // Chỉ tìm khi > 3 số hoặc rỗng
                    performSearch();
                }
            }, 800);
        });
    });
</script>

@endsection