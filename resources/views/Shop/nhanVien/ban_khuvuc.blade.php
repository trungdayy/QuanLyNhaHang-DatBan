@extends('layouts.shop.layout-nhanvien')

@section('title', 'Quản lý bàn ăn')

@section('content')
    {{-- Import Fonts --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        /* --- BỘ BIẾN MÀU --- */
        :root {
            --primary: #fea116;
            --primary-dark: #d98a12;
            --dark: #0f172b;
            --white: #ffffff;
            --success: #20d489;
            --danger: #ff4d4f;
            --text-main: #1e293b;
            --text-sub: #64748b;
            --radius: 8px;
        }

        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; color: var(--text-main); }
        h3, h4, h5, strong, .table-number { font-family: 'Heebo', sans-serif; }

        /* --- GLOBAL CARD --- */
        .card { border: none; border-radius: var(--radius); box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: var(--white); margin-bottom: 24px; overflow: hidden; }
        .card-header { background: var(--dark) !important; color: var(--white); padding: 12px 20px; border-bottom: none; font-weight: 700; text-transform: uppercase; display: flex; justify-content: space-between; align-items: center; }

        /* --- TABLE CARD --- */
        .table-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 15px;
            height: 100%;
            min-height: 220px;
            display: flex; flex-direction: column; justify-content: space-between;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            position: relative;
        }
        .table-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        .card-active { border: 1px solid var(--primary); background: #fffaf0; }
        .card-reserved { border: 1px dashed #94a3b8; background: #f8fafc; }

        .table-number { font-size: 1.8rem; font-weight: 800; color: var(--dark); line-height: 1; }
        .table-seats { font-size: 0.85rem; color: var(--text-sub); font-weight: 600; }

        .badge-status { padding: 4px 10px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px; }
        .badge-trong { background: #dcfce7; color: #166534; }
        .badge-co-khach { background: #ffedd5; color: #c2410c; }
        .badge-dadat { background: #f1f5f9; color: var(--dark); }

        .active-info-box {
            margin-top: 10px;
            padding: 10px;
            background: #fff;
            border-radius: 6px;
            border-left: 3px solid var(--primary);
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }
        .customer-name { font-weight: 700; color: var(--dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .time-in { font-size: 0.75rem; color: var(--text-sub); }

        .live-timer {
            margin-top: 8px;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            background: var(--dark); color: #fff;
            padding: 4px 8px; border-radius: 4px;
            font-family: 'JetBrains Mono', monospace; font-size: 0.9rem; font-weight: 700;
        }
        .timer-dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: pulse 1s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }

        .btn-custom { border: none; border-radius: 6px; font-weight: 700; font-family: 'Heebo', sans-serif; text-transform: uppercase; font-size: 0.75rem; padding: 8px; width: 100%; display: flex; align-items: center; justify-content: center; gap: 5px; text-decoration: none; transition: 0.2s; }
        .btn-checkin { background: #fff; border: 1px solid var(--primary); color: var(--primary); }
        .btn-checkin:hover { background: var(--primary); color: #fff; }
        .btn-payment { background: var(--primary); color: #fff; }
        .btn-payment:hover { background: var(--primary-dark); color: #fff; }

        /* --- SIDEBAR LIST --- */
        .incoming-item { background: var(--white); border-bottom: 1px dashed #e2e8f0; padding: 12px 0; }
        
        /* Tag nhỏ bên trái (chỉ dùng cho SẮP ĐẾN) */
        .status-tag { font-size: 0.65rem; font-weight: 800; padding: 3px 6px; border-radius: 4px; text-transform: uppercase; }
        .tag-waiting { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }

        /* Nút Check-in bên phải */
        .btn-checkin-link {
            background: var(--primary); color: #fff; font-size: 0.75rem; font-weight: 700; padding: 6px 12px; border-radius: 4px; text-decoration: none; display: inline-block;
        }
        .btn-checkin-link:hover { background: var(--primary-dark); color: #fff; }

        /* [MỚI] Badge ĐANG ĂN to bên phải (giống nút check-in) */
        .badge-serving-right {
            background: #dcfce7; /* Nền xanh nhạt */
            color: #15803d;      /* Chữ xanh đậm */
            border: 1px solid #15803d;
            font-size: 0.75rem; 
            font-weight: 800; 
            padding: 6px 12px;   /* Padding giống nút check-in */
            border-radius: 4px; 
            text-transform: uppercase; 
            display: inline-block;
            white-space: nowrap;
        }

        .form-control-custom { border: 1px solid #e2e8f0; border-radius: 6px; padding: 5px 10px; font-size: 0.9rem; }
    </style>

    {{-- Flash messages --}}
    @if (session('success')) <div class="alert alert-success mb-3 border-0 bg-success-subtle text-success-emphasis"><i class="fa-solid fa-check-circle"></i> {{ session('success') }}</div> @endif
    @if (session('error')) <div class="alert alert-danger mb-3 border-0 bg-danger-subtle text-danger-emphasis"><i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}</div> @endif

    <div class="container-fluid py-4 px-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="m-0 text-uppercase fw-bold text-dark"><i class="fa-solid fa-layer-group text-primary"></i> Quản lý bàn ăn</h4>
            <a href="{{ route('nhanVien.ban-an.index') }}" class="btn btn-light border fw-bold"><i class="fa-solid fa-rotate"></i> Làm mới</a>
        </div>

        <div class="row g-4">
            {{-- CỘT TRÁI: DANH SÁCH BÀN --}}
            <div class="col-lg-9 col-md-8">
                @foreach ($khuVucs as $khu)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <span><i class="fa-solid fa-map-pin"></i> {{ $khu->ten_khu_vuc }} <span style="opacity: 0.7; font-weight: 400;">(Tầng {{ $khu->tang }})</span></span>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row g-3">
                            @foreach ($khu->banAns as $ban)
                            @php
                                $khachDangNgoi = $datBans->where('ban_id', $ban->id)->where('trang_thai', 'khach_da_den')->first();
                                $donDatTruoc = $datBans->where('ban_id', $ban->id)->where('trang_thai', 'da_xac_nhan')->first();
                                
                                $cardClass = '';
                                if ($khachDangNgoi) $cardClass = 'card-active';
                                elseif ($donDatTruoc) $cardClass = 'card-reserved';
                            @endphp

                            <div class="col-6 col-md-4 col-xl-3">
                                <div class="table-card {{ $cardClass }}">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="table-number">{{ $ban->so_ban }}</div>
                                            <div class="table-seats"><i class="fa-solid fa-chair"></i> {{ $ban->so_ghe }}</div>
                                        </div>

                                        <div>
                                            @if($khachDangNgoi)
                                                <span class="badge-status badge-co-khach"><i class="fa-solid fa-utensils"></i> Đang ăn</span>
                                            @elseif($donDatTruoc)
                                                <span class="badge-status badge-dadat"><i class="fa-solid fa-calendar-check"></i> Đã đặt</span>
                                            @elseif($ban->trang_thai == 'khong_su_dung')
                                                <span class="badge-status badge-dadat">Đóng</span>
                                            @else
                                                <span class="badge-status badge-trong"><i class="fa-solid fa-check"></i> Trống</span>
                                            @endif
                                        </div>

                                        @if($khachDangNgoi)
                                        <div class="active-info-box">
                                            <div class="customer-name" title="{{ $khachDangNgoi->ten_khach }}">{{ $khachDangNgoi->ten_khach }}</div>
                                            <div class="time-in"><i class="fa-regular fa-clock"></i> Vào: {{ \Carbon\Carbon::parse($khachDangNgoi->gio_den)->format('H:i') }}</div>
                                            
                                            <div class="mt-1 pt-1 border-top border-secondary-subtle small text-muted d-flex align-items-center gap-1">
                                                 <i class="fa-solid fa-user-tie text-primary"></i> 
                                                 @if($khachDangNgoi->nhanVien)
                                                     <span class="fw-bold text-dark">{{ $khachDangNgoi->nhanVien->ho_ten }}</span>
                                                 @else
                                                     <span class="text-danger fst-italic">Chưa phân công</span>
                                                 @endif
                                            </div>
                                                <div class="live-timer" data-start="{{ $khachDangNgoi->gio_den }}">
                                                    <div class="timer-dot"></div>
                                                    <span class="timer-text">00:00:00</span>
                                                </div>
                                            </div>
                                        @elseif($donDatTruoc)
                                            <div class="mt-3 text-muted small">
                                                <strong>Sắp đến:</strong><br>
                                                {{ $donDatTruoc->ten_khach }} ({{ \Carbon\Carbon::parse($donDatTruoc->gio_den)->format('H:i') }})
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-3">
                                        @if($donDatTruoc)
                                            <a href="{{ route('nhanVien.ban-an.show-checkin-dattruoc', $donDatTruoc->id) }}" class="btn-custom btn-checkin">
                                                <i class="fa-solid fa-bell"></i> Check-in
                                            </a>
                                        @endif
                                        @if($khachDangNgoi)
                                            <a href="{{ route('nhanVien.thanh-toan.ban', $ban->id) }}" class="btn-custom btn-payment">
                                                <i class="fa-solid fa-receipt"></i> Thanh toán
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            {{-- CỘT PHẢI: LIST KHÁCH --}}
            <div class="col-lg-3 col-md-4">
                <div class="card sticky-top" style="top: 20px; z-index: 99;">
                    <div class="card-header">
                        <span><i class="fa-solid fa-users"></i> Khách sắp đến</span>
                    </div>
                    
                    <div class="p-3 border-bottom bg-light">
                         <form method="get" action="{{ route('nhanVien.ban-an.index') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control-custom w-100" placeholder="Tìm tên/SĐT..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-primary fw-bold">TÌM</button>
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <div style="max-height: 70vh; overflow-y: auto; padding: 0 15px;">
                            @forelse ($datBans as $datban)
                            <div class="incoming-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-primary" style="font-family: 'Heebo'">
                                        {{ \Carbon\Carbon::parse($datban->gio_den)->format('H:i') }}
                                        
                                        {{-- CHỈ HIỆN BADGE SẮP ĐẾN Ở ĐÂY (NẾU CẦN) --}}
                                        @if($datban->trang_thai === 'da_xac_nhan')
                                            <span class="status-tag tag-waiting ms-1">SẮP ĐẾN</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted"> Mã KH : {{ $datban->ma_dat_ban }}</div>
                                    <div class="fw-bold text-dark my-1">Tên khách : {{ $datban->ten_khach }}</div>
                                    <div class="small text-muted"> SDT Khách : {{ $datban->sdt_khach }}</div>
                                </div>
                                <div class="text-end ps-2">
                                    @if($datban->trang_thai === 'da_xac_nhan')
                                        {{-- Nút Check-in --}}
                                        <a href="{{ route('nhanVien.ban-an.show-checkin-dattruoc', $datban->id) }}" class="btn-checkin-link">
                                            CHECK-IN
                                        </a>
                                    @elseif($datban->trang_thai === 'khach_da_den')
                                        {{-- BADGE ĐANG ĂN TO NẰM BÊN PHẢI --}}
                                        <span class="badge-serving-right">
                                            ĐANG ĂN
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @empty
                                <div class="text-center py-5 text-muted small">
                                    <i class="fa-solid fa-mug-hot fa-2x mb-2 opacity-50"></i><br>
                                    Hiện chưa có khách đặt
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Đồng hồ đếm giờ --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        function updateTimers() {
            const timers = document.querySelectorAll('.live-timer');
            const now = new Date().getTime();

            timers.forEach(timer => {
                const startTimeStr = timer.getAttribute('data-start');
                if (!startTimeStr) return;

                const startTime = new Date(startTimeStr).getTime();
                const diffMs = now - startTime;

                if (diffMs > 0) {
                    const diffHrs = Math.floor((diffMs % 86400000) / 3600000);
                    const diffMins = Math.floor(((diffMs % 86400000) % 3600000) / 60000);
                    const diffSecs = Math.floor((diffMs % 60000) / 1000);

                    const h = diffHrs.toString().padStart(2, '0');
                    const m = diffMins.toString().padStart(2, '0');
                    const s = diffSecs.toString().padStart(2, '0');

                    const textSpan = timer.querySelector('.timer-text');
                    if(textSpan) textSpan.innerText = `${h}:${m}:${s}`;

                    if(diffHrs >= 2) {
                        timer.style.background = '#ef4444'; 
                    }
                }
            });
        }
        setInterval(updateTimers, 1000); 
        updateTimers();
    });
    </script>

    {{-- [MỚI] SCRIPT KIỂM TRA THÔNG BÁO GỌI HỖ TRỢ --}}
    <script>
        $(document).ready(function() {
            // Cấu hình: 5 giây check 1 lần
            const CHECK_INTERVAL = 5000; 

            function checkNotifications() {
                $.ajax({
                    // Route này phải khớp với route ông đặt trong web.php 
                    // (Ví dụ: Route::get('check-notifications', ...)->name('nhanVien.ban-an.check_notif'))
                    url: "{{ route('nhanVien.ban-an.check_notif') }}", 
                    type: "GET",
                    success: function(data) {
                        // data = [{id: 1, so_ban: "Bàn 1", nhan_vien_phu_trach: "Tên NV"}, ...]
                        if (data && data.length > 0) {
                            data.forEach(function(ban) {
                                // Hiển thị thông báo
                                let msg = "🔔 KHÁCH GỌI HỖ TRỢ!\n" +
                                          "📍 " + ban.so_ban + "\n" +
                                          "👤 NV Phụ trách: " + (ban.nhan_vien_phu_trach || "Chưa phân công") + "\n\n" +
                                          "Bấm OK để xác nhận đã xử lý.";

                                if (confirm(msg)) {
                                    // Gọi API tắt thông báo
                                    $.ajax({
                                        url: "{{ route('nhanVien.ban-an.complete_support') }}",
                                        method: "POST",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            ban_id: ban.id
                                        },
                                        success: function() {
                                            console.log("Đã xử lý xong bàn " + ban.id);
                                        }
                                    });
                                }
                            });
                        }
                    },
                    error: function(err) {
                        console.log("Lỗi check notification:", err);
                    }
                });
            }

            // Chạy lặp lại
            setInterval(checkNotifications, CHECK_INTERVAL);
        });
    </script>
@endsection