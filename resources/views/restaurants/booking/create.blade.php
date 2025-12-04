@extends('layouts.page')
@section('title', 'Đặt Bàn Online - Tạo mới')
@section('content')

<div class="container py-5">
    <h2 class="mb-4 text-center">Đặt Bàn Online</h2>
    @include('restaurants.booking._form', ['action' => route('booking.store'), 'method' => 'POST'])
</div>

@endsection