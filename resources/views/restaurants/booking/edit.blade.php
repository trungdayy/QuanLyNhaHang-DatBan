@extends('layouts.page')
@section('title', 'Sửa Đặt Bàn Online')
@section('content')

<div class="container py-5">
    <h2 class="mb-4 text-center">Sửa Đặt Bàn</h2>
    @include('restaurants.booking._form', ['action' => route('booking.update', $datBan->id), 'method' => 'PUT'])
</div>

@endsection