@extends('layouts.restaurants.layout-shop')

@section('title', 'Chọn phương thức thanh toán')

@section('content')
<main class="app-content">

    <div class="app-title mb-3">
        <h1>Chọn phương thức thanh toán cho đơn #{{ $datBan->ma_dat_ban }}</h1>
       <a href="{{ route('booking.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    {{-- Thông báo --}}
    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow p-3">
                <h4>Thông tin đặt bàn</h4>
                <p><strong>Mã đặt bàn:</strong> {{ $datBan->ma_dat_ban }}</p>
                <p><strong>Tên khách:</strong> {{ $datBan->ten_khach }}</p>
                <p><strong>SĐT:</strong> {{ $datBan->sdt_khach }}</p>
                <p><strong>Email:</strong> {{ $datBan->email_khach }}</p>
                <p><strong>Số khách:</strong> {{ $datBan->so_khach }}</p>
                <p><strong>Giá tiền quý khách gọi món:</strong>{{$datBan->gia_co_ban}}</p>
                <p><strong>Giờ đến:</strong> {{ \Carbon\Carbon::parse($datBan->gio_den)->format('H:i d/m/Y') }}</p>
                <p><strong>Tiền cọc:</strong> 50.000đ</p>
            </div>
        </div>

        {{-- Các phương thức thanh toán --}}
        <div class="col-md-6">
            <div class="card shadow p-3">
                <h4>Chọn phương thức thanh toán</h4>

                <a href="{{ route('booking.pay_momo', $datBan->id) }}" class="btn btn-primary w-100 my-2">
                    Thanh toán qua Momo
                </a>

                <a href="{{ route('booking.pay_vnpay', $datBan->id) }}" class="btn btn-info w-100 my-2">
                    Thanh toán qua VNPay
                </a>

                <a href="{{ route('booking.pay_cash', $datBan->id) }}" class="btn btn-success w-100 my-2">
                    Đặt cọc tại nhà hàng
                </a>
            </div>
        </div>
    </div>

</main>
@endsection