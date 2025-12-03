@extends('layouts.page')
@section('title', 'Combo Buffet')

@section('content')
<div class="container py-4">

    <h2 class="mb-4 text-center">Các Combo Buffet</h2>

    <div class="row">
        @forelse($combos as $combo)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm h-100 combo-card position-relative">

                {{-- Hình ảnh combo --}}
                <img src="{{ asset('uploads/' . $combo->anh) }}" alt="{{ $combo->ten_combo }}" class="card-img-top"
                    style="height: 220px; object-fit: cover;">

                <div class="card-body d-flex flex-column">

                    {{-- Tên combo --}}
                    <h5 class="card-title">{{ $combo->ten_combo }}</h5>

                    {{-- Trạng thái combo (bên trái) --}}
                    <span class="badge badge-status position-absolute" style="top: 15px; left: 15px;">
                        {{ $combo->trang_thai_display }}
                    </span>

                    {{-- Loại combo (giữa) --}}
                    <span class="combo-type-badge">
                        {{ $combo->loai_combo_display }}
                    </span>

                    {{-- Giá combo --}}
                    <p class="card-text text-success fw-bold mt-2">
                        {{ number_format($combo->gia_co_ban, 0, ',', '.') }} VNĐ
                    </p>

                    {{-- Nút xem chi tiết --}}
                    <a href="{{ route('combos.show', $combo->id) }}" class="btn btn-warning mt-auto">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-center text-muted">Hiện chưa có combo nào.</p>
        </div>
        @endforelse
    </div>

</div>

@push('styles')
<style>
    .combo-card:hover {
        transform: translateY(-4px);
        transition: all 0.3s ease;
    }

    /* Trạng thái combo bên trái, chữ xanh, nền trong suốt */
    .badge-status {
        background-color: transparent;
        color: #28a745;
        border: 1px solid #28a745;
        font-weight: 500;
        padding: 0.25em 0.5em;
        border-radius: 0.25rem;
        z-index: 10;
    }

    /* Loại combo ở giữa, nền cam, chữ trắng */
    .badge-type {
        display: inline-block;
        background-color: #fd7e14;
        color: #141313;
        font-weight: 500;
        padding: 0.35em 0.75em;
        border-radius: 0.25rem;
        text-align: center;
        margin-bottom: 0.5rem;
    }

    .combo-type-badge {
        display: inline-block;
        background-color: #fd7e14 !important;
        /* cam */
        color: #fff !important;
        /* trắng */
        font-weight: 500;
        padding: 0.35em 0.75em;
        border-radius: 0.25rem;
        text-align: center;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@endsection