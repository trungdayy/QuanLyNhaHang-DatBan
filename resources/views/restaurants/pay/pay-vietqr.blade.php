@extends('layouts.page')

@section('title', 'Thanh toán bằng QR')

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
                <p><strong>Số tiền cần thanh toán:</strong> {{ number_format($datBan->gia_co_ban, 0, ',', '.') }} VND
                </p>

                <hr>

                <h4 class="mb-3">Quét QR để thanh toán</h4>
                <div class="d-flex justify-content-center mb-3">
                    <img src="{{ asset('admin/images/QR_code.jpg') }}" alt="QR Code thanh toán"
                        style="width:200px; height:200px; object-fit:contain;">
                </div>
                <p>Quét QR code bằng MOMO hoặc ví ngân hàng của bạn</p>

                <div class="mt-3">
                    <a href="{{ route('booking.index') }}" class="btn btn-secondary">Quay lại danh sách đặt bàn</a>
                </div>
            </div>
        </div>
    </div>

</main>
@endsection