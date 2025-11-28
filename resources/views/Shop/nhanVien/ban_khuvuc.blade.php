@extends('layouts.shop.layout-nhanvien')

@section('title', 'Quản lý bàn ăn')

@section('content')
    {{-- Import Fonts --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- BỘ BIẾN MÀU (Đồng bộ Menu) --- */
        :root {
            --primary: #fea116;       /* Cam vàng */
            --primary-dark: #d98a12;  /* Cam đậm */
            --dark: #0f172b;          /* Xanh đen */
            --white: #ffffff;
            --success: #20d489;       /* Xanh mint */
            --danger: #ff4d4f;        /* Đỏ */
            --text-main: #1e293b;
            --text-sub: #64748b;
            --shadow-card: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
            --radius: 8px;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
            color: var(--text-main);
        }

        h3, h4, h5, strong, .table-number {
            font-family: 'Heebo', sans-serif;
        }

        /* --- GLOBAL CARD --- */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow-card);
            background: var(--white);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header {
            background: var(--dark) !important;
            color: var(--white);
            padding: 15px 20px;
            border-bottom: none;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header strong { font-size: 1.1rem; }
        .card-header small { color: rgba(255,255,255, 0.6); }

        /* --- TABLE CARD (BÊN TRÁI - GIỮ NGUYÊN STYLE CŨ) --- */
        .table-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 15px;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
        }

        .table-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(254, 161, 22, 0.3);
        }

        .table-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
        }

        .table-seats {
            font-size: 0.85rem;
            color: var(--text-sub);
            font-weight: 600;
        }

        /* --- BADGES TRÒN (PILL) CÓ ICON (GIỮ NGUYÊN) --- */
        .badge-status {
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-trong { background: rgba(32, 212, 137, 0.15); color: #15803d; border: 1px solid rgba(32, 212, 137, 0.2); }
        .badge-co-khach { background: rgba(254, 161, 22, 0.15); color: #b45309; border: 1px solid rgba(254, 161, 22, 0.3); }
        .badge-dadat, .badge-giu { background: #e2e8f0; color: var(--dark); border: 1px solid #cbd5e1; }
        .badge-khong-su-dung { background: #f1f5f9; color: #94a3b8; }

        /* --- INFO KHÁCH --- */
        .guest-info {
            background: #f8fafc;
            border-radius: 6px;
            padding: 8px;
            font-size: 0.85rem;
            border-left: 3px solid var(--success);
            color: var(--text-main);
            margin-bottom: 8px;
        }

        /* --- BUTTONS --- */
        .btn-custom {
            border: none;
            border-radius: 6px;
            font-weight: 700;
            font-family: 'Heebo', sans-serif;
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 8px 12px;
            transition: 0.2s;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .btn-checkin { background: var(--white); border: 1px solid var(--primary); color: var(--primary); }
        .btn-checkin:hover { background: var(--primary); color: var(--white); }
        .btn-payment { background: var(--primary); color: var(--white); box-shadow: 0 4px 10px rgba(254, 161, 22, 0.3); }
        .btn-payment:hover { background: var(--primary-dark); transform: translateY(-2px); }

        /* =========================================================
           CỘT PHẢI: CUSTOM LẠI THÀNH DẠNG CHỮ (TEXT ONLY)
           ========================================================= */
        .incoming-item {
            background: var(--white);
            border-bottom: 1px dashed #e2e8f0;
            padding: 15px 0;
            transition: 0.2s;
        }
        .incoming-item:last-child { border-bottom: none; }

        /* 1. Nhãn Text (Thay icon) */
        .lbl {
            color: #94a3b8; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; margin-right: 4px;
        }

        /* 2. Badge Trạng thái chữ */
        .status-tag-text {
            font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
            padding: 4px 8px; border-radius: 4px; display: inline-block; margin: 4px 0;
        }
        .st-waiting { background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5; }
        .st-serving { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }

        /* 3. Nút Check-in Chữ (Vuông bo góc nhẹ) */
        .btn-checkin-text {
            background: var(--primary); color: #fff; border: none;
            font-size: 0.8rem; font-weight: 800; padding: 8px 14px;
            border-radius: 6px; text-transform: uppercase;
            font-family: 'Heebo', sans-serif; cursor: pointer;
            box-shadow: 0 2px 5px rgba(254, 161, 22, 0.2);
            transition: 0.2s;
        }
        .btn-checkin-text:hover { background: var(--primary-dark); transform: translateY(-1px); }

        /* 4. Badge Đã Vào */
        .badge-da-vao {
            font-weight: 800; color: var(--success); font-size: 0.8rem;
            border: 2px solid var(--success); padding: 5px 10px; border-radius: 6px;
            text-transform: uppercase; display: inline-block; background: #f0fdf4;
        }
        
        /* Form Search */
        .form-control-custom { border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 12px; font-size: 0.9rem; }
    </style>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success mb-3 shadow-sm border-0" style="background: #dcfce7; color: #166534;"><i class="fa-solid fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger mb-3 shadow-sm border-0" style="background: #fee2e2; color: #991b1b;"><i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="container py-4">
        <div class="d-flex align-items-center gap-2 mb-4">
            <h3 class="m-0 text-uppercase" style="color: var(--dark); font-weight: 800;"><i class="fa-solid fa-layer-group text-primary"></i> Quản lý bàn ăn</h3>
        </div>

        <div class="row">
            {{-- Left: Sơ đồ bàn (GIỮ NGUYÊN ICON BADGES) --}}
            <div class="col-md-8">
                @foreach ($khuVucs as $khu)
                <div class="card">
                    <div class="card-header">
                        <div>
                            <i class="fa-solid fa-map-location-dot text-primary"></i> {{ $khu->ten_khu_vuc }}
                            <small class="ms-2" style="font-weight: 400;">(Tầng {{ $khu->tang }})</small>
                        </div>
                        <form method="get" action="{{ route('nhanVien.ban-an.index') }}">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            <button type="submit" class="btn btn-sm btn-outline-light border-0"><i class="fa-solid fa-rotate"></i> Làm mới</button>
                        </form>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row g-3">
                            @foreach ($khu->banAns as $ban)
                            @php
                                $status = $ban->trang_thai;
                                $isHeld = $ban->giu_den && $ban->giu_den > now();
                            @endphp
                            <div class="col-6 col-sm-4 col-md-3">
                                <div class="table-card">
                                    {{-- Phần trên --}}
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="table-number">{{ $ban->so_ban }}</div>
                                            <div class="table-seats"><i class="fa-solid fa-chair"></i> {{ $ban->so_ghe }}</div>
                                        </div>

                                        {{-- Badge có ICON (Style cũ) --}}
                                        <div class="mb-3">
                                            @if ($status === 'trong' && !$isHeld)
                                                <span class="badge-status badge-trong"><i class="fa-solid fa-circle-check"></i> Trống</span>
                                            @elseif($status === 'dang_phuc_vu' || $status === 'khach_da_den')
                                                <span class="badge-status badge-co-khach"><i class="fa-solid fa-utensils"></i> Đang ăn</span>
                                            @elseif ($status === 'da_dat' && $isHeld)
                                                <span class="badge-status badge-giu" data-countdown="{{ $ban->giu_den }}"><i class="fa-solid fa-clock"></i> Giữ...</span>
                                            @elseif ($status === 'da_dat')
                                                <span class="badge-status badge-dadat"><i class="fa-solid fa-calendar-check"></i> Đã đặt</span>
                                            @else
                                                <span class="badge-status badge-khong-su-dung"><i class="fa-solid fa-ban"></i> Đóng</span>
                                            @endif
                                        </div>

                                        @if(!empty($ban->khach_dang_ngoi))
                                            <div class="guest-info">
                                                <div style="font-weight: 700;">{{ $ban->khach_dang_ngoi }}</div>
                                                <div style="font-size: 0.75rem; color: var(--text-sub);">
                                                    <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($ban->gio_bat_dau)->format('H:i') }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Phần dưới --}}
                                    <div class="mt-2">
                                        @php $datBan = $datBans->firstWhere('ban_id', $ban->id); @endphp
                                        @if($datBan && $datBan->trang_thai === 'da_xac_nhan')
                                            <form method="POST" action="{{ route('nhanVien.ban-an.check-in-dattruoc') }}">
                                                @csrf
                                                <input type="hidden" name="dat_ban_id" value="{{ $datBan->id }}">
                                                <button class="btn-custom btn-checkin"><i class="fa-solid fa-user-check"></i> Check-in</button>
                                            </form>
                                        @endif
                                        @if ($status === 'dang_phuc_vu' || $status === 'khach_da_den')
                                            <a href="{{ route('nhanVien.thanh-toan.ban', $ban->id) }}" class="btn-custom btn-payment">
                                                <i class="fa-solid fa-file-invoice-dollar"></i> Thanh toán
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
            
            {{-- Right: Khách sắp đến (THAY ĐỔI: DÙNG TEXT THAY VÌ ICON) --}}
            <div class="col-md-4">
                <div class="card" style="position: sticky; top: 20px;">
                    <div class="card-header">
                        <span><i class="fa-solid fa-users-viewfinder"></i> Khách sắp đến</span>
                    </div>
                    
                    <div class="p-3 border-bottom bg-light">
                         <form method="get" action="{{ route('nhanVien.ban-an.index') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-control-custom w-100" placeholder="Tìm tên/SĐT/Mã..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-sm btn-primary" style="background: var(--primary); border: none; font-weight:700;">TÌM</button>
                            @if(request('search'))
                                <a href="{{ route('nhanVien.ban-an.index') }}" class="btn btn-sm btn-danger" style="font-weight:700;" title="Xóa tìm kiếm">X</a>
                            @endif
                        </form>
                    </div>

                    <div class="card-body p-0">
                        <div style="max-height: 70vh; overflow-y: auto; padding: 0 20px;">
                            @forelse ($datBans as $datban)
                            <div class="incoming-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div style="font-weight: 700; color: var(--primary); font-family: 'Heebo';">
                                        {{ \Carbon\Carbon::parse($datban->gio_den)->format('H:i') }} 
                                        <span class="text-muted small" style="font-weight: 400;">(#{{ $datban->ma_dat_ban ?? '...' }})</span>
                                    </div>
                                    <div class="my-1" style="font-size: 1rem;"><strong>{{ $datban->ten_khach }}</strong></div>
                                    
                                    {{-- NEW: Badge trạng thái Text --}}
                                    <div>
                                        @if($datban->trang_thai === 'da_xac_nhan')
                                            <span class="status-tag-text st-waiting">SẮP ĐẾN</span>
                                        @elseif($datban->trang_thai === 'khach_da_den')
                                            <span class="status-tag-text st-serving">ĐANG PHỤC VỤ</span>
                                        @endif
                                    </div>

                                    {{-- NEW: Nhãn Text --}}
                                    <div class="mt-1" style="font-size: 0.9rem;">
                                        <div class="incoming-label"><span class="lbl">SĐT:</span> {{ $datban->sdt_khach }}</div>
                                        <div class="incoming-label">
                                            <span class="lbl">BÀN:</span>
                                            @if($datban->banAn)
                                                <strong class="text-dark">Số {{ $datban->banAn->so_ban }}</strong>
                                            @else
                                                <span class="text-danger">Chưa xếp</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- NEW: Nút Check-in Text --}}
                                <div class="text-end ps-2">
                                    @if($datban->trang_thai === 'da_xac_nhan')
                                        <form method="POST" action="{{ route('nhanVien.ban-an.check-in-dattruoc') }}">
                                            @csrf
                                            <input type="hidden" name="dat_ban_id" value="{{ $datban->id }}">
                                            <button class="btn-checkin-text" title="Khách đã đến quán">
                                                CHECK-IN
                                            </button>
                                        </form>
                                    @elseif($datban->trang_thai === 'khach_da_den')
                                        <span class="badge-da-vao">ĐÃ VÀO</span>
                                    @endif
                                </div>
                            </div>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-clipboard-list fa-2x mb-2 opacity-50"></i>
                                    <p>Chưa có khách đặt sắp tới</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Script --}}
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const countdowns = document.querySelectorAll("[data-countdown]");
        countdowns.forEach(el => {
            const endTime = new Date(el.getAttribute("data-countdown")).getTime();
            const timer = setInterval(function () {
                const now = new Date().getTime();
                const distance = endTime - now;
                if (distance <= 0) {
                    el.innerHTML = "<i class='fa-solid fa-circle-exclamation'></i> Hết giờ giữ";
                    el.classList.remove("badge-giu"); el.classList.add("badge-dadat");
                    el.style.background = "#e2e8f0"; el.style.color = "#0f172b";
                    clearInterval(timer); return;
                }
                const mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const secs = Math.floor((distance % (1000 * 60)) / 1000);
                el.innerHTML = `<i class='fa-regular fa-clock'></i> ${mins}p ${secs}s`;
            }, 1000);
        });
    });
    </script>
@endsection