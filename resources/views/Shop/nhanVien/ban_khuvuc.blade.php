@extends('layouts.shop.layout-nhanvien')

@section('title', 'Quản lý bàn ăn - Buffet Ocean')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        :root { --primary: #fea116; --primary-hover: #db8a10; --dark: #0f172b; --light: #F1F8FF; --text-main: #1e293b; --white: #ffffff; --radius: 4px; --shadow-card: 0 0 45px rgba(0, 0, 0, 0.08); }
        body { font-family: 'Nunito', sans-serif; background-color: var(--light); color: var(--text-main); }
        
        .page-header-title { font-family: 'Heebo', sans-serif; font-weight: 800; color: var(--dark); text-transform: uppercase; position: relative; padding-left: 15px; font-size: 1.5rem; }
        .page-header-title::before { content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%); height: 80%; width: 5px; background-color: var(--primary); border-radius: 2px; }

        .card-zone { border: none; border-radius: var(--radius); box-shadow: var(--shadow-card); background: var(--white); margin-bottom: 24px; overflow: hidden; }
        .card-zone-header { background: var(--dark); color: var(--primary); padding: 15px 20px; font-family: 'Heebo', sans-serif; font-weight: 700; text-transform: uppercase; border-bottom: 3px solid var(--primary); }

        .table-card { background: var(--white); border-radius: var(--radius); padding: 15px; height: 100%; min-height: 230px; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #e2e8f0; transition: all 0.3s ease; position: relative; overflow: hidden; }
        .table-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-color: var(--primary); }
        .card-active { background: #fff8e6; border: 1px solid var(--primary); }
        .card-reserved { background: #f1f5f9; border: 1px dashed #94a3b8; }

        .table-number { font-family: 'Heebo', sans-serif; font-size: 2rem; font-weight: 800; color: var(--dark); line-height: 1; }
        .table-icon-bg { position: absolute; top: -10px; right: -10px; font-size: 4rem; color: rgba(254, 161, 22, 0.1); transform: rotate(15deg); }

        .badge-custom { padding: 5px 12px; border-radius: 0px; font-weight: 700; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; }
        .badge-trong { background: rgba(32, 212, 137, 0.1); color: #10b981; }
        .badge-co-khach { background: var(--primary); color: #fff; }
        .badge-dadat { background: #e2e8f0; color: #475569; }

        .btn-ocean { background: var(--primary); color: #fff; border: none; border-radius: 2px; font-weight: 700; text-transform: uppercase; padding: 10px; width: 100%; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; }
        .btn-ocean:hover { background: var(--primary-hover); color: #fff; box-shadow: 0 5px 15px rgba(254, 161, 22, 0.3); }
        .btn-ocean-outline { background: transparent; border: 2px solid var(--primary); color: var(--primary); border-radius: 2px; font-weight: 700; text-transform: uppercase; padding: 8px; width: 100%; display: block; text-align: center; text-decoration: none; transition: 0.3s; }
        .btn-ocean-outline:hover { background: var(--primary); color: #fff; }

        .customer-info { background: #fff; border-left: 4px solid var(--primary); padding: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-top: 10px; }
        .live-timer { font-family: 'JetBrains Mono', monospace; background: var(--dark); color: var(--primary); padding: 4px 8px; border-radius: 4px; font-weight: bold; display: inline-flex; align-items: center; gap: 5px; font-size: 0.85rem; margin-top: 5px; }
        .timer-dot { width: 6px; height: 6px; background: #ef4444; border-radius: 50%; animation: blink 1s infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        .sidebar-card { border: none; border-radius: var(--radius); box-shadow: var(--shadow-card); background: #fff; }
        .sidebar-header { background: var(--dark); color: #fff; padding: 15px; font-weight: 700; text-transform: uppercase; font-family: 'Heebo', sans-serif; }
        .incoming-item { padding: 15px; border-bottom: 1px solid #f1f5f9; transition: 0.2s; }
        .incoming-item:hover { background: #fffcf5; }
        .form-search { border: 2px solid #e2e8f0; padding: 8px 15px; border-radius: 4px; width: 100%; outline: none; }
        .form-search:focus { border-color: var(--primary); }

        /* --- SỬA LỖI SWITCH --- */
        .table-row-item {
            background: #fff; border: 1px solid #f1f5f9; padding: 10px 15px; border-radius: 6px;
            display: flex; justify-content: space-between; align-items: center; transition: 0.2s; margin-bottom: 8px;
        }
        .table-row-item:hover { border-color: var(--primary); background: #fffcf5; }
        .table-row-item.locked { opacity: 0.6; background: #f8fafc; pointer-events: none; }

        /* Container của nút gạt - Dùng DIV thay vì LABEL để tránh lỗi click */
        .toggle-switch-container {
            display: flex; background: #e2e8f0; border-radius: 20px; padding: 3px;
            position: relative; width: 140px; height: 34px; user-select: none;
        }
        
        /* Ẩn input radio */
        .toggle-switch-container input[type="radio"] { display: none; }
        
        /* Style cho 2 nút bấm */
        .toggle-btn {
            flex: 1; text-align: center; font-size: 0.75rem; font-weight: 700; z-index: 2;
            line-height: 28px; transition: 0.3s; color: #64748b; border-radius: 17px; cursor: pointer;
        }
        
        /* Hiệu ứng khi được chọn (Checked) */
        /* Nếu input value="trong" được check -> label kế tiếp nó (nút Mở) đổi màu */
        .toggle-switch-container input[value="trong"]:checked ~ .lbl-open {
            background: #10b981; color: white; box-shadow: 0 2px 4px rgba(16,185,129,0.3);
        }
        /* Nếu input value="khong_su_dung" được check -> label kế tiếp (nút Đóng) đổi màu */
        .toggle-switch-container input[value="khong_su_dung"]:checked ~ .lbl-close {
            background: #64748b; color: white; box-shadow: 0 2px 4px rgba(100,116,139,0.3);
        }
    </style>

    {{-- Flash messages --}}
    @if (session('success')) <div class="alert alert-success m-4 mb-0 border-0 shadow-sm"><i class="fa-solid fa-check-circle"></i> {{ session('success') }}</div> @endif
    @if (session('error')) <div class="alert alert-danger m-4 mb-0 border-0 shadow-sm"><i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}</div> @endif

    <div class="container-fluid py-4 px-4">
        
        {{-- HEADER TRANG --}}
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h4 class="page-header-title m-0">Quản lý bàn ăn</h4>
                <p class="text-muted small ms-3 mb-0 mt-1">Theo dõi trạng thái và phục vụ khách hàng</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-warning shadow-sm border fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#modalBatchUpdate">
                    <i class="fas fa-sliders-h"></i> Cài đặt trạng thái
                </button>
                <a href="{{ route('nhanVien.ban-an.index') }}" class="btn btn-white shadow-sm border fw-bold text-dark">
                    <i class="fa-solid fa-rotate text-primary"></i> Làm mới dữ liệu
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- CỘT TRÁI: DANH SÁCH BÀN --}}
            <div class="col-lg-9 col-md-8">
                @foreach ($khuVucs as $khu)
                <div class="card-zone">
                    <div class="card-zone-header d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-utensils me-2"></i> {{ $khu->ten_khu_vuc }}</span>
                        <span class="badge bg-white text-dark" style="opacity: 0.9;">Tầng {{ $khu->tang }}</span>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
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
                                    <i class="fa-solid fa-bowl-food table-icon-bg"></i>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-start mb-2 position-relative">
                                            <div class="table-number">{{ $ban->so_ban }}</div>
                                            <div class="text-secondary fw-bold small"><i class="fa-solid fa-chair text-primary"></i> {{ $ban->so_ghe }}</div>
                                        </div>
                                        <div class="mb-3">
                                            @if($khachDangNgoi) <span class="badge-custom badge-co-khach">Đang phục vụ</span>
                                            @elseif($donDatTruoc) <span class="badge-custom badge-dadat">Đã đặt trước</span>
                                            @elseif($ban->trang_thai == 'khong_su_dung') <span class="badge-custom badge-dadat">Bảo trì</span>
                                            @else <span class="badge-custom badge-trong">Bàn trống</span> @endif
                                        </div>
                                        @if($khachDangNgoi)
                                            <div class="customer-info">
                                                <div class="fw-bold text-dark text-truncate">{{ $khachDangNgoi->ten_khach }}</div>
                                                <div class="small text-muted mt-1"><i class="fa-solid fa-clock"></i> Vào: {{ \Carbon\Carbon::parse($khachDangNgoi->gio_den)->format('H:i') }}</div>
                                                <div class="live-timer" data-start="{{ $khachDangNgoi->gio_den }}">
                                                    <div class="timer-dot"></div> <span class="timer-text">00:00:00</span>
                                                </div>
                                            </div>
                                        @elseif($donDatTruoc)
                                            <div class="p-2 bg-light rounded border border-dashed mt-2">
                                                <div class="small fw-bold text-primary">SẮP ĐẾN:</div>
                                                <div class="fw-bold text-dark">{{ $donDatTruoc->ten_khach }}</div>
                                                <div class="small text-danger"><i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($donDatTruoc->gio_den)->format('H:i') }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 position-relative" style="z-index: 2;">
                                        @if($donDatTruoc)
                                            <a href="{{ route('nhanVien.ban-an.show-checkin-dattruoc', $donDatTruoc->id) }}" class="btn-ocean-outline">Check-in</a>
                                        @endif
                                        @if($khachDangNgoi)
                                            <a href="{{ route('nhanVien.thanh-toan.ban', $ban->id) }}" class="btn-ocean"><i class="fa-solid fa-file-invoice-dollar"></i> Thanh toán</a>
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
                <div class="sidebar-card sticky-top" style="top: 20px; z-index: 99;">
                    <div class="sidebar-header"><span><i class="fa-solid fa-clipboard-list me-2"></i> Danh sách chờ</span></div>
                    <div class="p-3 border-bottom">
                         <form method="get" action="{{ route('nhanVien.ban-an.index') }}" class="d-flex gap-2">
                            <input type="text" name="search" class="form-search" placeholder="Tìm tên hoặc SĐT..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-warning text-white"><i class="fa-solid fa-search"></i></button>
                        </form>
                    </div>
                    <div class="card-body p-0">
                        <div style="max-height: 70vh; overflow-y: auto;">
                            @forelse ($datBans as $datban)
                            <div class="incoming-item">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="fw-bold text-dark" style="font-size: 1.1rem;">{{ \Carbon\Carbon::parse($datban->gio_den)->format('H:i') }}</span>
                                    @if($datban->trang_thai === 'da_xac_nhan') <span class="badge bg-warning text-dark">Sắp đến</span>
                                    @elseif($datban->trang_thai === 'khach_da_den') <span class="badge bg-success">Đang ăn</span> @endif
                                </div>
                                <h6 class="fw-bold text-primary m-0">{{ $datban->ten_khach }}</h6>
                                <div class="small text-muted mb-2"><i class="fa-solid fa-phone me-1"></i> {{ $datban->sdt_khach }}</div>
                                <div class="small text-muted">Mã: #{{ $datban->ma_dat_ban }}</div>
                                @if($datban->trang_thai === 'da_xac_nhan')
                                    <div class="mt-2 text-end">
                                        <a href="{{ route('nhanVien.ban-an.show-checkin-dattruoc', $datban->id) }}" class="btn btn-sm btn-ocean" style="width: auto; display: inline-block;">Check-in</a>
                                    </div>
                                @endif
                            </div>
                            @empty
                                <div class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" width="60" style="opacity: 0.5; margin-bottom: 10px;">
                                    <p class="text-muted small">Không có khách đặt trước</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CẬP NHẬT TRẠNG THÁI --}}
    <div class="modal fade" id="modalBatchUpdate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning border-0">
                    <h5 class="modal-title text-dark fw-bold"><i class="fas fa-sliders-h me-2"></i> Cài đặt trạng thái bàn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body bg-light">
                    <div class="alert alert-white border shadow-sm mb-4">
                        <i class="fas fa-info-circle text-primary"></i> 
                        Chuyển đổi nhanh: <b>Mở</b> (Trống) / <b>Đóng</b> (Bảo trì). Bàn đang có khách sẽ bị khóa.
                    </div>

                    <form id="formBatchUpdate">
                        @csrf
                        @foreach ($khuVucs as $khu)
                            <div class="mb-4">
                                <h6 class="fw-bold text-uppercase text-secondary mb-3 ms-1 border-bottom pb-2">
                                    {{ $khu->ten_khu_vuc }} <span class="fw-normal text-muted">(Tầng {{ $khu->tang }})</span>
                                </h6>
                                <div class="row g-2">
                                    @foreach($khu->banAns as $ban)
                                        @php
                                            $isLocked = in_array($ban->trang_thai, ['dang_phuc_vu', 'da_dat', 'da_xac_nhan']);
                                            $lockReason = ($ban->trang_thai == 'dang_phuc_vu') ? 'Có khách' : 'Đã đặt';
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="table-row-item {{ $isLocked ? 'locked' : '' }}">
                                                {{-- Thông tin bàn --}}
                                                <div>
                                                    <div class="fw-bold text-dark fs-6">{{ $ban->so_ban }}</div>
                                                    @if($isLocked) <small class="text-danger fw-bold"><i class="fas fa-lock"></i> {{ $lockReason }}</small>
                                                    @else <small class="text-muted">{{ $ban->so_ghe }} ghế</small> @endif
                                                </div>

                                                {{-- SWITCH ĐÃ SỬA LẠI (Dùng DIV + LABEL) --}}
                                                <div class="toggle-switch-container">
                                                    {{-- Input ID bàn để gửi lên server --}}
                                                    <input type="hidden" name="changes[{{ $ban->id }}][id]" value="{{ $ban->id }}" {{ $isLocked ? 'disabled' : '' }}>

                                                    {{-- Radio "Mở" --}}
                                                    <input type="radio" id="open_{{ $ban->id }}" name="changes[{{ $ban->id }}][status]" value="trong" 
                                                        {{ $ban->trang_thai == 'trong' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>
                                                    
                                                    {{-- Radio "Đóng" --}}
                                                    <input type="radio" id="close_{{ $ban->id }}" name="changes[{{ $ban->id }}][status]" value="khong_su_dung" 
                                                        {{ $ban->trang_thai == 'khong_su_dung' ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }}>

                                                    {{-- Các nút bấm Label trỏ tới ID của input --}}
                                                    <label for="open_{{ $ban->id }}" class="toggle-btn lbl-open">Mở</label>
                                                    <label for="close_{{ $ban->id }}" class="toggle-btn lbl-close">Đóng</label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </form>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Thoát</button>
                    <button type="button" class="btn btn-warning fw-bold px-4 shadow-sm" onclick="submitBatchUpdate()">
                        <i class="fas fa-save me-1"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT --}}
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
                    const textSpan = timer.querySelector('.timer-text');
                    if(textSpan) textSpan.innerText = `${diffHrs.toString().padStart(2, '0')}:${diffMins.toString().padStart(2, '0')}:${diffSecs.toString().padStart(2, '0')}`;
                }
            });
        }
        setInterval(updateTimers, 1000); updateTimers();
    });

    // AJAX
    function submitBatchUpdate() {
        let form = document.getElementById('formBatchUpdate');
        let formData = new FormData(form);
        let hasData = false;
        for (let pair of formData.entries()) { if (pair[0].includes('[id]')) { hasData = true; break; } }

        if (!hasData) { alert('Không có thay đổi nào hợp lệ!'); return; }

        let btn = document.querySelector('#modalBatchUpdate .btn-warning');
        let originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
        btn.disabled = true;

        $.ajax({
            url: "{{ route('nhanVien.ban-an.update_batch') }}", // Kiểm tra đúng tên route
            type: "POST",
            data: formData,
            processData: false, contentType: false,
            success: function(response) {
                if(response.success) {
                    location.reload(); 
                } else {
                    alert('❌ Lỗi: ' + response.message);
                    btn.innerHTML = originalText; btn.disabled = false;
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('❌ Lỗi hệ thống!');
                btn.innerHTML = originalText; btn.disabled = false;
            }
        });
    }
    </script>
@endsection