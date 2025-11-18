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
</head>

<body onload="time()" class="app sidebar-mini rtl">
    <header class="app-header">
        <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
        <ul class="app-nav">


            <li><a class="app-nav__item" href="../index.html"><i class='bx bx-log-out bx-rotate-180'></i> </a>

            </li>
        </ul>
    </header>
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
        <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="/images/hay.jpg" width="50px"
                alt="User Image">
            <div>
                <p class="app-sidebar__user-name"><b>Ocean Buffet</b></p>
                <p class="app-sidebar__user-designation">Chào mừng bạn trở lại</p>
            </div>
        </div>
        <hr>
        <ul class="app-menu">
            <li><a class="app-menu__item " href="{{ route('admin.dashboard') }}">
                    <i class='app-menu__icon bx bx-home'></i>
                    <span class="app-menu__label">Trang chủ</span>
                </a>
            </li>
            <li>
                <a class="app-menu__item " href="{{ route('admin.danh-muc.index') }}">
                    <i class='app-menu__icon bx bx-home'></i>
                    <span class="app-menu__label">Quản lý danh mục Món</span>
                </a>
            </li>
            <li>
                <a class="app-menu__item" href="{{ route('admin.san-pham.index') }}">
                    <i class='app-menu__icon bx bx-purchase-tag-alt'></i>
                    <span class="app-menu__label">Quản lý món ăn</span>
                </a>
            </li>

            <a class="app-menu__item" href="{{ route('admin.combo-buffet.index') }}">
                <i class='app-menu__icon bx bx-task'></i>
                <span class="app-menu__label">Quản lý combo buffet</span>
            </a>
            <li>
                <a class="app-menu__item " href="{{ route('admin.mon-trong-combo.index') }}">
                    <i class='app-menu__icon bx bx-table'></i>
                    <span class="app-menu__label">Quản lý món trong combo</span>
                </a>
            </li>
            <li>
                <a class="app-menu__item " href="{{ route('admin.khu-vuc-ban-an') }}">
                    <i class='app-menu__icon bx bx-table'></i>
                    <span class="app-menu__label">Khu vực & bàn ăn</span>
                </a>
            </li>

            {{-- 💡 ĐÃ THÊM ROUTE ĐẶT BÀN MỚI VÀO ĐÂY --}}
            <li>
                <a class="app-menu__item" href="{{ route('admin.dat-ban.index') }}">
                    <i class='app-menu__icon bx bx-calendar-check'></i>
                    <span class="app-menu__label">Quản lý Đặt Bàn</span>
                </a>
            </li>

            <li>
                <a class="app-menu__item" href="{{ route('admin.order-mon.index') }}">
                    <i class='app-menu__icon bx bx-task'></i>
                    <span class="app-menu__label">Quản lý order</span>
                </a>
            </li>

            <li>
                <a class="app-menu__item " href="{{ route('admin.chi-tiet-order.index') }}">
                    <i class='app-menu__icon bx bx-building'></i>
                    <span class="app-menu__label">Chi tiết order</span>
                </a>
            </li>

            <li>
                <a class="app-menu__item" href="{{ route('admin.nhan-vien.index') }}">
                    <i class='app-menu__icon bx bx-calendar-check'></i>
                    <span class="app-menu__label">Quản lý Nhân Viên</span>
                </a>
            </li>
            <li>
                <a class="app-menu__item" href="{{ route('admin.hoa-don.index') }}">
                    <i class='app-menu__icon bx bx-calendar-check'></i>
                    <span class="app-menu__label">Quản lý hóa đơn</span>
                </a>
            </li>

            <li>
                <a class="app-menu__item {{ Request::is('admin/voucher*') ? 'active' : '' }}"
                    href="{{ route('admin.voucher.index') }}">
                    <i class="app-menu__icon fa fa-ticket-alt"></i><span class="app-menu__label">Quản lý Voucher</span>
                </a>
            </li>

            <li>
                <a class="app-menu__item" href="{{ route('admin.ban-an.qr_tool') }}">
                    <i class='app-menu__icon bx bx-calendar-check'></i>
                    <span class="app-menu__label">Quản lý mã QR</span>
                </a>
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
