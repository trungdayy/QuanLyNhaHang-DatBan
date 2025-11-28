@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Sửa món')

@section('content')
    {{-- 1. IMPORT FONTS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    {{-- 2. CSS STYLING (Design System) --}}
    <style>
        :root {
            --primary: #fea116; --primary-dark: #d98a12;
            --dark: #0f172b; --white: #ffffff;
            --text-main: #1e293b; --text-sub: #64748b;
            --bg-light: #f8f9fa;
            --radius: 8px;
            --shadow-card: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            --anim-fast: 0.2s ease;
        }

        body { font-family: 'Nunito', sans-serif; background-color: var(--bg-light); color: var(--text-main); }
        h3, h5, strong, .font-heading { font-family: 'Heebo', sans-serif; }

        /* --- CENTERED CARD --- */
        .edit-card {
            background: var(--white); border-radius: var(--radius); overflow: hidden;
            border: 1px solid #f1f5f9; box-shadow: var(--shadow-card);
            max-width: 600px; margin: 0 auto; /* Căn giữa */
        }

        .edit-card-header {
            background: var(--dark); color: var(--white); padding: 20px;
            text-align: center;
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.1) 1px, transparent 0);
            background-size: 20px 20px;
        }
        
        .dish-name-header {
            font-family: 'Heebo'; font-weight: 800; font-size: 1.4rem; text-transform: uppercase; margin: 0;
        }

        /* --- FORM ELEMENTS --- */
        .form-group { margin-bottom: 20px; }
        
        .form-label-custom {
            font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--text-sub);
            margin-bottom: 8px; display: block;
        }

        .form-control-custom {
            width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 6px;
            font-size: 1rem; color: var(--dark); font-weight: 600; background: #fff;
            transition: var(--anim-fast);
        }
        .form-control-custom:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(254, 161, 22, 0.15);
        }
        .form-control-custom[readonly] {
            background: #f1f5f9; color: #64748b; cursor: not-allowed;
        }

        /* --- DISH PREVIEW --- */
        .dish-preview {
            display: flex; align-items: center; gap: 15px; margin-bottom: 25px;
            padding-bottom: 20px; border-bottom: 1px dashed #e2e8f0;
        }
        .preview-img {
            width: 80px; height: 80px; border-radius: 8px; object-fit: cover;
            border: 1px solid #e2e8f0;
        }
        .preview-info h5 { margin: 0 0 5px 0; color: var(--dark); font-weight: 700; }
        .preview-price { color: var(--primary); font-weight: 800; font-family: 'Heebo'; }

        /* --- BUTTONS --- */
        .btn-group-action { display: flex; gap: 10px; margin-top: 30px; }
        
        .btn-submit {
            flex: 2; border: none; padding: 12px; border-radius: 6px;
            background: var(--primary); color: var(--white);
            font-family: 'Heebo'; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;
            cursor: pointer; transition: var(--anim-fast);
            box-shadow: 0 4px 10px rgba(254, 161, 22, 0.3);
        }
        .btn-submit:hover { background: var(--primary-dark); transform: translateY(-2px); }

        .btn-cancel {
            flex: 1; border: 1px solid #e2e8f0; padding: 12px; border-radius: 6px;
            background: var(--white); color: var(--text-sub);
            font-family: 'Heebo'; font-weight: 700; text-transform: uppercase;
            text-decoration: none; text-align: center; display: inline-block;
            transition: var(--anim-fast);
        }
        .btn-cancel:hover { background: #f1f5f9; color: var(--dark); }

    </style>

    <div class="container py-5">
        
        <div class="edit-card">
            <div class="edit-card-header">
                <h3 class="dish-name-header">Sửa món</h3>
                <div style="font-size: 0.9rem; opacity: 0.8; margin-top: 5px;">Order #{{ $order->id }}</div>
            </div>

            <div class="card-body p-4">
                {{-- Preview món ăn --}}
                <div class="dish-preview">
                    <img src="{{ asset($ct->monAn->hinh_anh ?? 'https://placehold.co/100x100?text=IMG') }}" 
                         alt="{{ $ct->monAn->ten_mon }}" class="preview-img">
                    <div class="preview-info">
                        <h5>{{ $ct->monAn->ten_mon }}</h5>
                        <div class="preview-price">{{ number_format($ct->monAn->gia, 0, ',', '.') }} đ</div>
                        <div style="font-size: 0.8rem; color: var(--text-sub); margin-top: 4px;">
                            {{ $ct->loai_mon == 'combo' ? 'Món trong Combo' : 'Món gọi thêm' }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('nhanVien.chi-tiet-order.update', $ct->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="order_id" value="{{ $order->id }}">

                    {{-- 1. SỐ LƯỢNG --}}
                    <div class="form-group">
                        <label class="form-label-custom">Số lượng</label>
                        
                        @if($ct->loai_mon === 'goi_them')
                            <input type="number" name="so_luong" class="form-control-custom" 
                                   value="{{ old('so_luong', $ct->so_luong) }}" min="1" required>
                        @else
                            <input type="number" class="form-control-custom" value="{{ $ct->so_luong }}" readonly>
                            <div class="alert alert-warning d-flex align-items-center mt-2 mb-0 py-2 px-3" style="font-size: 0.8rem; border-radius: 6px;">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Không thể đổi số lượng món trong Combo
                            </div>
                        @endif
                    </div>

                    {{-- 2. GHI CHÚ --}}
                    <div class="form-group">
                        <label class="form-label-custom">Ghi chú bếp</label>
                        <input type="text" name="ghi_chu" class="form-control-custom" 
                               value="{{ old('ghi_chu', $ct->ghi_chu) }}" 
                               placeholder="VD: Không hành, ít cay...">
                    </div>

                    {{-- ACTIONS --}}
                    <div class="btn-group-action">
                        <a href="{{ route('nhanVien.order.page', $order->id) }}" class="btn-cancel">
                            Hủy bỏ
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fa-solid fa-save me-1"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection