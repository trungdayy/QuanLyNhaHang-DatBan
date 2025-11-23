@extends('layouts.restaurants.layout-shop')

@section('content')
<div class="container mt-5">
    <h2>Thanh toán MOMO</h2>

    <div class="card mb-3">
        <div class="card-header">Thông tin đặt bàn</div>
        <div class="card-body">
            <p><strong>Mã đặt bàn:</strong> {{ $datBan->ma_dat_ban }}</p>
            <p><strong>Tên khách:</strong> {{ $datBan->ten_khach }}</p>
            <p><strong>SĐT:</strong> {{ $datBan->sdt_khach }}</p>
            <p><strong>Số tiền đặt cọc:</strong> {{ number_format($datBan->tien_coc) }} VND</p>
        </div>
    </div>

    <form action="{{ url('/booking/momo/' . $datBan->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success btn-lg">Thanh toán MOMO</button>
    </form>

    <a href="{{ route('booking.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
</div>
@endsection