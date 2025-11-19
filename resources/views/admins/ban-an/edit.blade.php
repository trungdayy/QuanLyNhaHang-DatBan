@extends('layouts.admins.layout-admin')

@section('title', 'Sửa Bàn Ăn')

@section('content')
<main class="app-content">
    <div class="app-title">
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.khu-vuc-ban-an') }}">Quản lý Khu Vực & Bàn Ăn</a></li>
            <li class="breadcrumb-item"><a href="#"><b>Sửa Bàn Ăn</b></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <h3 class="tile-title">Sửa Bàn Ăn: {{ $banAn->so_ban }}</h3>
                <div class="tile-body">
                    
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form class="row" method="POST" action="{{ route('admin.ban-an.update', $banAn->id) }}">
                        @csrf
                        
                        <div class="form-group col-md-6">
                            <label class="control-label">Khu Vực (*)</label>
                            <select class="form-control" name="khu_vuc_id" required>
                                <option value="">-- Chọn Khu Vực --</option>
                                @foreach ($khuVucs as $kv)
                                <option value="{{ $kv->id }}"
                                    {{ old('khu_vuc_id', $banAn->khu_vuc_id) == $kv->id ? 'selected' : '' }}>
                                    {{ $kv->ten_khu_vuc }} (Tầng {{ $kv->tang }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label">Số Bàn (*)</label>
                            <input class="form-control" type="text" name="so_ban"
                                value="{{ old('so_ban', $banAn->so_ban) }}" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="control-label">Số Ghế (*)</label>
                            <input class="form-control" type="number" name="so_ghe" min="1"
                                value="{{ old('so_ghe', $banAn->so_ghe) }}" required>
                        </div>
                        
                        {{-- 💡 SỬA PHẦN NÀY: CHẶN KHÔNG CHO MỞ THANH SỬA NẾU BẬN --}}
                        <div class="form-group col-md-6">
                            <label class="control-label">Trạng Thái (*)</label>

                            @if(in_array($banAn->trang_thai, ['dang_phuc_vu', 'da_dat']))
                                {{-- TRƯỜNG HỢP 1: BÀN ĐANG BẬN -> HIỆN INPUT KHÓA --}}
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-danger text-white">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" 
                                           value="{{ $banAn->trang_thai == 'dang_phuc_vu' ? 'Đang phục vụ' : 'Đã đặt' }}" 
                                           disabled 
                                           style="background-color: #f2dede; color: #a94442; font-weight: bold;">
                                </div>
                                
                                {{-- Input ẩn để gửi giá trị cũ về controller (để bypass validate) --}}
                                <input type="hidden" name="trang_thai" value="{{ $banAn->trang_thai }}">
                                
                                <small class="text-danger mt-1 d-block">
                                    * Bàn đang hoạt động. Không thể sửa trạng thái lúc này.
                                </small>
                            @else
                                {{-- TRƯỜNG HỢP 2: BÀN RẢNH -> HIỆN SELECT ĐỂ SỬA --}}
                                <select class="form-control" name="trang_thai" required>
                                    <option value="trong" 
                                        {{ old('trang_thai', $banAn->trang_thai) == 'trong' ? 'selected' : '' }}>
                                        Trống (Sẵn sàng)
                                    </option>
                                    <option value="khong_su_dung" 
                                        {{ old('trang_thai', $banAn->trang_thai) == 'khong_su_dung' ? 'selected' : '' }}>
                                        Không sử dụng (Bảo trì/Hỏng)
                                    </option>
                                </select>
                            @endif
                        </div>

                        <div class="form-group col-md-12 mt-3">
                            <button class="btn btn-save" type="submit">Cập nhật</button>
                            <a class="btn btn-cancel" href="{{ route('admin.khu-vuc-ban-an') }}">Hủy bỏ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection