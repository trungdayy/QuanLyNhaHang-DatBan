@extends('layouts.shop.layout-nhanvien')

@section('title', 'Quản lý bàn ăn')

@section('content')

{{-- Flash messages --}}
@if (session('success'))
    <div class="alert alert-success mb-2">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger mb-2">{{ session('error') }}</div>
@endif

<div class="container py-4">
    <h3 class="mb-4">Quản lý bàn ăn - Nhân viên trực</h3>

    <div class="row">
        {{-- Left: Sơ đồ bàn --}}
        <div class="col-md-8">
            @foreach ($khuVucs as $khu)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ $khu->ten_khu_vuc }}</strong>
                    <small class="text-muted"> - Tầng {{ $khu->tang }} -</small>
                    <form method="get" action="{{ route('nhanVien.ban-an.index') }}">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Làm mới</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($khu->banAns as $ban)
                        @php
                            $status = $ban->trang_thai;
                            $isHeld = $ban->giu_den && $ban->giu_den > now();
                        @endphp
                        <div class="col-6 col-sm-4 col-md-3 mb-3">
                            <div class="table-card p-3 rounded shadow-sm text-center">
                                <div class="table-number mb-1">{{ $ban->so_ban }}</div>
                                <div class="table-seats small mb-1">{{ $ban->so_ghe }} chỗ</div>

                                @if(!empty($ban->khach_dang_ngoi))
                                    <div class="text-success mb-1">
                                        <small>
                                            {{ $ban->khach_dang_ngoi }}<br>
                                            (Bắt đầu: {{ \Carbon\Carbon::parse($ban->gio_bat_dau)->format('H:i') }})
                                        </small>
                                    </div>
                                @endif

                                <div class="table-status mb-2">
                                    @if ($status === 'trong' && !$isHeld)
                                        <span class="badge badge-status badge-trong">Trống</span>
                                    @elseif($status === 'dang_phuc_vu' || $status === 'khach_da_den')
                                        <span class="badge badge-status badge-co-khach">Đang phục vụ</span>
                                    @elseif ($status === 'da_dat' && $isHeld)
                                        <span class="badge badge-status badge-giu" data-countdown="{{ $ban->giu_den }}">
                                            Đang giữ...
                                        </span>
                                    @elseif ($status === 'da_dat')
                                        <span class="badge badge-status badge-dadat">Đã đặt</span>
                                    @else
                                        <span class="badge badge-status badge-khong-su-dung">Không dùng</span>
                                    @endif
                                </div>

                                {{-- Check-in khách đặt trước --}}
                                @php
                                    $datBan = $datBans->firstWhere('ban_id', $ban->id);
                                @endphp
                                @if($datBan && $datBan->trang_thai === 'da_xac_nhan')
                                    <form method="POST" action="{{ route('nhanVien.ban-an.check-in-dattruoc') }}">
                                        @csrf
                                        <input type="hidden" name="dat_ban_id" value="{{ $datBan->id }}">
                                        <button class="btn btn-sm btn-primary mb-1">Check-in</button>
                                    </form>
                                @endif

                                @if ($status === 'dang_phuc_vu' || $status === 'khach_da_den')
                                    <button class="btn btn-sm btn-warning mt-1">Thanh toán</button>
                                @endif

                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        

        {{-- Right: Khách sắp đến --}}
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Khách sắp đến</strong>
                    {{-- Form tìm kiếm --}}
                    <form method="get" action="{{ route('nhanVien.ban-an.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm mr-1" placeholder=" Tên/Mã/SDT khách" value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Tìm</button>
                    </form>
                </div>
                <div class="card-body">
                    @forelse ($datBans as $datban)
                    <div class="mb-2 p-2 border rounded d-flex justify-content-between align-items-center">
                        <div>
                            <div><strong>Mã đặt: </strong>{{ $datban->ma_dat_ban ?? '-' }}</div>
                            <div><strong>Khách: </strong>{{ $datban->ten_khach }}</div>
                            <div><strong>SĐT: </strong>{{ $datban->sdt_khach }}</div>
                            <div><strong>Giờ đến: </strong>{{ \Carbon\Carbon::parse($datban->gio_den)->format('H:i') }}</div>
                            <div><strong>Bàn: </strong>{{ $datban->banAn?->so_ban ?? 'Chưa chọn' }} ({{ $datban->banAn?->khuVuc?->ten_khu_vuc ?? '-' }})</div>
                        </div>

                        <div class="text-right">
                            @if($datban->trang_thai === 'da_xac_nhan')
                                <form method="POST" action="{{ route('nhanVien.ban-an.check-in-dattruoc') }}">
                                    @csrf
                                    <input type="hidden" name="dat_ban_id" value="{{ $datban->id }}">
                                    <button class="btn btn-sm btn-primary mb-1">Check-in</button>
                                </form>
                            @elseif($datban->trang_thai === 'khach_da_den')
                                <span class="badge badge-success p-2">✓ Đã check-in</span>
                            @endif
                        </div>
                    </div>
                    @empty
                        <p class="text-muted">Không tìm thấy khách nào.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

