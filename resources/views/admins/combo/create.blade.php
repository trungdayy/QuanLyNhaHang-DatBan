@extends('layouts.admins.layout-admin')

@section('title', 'Thêm Combo Buffet')

@section('content')
    <main class="app-content">
        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.combo-buffet.index') }}">Danh sách combo</a></li>
                <li class="breadcrumb-item"><a href="#">Thêm combo</a></li>
            </ul>
            <div id="clock"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title">Tạo mới Combo Buffet</h3>
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

                        {{-- Form thêm combo --}}
                        <form action="{{ route('admin.combo-buffet.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                {{-- CỘT TRÁI: Thông tin cơ bản --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Tên combo <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="ten_combo"
                                            value="{{ old('ten_combo') }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Mô tả Combo</label>
                                        <textarea class="form-control" name="mo_ta" rows="4">{{ old('mo_ta') }}</textarea>
                                    </div>

                                    {{-- 🔽 SỬA: Đưa Loại Combo lên trước để chọn xong tự điền giá xuống dưới --}}
                                    <div class="form-group">
                                        <label class="control-label">Loại combo (Chọn để tự điền giá)</label>
                                        <div class="d-block mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input combo-type-radio" type="radio" name="loai_combo" 
                                                       id="loai_99k" value="99k" data-price="99000"
                                                       {{ old('loai_combo') == '99k' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loai_99k">Combo 99k</label>
                                            </div>
                                            
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input combo-type-radio" type="radio" name="loai_combo" 
                                                       id="loai_199k" value="199k" data-price="199000"
                                                       {{ old('loai_combo') == '199k' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loai_199k">Combo 199k</label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input combo-type-radio" type="radio" name="loai_combo" 
                                                       id="loai_299k" value="299k" data-price="299000"
                                                       {{ old('loai_combo') == '299k' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loai_299k">Combo 299k</label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input combo-type-radio" type="radio" name="loai_combo" 
                                                       id="loai_399k" value="399k" data-price="399000"
                                                       {{ old('loai_combo') == '399k' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loai_399k">Combo 399k</label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input combo-type-radio" type="radio" name="loai_combo" 
                                                       id="loai_499k" value="499k" data-price="499000"
                                                       {{ old('loai_combo') == '499k' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="loai_499k">Combo 499k</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Giá cơ bản <span class="text-danger">*</span></label>
                                        {{-- Thêm id="gia_co_ban" để JS tìm thấy --}}
                                        <input class="form-control" type="number" name="gia_co_ban" id="gia_co_ban"
                                            value="{{ old('gia_co_ban') }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Thời lượng (phút)</label>
                                        <input class="form-control" type="number" name="thoi_luong_phut"
                                            value="{{ old('thoi_luong_phut') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Thời gian bắt đầu</label>
                                        <input class="form-control" type="datetime-local" name="thoi_gian_bat_dau"
                                            value="{{ old('thoi_gian_bat_dau') }}">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Thời gian kết thúc</label>
                                        <input class="form-control" type="datetime-local" name="thoi_gian_ket_thuc"
                                            value="{{ old('thoi_gian_ket_thuc') }}">
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">Trạng thái <span class="text-danger">*</span></label>
                                        <div class="d-block mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="trang_thai"
                                                    id="tt_db" value="dang_ban"
                                                    {{ old('trang_thai', 'dang_ban') == 'dang_ban' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tt_db">Đang bán</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="trang_thai"
                                                    id="tt_nb" value="ngung_ban"
                                                    {{ old('trang_thai') == 'ngung_ban' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="tt_nb">Ngừng bán</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- CỘT PHẢI: Upload Ảnh và Preview --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Ảnh combo buffet</label>
                                        <input type="file" name="anh" class="form-control" accept="image/*"
                                            onchange="previewImage(event, 'preview_combo')">
                                        <small class="text-muted">Định dạng: JPG, PNG, JPEG — tối đa 2MB</small>
                                    </div>

                                    <div class="form-group text-center border p-3 rounded bg-light">
                                        <label class="control-label d-block mb-2 fw-bold">Preview Ảnh</label>
                                        <img id="preview_combo" src="https://placehold.co/200x200/eee/ccc?text=Ảnh+Combo"
                                            class="img-fluid rounded shadow-sm"
                                            style="max-width: 100%; max-height: 200px; object-fit: cover;">
                                    </div>
                                </div>
                            </div>

                            <div class="tile-footer">
                                <button class="btn btn-add" type="submit">
                                    <i class="fas fa-plus-circle me-2"></i>Lưu lại
                                </button>
                                <a class="btn btn-secondary" href="{{ route('admin.combo-buffet.index') }}">
                                    <i class="fas fa-arrow-left me-2"></i>Hủy bỏ
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
        // 1. Hàm tự động điền giá khi chọn Loại Combo
        document.addEventListener('DOMContentLoaded', function() {
            // Lấy tất cả các nút radio có class 'combo-type-radio'
            const radioButtons = document.querySelectorAll('.combo-type-radio');
            // Lấy ô nhập giá
            const priceInput = document.getElementById('gia_co_ban');

            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Nếu nút này được chọn
                    if (this.checked) {
                        // Lấy giá trị từ thuộc tính data-price
                        const price = this.getAttribute('data-price');
                        // Điền vào ô giá
                        priceInput.value = price;
                    }
                });
            });
        });

        // 2. Hàm preview ảnh
        function previewImage(event, previewId) {
            const preview = document.getElementById(previewId);
            if (event.target.files.length > 0) {
                preview.src = URL.createObjectURL(event.target.files[0]);
                preview.onload = () => URL.revokeObjectURL(preview.src);
            } else {
                preview.src = "https://placehold.co/200x200/eee/ccc?text=Ảnh+Combo";
            }
        }
    </script>
@endsection