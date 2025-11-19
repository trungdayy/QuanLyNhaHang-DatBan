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
      <form action="{{ route('admin.nhan-vien.update', $nhanVien->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label>Họ tên</label>
          <input type="text" name="ho_ten" class="form-control" value="{{ old('ho_ten', $nhanVien->ho_ten) }}">
          @error('ho_ten') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email', $nhanVien->email) }}">
          @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
          <label>Số điện thoại</label>
          <input type="text" name="sdt" class="form-control" value="{{ old('sdt', $nhanVien->sdt) }}">
          @error('sdt') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
          <label>Vai trò</label>
          <select name="vai_tro" class="form-control">
            <option value="quan_ly" {{ old('vai_tro', $nhanVien->vai_tro) == 'quan_ly' ? 'selected' : '' }}>Quản lý</option>
            <option value="phuc_vu" {{ old('vai_tro', $nhanVien->vai_tro) == 'phuc_vu' ? 'selected' : '' }}>Phục vụ</option>
            <option value="bep" {{ old('vai_tro', $nhanVien->vai_tro) == 'bep' ? 'selected' : '' }}>Bếp</option>
            <option value="le_tan" {{ old('vai_tro', $nhanVien->vai_tro) == 'le_tan' ? 'selected' : '' }}>Lễ tân</option>
          </select>
          @error('vai_tro') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
          <label>Trạng thái</label>
          <select name="trang_thai" class="form-control">
            <option value="1" {{ old('trang_thai', $nhanVien->trang_thai) == 1 ? 'selected' : '' }}>Đang làm</option>
            <option value="0" {{ old('trang_thai', $nhanVien->trang_thai) == 0 ? 'selected' : '' }}>Nghỉ</option>
            <option value="2" {{ old('trang_thai', $nhanVien->trang_thai) == 2 ? 'selected' : '' }}>Khóa</option>
          </select>
          @error('trang_thai') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('admin.nhan-vien.index') }}" class="btn btn-secondary">Hủy</a>
      </form>
    </div>
  </div>
</main>
@endsection