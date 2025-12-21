@extends('layouts.admins.layout-admin')

@section('title', 'Sửa Combo Buffet')

@section('style')
    <style>
        .tile {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: none;
            background: #fff;
            padding: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #ccc;
            padding: 8px 10px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 4px rgba(0, 123, 255, 0.25);
        }

        /* Ảnh preview ở cột phải */
        #previewImage {
            width: 100%;
            max-height: 250px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
        }

        .btn-save {
            background-color: #007bff;
            color: #fff;
            font-weight: 600;
            border-radius: 6px;
            padding: 8px 18px;
            border: none;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: #fff;
            border-radius: 6px;
            padding: 8px 18px;
            font-weight: 500;
            text-decoration: none;
        }

        .form-check-inline {
            margin-right: 15px;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    <main class="app-content">
        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item">Quản lý combo</li>
                <li class="breadcrumb-item"><a href="{{ route('admin.combo-buffet.index') }}">Danh sách combo</a></li>
                <li class="breadcrumb-item active">Sửa combo</li>
            </ul>
            <div id="clock"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title" style="color: #002b5b; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                        Sửa Combo: {{ $combo->ten_combo }}
                    </h3>
                    
                    <div class="tile-body">
                        {{-- Hiển thị lỗi --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.combo-buffet.update', $combo->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                {{-- CỘT TRÁI: THÔNG TIN --}}
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label class="control-label">Tên combo <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="ten_combo"
                                            value="{{ old('ten_combo', $combo->ten_combo) }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Mô tả Combo</label>
                                        <textarea class="form-control" name="mo_ta" rows="3">{{ old('mo_ta', $combo->mo_ta) }}</textarea>
                                    </div>

                                    {{-- LOẠI COMBO (CÓ TỰ ĐỘNG ĐIỀN GIÁ) --}}
                                    <div class="form-group">
                                        <label class="control-label">Loại combo (Chọn để cập nhật giá)</label>
                                        <div class="d-block mt-2">
                                            @foreach (['99k' => 'Combo 99k', '199k' => 'Combo 199k', '299k' => 'Combo 299k', '399k' => 'Combo 399k', '499k' => 'Combo 499k'] as $value => $label)
                                                @php
                                                    // Tính giá dựa trên key (vd: 99k -> 99000)
                                                    $price = intval(str_replace('k', '', $value)) * 1000;
                                                @endphp
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input combo-type-radio" type="radio" name="loai_combo"
                                                        id="loai_{{ $value }}" value="{{ $value }}" data-price="{{ $price }}"
                                                        {{ old('loai_combo', $combo->loai_combo) == $value ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="loai_{{ $value }}">{{ $label }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="control-label">Giá cơ bản <span class="text-danger">*</span></label>
                                            {{-- Thêm ID gia_co_ban để JS bắt được --}}
                                            <input class="form-control" type="number" name="gia_co_ban" id="gia_co_ban"
                                                value="{{ old('gia_co_ban', $combo->gia_co_ban) }}" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label">Thời lượng (phút)</label>
                                            <input class="form-control" type="number" name="thoi_luong_phut"
                                                value="{{ old('thoi_luong_phut', $combo->thoi_luong_phut) }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="control-label">Thời gian bắt đầu</label>
                                            @php
                                                $start_time = $combo->thoi_gian_bat_dau ? \Carbon\Carbon::parse($combo->thoi_gian_bat_dau)->format('Y-m-d\TH:i') : '';
                                            @endphp
                                            <input class="form-control" type="datetime-local" name="thoi_gian_bat_dau"
                                                value="{{ old('thoi_gian_bat_dau', $start_time) }}">
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="control-label">Thời gian kết thúc</label>
                                            @php
                                                $end_time = $combo->thoi_gian_ket_thuc ? \Carbon\Carbon::parse($combo->thoi_gian_ket_thuc)->format('Y-m-d\TH:i') : '';
                                            @endphp
                                            <input class="form-control" type="datetime-local" name="thoi_gian_ket_thuc"
                                                value="{{ old('thoi_gian_ket_thuc', $end_time) }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Trạng thái <span class="text-danger">*</span></label>
                                        <div class="d-block mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="trang_thai" id="tt_db" value="dang_ban"
                                                    {{ old('trang_thai', $combo->trang_thai) == 'dang_ban' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tt_db">Đang bán</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="trang_thai" id="tt_nb" value="ngung_ban"
                                                    {{ old('trang_thai', $combo->trang_thai) == 'ngung_ban' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tt_nb">Ngừng bán</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- CỘT PHẢI: ẢNH --}}
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label">Ảnh Combo Buffet</label>
                                        <input type="file" class="form-control mb-3" name="anh" accept="image/*" onchange="previewFile(event)">
                                        
                                        <div class="text-center">
                                            @php
                                                $imagePath = asset('images/no-image.png');
                                                if ($combo->anh && file_exists(public_path('uploads/' . $combo->anh))) {
                                                    $imagePath = asset('uploads/' . $combo->anh);
                                                }
                                            @endphp
                                            <img id="previewImage" src="{{ $imagePath }}" alt="Ảnh combo">
                                        </div>
                                        <small class="text-muted d-block text-center mt-2">Ảnh hiện tại (Chọn ảnh mới để thay thế)</small>
                                    </div>
                                </div>
                            </div>

                            <div class="tile-footer mt-3">
                                <button class="btn btn-save" type="submit">
                                    <i class="fas fa-save me-2"></i> Lưu thay đổi
                                </button>
                                <a class="btn btn-cancel ms-2" href="{{ route('admin.combo-buffet.index') }}">
                                    <i class="fas fa-arrow-left me-2"></i> Hủy bỏ
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    <script>
        // 1. Script preview ảnh
        function previewFile(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('previewImage');
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.onload = () => URL.revokeObjectURL(preview.src);
            }
        }

        // 2. Script tự động điền giá khi đổi Loại Combo
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('.combo-type-radio');
            const priceInput = document.getElementById('gia_co_ban');

            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        const price = this.getAttribute('data-price');
                        priceInput.value = price;
                    }
                });
            });
        });
    </script>
@endsection