@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Sửa món')

{{-- IMPORT FONTS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

@section('content')
<div class="container py-5">

    <div class="edit-card" style="max-width:600px; margin:0 auto; background:#fff; border-radius:8px; box-shadow:0 10px 30px -5px rgba(0,0,0,0.05); overflow:hidden;">

        <div class="edit-card-header" style="background:#0f172b; color:#fff; text-align:center; padding:20px; background-image:radial-gradient(circle at 1px 1px, rgba(255,255,255,0.1) 1px, transparent 0); background-size:20px 20px;">
            <h3 class="dish-name-header" style="font-family:'Heebo'; font-weight:800; font-size:1.4rem; text-transform:uppercase; margin:0;">Sửa món</h3>
            <div style="font-size:0.9rem; opacity:0.8; margin-top:5px;">Order #{{ $order->id }}</div>
        </div>

        <div class="card-body p-4">
            {{-- Preview món ăn --}}
            <div class="dish-preview" style="display:flex; align-items:center; gap:15px; margin-bottom:25px; padding-bottom:20px; border-bottom:1px dashed #e2e8f0;">
                <img src="{{ asset($ct->monAn->hinh_anh ?? 'https://placehold.co/100x100?text=IMG') }}" alt="{{ $ct->monAn->ten_mon }}" style="width:80px; height:80px; object-fit:cover; border-radius:8px; border:1px solid #e2e8f0;">
                <div class="preview-info">
                    <h5 style="margin:0 0 5px 0; color:#0f172b; font-weight:700;">{{ $ct->monAn->ten_mon }}</h5>
                    <div style="color:#fea116; font-weight:800; font-family:'Heebo';">{{ number_format($ct->monAn->gia, 0, ',', '.') }} đ</div>
                    <div style="font-size:0.8rem; color:#64748b; margin-top:4px;">
                        {{ $ct->loai_mon == 'combo' ? 'Món trong Combo' : 'Món gọi thêm' }}
                    </div>
                </div>
            </div>

            {{-- FORM --}}
            <form action="{{ route('nhanVien.chi-tiet-order.update', $ct->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="order_id" value="{{ $order->id }}">

                {{-- SỐ LƯỢNG --}}
                @if($ct->loai_mon === 'goi_them')
                <div class="form-group mb-3">
                    <label class="form-label-custom" style="font-size:0.8rem; font-weight:700; text-transform:uppercase; color:#64748b; margin-bottom:8px; display:block;">Số lượng</label>
                    <input type="number" name="so_luong" class="form-control-custom" value="{{ old('so_luong', $ct->so_luong) }}" min="1" required style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:6px; font-size:1rem; color:#0f172b; font-weight:600; background:#fff;">
                </div>
                @else
                <div class="alert alert-warning d-flex align-items-center mb-3 py-2 px-3" style="font-size:0.8rem; border-radius:6px;">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Không thể đổi số lượng món trong Combo
                </div>
                @endif

                {{-- GHI CHÚ --}}
                <div class="form-group mb-3">
                    <label class="form-label-custom" style="font-size:0.8rem; font-weight:700; text-transform:uppercase; color:#64748b; margin-bottom:8px; display:block;">Ghi chú bếp</label>
                    <input type="text" name="ghi_chu" class="form-control-custom" value="{{ old('ghi_chu', $ct->ghi_chu) }}" placeholder="{{ $ct->loai_mon === 'combo' ? 'Chỉ thêm ghi chú cho combo...' : 'VD: Không hành, ít cay...' }}" style="width:100%; padding:12px; border:1px solid #e2e8f0; border-radius:6px; font-size:1rem; color:#0f172b; font-weight:600; background:#fff;">
                </div>

                {{-- NÚT HÀNH ĐỘNG --}}
                <div class="btn-group-action" style="display:flex; gap:10px; margin-top:30px;">
                    <a href="{{ route('nhanVien.order.page', $order->id) }}" class="btn-cancel" style="flex:1; border:1px solid #e2e8f0; padding:12px; border-radius:6px; background:#fff; color:#64748b; font-family:'Heebo'; font-weight:700; text-transform:uppercase; text-align:center; text-decoration:none; transition:0.2s;">Hủy bỏ</a>
                    <button type="submit" class="btn-submit" style="flex:2; border:none; padding:12px; border-radius:6px; background:#fea116; color:#fff; font-family:'Heebo'; font-weight:800; text-transform:uppercase; cursor:pointer; transition:0.2s;"><i class="fa-solid fa-save me-1"></i> Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- CSS bổ sung --}}
<style>
    .form-control-custom:focus {
        outline: none;
        border-color: #fea116;
        box-shadow: 0 0 0 3px rgba(254, 161, 22, 0.15);
    }

    .btn-submit:hover {
        background: #d98a12;
        transform: translateY(-2px);
    }

    .btn-cancel:hover {
        background: #f1f5f9;
        color: #0f172b;
    }
</style>
@endsection