@extends('layouts.page')

@section('title', 'Thanh toán PayOS')

@section('content')
<main class="app-content">

    <div class="app-title mb-3">
        <h1>Thanh toán cho đơn #{{ $datBan->ma_dat_ban }}</h1>
        <a href="{{ route('booking.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    {{-- Thông báo --}}
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-3 text-center">
                <h4>Thông tin đơn đặt bàn</h4>
                <p><strong>Mã đặt bàn:</strong> {{ $datBan->ma_dat_ban }}</p>
                <p><strong>Tên khách:</strong> {{ $datBan->ten_khach }}</p>
                <p><strong>Số tiền cần thanh toán:</strong> {{ number_format($datBan->tien_coc, 0, ',', '.') }} VND</p>

                <hr>

                <h4 class="mb-3">Thanh toán trực tuyến qua PayOS</h4>
                <p>Nhấn nút dưới đây để được chuyển sang trang thanh toán PayOS</p>

                <div class="mt-3">
                    <a href="{{ route('booking.pay_os', $datBan->id) }}" class="btn btn-warning btn-lg w-100">
                        Thanh toán PayOS
                    </a>
                </div>

                <div class="mt-2">
                    <a href="{{ route('booking.payment_method', $datBan->id) }}" class="btn btn-secondary w-100">
                        Quay lại chọn phương thức thanh toán
                    </a>
                </div>
            </div>
        </div>
    </div>

</main>
@endsection