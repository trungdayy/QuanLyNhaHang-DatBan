@extends('layouts.admins.layout-admin')

@section('title', 'Sửa hóa đơn')

@section('content')
<main class="app-content">
    <div class="app-title d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fa fa-edit"></i> Sửa hóa đơn #{{ $hoaDon->ma_hoa_don }}</h1>
        <a href="{{ route('admin.hoa-don.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
        $chiTiet = $hoaDon->chiTietHoaDon;
        
        // Thông tin khách hàng - ưu tiên từ chi_tiet_hoa_don
        $tenKhach = $chiTiet->ten_khach ?? $hoaDon->datBan->ten_khach ?? 'N/A';
        $sdtKhach = $chiTiet->sdt_khach ?? $hoaDon->datBan->sdt_khach ?? 'N/A';
        $emailKhach = $chiTiet->email_khach ?? $hoaDon->datBan->email_khach ?? 'N/A';
        $soKhach = $chiTiet->so_khach ?? $hoaDon->datBan->so_khach ?? 0;
        $nguoiLon = $chiTiet->nguoi_lon ?? $hoaDon->datBan->nguoi_lon ?? 0;
        $treEm = $chiTiet->tre_em ?? $hoaDon->datBan->tre_em ?? 0;
        
        // Thông tin bàn - ưu tiên từ chi_tiet_hoa_don
        $banSo = $chiTiet->ban_so ?? $hoaDon->datBan->banAn->so_ban ?? 'N/A';
        $khuVuc = $chiTiet->khu_vuc ?? $hoaDon->datBan->banAn->khuVuc->ten_khu_vuc ?? 'N/A';
        $tang = $chiTiet->tang ?? $hoaDon->datBan->banAn->khuVuc->tang ?? null;
        $soGhe = $chiTiet->so_ghe ?? $hoaDon->datBan->banAn->so_ghe ?? 'N/A';
        $maDatBan = $chiTiet->ma_dat_ban ?? $hoaDon->datBan->ma_dat_ban ?? 'N/A';
        
        // Thời gian phục vụ
        $gioVao = $chiTiet->gio_vao ?? ($hoaDon->datBan->gio_den ? \Carbon\Carbon::parse($hoaDon->datBan->gio_den) : null);
        $gioRa = $chiTiet->gio_ra ?? $hoaDon->created_at;
        $thoiGianPhucVu = $chiTiet->thoi_gian_phuc_vu_phut ?? ($gioVao ? $gioVao->diffInMinutes($gioRa) : 0);
        
        // Tính toán tiền
        $tongTienComboMon = $chiTiet->tong_tien_combo_mon ?? $hoaDon->tong_tien ?? 0;
        $tienGiamVoucher = $chiTiet->tien_giam_voucher ?? $hoaDon->tien_giam ?? 0;
        $tienCoc = $chiTiet->tien_coc ?? $hoaDon->datBan->tien_coc ?? 0;
        $tongPhuThu = $chiTiet->tong_phu_thu ?? $hoaDon->phu_thu ?? 0;
        $phaiThanhToan = $chiTiet->phai_thanh_toan ?? null;
        if($phaiThanhToan === null) {
            $phaiThanhToan = $tongTienComboMon - $tienGiamVoucher - $tienCoc + $tongPhuThu;
            if($phaiThanhToan < 0) $phaiThanhToan = 0;
        }
        $tienKhachDua = $chiTiet->tien_khach_dua ?? 0;
        $tienTraLai = $chiTiet->tien_tra_lai ?? 0;
        
        // Phương thức thanh toán hiện tại
        $phuongThucHienTai = $hoaDon->phuong_thuc_tt ?? 'chua_thanh_toan';
        if($phuongThucHienTai == 'Tiền mặt') $phuongThucHienTai = 'tien_mat';
        if($phuongThucHienTai == 'Chuyển khoản ngân hàng') $phuongThucHienTai = 'chuyen_khoan';
        
        // Trạng thái hiện tại
        $trangThaiHienTai = $hoaDon->trang_thai ?? 'chua_thanh_toan';
    @endphp

    <form action="{{ route('admin.hoa-don.update', $hoaDon->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Cột trái: Thông tin khách và bàn (chỉ đọc) --}}
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa fa-user"></i> Thông tin khách hàng</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Tên khách:</strong> {{ $tenKhach }}</p>
                        <p class="mb-2"><strong>SĐT:</strong> {{ $sdtKhach }}</p>
                        <p class="mb-2"><strong>Email:</strong> {{ $emailKhach }}</p>
                        <p class="mb-2"><strong>Số khách:</strong> {{ $soKhach }} người 
                            <small class="text-muted">(Người lớn: {{ $nguoiLon }}, Trẻ em: {{ $treEm }})</small>
                        </p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fa fa-table"></i> Thông tin bàn</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Bàn số:</strong> {{ $banSo }}</p>
                        <p class="mb-2"><strong>Khu vực:</strong> {{ $khuVuc }}@if($tang) - Tầng {{ $tang }}@endif</p>
                        <p class="mb-2"><strong>Sức chứa:</strong> {{ $soGhe }} chỗ</p>
                        <p class="mb-0"><strong>Mã đặt bàn:</strong> <code>{{ $maDatBan }}</code></p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fa fa-clock"></i> Thời gian phục vụ</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Giờ vào:</strong> {{ $gioVao ? $gioVao->format('d/m/Y H:i') : 'N/A' }}</p>
                        <p class="mb-2"><strong>Giờ ra:</strong> {{ $gioRa ? $gioRa->format('d/m/Y H:i') : 'N/A' }}</p>
                        <p class="mb-0"><strong>Thời gian phục vụ:</strong> 
                            {{ floor($thoiGianPhucVu / 60) }} giờ {{ $thoiGianPhucVu % 60 }} phút
                        </p>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Thông tin thanh toán (có thể sửa) --}}
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fa fa-money-bill"></i> Thông tin thanh toán</h5>
                    </div>
                    <div class="card-body">
                        {{-- Trạng thái (chọn trước) --}}
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>Trạng thái <span class="text-danger">*</span></strong></label>
                            <select name="trang_thai" id="trang_thai" class="form-control" required>
                                <option value="chua_thanh_toan" {{ old('trang_thai', $trangThaiHienTai) == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán</option>
                                <option value="da_thanh_toan" {{ old('trang_thai', $trangThaiHienTai) == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                            </select>
                        </div>

                        {{-- Phương thức thanh toán (chỉ cho chọn khi đã thanh toán) --}}
                        <div class="form-group mb-3">
                            <label class="form-label"><strong>Phương thức thanh toán <span class="text-danger">*</span></strong></label>
                            <select name="phuong_thuc_tt" id="phuong_thuc_tt" class="form-control" required>
                                <option value="tien_mat" {{ old('phuong_thuc_tt', $phuongThucHienTai) == 'tien_mat' ? 'selected' : '' }}>Tiền mặt</option>
                                <option value="chuyen_khoan" {{ old('phuong_thuc_tt', $phuongThucHienTai) == 'chuyen_khoan' ? 'selected' : '' }}>Chuyển khoản</option>
                                <option value="the_ATM" {{ old('phuong_thuc_tt', $phuongThucHienTai) == 'the_ATM' ? 'selected' : '' }}>Thẻ ATM</option>
                                <option value="vnpay" {{ old('phuong_thuc_tt', $phuongThucHienTai) == 'vnpay' ? 'selected' : '' }}>VNPay</option>
                                <option value="chua_thanh_toan" {{ old('phuong_thuc_tt', $phuongThucHienTai) == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán</option>
                            </select>
                            <small class="form-text text-muted" id="phuong_thuc_note">Vui lòng chọn trạng thái "Đã thanh toán" để chọn phương thức thanh toán</small>
                        </div>

                        {{-- Tiền khách đưa (chỉ hiện khi chọn tiền mặt và đã thanh toán) --}}
                        <div class="form-group mb-3" id="tien_khach_dua_group" style="display: none;">
                            <label class="form-label"><strong>Tiền khách đưa</strong></label>
                            <input type="number" name="tien_khach_dua" id="tien_khach_dua" 
                                   class="form-control" 
                                   value="{{ old('tien_khach_dua', $tienKhachDua) }}" 
                                   min="0" 
                                   step="1000"
                                   placeholder="Nhập số tiền khách đưa">
                            <small class="form-text text-muted">Số tiền khách đưa khi thanh toán bằng tiền mặt</small>
                        </div>

                        {{-- Tiền trả lại (hiển thị tự động) --}}
                        <div class="form-group mb-3" id="tien_tra_lai_group" style="display: none;">
                            <label class="form-label"><strong>Tiền trả lại</strong></label>
                            <input type="text" id="tien_tra_lai_display" 
                                   class="form-control bg-light" 
                                   readonly
                                   value="0 đ">
                            <small class="form-text text-muted">Số tiền trả lại cho khách (tự động tính)</small>
                        </div>

                        <hr>

                        {{-- Thông tin tính toán (chỉ đọc) --}}
                        <div class="mb-2">
                            <strong>Tổng tiền món:</strong> 
                            <span class="float-end">{{ number_format($tongTienComboMon) }} đ</span>
                        </div>
                        @if($tienGiamVoucher > 0)
                        <div class="mb-2 text-success">
                            <strong>Voucher giảm:</strong> 
                            <span class="float-end">-{{ number_format($tienGiamVoucher) }} đ</span>
                        </div>
                        @endif
                        @if($tienCoc > 0)
                        <div class="mb-2 text-primary">
                            <strong>Tiền cọc:</strong> 
                            <span class="float-end">-{{ number_format($tienCoc) }} đ</span>
                        </div>
                        @endif
                        @if($tongPhuThu > 0)
                        <div class="mb-2 text-danger">
                            <strong>Phụ thu:</strong> 
                            <span class="float-end">+{{ number_format($tongPhuThu) }} đ</span>
                        </div>
                        @endif
                        <div class="mb-2 border-top pt-2">
                            <strong class="text-danger fs-5">THỰC THU:</strong> 
                            <span class="float-end text-danger fs-5 fw-bold">{{ number_format($phaiThanhToan) }} đ</span>
                        </div>
                        <div class="mb-2" id="khach_dua_display" style="display: none;">
                            <strong>Khách đưa:</strong> 
                            <span class="float-end" id="khach_dua_value">0 đ</span>
                        </div>
                        <div class="mb-2" id="tra_lai_display" style="display: none;">
                            <strong>Trả lại:</strong> 
                            <span class="float-end" id="tra_lai_value">0 đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fa fa-save"></i> Cập nhật hóa đơn
            </button>
            <a href="{{ route('admin.hoa-don.index') }}" class="btn btn-secondary btn-lg">
                <i class="fa fa-times"></i> Hủy
            </a>
        </div>
    </form>
</main>

<script>
    const phaiThanhToan = {{ $phaiThanhToan }};
    
    // Cập nhật trạng thái phương thức thanh toán dựa trên trạng thái
    function updatePhuongThucState() {
        const trangThai = document.getElementById('trang_thai').value;
        const phuongThucSelect = document.getElementById('phuong_thuc_tt');
        const phuongThucNote = document.getElementById('phuong_thuc_note');
        
        if (trangThai === 'chua_thanh_toan') {
            // Chưa thanh toán: disable và set về chua_thanh_toan
            phuongThucSelect.disabled = true;
            phuongThucSelect.value = 'chua_thanh_toan';
            phuongThucNote.textContent = 'Vui lòng chọn trạng thái "Đã thanh toán" để chọn phương thức thanh toán';
            phuongThucNote.style.color = '#dc3545';
            
            // Ẩn tiền khách đưa và tiền trả lại
            document.getElementById('tien_khach_dua_group').style.display = 'none';
            document.getElementById('tien_tra_lai_group').style.display = 'none';
            document.getElementById('khach_dua_display').style.display = 'none';
            document.getElementById('tra_lai_display').style.display = 'none';
            document.getElementById('tien_khach_dua').value = '';
        } else {
            // Đã thanh toán: enable
            phuongThucSelect.disabled = false;
            phuongThucNote.textContent = '';
            phuongThucNote.style.color = '';
            
            // Hiển thị/ẩn tiền khách đưa dựa trên phương thức
            toggleTienKhachDua();
        }
    }
    
    // Hiển thị/ẩn trường tiền khách đưa dựa trên phương thức thanh toán
    function toggleTienKhachDua() {
        const trangThai = document.getElementById('trang_thai').value;
        const phuongThuc = document.getElementById('phuong_thuc_tt').value;
        const tienKhachDuaGroup = document.getElementById('tien_khach_dua_group');
        const tienTraLaiGroup = document.getElementById('tien_tra_lai_group');
        
        if (trangThai === 'da_thanh_toan' && phuongThuc === 'tien_mat') {
            tienKhachDuaGroup.style.display = 'block';
            tienTraLaiGroup.style.display = 'block';
            calculateTienTraLai();
        } else {
            tienKhachDuaGroup.style.display = 'none';
            tienTraLaiGroup.style.display = 'none';
            document.getElementById('tien_khach_dua').value = '';
            document.getElementById('tien_tra_lai_display').value = '0 đ';
            document.getElementById('khach_dua_display').style.display = 'none';
            document.getElementById('tra_lai_display').style.display = 'none';
        }
    }
    
    // Tính tiền trả lại
    function calculateTienTraLai() {
        const tienKhachDua = parseFloat(document.getElementById('tien_khach_dua').value) || 0;
        const tienTraLai = Math.max(0, tienKhachDua - phaiThanhToan);
        
        // Cập nhật hiển thị
        document.getElementById('tien_tra_lai_display').value = tienTraLai.toLocaleString('vi-VN') + ' đ';
        
        // Cập nhật trong phần tóm tắt
        if (tienKhachDua > 0) {
            document.getElementById('khach_dua_display').style.display = 'block';
            document.getElementById('tra_lai_display').style.display = 'block';
            document.getElementById('khach_dua_value').textContent = tienKhachDua.toLocaleString('vi-VN') + ' đ';
            document.getElementById('tra_lai_value').textContent = tienTraLai.toLocaleString('vi-VN') + ' đ';
        } else {
            document.getElementById('khach_dua_display').style.display = 'none';
            document.getElementById('tra_lai_display').style.display = 'none';
        }
    }
    
    // Gọi khi trang load
    document.addEventListener('DOMContentLoaded', function() {
        updatePhuongThucState();
        document.getElementById('trang_thai').addEventListener('change', updatePhuongThucState);
        document.getElementById('phuong_thuc_tt').addEventListener('change', toggleTienKhachDua);
        document.getElementById('tien_khach_dua').addEventListener('input', calculateTienTraLai);
    });
</script>
@endsection
