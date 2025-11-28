@extends('layouts.shop.layout-nhanvien')
@section('title', 'Quản lý Đặt Bàn')

@section('content')
    {{-- 1. IMPORT FONTS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    {{-- 2. CSS STYLING (Design System) --}}
    <style>
        :root {
            --primary: #fea116; --primary-dark: #d98a12;
            --dark: #0f172b; --white: #ffffff;
            --text-main: #1e293b; --text-sub: #64748b;
            --bg-light: #f8f9fa;
            --radius: 8px;
            --shadow-card: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            --anim-fast: 0.2s ease;
        }

        body { font-family: 'Nunito', sans-serif; background-color: var(--bg-light); color: var(--text-main); }
        h4, h5, strong, .font-heading { font-family: 'Heebo', sans-serif; }

        /* --- HEADER & BUTTONS --- */
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .page-title { color: var(--dark); font-weight: 800; font-size: 1.5rem; text-transform: uppercase; margin: 0; }

        .btn-create {
            background: var(--primary); color: var(--white); border: none; padding: 10px 20px;
            border-radius: 6px; font-weight: 700; font-family: 'Heebo'; text-transform: uppercase;
            font-size: 0.85rem; box-shadow: 0 4px 10px rgba(254, 161, 22, 0.3); transition: var(--anim-fast);
            text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-create:hover { background: var(--primary-dark); transform: translateY(-2px); color: var(--white); }

        /* --- FILTER CARD --- */
        .card-filter {
            background: var(--white); border-radius: var(--radius); border: 1px solid #f1f5f9;
            box-shadow: var(--shadow-card); overflow: hidden; margin-bottom: 24px;
        }
        .filter-header {
            background: var(--dark); color: var(--white); padding: 12px 20px;
            font-family: 'Heebo'; font-weight: 700; font-size: 0.95rem; text-transform: uppercase;
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.1) 1px, transparent 0);
            background-size: 20px 20px;
        }
        .filter-body { padding: 20px; }
        
        .form-label-custom { font-size: 0.8rem; font-weight: 700; color: var(--text-sub); text-transform: uppercase; margin-bottom: 5px; }
        .form-control-custom, .form-select-custom {
            border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; font-size: 0.9rem; width: 100%;
        }
        .form-control-custom:focus, .form-select-custom:focus {
            outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(254, 161, 22, 0.1);
        }

        .btn-search { background: var(--dark); color: var(--white); border: none; }
        .btn-search:hover { background: #1e293b; }
        .btn-reset { background: #e2e8f0; color: var(--text-sub); border: none; }
        .btn-reset:hover { background: #cbd5e1; color: var(--dark); }
        .btn-filter-action {
            padding: 8px 16px; border-radius: 6px; font-weight: 700; font-size: 0.85rem; transition: 0.2s;
        }

        /* --- TABLE STYLE --- */
        .card-table { border: none; border-radius: var(--radius); box-shadow: var(--shadow-card); overflow: hidden; background: var(--white); }
        .custom-table thead th {
            background: #f8fafc; color: var(--text-sub); font-weight: 700; text-transform: uppercase;
            font-size: 0.75rem; border-bottom: 2px solid #e2e8f0; padding: 12px; text-align: center;
        }
        .custom-table tbody td {
            vertical-align: middle; padding: 12px; border-bottom: 1px dashed #f1f5f9;
            color: var(--text-main); font-size: 0.9rem;
        }
        .custom-table tbody tr:hover { background: #fdfdfd; }

        /* --- BADGES & TAGS --- */
        .badge-pill { padding: 5px 10px; border-radius: 30px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; display: inline-block; }
        /* Chờ xác nhận: Vàng */
        .st-cho { background: #fffbeb; color: #b45309; border: 1px solid #fcd34d; }
        /* Đã xác nhận: Xanh dương */
        .st-xac-nhan { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        /* Đang phục vụ: Cam */
        .st-phuc-vu { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        /* Hoàn tất: Xanh lá */
        .st-hoan-tat { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        /* Hủy: Đỏ */
        .st-huy { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        .tag-table { background: #f1f5f9; color: var(--dark); padding: 2px 8px; border-radius: 4px; font-weight: 700; font-size: 0.8rem; }

        /* --- ACTION BUTTONS SMALL --- */
        .btn-act-sm {
            width: 28px; height: 28px; border-radius: 4px; display: inline-flex;
            align-items: center; justify-content: center; border: none; transition: 0.2s; cursor: pointer;
        }
        .btn-check { background: #ecfdf5; color: #059669; } .btn-check:hover { background: #059669; color: white; }
        .btn-cancel { background: #fef2f2; color: #dc2626; } .btn-cancel:hover { background: #dc2626; color: white; }
        .btn-serve { background: #fff7ed; color: #ea580c; } .btn-serve:hover { background: #ea580c; color: white; }
        .btn-finish { background: #f1f5f9; color: #475569; } .btn-finish:hover { background: #475569; color: white; }

        /* Countdown */
        .countdown-timer { font-weight: 700; font-family: 'Heebo'; }
        .blink-slow { animation: blinker 1.5s linear infinite; }
        .blink-fast { animation: blinker 0.8s linear infinite; }
        @keyframes blinker { 50% { opacity: 0.4; } }

        /* --- ACTION BUTTONS NEW STYLE --- */
.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: .2s;
    white-space: nowrap;
}

/* Xác nhận (Chờ xác nhận → Đã xác nhận) */
.btn-accept {
    background: #fffbeb; /* vàng nhạt */
    color: #b45309;       /* vàng đậm */
    border: 1px solid #fcd34d;
}
.btn-accept:hover {
    background: #b45309;
    color: #fff;
}

/* Hủy */
.btn-reject {
    background: #fef2f2; /* đỏ nhạt */
    color: #b91c1c;       /* đỏ đậm */
    border: 1px solid #fecaca;
}
.btn-reject:hover {
    background: #b91c1c;
    color: #fff;
}

/* Khách tới (Đang phục vụ) */
.btn-arrived {
    background: #fff7ed; /* cam nhạt */
    color: #c2410c;       /* cam đậm */
    border: 1px solid #fed7aa;
}
.btn-arrived:hover {
    background: #c2410c;
    color: #fff;
}

/* Kết thúc (Hoàn tất) */
.btn-finish {
    background: #ecfdf5; /* xanh lá nhạt */
    color: #047857;       /* xanh lá đậm */
    border: 1px solid #a7f3d0;
}
.btn-finish:hover {
    background: #047857;
    color: #fff;
}

    </style>

    <div class="container py-4">
        
        {{-- HEADER --}}
        <div class="page-header">
            <h4 class="page-title"><i class="fa-solid fa-clipboard-list text-primary"></i> Danh sách đặt bàn</h4>
            <a href="{{ route('nhanVien.datban.create') }}" class="btn-create">
                <i class="fa-solid fa-plus"></i> Tạo mới
            </a>
        </div>

        {{-- FLASH MESSAGES --}}
        @foreach(['success', 'error'] as $msg)
            @if(session($msg))
                <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }} alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="font-weight: 600;">
                    <i class="fa-solid fa-{{ $msg == 'success' ? 'check-circle' : 'exclamation-triangle' }} me-2"></i> {{ session($msg) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endforeach

        {{-- FILTER CARD --}}
        <div class="card-filter">
            <div class="filter-header"><i class="fa-solid fa-filter me-2"></i> Bộ lọc tìm kiếm</div>
            <div class="filter-body">
                <form action="" method="GET">
                    <div class="row g-3 align-items-end">
                        {{-- Trạng thái --}}
                        <div class="col-md-3">
                            <label class="form-label-custom">Trạng thái</label>
                            <select name="trang_thai" class="form-select-custom">
                                <option value="">-- Tất cả --</option>
                                <option value="cho_xac_nhan" {{ request('trang_thai')=='cho_xac_nhan'?'selected':'' }}>Chờ xác nhận</option>
                                <option value="da_xac_nhan" {{ request('trang_thai')=='da_xac_nhan'?'selected':'' }}>Đã xác nhận</option>
                                <option value="khach_da_den" {{ request('trang_thai')=='khach_da_den'?'selected':'' }}>Đang phục vụ</option>
                                <option value="hoan_tat" {{ request('trang_thai')=='hoan_tat'?'selected':'' }}>Hoàn tất</option>
                                <option value="huy" {{ request('trang_thai')=='huy'?'selected':'' }}>Đã hủy</option>
                            </select>
                        </div>
                        {{-- Mã --}}
                        <div class="col-md-3">
                            <label class="form-label-custom">Mã đặt bàn</label>
                            <input type="text" name="ma" class="form-control-custom" placeholder="Nhập mã..." value="{{ request('ma') }}">
                        </div>
                        {{-- Bàn --}}
                        <div class="col-md-3">
                            <label class="form-label-custom">Số bàn</label>
                            <input type="text" name="ban" class="form-control-custom" placeholder="VD: 10..." value="{{ request('ban') }}">
                        </div>
                        {{-- Khách --}}
                        <div class="col-md-3">
                            <label class="form-label-custom">Khách hàng</label>
                            <input type="text" name="khach" class="form-control-custom" placeholder="Tên hoặc SĐT..." value="{{ request('khach') }}">
                        </div>
                        
                        {{-- Buttons --}}
                        <div class="col-12 mt-3 d-flex gap-2">
                            <button type="submit" class="btn-filter-action btn-search">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Tìm kiếm
                            </button>
                            <a href="{{ route('nhanVien.datban.index') }}" class="btn-filter-action btn-reset text-decoration-none">
                                <i class="fa-solid fa-rotate-right me-1"></i> Đặt lại
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE CARD --}}
        <div class="card-table">
            <div class="table-responsive">
                <table class="table custom-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Mã Đặt</th>
                            <th>Bàn</th>
                            <th class="text-start">Khách hàng</th>
                            <th>Số khách</th>
                            <th>Giờ đến</th>
                            <th class="text-start">Combo</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th style="width: 100px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ds as $index => $d)
                        <tr style="{{ $d->trang_thai == 'huy' ? 'opacity: 0.6;' : '' }}">
                            <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                            <td class="text-center font-heading text-primary fw-bold">{{ $d->ma_dat_ban }}</td>
                            <td class="text-center">
                                @if ($d->banAn)
                                    <span class="tag-table">Bàn {{ $d->banAn->so_ban }}</span>
                                    <div class="small text-muted mt-1" style="font-size: 0.7rem;">{{ $d->banAn->khuVuc->ten_khu_vuc ?? '' }}</div>
                                @else
                                    <span class="text-muted small fst-italic">Chưa xếp</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $d->ten_khach }}</div>
                                <div class="small text-muted"><i class="fa-solid fa-phone me-1" style="font-size: 0.7rem;"></i>{{ $d->sdt_khach }}</div>
                            </td>
                            <td class="text-center fw-bold">{{ $d->so_khach }}</td>
                            <td class="text-center small">
                                @if($d->gio_den)
                                    <div class="fw-bold">{{ \Carbon\Carbon::parse($d->gio_den)->format('H:i') }}</div>
                                    <div class="text-muted">{{ \Carbon\Carbon::parse($d->gio_den)->format('d/m') }}</div>
                                @else - @endif
                            </td>
                            
                            <td>
                                @if ($d->comboBuffet)
                                    <span class="fw-bold text-dark">{{ $d->comboBuffet->ten_combo }}</span>
                                    <span class="text-muted small">({{ $d->thoi_luong_phut ?? $d->comboBuffet->thoi_luong_phut }}p)</span>
                                @else
                                    <span class="text-muted small fst-italic">Chưa chọn</span>
                                @endif
                            </td>

                            {{-- COUNTDOWN --}}
                            <td class="text-center small">
                                @if ($d->trang_thai == 'khach_da_den' && $d->comboBuffet)
                                    @php
                                        $orderDau = \App\Models\OrderMon::where('dat_ban_id', $d->id)->orderBy('created_at', 'asc')->first();
                                        $thoiLuong = $d->comboBuffet->thoi_luong_phut ?? 120;
                                        $endTime = $orderDau ? \Carbon\Carbon::parse($orderDau->created_at)->addMinutes($thoiLuong)->timestamp * 1000 : null;
                                    @endphp
                                    @if ($endTime)
                                        <span class="countdown-timer text-primary" data-endtime="{{ $endTime }}">...</span>
                                    @else
                                        <span class="text-muted">Chưa gọi món</span>
                                    @endif
                                @elseif(in_array($d->trang_thai, ['cho_xac_nhan', 'da_xac_nhan']))
                                    <span class="text-muted">Chưa đến</span>
                                @else
                                    -
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td class="text-center">
                                @php
                                    $st = $d->trang_thai;
                                    $badgeClass = 'bg-secondary text-white';
                                    $badgeText = $st;
                                    if($st == 'cho_xac_nhan') { $badgeClass = 'st-cho'; $badgeText = 'Chờ duyệt'; }
                                    elseif($st == 'da_xac_nhan') { $badgeClass = 'st-xac-nhan'; $badgeText = 'Đã duyệt'; }
                                    elseif($st == 'khach_da_den') { $badgeClass = 'st-phuc-vu'; $badgeText = 'Đang ăn'; }
                                    elseif($st == 'hoan_tat') { $badgeClass = 'st-hoan-tat'; $badgeText = 'Hoàn tất'; }
                                    elseif($st == 'huy') { $badgeClass = 'st-huy'; $badgeText = 'Hủy'; }
                                @endphp
                                <span class="badge-pill {{ $badgeClass }}">{{ $badgeText }}</span>
                            </td>

                            {{-- ACTIONS --}}
<td class="text-center">
    <div class="d-flex justify-content-center flex-wrap gap-1">

        {{-- 1. CHỜ XÁC NHẬN --}}
        @if ($d->trang_thai == 'cho_xac_nhan')

            {{-- Xác nhận --}}
            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">
                @csrf
                <input type="hidden" name="trang_thai" value="da_xac_nhan">
                <button class="action-btn btn-accept" onclick="return confirm('Xác nhận đơn này?')">
                    <i class="fa-solid fa-check"></i>
                    Xác nhận
                </button>
            </form>

            {{-- Hủy --}}
            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">
                @csrf
                <input type="hidden" name="trang_thai" value="huy">
                <button class="action-btn btn-reject" onclick="return confirm('Hủy đơn này?')">
                    <i class="fa-solid fa-xmark"></i>
                    Hủy
                </button>
            </form>

        {{-- 2. ĐÃ XÁC NHẬN — Khách đến --}}
        @elseif($d->trang_thai == 'da_xac_nhan')
            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">
                @csrf
                <input type="hidden" name="trang_thai" value="khach_da_den">
                <button class="action-btn btn-arrived" onclick="return confirm('Khách đã đến?')">
                    <i class="fa-solid fa-person-walking"></i>
                    Khách tới
                </button>
            </form>

        {{-- 3. ĐANG PHỤC VỤ — Kết thúc --}}
        @elseif($d->trang_thai == 'khach_da_den')
            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">
                @csrf
                <input type="hidden" name="trang_thai" value="hoan_tat">
                <button class="action-btn btn-finish" onclick="return confirm('Kết thúc bàn này?')">
                    <i class="fa-solid fa-flag-checkered"></i>
                    Kết thúc
                </button>
            </form>

        {{-- 4. Hoàn tất / Hủy --}}
        @else
            <span class="text-muted">-</span>
        @endif

    </div>
</td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-inbox fa-2x mb-2 opacity-25"></i>
                                <p class="fw-bold mb-0">Không tìm thấy dữ liệu đặt bàn.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Script đồng hồ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateCountdowns() {
                const timers = document.querySelectorAll('.countdown-timer');
                const now = new Date().getTime();
                const fiveMinutes = 5 * 60 * 1000;

                timers.forEach(timer => {
                    const endTime = parseInt(timer.getAttribute('data-endtime'));
                    if (isNaN(endTime)) return;
                    const distance = endTime - now;

                    if (distance < 0) {
                        timer.innerHTML = "HẾT GIỜ";
                        timer.className = "countdown-timer text-danger fw-bold blink-fast";
                        return;
                    }

                    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((distance % (1000 * 60)) / 1000);
                    const timeString = `${h < 10 ? "0"+h : h}:${m < 10 ? "0"+m : m}:${s < 10 ? "0"+s : s}`;

                    if (distance <= fiveMinutes) {
                        timer.innerHTML = `${timeString} <i class="fa-solid fa-triangle-exclamation"></i>`;
                        timer.className = "countdown-timer text-warning fw-bold blink-slow";
                    } else {
                        timer.innerHTML = timeString;
                        timer.className = "countdown-timer text-success fw-bold";
                    }
                });
            }
            setInterval(updateCountdowns, 1000);
            updateCountdowns();
        });
    </script>
@endsection