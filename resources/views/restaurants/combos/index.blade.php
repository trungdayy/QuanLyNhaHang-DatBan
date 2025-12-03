@extends('layouts.page')

@section('title', 'Danh sách Combo Buffet')

@section('content')
<div class="container py-4">

    <h2 class="mb-4 text-center fw-bold text-uppercase">Các Combo Buffet</h2>

    <div class="row g-4"> {{-- Thêm g-4 để giãn cách đều đẹp hơn --}}
        @forelse($combos as $combo)
        <div class="col-md-4 col-sm-6">
            {{-- 
                QUAN TRỌNG: Thêm class 'product-card-trigger' và các thuộc tính data- 
                để code JS giỏ hàng (ở layout hoặc partial) có thể bắt được sự kiện click
            --}}
            <div class="card shadow-sm h-100 combo-card position-relative product-card-trigger"
                 style="cursor: pointer;"
                 data-key="combo_{{ $combo->id }}" 
                 data-type="combo" 
                 data-name="{{ $combo->ten_combo }}"
                 data-price="{{ $combo->gia_co_ban }}" 
                 data-desc="{{ $combo->mo_ta ?? 'Combo ưu đãi đặc biệt' }}"
                 data-img="{{ asset('uploads/' . $combo->anh) }}">

                {{-- Hình ảnh combo --}}
                <div class="position-relative">
                    <img src="{{ asset('uploads/' . $combo->anh) }}" alt="{{ $combo->ten_combo }}" class="card-img-top"
                        style="height: 220px; object-fit: cover;">
                    
                    {{-- Nút thêm nhanh (Giống trang Menu) --}}
                    <button class="btn btn-primary position-absolute top-0 end-0 m-2 btn-quick-add"
                            title="Thêm ngay vào giỏ">
                        <i class="fa fa-plus text-white"></i>
                    </button>
                </div>

                <div class="card-body d-flex flex-column">

                    {{-- Tên combo --}}
                    <h5 class="card-title fw-bold">{{ $combo->ten_combo }}</h5>

                    {{-- Trạng thái combo (bên trái) --}}
                    <span class="badge badge-status position-absolute bg-white" style="top: 15px; left: 15px;">
                        {{ $combo->trang_thai_display ?? 'Đang bán' }}
                    </span>

                    {{-- Loại combo --}}
                    <div class="mb-2">
                        <span class="combo-type-badge">
                            {{ $combo->loai_combo_display ?? $combo->loai_combo }}
                        </span>
                    </div>

                    {{-- Giá combo --}}
                    <p class="card-text text-danger fw-bold fs-5 mt-auto">
                        {{ number_format($combo->gia_co_ban, 0, ',', '.') }} VNĐ
                    </p>

                    {{-- Group Button: Xem chi tiết & Thêm giỏ --}}
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('combos.show', $combo->id) }}" class="btn btn-outline-warning flex-grow-1">
                            <i class="fa fa-eye"></i> Chi tiết
                        </a>
                        {{-- Nút này sẽ kích hoạt JS addToCart --}}
                        <button type="button" class="btn btn-primary flex-grow-1 btn-quick-add">
                            <i class="fa fa-cart-plus"></i> Chọn
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-warning text-center">
                Hiện chưa có combo nào đang mở bán.
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- 
    LƯU Ý: 
    Bạn cần đảm bảo đoạn Code HTML/CSS/JS của GIỎ HÀNG (Floating Cart & Modal) 
    đã được include vào trang này (hoặc nằm trong layouts.page).
    Nếu chưa có, hãy copy đoạn code "PHẦN 4" và "PHẦN 5" từ trang Menu sang đây 
    hoặc tách ra thành file 'partials.cart' rồi include vào.
--}}

@push('styles')
<style>
    .combo-card {
        transition: all 0.3s ease;
        border: none;
    }
    .combo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .badge-status {
        color: #28a745;
        border: 1px solid #28a745;
        font-weight: 600;
        z-index: 5;
    }

    .combo-type-badge {
        display: inline-block;
        background-color: #fd7e14;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.35em 0.75em;
        border-radius: 20px;
    }
    
    /* Copy style btn-quick-add từ trang trước nếu chưa có trong layout */
    .btn-quick-add { z-index: 10; }
</style>
@endpush

@endsection