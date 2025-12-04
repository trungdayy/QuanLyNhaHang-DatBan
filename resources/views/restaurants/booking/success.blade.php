@extends('layouts.page')
@section('title', 'Đặt Bàn Thành Công')
@section('content')

<div class="container py-5 text-center">
    <h2 class="mb-4">Đặt bàn thành công!</h2>
    <p>Chờ nhà hàng xác nhận. <a href="{{ route('booking') }}">Quay lại trang đặt bàn</a></p>
</div>

@endsection