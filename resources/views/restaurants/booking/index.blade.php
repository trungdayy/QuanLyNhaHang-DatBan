{{-- resources/views/restaurants/booking/index.blade.php --}}
@extends('layouts.restaurants.layout-shop')

@section('title', 'Đặt Bàn Online')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">Đặt Bàn Online</h2>

    {{-- Thông báo --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        {{-- Cột trái: Form đặt bàn --}}
        <div class="col-lg-6 mb-4">
            @include('restaurants.booking._form', [
            'action' => route('booking.store'),
            'method' => 'POST',
            'datBan' => null
            ])
        </div>

        {{-- Cột phải: Danh sách booking + form search --}}
        <div class="col-lg-6">
            <h4>Danh sách đặt bàn của bạn</h4>
            @if($datBans->isEmpty())
            <p>Chưa có đặt bàn nào.</p>
            @else
            <ul class="list-group mb-3">
                @foreach($datBans as $datBan)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $datBan->ten_khach }}</strong> - Người lớn: {{ $datBan->nguoi_lon }}, Trẻ em:
                        {{ $datBan->tre_em }}
                        <br>Ngày giờ: {{ \Carbon\Carbon::parse($datBan->gio_den)->format('H:i d/m/Y') }}
                        <br>Trạng thái: <span class="badge bg-info">{{ $datBan->trang_thai ?? 'Chưa xác định' }}</span>
                    </div>
                    <div>
                        <a href="{{ route('booking.edit', $datBan->id) }}" class="btn btn-sm btn-primary mb-1">Sửa</a>
                        <form action="{{ route('booking.destroy', $datBan->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn có chắc hủy?')">Hủy</button>
                        </form>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif

            {{-- Form search số điện thoại nằm dưới danh sách --}}
            <div class="card p-3">
                <h5>Tìm đặt bàn theo số điện thoại</h5>
                <form action="{{ route('booking.index') }}" method="GET" class="d-flex mt-2">
                    <input type="text" name="sdt" class="form-control me-2" placeholder="Nhập số điện thoại"
                        value="{{ $sdt ?? '' }}" required>
                    <button type="submit" class="btn btn-primary">Tìm</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection