@extends('layouts.page')
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
        {{-- Form đặt bàn --}}
        <div class="col-lg-6 mb-4">
            @include('restaurants.booking._form', ['action' => route('booking.store'), 'method' => 'POST'])
        </div>

        {{-- Danh sách booking --}}
        <div class="col-lg-6">
            <h4>Danh sách đặt bàn của bạn</h4>
            @if($datBans->isEmpty())
            <p>Chưa có đặt bàn nào.</p>
            @else
            <ul class="list-group">
                @foreach($datBans as $datBan)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>{{ $datBan->ten_khach }}</strong> - {{ $datBan->so_khach }} khách
                        <br>Ngày giờ: {{ \Carbon\Carbon::parse($datBan->gio_den)->format('H:i d/m/Y') }}
                        <br>Bàn: {{ $datBan->ban?->so_ban ?? 'Chưa chọn' }}
                        <br>Trạng thái: <span class="badge bg-info">{{ $datBan->trang_thai }}</span>
                    </div>
                    <div>
                        @if($datBan->trang_thai === 'cho_xac_nhan')
                        <a href="{{ route('booking.edit', $datBan->id) }}" class="btn btn-sm btn-primary mb-1">Sửa</a>
                        <form action="{{ route('booking.destroy', $datBan->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                onclick="return confirm('Bạn có chắc hủy?')">Hủy</button>
                        </form>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>

@endsection