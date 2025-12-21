<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/css/main.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="http://code.jquery.com/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('admin/ckeditor/ckeditor.js') }}"></script>

    @yield('style')

    <style>
    /* 1. Tăng độ thoáng cho menu chính */
    .app-menu__item {
        margin-bottom: 5px; /* Tạo khe hở giữa các mục */
        border-radius: 5px; /* Bo góc nhẹ cho mềm mại */
        font-weight: 600; /* Chữ đậm hơn chút cho dễ đọc */
        transition: all 0.3s ease;
        border-left: 4px solid transparent; /* Chuẩn bị sẵn viền trái */
    }

    /* 2. Hiệu ứng khi di chuột vào menu cha */
    .app-menu__item:hover, 
    .app-menu__item.active {
        background: rgba(255, 255, 255, 0.15); /* Sáng hơn chút */
        border-left-color: #ffd43b; /* Có vạch màu vàng nổi bật bên trái */
        text-decoration: none;
        color: #fff;
    }

    /* 3. Xử lý Menu Con (Dropdown) cho rõ ràng */
    .treeview-menu {
        background-color: rgba(0, 0, 0, 0.2) !important; /* Nền tối hơn nền chính để tạo chiều sâu */
        border-radius: 0 0 5px 5px;
        padding-top: 5px;
        padding-bottom: 5px;
    }

    /* 4. Định dạng từng mục con */
    .treeview-item {
        padding: 10px 15px 10px 50px !important; /* Thụt đầu dòng sâu hơn để phân biệt với cha */
        font-size: 0.9rem;
        color: #e0e0e0;
        border-bottom: 1px dashed rgba(255,255,255,0.1); /* Đường kẻ mờ ngăn cách các mục con */
    }

    /* Bỏ đường kẻ ở mục con cuối cùng */
    .treeview-item:last-child {
        border-bottom: none;
    }

    /* 5. Hiệu ứng hover vào mục con */
    .treeview-item:hover {
        color: #ffd43b !important; /* Đổi màu chữ sang vàng */
        background: rgba(255,255,255,0.05);
        padding-left: 55px !important; /* Hiệu ứng trượt nhẹ sang phải */
        transition: all 0.2s;
    }

    /* 6. Trạng thái khi menu cha đang mở (Expanded) */
    .treeview.is-expanded .app-menu__item {
        background: rgba(0, 0, 0, 0.3); /* Nền đậm khi đang mở */
        border-left-color: #ffd43b;
        color: #fff;
    }

    /* Mũi tên chỉ xuống */
    .treeview-indicator {
        font-size: 1.1rem;
    }
</style>
</head>

<body onload="time()" class="app sidebar-mini rtl">
    <header class="app-header">
        <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
        <ul class="app-nav">
            {{-- Chỗ này có thể thêm thông báo hoặc icon khác --}}

            {{-- Thêm form Đăng Xuất --}}
            <li>
                <a class="app-nav__item" href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class='bx bx-log-out bx-rotate-180'></i>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </header>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
        <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="http://127.0.0.1:8000/assets/img/hero.png" width="50px"
                alt="User Image">
            <div>
                <p class="app-sidebar__user-name"><b>Ocean Buffet</b></p>
                <p class="app-sidebar__user-designation">Chào mừng bạn trở lại</p>
            </div>
        </div>
        <hr>
<ul class="app-menu">

    {{-- ===== DASHBOARD ===== --}}
    <li>
        <a class="app-menu__item" href="{{ route('admin.dashboard') }}">
            <i class='app-menu__icon bx bx-home'></i>
            <span class="app-menu__label">Trang chủ</span>
        </a>
    </li>

    {{-- ================================================= --}}
    {{-- QUẢN LÝ THỰC ĐƠN (CHIẾN) --}}
    {{-- ================================================= --}}
    <li class="treeview {{ request()->routeIs(
        'admin.danh-muc.*',
        'admin.san-pham.*',
        'admin.combo-buffet.*',
        'admin.mon-trong-combo.*'
    ) ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class='app-menu__icon bx bx-food-menu'></i>
            <span class="app-menu__label">Quản lý thực đơn</span>
            <i class="treeview-indicator bx bx-chevron-right"></i>
        </a>

        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.danh-muc.index') }}">Danh mục món</a></li>
            <li><a class="treeview-item" href="{{ route('admin.san-pham.index') }}">Món ăn</a></li>
            <li><a class="treeview-item" href="{{ route('admin.combo-buffet.index') }}">Combo buffet</a></li>
            <li><a class="treeview-item" href="{{ route('admin.mon-trong-combo.index') }}">Món trong combo</a></li>
        </ul>
    </li>

    {{-- ================================================= --}}
    {{-- QUẢN LÝ VẬN HÀNH (VINH) --}}
    {{-- ================================================= --}}
    <li class="treeview {{ request()->routeIs(
        'admin.khu-vuc-ban-an',
        'admin.ban-an.*',
        'admin.nhan-vien.*',
        'admin.voucher.*'
    ) ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class='app-menu__icon bx bx-cog'></i>
            <span class="app-menu__label">Quản lý vận hành</span>
            <i class="treeview-indicator bx bx-chevron-right"></i>
        </a>

        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.khu-vuc-ban-an') }}">Khu vực & bàn ăn</a></li>
            <li><a class="treeview-item" href="{{ route('admin.ban-an.qr_tool') }}">Mã QR bàn</a></li>
            <li><a class="treeview-item" href="{{ route('admin.nhan-vien.index') }}">Nhân viên</a></li>
            <li><a class="treeview-item" href="{{ route('admin.voucher.index') }}">Voucher</a></li>
        </ul>
    </li>

    {{-- ================================================= --}}
    {{-- QUẢN LÝ ĐƠN HÀNG & THỐNG KÊ (CẢNH) --}}
    {{-- ================================================= --}}
    <li class="treeview {{ request()->routeIs(
        'admin.dat-ban.*',
        'admin.order-mon.*',
        'admin.hoa-don.*',
        'admin.danh-gia.*'
    ) ? 'is-expanded' : '' }}">
        <a class="app-menu__item" href="#" data-toggle="treeview">
            <i class='app-menu__icon bx bx-bar-chart'></i>
            <span class="app-menu__label">Quản lý & thống kê</span>
            <i class="treeview-indicator bx bx-chevron-right"></i>
        </a>

        <ul class="treeview-menu">
            <li><a class="treeview-item" href="{{ route('admin.dat-ban.index') }}">Đặt bàn</a></li>
            <li><a class="treeview-item" href="{{ route('admin.order-mon.index') }}">Order</a></li>
            <li><a class="treeview-item" href="{{ route('admin.hoa-don.index') }}">Hóa đơn</a></li>
            <li><a class="treeview-item" href="{{ route('admin.danh-gia.index') }}">Đánh giá</a></li>
        </ul>
    </li>

</ul>


    </aside>

    <main>
        @yield('content')
    </main>

    <script src="{{ asset('admin/doc/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('admin/doc/js/popper.min.js') }}"></script>
    <script src="https://unpkg.com/boxicons@latest/dist/boxicons.js"></script>
    <script src="{{ asset('admin/doc/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin/doc/js/main.js') }}"></script>
    <script src="{{ asset('admin/doc/js/plugins/pace.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/doc/js/plugins/chart.js') }}"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="src/jquery.table2excel.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css"></script>
    <script type="text/javascript" src="{{ asset('admin/doc/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin/doc/js/plugins/dataTables.bootstrap.min.js') }}"></script>
    <script type="text/javascript">
        $('#sampleTable').DataTable();
    </script>

    {{-- thời gian thực --}}
    <script type="text/javascript">
        //Thời Gian
        function time() {
            var today = new Date();
            var weekday = new Array(7);
            weekday[0] = "Chủ Nhật";
            weekday[1] = "Thứ Hai";
            weekday[2] = "Thứ Ba";
            weekday[3] = "Thứ Tư";
            weekday[4] = "Thứ Năm";
            weekday[5] = "Thứ Sáu";
            weekday[6] = "Thứ Bảy";
            var day = weekday[today.getDay()];
            var dd = today.getDate();
            var mm = today.getMonth() + 1;
            var yyyy = today.getFullYear();
            var h = today.getHours();
            var m = today.getMinutes();
            var s = today.getSeconds();
            m = checkTime(m);
            s = checkTime(s);
            nowTime = h + " giờ " + m + " phút " + s + " giây";
            if (dd < 10) {
                dd = '0' + dd
            }
            if (mm < 10) {
                mm = '0' + mm
            }
            today = day + ', ' + dd + '/' + mm + '/' + yyyy;
            tmp = '<span class="date"> ' + today + ' - ' + nowTime +
                '</span>';
            document.getElementById("clock").innerHTML = tmp;
            clocktime = setTimeout("time()", "1000", "Javascript");

            function checkTime(i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
    const treeviews = document.querySelectorAll('.treeview > a');

    treeviews.forEach(function (menu) {
        menu.addEventListener('click', function (e) {
            e.preventDefault();

            const parent = this.parentElement;
            const isOpen = parent.classList.contains('is-expanded');

            // Đóng tất cả menu khác (accordion)
            document.querySelectorAll('.treeview').forEach(function (item) {
                item.classList.remove('is-expanded');
            });

            // Mở menu hiện tại nếu trước đó đang đóng
            if (!isOpen) {
                parent.classList.add('is-expanded');
            }
        });
    });
});
    </script>

    {{-- máy in --}}
    <script>
        var myApp = new function() {
            this.printTable = function() {
                var tab = document.getElementById('sampleTable');
                var win = window.open('', '', 'height=700,width=700');
                win.document.write(tab.outerHTML);
                win.document.close();
                win.print();
            }
        }
    </script>

    {{-- script riêng các trang --}}
    @yield('script')
</body>

</html>