{{-- COUNTDOWN SCRIPT --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const countdowns = document.querySelectorAll("[data-countdown]");
    countdowns.forEach(el => {
        const endTime = new Date(el.getAttribute("data-countdown")).getTime();
        const timer = setInterval(function () {
            const now = new Date().getTime();
            const distance = endTime - now;
            if (distance <= 0) {
                el.innerHTML = "Hết giữ";
                el.classList.remove("badge-giu");
                el.classList.add("badge-dadat");
                clearInterval(timer);
                return;
            }
            const mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const secs = Math.floor((distance % (1000 * 60)) / 1000);
            el.innerHTML = `Giữ còn: ${mins}p ${secs}s`;
        }, 1000);
    });
});
</script>

<style>
/* ======= CARD BÀN ĂN ======= */
.table-card {
    min-height: 190px; /* hoặc 180-200 tùy bạn */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.table-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

/* ======= SỐ BÀN ======= */
.table-number {
    font-size: 20px;
    font-weight: 700;
    color: #333;
}

/* ======= BADGE TRẠNG THÁI ======= */
.badge-status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
}

/* Trống */
.badge-trong {
    background: #e9f9ef;
    color: #1e7c35;
    border: 1px solid #bde8ca;
}

/* Giữ bàn */
.badge-giu {
    background: #ffde83;
    color: #9a6b00;
    border: 1px solid #ffe0a8;
}

/* Đã đặt */
.badge-dadat {
    background: #ff8244;
    color: #9a4a00;
    border: 1px solid #ffcfb3;
}

/* Có khách */
.badge-co-khach {
    background: #ff5b70;
    color: #b20016;
    border: 1px solid #ffb8c0;
}

/* Không dùng */
.badge-khong-su-dung {
    background: #eef0f3;
    color: #5c636a;
    border: 1px solid #d6d8db;
}

/* Badge success */
.badge-success {
    background: #28a745;
    color: #fff;
    border-radius: 20px;
}

/* ======= NÚT ======= */
.btn-sm {
    border-radius: 8px;
}

/* Nâng giao diện nút Thanh toán */
.btn-warning {
    background: #ffc107;
    border: none;
    color: #000;
    font-weight: 600;
}

/* Nút check-in */
.btn-primary {
    font-weight: 600;
}

/* ======= CỘT PHẢI (khách sắp đến) ======= */
.card-header {
    background: #fafafa !important;
    border-bottom: 1px solid #eee !important;
}

.card {
    border-radius: 14px !important;
    overflow: hidden;
    border: 1px solid #e6e6e6 !important;
}

/* Khung từng khách */
.border.rounded {
    background: #fafafa;
    border: 1px solid #e3e3e3 !important;
}

</style>
