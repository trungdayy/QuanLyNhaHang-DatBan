@extends('layouts.admins.layout-admin')

@section('title', 'Sửa Combo Buffet')

@section('style')
    <style>
        .tile {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: none;
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

        #previewImage {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
        }

        #previewImage:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        input[type="file"] {
            border-radius: 6px;
            padding: 6px;
            cursor: pointer;
        }

        .btn-save {
            background-color: #007bff;
            color: #fff;
            font-weight: 600;
            border-radius: 6px;
            padding: 8px 18px;
            transition: all 0.2s ease;
        }

        .btn-save:hover {
            background-color: #0056b3;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: #fff;
            border-radius: 6px;
            padding: 8px 18px;
            font-weight: 500;
        }

        .btn-cancel:hover {
            background-color: #565e64;
        }

        .form-check-inline {
            margin-right: 15px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .tile-title {
            color: #002b5b;
            font-weight: 700;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
                    <h3 class="tile-title">Sửa Combo Buffet: {{ $combo->ten_combo }}</h3>
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

                        <form class="row" action="{{ route('admin.combo-buffet.update', $combo->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group col-md-6">
                                <label class="control-label">Tên combo <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="ten_combo"
                                    value="{{ old('ten_combo', $combo->ten_combo) }}" required>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="control-label">Mô tả Combo</label>
                                <textarea class="form-control" name="mo_ta" rows="4">{{ old('mo_ta', $combo->mo_ta) }}</textarea>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Giá cơ bản <span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="gia_co_ban"
                                    value="{{ old('gia_co_ban', $combo->gia_co_ban) }}" required>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Loại combo</label>
                                <div class="d-block mt-2">
                                    @foreach (['nguoi_lon' => 'Người lớn', 'tre_em' => 'Trẻ em', 'vip' => 'VIP', 'khuyen_mai' => 'Khuyến mãi'] as $value => $label)
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="loai_combo"
                                                id="loai_{{ $value }}" value="{{ $value }}"
                                                {{ old('loai_combo', $combo->loai_combo) == $value ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="loai_{{ $value }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Thời lượng (phút)</label>
                                <input class="form-control" type="number" name="thoi_luong_phut"
                                    value="{{ old('thoi_luong_phut', $combo->thoi_luong_phut) }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Thời gian bắt đầu</label>
                                @php
                                    $start_time = $combo->thoi_gian_bat_dau
                                        ? \Carbon\Carbon::parse($combo->thoi_gian_bat_dau)->format('Y-m-d\TH:i')
                                        : '';
                                @endphp
                                <input class="form-control" type="datetime-local" name="thoi_gian_bat_dau"
                                    value="{{ old('thoi_gian_bat_dau', $start_time) }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Thời gian kết thúc</label>
                                @php
                                    $end_time = $combo->thoi_gian_ket_thuc
                                        ? \Carbon\Carbon::parse($combo->thoi_gian_ket_thuc)->format('Y-m-d\TH:i')
                                        : '';
                                @endphp
                                <input class="form-control" type="datetime-local" name="thoi_gian_ket_thuc"
                                    value="{{ old('thoi_gian_ket_thuc', $end_time) }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label class="control-label">Trạng thái <span class="text-danger">*</span></label>
                                <div class="d-block mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="trang_thai" id="tt_db"
                                            value="dang_ban"
                                            {{ old('trang_thai', $combo->trang_thai) == 'dang_ban' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tt_db">Đang bán</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="trang_thai" id="tt_nb"
                                            value="ngung_ban"
                                            {{ old('trang_thai', $combo->trang_thai) == 'ngung_ban' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tt_nb">Ngừng bán</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Ảnh Combo: đặt xuống cuối --}}
                            <div class="form-group col-md-12 mt-3">
                                <label class="control-label d-block">Ảnh Combo Buffet</label>

                                @php
                                    $imagePath = asset('images/no-image.png');
                                    if ($combo->anh && file_exists(public_path('uploads/' . $combo->anh))) {
                                        $imagePath = asset('uploads/' . $combo->anh);
                                    }
                                @endphp

                                <div class="mb-3">
                                    <img id="previewImage" src="{{ $imagePath }}" alt="Ảnh combo">
                                </div>

                                <input type="file" class="form-control" name="anh" accept="image/*"
                                    onchange="previewFile(event)">
                                <small class="text-muted">Chọn ảnh mới nếu muốn thay đổi</small>
                            </div>

                            <div class="form-group col-md-12 tile-footer mt-4">
                                <button class="btn btn-save" type="submit">
                                    <i class="fas fa-save me-2"></i> Lưu lại
                                </button>
                                <a class="btn btn-cancel" href="{{ route('admin.combo-buffet.index') }}">
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
        function previewFile(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('previewImage');
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.onload = () => URL.revokeObjectURL(preview.src);
            }
        }
    </script>
@endsection