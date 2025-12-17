@extends('layouts.admins.layout-admin')

@section('title', 'Thêm món ăn')

@section('content')
<main class="app-content">

    <div class="app-title">
        <ul class="app-breadcrumb breadcrumb side">
            <li class="breadcrumb-item"><a href="{{ route('admin.san-pham.index') }}">Danh sách món ăn</a></li>
            <li class="breadcrumb-item active"><a href="#"><b>Thêm món ăn</b></a></li>
        </ul>
        <div id="clock"></div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Tạo mới món ăn</h3>

                {{-- Đảm bảo Form có method="POST" và enctype="multipart/form-data" --}}
                <form action="{{ route('admin.san-pham.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="tile-body">

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <div class="row">
                            {{-- Cột trái (Thông tin chính) --}}
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Tên món <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ten_mon"
                                        value="{{ old('ten_mon') }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="gia" min="0"
                                                value="{{ old('gia') }}" required>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Thời gian chế biến (phút) <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" min="1" name="thoi_gian_che_bien"
                                                value="{{ old('thoi_gian_che_bien') }}" required>
                                        </div>
                                    </div> --}}
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea class="form-control" name="mo_ta"
                                        rows="4">{{ old('mo_ta') }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                            <select name="danh_muc_id" class="form-control" required>
                                                <option value="">-- Chọn danh mục --</option>
                                                @foreach($danhMucs as $dm)
                                                <option value="{{ $dm->id }}" {{ old('danh_muc_id') == $dm->id ? 'selected' : '' }}>
                                                    {{ $dm->ten_danh_muc }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Loại món</label>

                                            @php
                                            $loaiMonDB = [
                                            'Sống',
                                            'Chín',
                                            'Nướng',
                                            'Xào / Luộc',
                                            'Bánh ngọt',
                                            'Trái cây',
                                            'Nước có ga',
                                            'Nước không ga',
                                            'Trà / Cà phê',
                                            ];
                                            @endphp

                                            <select name="loai_mon" class="form-control">
                                                <option value="">— Chọn loại món —</option>
                                                @foreach($loaiMonDB as $loai)
                                                <option value="{{ $loai }}"
                                                    {{ old('loai_mon', $mon_an->loai_mon ?? '') == $loai ? 'selected' : '' }}>
                                                    {{ $loai }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <div class="d-block mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="trang_thai" id="trang_thai_con" value="con"
                                                {{ old('trang_thai', 'con') == 'con' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="trang_thai_con">Còn món</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="trang_thai" id="trang_thai_het" value="het"
                                                {{ old('trang_thai') == 'het' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="trang_thai_het">Hết món</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="trang_thai" id="trang_thai_an" value="an"
                                                {{ old('trang_thai') == 'an' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="trang_thai_an">Ẩn</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Cột phải (Ảnh chính và Thư viện ảnh) --}}
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Ảnh đại diện (Chính)</label>
                                    <input type="file" name="hinh_anh" class="form-control" accept="image/*"
                                        onchange="previewImage(event, 'preview_chinh')">
                                    <div class="text-center mt-2">
                                        <img id="preview_chinh" src="https://placehold.co/200x200/eee/ccc?text=Ảnh+chính" class="img-fluid rounded border shadow-sm"
                                            style="max-height: 200px; object-fit: cover;">
                                    </div>
                                </div>

                                {{-- START: Thêm trường Thư viện ảnh --}}
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label">Thư viện ảnh (Ảnh phụ)</label>
                                    <input type="file" name="anh_thu_vien[]" class="form-control" accept="image/*" multiple
                                        onchange="previewGallery(event)">
                                    <small class="form-text text-muted">Có thể chọn nhiều ảnh.</small>
                                </div>

                                {{-- Khu vực hiển thị preview ảnh phụ --}}
                                <div class="d-flex flex-wrap gap-2 mt-2 border p-2 rounded" id="gallery_preview">
                                    <span class="text-muted small">Ảnh sẽ hiển thị ở đây.</span>
                                </div>
                                {{-- END: Thêm trường Thư viện ảnh --}}
                            </div>
                        </div>
                    </div>

                    <div class="tile-footer">
                        <a href="{{ route('admin.san-pham.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-add">
                            <i class="fas fa-plus-circle me-2"></i> Thêm món
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

{{-- PHẦN SỬA ĐỔI QUAN TRỌNG: Đảm bảo JavaScript được chèn đúng bằng @section('script') --}}
@section('script')
<script>
    // Hàm preview cho ảnh chính
    function previewImage(event, previewId) {
        const preview = document.getElementById(previewId);
        // Kiểm tra xem có file nào được chọn không
        if (event.target.files.length > 0) {
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.onload = () => URL.revokeObjectURL(preview.src);
        } else {
            // Trường hợp hủy chọn file, đặt lại ảnh placeholder
            preview.src = "https://placehold.co/200x200/eee/ccc?text=Ảnh+chính";
        }
    }

    // Hàm preview cho Thư viện ảnh (chọn nhiều ảnh)
    function previewGallery(event) {
        const previewContainer = document.getElementById('gallery_preview');
        // Xóa tất cả các ảnh preview cũ
        previewContainer.innerHTML = '';

        const files = event.target.files;

        if (files.length === 0) {
            previewContainer.innerHTML = '<span class="text-muted small">Ảnh sẽ hiển thị ở đây.</span>';
            return;
        }

        // Lặp qua tất cả các file được chọn và tạo thẻ <img>
        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.classList.add('img-thumbnail'); // Dùng class Bootstrap để làm thumbnail
            img.style.width = '80px';
            img.style.height = '80px';
            img.style.objectFit = 'cover';
            img.style.marginRight = '5px'; // Thêm khoảng cách giữa các ảnh

            previewContainer.appendChild(img);
            img.onload = () => URL.revokeObjectURL(img.src);
        }
    }
</script>
@endsection