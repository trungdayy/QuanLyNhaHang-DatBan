@extends('layouts.admins.layout-admin')

@section('title', 'Sửa nhân viên')

@section('content')
<main class="app-content">
  <div class="app-title">
    <ul class="app-breadcrumb breadcrumb side">
      <li class="breadcrumb-item"><a href="{{ route('admin.nhan-vien.index') }}"><b>Quản lý nhân viên</b></a></li>
      <li class="breadcrumb-item active">Sửa</li>
    </ul>
  </div>

  <div class="tile">
    <div class="tile-body">
      <form action="{{ route('admin.nhan-vien.update', $nhanVien->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label>Ảnh đại diện</label>
          @if($nhanVien->hinh_anh)
            <div class="mb-2">
              <img src="{{ asset($nhanVien->hinh_anh) }}" alt="Ảnh hiện tại" style="max-width: 150px; max-height: 150px; border-radius: 50%; object-fit: cover;">
            </div>
          @endif
          <input type="file" name="hinh_anh" class="form-control" accept="image/*">
          <small class="text-muted">Chọn ảnh mới để thay thế (để trống nếu giữ nguyên ảnh cũ)</small>
          @error('hinh_anh') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
          <label>Họ tên</label>
          <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten', $nhanVien->ho_ten) }}">
          @error('ho_ten') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email', $nhanVien->email) }}" readonly>
          <small class="text-muted">Email không thể thay đổi</small>
          @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
          <label>Số điện thoại</label>
          <input type="text" name="sdt" class="form-control" value="{{ old('sdt', $nhanVien->sdt) }}">
          @error('sdt') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
          <label>Vai trò</label>
          <select name="vai_tro" class="form-control" disabled>
            <option value="quan_ly" {{ old('vai_tro', $nhanVien->vai_tro) == 'quan_ly' ? 'selected' : '' }}>Quản lý</option>
            <option value="phuc_vu" {{ old('vai_tro', $nhanVien->vai_tro) == 'phuc_vu' ? 'selected' : '' }}>Phục vụ</option>
            <option value="bep" {{ old('vai_tro', $nhanVien->vai_tro) == 'bep' ? 'selected' : '' }}>Bếp</option>
            <option value="le_tan" {{ old('vai_tro', $nhanVien->vai_tro) == 'le_tan' ? 'selected' : '' }}>Lễ tân</option>
          </select>
          <input type="hidden" name="vai_tro" value="{{ $nhanVien->vai_tro }}">
          <small class="text-muted">Vai trò không thể thay đổi</small>
          @error('vai_tro') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
          <label>Trạng thái</label>
          <select name="trang_thai" class="form-control" disabled>
            <option value="1" {{ old('trang_thai', $nhanVien->trang_thai) == 1 ? 'selected' : '' }}>Đang làm</option>
            <option value="0" {{ old('trang_thai', $nhanVien->trang_thai) == 0 ? 'selected' : '' }}>Nghỉ</option>
            <option value="2" {{ old('trang_thai', $nhanVien->trang_thai) == 2 ? 'selected' : '' }}>Khóa</option>
          </select>
          <input type="hidden" name="trang_thai" value="{{ $nhanVien->trang_thai }}">
          <small class="text-muted">Trạng thái không thể thay đổi. Sử dụng nút tắt/mở trong danh sách để thay đổi trạng thái.</small>
          @error('trang_thai') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('admin.nhan-vien.index') }}" class="btn btn-secondary">Hủy</a>
      </form>
    </div>
  </div>
</main>
@endsection