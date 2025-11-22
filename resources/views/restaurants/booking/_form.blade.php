{{-- resources/views/restaurants/booking/create.blade.php --}}
@extends('layouts.restaurants.layout-shop')

@section('title', $method === 'PUT' ? 'Cập nhật đặt bàn' : 'Đặt Bàn Online')

@section('content')

<div class="container py-5">
    <h2 class="mb-4 text-center">{{ $method === 'PUT' ? 'Cập nhật đặt bàn' : 'Đặt Bàn Online' }}</h2>

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


    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ $action }}" method="POST" class="booking-form">
                @csrf
                @if($method === 'PUT') @method('PUT') @endif

                <div class="row g-3">

                    {{-- Tên khách --}}
                    <div class="col-md-6">
                        <input type="text" name="ten_khach" class="form-control" placeholder="Họ và tên"
                            value="{{ old('ten_khach', $datBan->ten_khach ?? '') }}" required>
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <input type="email" name="email_khach" class="form-control" placeholder="Email"
                            value="{{ old('email_khach', $datBan->email_khach ?? '') }}">
                    </div>

                    {{-- SĐT --}}
                    <div class="col-md-6">
                        <input type="text" name="sdt_khach" class="form-control" placeholder="Số điện thoại"
                            value="{{ old('sdt_khach', $datBan->sdt_khach ?? '') }}" required>
                    </div>

                    {{-- Số khách --}}
                    <div class="col-md-6">
                        <input type="number" name="so_khach" class="form-control" placeholder="Số khách"
                            value="{{ old('so_khach', $datBan->so_khach ?? 1) }}" min="1" required>
                    </div>


                    {{-- ⭐ COMBO --}}
                    <div class="col-md-6">
                        <select name="combo_id" class="form-select" required>
                            <option value="">-- Chọn Combo --</option>
                            @foreach($combos as $c)
                            <option value="{{ $c->id }}" @if( old('combo_id', $datBan->combo_id ?? $selectedCombo->id ??
                                '') == $c->id ) selected @endif>
                                {{ $c->ten_combo }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Khu vực --}}
                    <div class="col-md-6">
                        <select name="khu_vuc_id" id="khu_vuc_id" class="form-select" required>
                            <option value="">-- Chọn Khu Vực --</option>
                            @foreach($khuVucs as $kv)
                            <option value="{{ $kv->id }}"
                                {{ old('khu_vuc_id', $datBan->ban?->khu_vuc_id ?? '') == $kv->id ? 'selected' : '' }}>
                                {{ $kv->ten_khu_vuc }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Bàn --}}
                    <div class="col-md-6">
                        <select name="ban_id" id="ban_id" class="form-select" required>
                            <option value="">-- Chọn bàn --</option>
                            @foreach($banAns as $ban)
                            <option value="{{ $ban->id }}"
                                {{ old('ban_id', $datBan->ban_id ?? '') == $ban->id ? 'selected' : '' }}>
                                Bàn {{ $ban->so_ban }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Giờ đến --}}
                    <div class="col-md-6">
                        <input type="datetime-local" name="gio_den" class="form-control"
                            value="{{ old('gio_den', isset($datBan->gio_den) ? \Carbon\Carbon::parse($datBan->gio_den)->format('Y-m-d\TH:i') : '') }}"
                            required>
                    </div>

                    {{-- Ghi chú --}}
                    <div class="col-12">
                        <textarea name="ghi_chu" class="form-control"
                            placeholder="Ghi chú">{{ old('ghi_chu', $datBan->ghi_chu ?? '') }}</textarea>
                    </div>


                    <div class="col-12">
                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold">
                            {{ $method === 'PUT' ? 'Cập nhật' : 'Đặt ngay' }}
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </div>
</div>

@endsection