@extends('layouts.admins.layout-admin')

@section('title', 'Sửa món ăn')

@section('content')
<main class="app-content">

    <div class="app-title">
        <ul class="app-breadcrumb breadcrumb side">
            <li class="breadcrumb-item"><a href="{{ route('admin.san-pham.index') }}">Danh sách món ăn</a></li>
            <li class="breadcrumb-item active"><a href="#"><b>Sửa món ăn</b></a></li>
        </ul>
        <div id="clock"></div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Sửa món ăn: {{ $san_pham->ten_mon }}</h3>

                <form action="{{ route('admin.san-pham.update', $san_pham->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
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
                                {{-- ... (Giữ nguyên các trường thông tin chính) ... --}}
                                <div class="mb-3">
                                    <label class="form-label">Tên món <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ten_mon"
                                        value="{{ old('ten_mon', $san_pham->ten_mon) }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="gia" min="0"
                                                value="{{ old('gia', $san_pham->gia) }}" required>
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Thời gian chế biến (phút) <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" min="1" name="thoi_gian_che_bien"
                                                value="{{ old('thoi_gian_che_bien', $san_pham->thoi_gian_che_bien) }}" required>
                                        </div>
                                    </div> --}}
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mô tả</label>
                                    <textarea class="form-control" name="mo_ta"
                                        rows="4">{{ old('mo_ta', $san_pham->mo_ta) }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                                            <select name="danh_muc_id" class="form-control" required>
                                                <option value="">-- Chọn danh mục --</option>
                                                @foreach($danhMucs as $dm)
                                                <option value="{{ $dm->id }}"
                                                    {{ old('danh_muc_id', $san_pham->danh_muc_id) == $dm->id ? 'selected' : '' }}>
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
    {{ old('loai_mon', $san_pham->loai_mon ?? '') == $loai ? 'selected' : '' }}>
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
                                                {{ old('trang_thai', $san_pham->trang_thai) == 'con' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="trang_thai_con">Còn món</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="trang_thai" id="trang_thai_het" value="het"
                                                {{ old('trang_thai', $san_pham->trang_thai) == 'het' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="trang_thai_het">Hết món</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="trang_thai" id="trang_thai_an" value="an"
                                                {{ old('trang_thai', $san_pham->trang_thai) == 'an' ? 'checked' : '' }}>
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
                                        @if($san_pham->hinh_anh)
                                        <img id="preview_chinh" src="{{ asset($san_pham->hinh_anh) }}" class="img-fluid rounded border shadow-sm"
                                            style="max-height: 200px; object-fit: cover;">
                                        @else
                                        <img id="preview_chinh" src="https://placehold.co/200x200/eee/ccc?text=Preview" class="img-fluid rounded border shadow-sm"
                                            style="max-height: 200px; object-fit: cover;">
                                        @endif
                                    </div>
                                </div>

                                {{-- START: Thêm trường Thư viện ảnh --}}
                                <hr>
                                <h4>Thư viện ảnh</h4>

                                <div class="mb-3">
                                    <label class="form-label">Thêm ảnh mới</label>
                                    <input type="file" name="anh_thu_vien[]" class="form-control" accept="image/*" multiple
                                        onchange="previewGallery(event)">
                                    <small class="form-text text-muted">Thêm ảnh mới vào thư viện.</small>
                                </div>

                                <h5 class="mt-4">Ảnh hiện có:</h5>
                                <div class="d-flex flex-wrap gap-2 mt-2 border p-2 rounded" id="gallery_current">

                                    {{-- Vòng lặp hiển thị ảnh cũ --}}
                                    @forelse ($san_pham->thuVienAnh as $anh)
                                    <div class="position-relative border p-1" id="anh_cu_{{ $anh->id }}">
                                        <img src="{{ asset($anh->duong_dan_anh) }}" style="width: 80px; height: 80px; object-fit: cover;" class="img-thumbnail">

                                        {{-- Nút xóa ảnh cũ --}}
                                        <button type="button"
                                            class="btn-close position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger p-2"
                                            aria-label="Close"
                                            onclick="removeCurrentImage({{ $anh->id }})">
                                        </button>
                                    </div>
                                    @empty
                                    <span class="text-muted small">Chưa có ảnh phụ nào.</span>
                                    @endforelse
                                </div>



                                {{-- Input ẩn để lưu ID của các ảnh cũ bị xóa --}}
                                <input type="hidden" name="anh_xoa" id="anh_xoa" value="">

                                {{-- END: Thêm trường Thư viện ảnh --}}
                            </div>
                        </div>
                    </div>

                    <div class="tile-footer">
                        <a href="{{ route('admin.san-pham.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    // Mảng lưu ID của các ảnh bị đánh dấu xóa (Được định nghĩa ở phạm vi toàn cục)
    let anhXoaIds = [];

    // HÀM PREVIEW CHO ẢNH CHÍNH (Phạm vi toàn cục)
    function previewImage(event, previewId) {
        const preview = document.getElementById(previewId);
        if (event.target.files.length > 0) {
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.onload = () => URL.revokeObjectURL(preview.src);
        }
    }

    // HÀM PREVIEW CHO THƯ VIỆN ẢNH MỚI (Phạm vi toàn cục)
    function previewGallery(event) {
        // ... (Giữ nguyên logic hàm previewGallery của bạn) ...
        const previewContainer = document.getElementById('gallery_preview');
        // Xóa nội dung cũ
        previewContainer.innerHTML = '';

        const files = event.target.files;
        if (files.length === 0) {
            if (previewContainer) previewContainer.innerHTML = '<span class="text-muted small">Ảnh mới sẽ hiển thị ở đây.</span>';
            return;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.classList.add('img-thumbnail');
            img.style.width = '80px';
            img.style.height = '80px';
            img.style.objectFit = 'cover';

            wrapperDiv.appendChild(img);
            if (previewContainer) previewContainer.appendChild(wrapperDiv);

            img.onload = () => URL.revokeObjectURL(img.src);
        }
    }

    // Hàm đánh dấu xóa ảnh cũ
    function removeCurrentImage(anhId) {
        // 1. Ẩn ảnh cũ khỏi giao diện
        const elementToRemove = document.getElementById(`anh_cu_${anhId}`);
        if (elementToRemove) {
            elementToRemove.remove();
        }

        // 2. Thêm ID vào mảng xóa
        anhXoaIds.push(anhId);

        // 3. Cập nhật input ẩn để gửi dữ liệu về Controller
        document.getElementById('anh_xoa').value = anhXoaIds.join(',');

        // 4. Cập nhật trạng thái hiển thị
        const galleryCurrent = document.getElementById('gallery_current');
        if (galleryCurrent) {
            setTimeout(() => {
                if (galleryCurrent.querySelectorAll('.position-relative').length === 0) {
                    galleryCurrent.innerHTML = '<span class="text-muted small">Chưa có ảnh phụ nào.</span>';
                }
            }, 10);
        }

        alert('Ảnh sẽ được xóa khỏi cơ sở dữ liệu khi bạn bấm "Cập nhật"');
    }

    // Khởi tạo preview cho ảnh chính với ID mới
    document.addEventListener('DOMContentLoaded', () => {
        // Do trong file cũ, bạn dùng ID là 'preview', ta đổi thành 'preview_chinh' cho rõ ràng.
        const inputAnhChinh = document.querySelector('input[name="hinh_anh"]');
        if (inputAnhChinh) {
            inputAnhChinh.setAttribute('onchange', "previewImage(event, 'preview_chinh')");
        }

        // Fix: Nếu không có ảnh cũ, gán lại text mặc định cho vùng preview
        const galleryCurrent = document.getElementById('gallery_current');
        if (galleryCurrent && galleryCurrent.querySelectorAll('.position-relative').length === 0) {
            galleryCurrent.innerHTML = '<span class="text-muted small">Chưa có ảnh phụ nào.</span>';
        }

        // Khởi tạo trạng thái ban đầu cho khu vực preview ảnh mới
        const galleryPreview = document.getElementById('gallery_preview');
        if (galleryPreview && galleryPreview.children.length === 0) {
             galleryPreview.innerHTML = '<span class="text-muted small">Chưa có ảnh mới nào được chọn.</span>';
        }

        // Khôi phục trạng thái xóa ảnh cũ (nếu có trong input hidden)
        const anhXoaInput = document.getElementById('anh_xoa');
        if (anhXoaInput && anhXoaInput.value) {
            anhXoaIds = anhXoaInput.value.split(',').map(id => parseInt(id));
        }

        // Gán ID cho input file (vì nó nằm trong thẻ HTML nên có thể gọi ngay)
        const inputAnhThuVien = document.querySelector('input[name="anh_thu_vien[]"]');
        if (inputAnhThuVien) {
             inputAnhThuVien.id = 'input_anh_thu_vien';
        }
    });
</script>
@endpush