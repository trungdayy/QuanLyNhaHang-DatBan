@extends('layouts.restaurants.layout-shop')

@section('title', 'Chọn phương thức thanh toán')

@section('content')
<main class="app-content py-5">

    <div class="container">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3"><i class="bi bi-credit-card-2-front"></i> Pay #{{ $datBan->ma_dat_ban }}</h1>
            <a href="{{ route('booking.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>

        {{-- Thông báo --}}
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row g-4">
            {{-- Thông tin đặt bàn --}}
            <div class="col-md-6">
                <div class="card shadow-sm p-4 h-100 border-primary">
                    <h4 class="mb-3 text-primary"><i class="bi bi-person-badge"></i> Thông tin đặt bàn</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Mã đặt bàn:</strong> {{ $datBan->ma_dat_ban }}</li>
                        <li class="list-group-item"><strong>Tên khách:</strong> {{ $datBan->ten_khach }}</li>
                        <li class="list-group-item"><strong>SĐT:</strong> {{ $datBan->sdt_khach }}</li>
                        <li class="list-group-item"><strong>Email:</strong> {{ $datBan->email_khach ?? '-' }}</li>
                        <li class="list-group-item"><strong>Số khách:</strong> {{ $datBan->so_khach }}</li>
                        <li class="list-group-item"><strong>Giờ đến:</strong>
                            {{ \Carbon\Carbon::parse($datBan->gio_den)->format('H:i d/m/Y') }}</li>
                        <li class="list-group-item"><strong>Tiền cọc:</strong>
                            {{ number_format($datBan->tien_coc) }}đ
                        </li>
                    </ul>
                    @if($datBan->combo)
                    <small class="text-muted mt-2 d-block">
                        Tiền cọc được tính theo combo <strong>{{ $datBan->combo->ten_combo }}</strong>
                        ({{ number_format($datBan->combo->gia) }}đ/người) và số khách.
                    </small>
                    @endif
                </div>
            </div>

            {{-- Các phương thức thanh toán --}}
            <div class="col-md-6">
                <div class="card shadow-sm p-4 h-100 border-success">
                    <h4 class="mb-4 text-success"><i class="bi bi-wallet2"></i> Chọn phương thức thanh toán</h4>

                    <div class="d-grid gap-3">
                        <a href="{{ route('booking.pay_os', $datBan->id) }}"
                            class="btn btn-primary btn-lg d-flex align-items-center justify-content-center">
                            <i class="bi bi-credit-card-2-front me-2"></i> Thanh toán qua PayOS
                        </a>

                        <a href="{{ route('booking.pay_cash', $datBan->id) }}"
                            class="btn btn-success btn-lg d-flex align-items-center justify-content-center">
                            <i class="bi bi-cash-stack me-2"></i> Đặt cọc tại nhà hàng
                        </a>

                        <a href="{{ route('booking.pay_vietqr', $datBan->id) }}"
                            class="btn btn-info btn-lg d-flex align-items-center justify-content-center text-white">
                            <i class="bi bi-bank me-2"></i> Chuyển khoản / VietQR
                        </a>
                    </div>

                    <div class="mt-4">
                        <h6 class="text-muted">Tỷ lệ cọc tham khảo:</h6>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li>1 người: 10%</li>
                            <li>2 người: 15%</li>
                            <li>3 người: 20%</li>
                            <li>4 người: 25%</li>
                            <li>5 người: 30%</li>
                            <li>6 người: 35%</li>
                            <li>7-8 người: 40%</li>
                            <li>9+ người: 50%</li>
                        </ul>
                    </div>

                    <p class="mt-4 text-muted text-center">
                        Lưu ý: Sau khi thanh toán, vui lòng chờ xác nhận từ nhà hàng.
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection