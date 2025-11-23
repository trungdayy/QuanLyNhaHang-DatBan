@extends('layouts.restaurants.layout-shop')

@section('title', 'Chi tiết Combo Buffet')

@section('content')
<div class="container py-4">

    {{-- Nút quay lại danh sách combo --}}
    <a href="{{ route('combos.index') }}" class="btn btn-secondary mb-3">
        ← Quay lại danh sách
    </a>

    <div class="row">
        {{-- Ảnh combo --}}
        <div class="col-md-5 position-relative">
            <img src="{{ asset('uploads/' . $combo->anh) }}" class="img-fluid rounded shadow"
                alt="{{ $combo->ten_combo }}" style="object-fit: cover; width: 100%; max-height: 350px;">

            {{-- Trạng thái combo nổi --}}
            <span class="badge-status position-absolute" style="top: 15px; left: 15px;">
                {{ $combo->trang_thai_display }}
            </span>
        </div>

        {{-- Thông tin combo --}}
        <div class="col-md-7">
            <h2>{{ $combo->ten_combo }}</h2>

            {{-- Loại combo --}}
            @php
            $loaiColors = [
            'nguoi_lon' => '#fd7e14',
            'tre_em' => '#17a2b8',
            'vip' => '#ffc107',
            'khuyen_mai' => '#dc3545',
            ];
            $bgColor = $loaiColors[$combo->loai_combo] ?? '#6c757d';
            @endphp
            <span class="combo-type-badge" style="background-color: {{ $bgColor }};">
                {{ $combo->loai_combo_display }}
            </span>

            {{-- Giá combo --}}
            <p class="text-success fw-bold fs-4 mt-2">
                {{ number_format($combo->gia_co_ban, 0, ',', '.') }} VNĐ
            </p>

            {{-- Thời lượng --}}
            @if($combo->thoi_luong_phut)
            <p><strong>Thời lượng phục vụ:</strong> {{ $combo->thoi_luong_phut }} phút</p>
            @endif

            {{-- Mô tả --}}
            @if($combo->description ?? false)
            <hr>
            <h5>Mô tả:</h5>
            <p>{!! nl2br(e($combo->description)) !!}</p>
            @endif

            {{-- Danh sách món ăn --}}
            @if($combo->danhSachMon->count() > 0)
            <hr>
            <h5>Món ăn trong combo:</h5>

            <ul>
                @foreach($combo->danhSachMon as $mon)
                <li>
                    <strong>{{ $mon->ten_mon }}</strong>

                    {{-- Nếu có số lượng trong combo --}}
                    @if($mon->pivot->so_luong ?? false)
                    — SL: {{ $mon->pivot->so_luong }}
                    @endif
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-muted">Combo này chưa có món nào.</p>
            @endif

            {{-- Nút Đặt ngay --}}
            <div class="mt-4">
                <a href="{{ route('booking.create', ['combo_id' => $combo->id]) }}" class="btn btn-warning btn-lg">
                    Đặt ngay
                </a>
            </div>

        </div>
    </div>

</div>

@push('styles')
<style>
    .badge-status {
        background-color: transparent;
        color: #28a745;
        border: 1px solid #28a745;
        font-weight: 500;
        padding: 0.25em 0.5em;
        border-radius: 0.25rem;
        z-index: 10;
    }

    .combo-type-badge {
        display: inline-block;
        color: #fff;
        font-weight: 500;
        padding: 0.2em 0.5em;
        border-radius: 1rem;
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .btn-warning:hover {
        background-color: #e69500;
        border-color: #e69500;
    }
</style>
@endpush

@endsection