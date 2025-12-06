@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Thanh toán')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-header bg-primary text-white py-4 rounded-top-4">
                        <h3 class="mb-0 text-center">
                            <i class="bi bi-cash-coin me-2"></i>Thanh toán bàn {{ $order->banAn->so_ban }}
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        {{-- Thông tin order --}}
                        <div class="mb-4 p-3 bg-light rounded-3">
                            <h5 class="fw-bold mb-3"><i class="bi bi-receipt me-2"></i>Thông tin đơn hàng</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Bàn số:</strong> {{ $order->banAn->so_ban }}</p>
                                    <p class="mb-2"><strong>Số khách:</strong> {{ $order->datBan->so_khach ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong>Khách hàng:</strong> {{ $order->datBan->ten_khach ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Tổng món:</strong> {{ $order->tong_mon }}</p>
                                    <p class="mb-2"><strong>Tổng tiền:</strong> <span class="text-danger fw-bold fs-5">{{ number_format($tongTienOrder) }} đ</span></p>
                                    <p class="mb-2"><strong>Tiền cọc:</strong> <span class="text-success">{{ number_format($tienCoc) }} đ</span></p>
                                </div>
                            </div>
                        </div>

                        {{-- Form thanh toán --}}
                        <form action="{{ route('nhanVien.thanh-toan.luu', $order->id) }}" method="POST" id="thanhToanForm">
                            @csrf

                            {{-- Phương thức thanh toán --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="bi bi-credit-card me-2"></i>Phương thức thanh toán <span class="text-danger">*</span></label>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-check border rounded p-3 h-100">
                                            <input class="form-check-input" type="radio" name="phuong_thuc_tt" id="tien_mat" value="tien_mat" checked>
                                            <label class="form-check-label w-100" for="tien_mat">
                                                <i class="bi bi-cash-stack text-success fs-4 d-block mb-2"></i>
                                                <strong>Tiền mặt</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check border rounded p-3 h-100">
                                            <input class="form-check-input" type="radio" name="phuong_thuc_tt" id="chuyen_khoan" value="chuyen_khoan">
                                            <label class="form-check-label w-100" for="chuyen_khoan">
                                                <i class="bi bi-bank text-primary fs-4 d-block mb-2"></i>
                                                <strong>Chuyển khoản</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check border rounded p-3 h-100">
                                            <input class="form-check-input" type="radio" name="phuong_thuc_tt" id="the_ATM" value="the_ATM">
                                            <label class="form-check-label w-100" for="the_ATM">
                                                <i class="bi bi-credit-card-2-front text-info fs-4 d-block mb-2"></i>
                                                <strong>Thẻ ATM</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tiền khách đưa (chỉ hiện khi chọn tiền mặt) --}}
                            <div class="mb-4" id="tienKhachDuaGroup" style="display: none;">
                                <label for="tien_khach_dua" class="form-label fw-bold">Tiền khách đưa</label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" id="tien_khach_dua" name="tien_khach_dua" min="0" step="1000" placeholder="Nhập số tiền">
                                    <span class="input-group-text">đ</span>
                                </div>
                                <div id="tienTraLai" class="mt-2 text-success fw-bold" style="display: none;"></div>
                            </div>

                            {{-- Voucher --}}
                            <div class="mb-4">
                                <label for="voucher_id" class="form-label fw-bold"><i class="bi bi-ticket-perforated me-2"></i>Voucher (nếu có)</label>
                                <select class="form-select form-select-lg" id="voucher_id" name="voucher_id">
                                    <option value="">-- Không sử dụng voucher --</option>
                                    @foreach($vouchers as $voucher)
                                    <option value="{{ $voucher->id }}" 
                                        data-loai="{{ $voucher->loai_giam }}"
                                        data-gia-tri="{{ $voucher->gia_tri }}"
                                        data-gia-tri-toi-da="{{ $voucher->gia_tri_toi_da }}">
                                        {{ $voucher->ma_voucher }} - 
                                        @if($voucher->loai_giam == 'phan_tram')
                                            Giảm {{ $voucher->gia_tri }}%
                                        @else
                                            Giảm {{ number_format($voucher->gia_tri) }} đ
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Phụ thu --}}
                            <div class="mb-4">
                                <label for="phu_thu" class="form-label fw-bold"><i class="bi bi-plus-circle me-2"></i>Phụ thu (nếu có)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control form-control-lg" id="phu_thu" name="phu_thu" value="0" min="0" step="1000">
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>

                            {{-- Tóm tắt thanh toán --}}
                            <div class="mb-4 p-4 bg-primary text-white rounded-3">
                                <h5 class="mb-3"><i class="bi bi-calculator me-2"></i>Tóm tắt thanh toán</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tổng tiền:</span>
                                    <strong id="tongTien">{{ number_format($tongTienOrder) }} đ</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tiền cọc:</span>
                                    <strong class="text-success" id="tienCoc">- {{ number_format($tienCoc) }} đ</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2" id="tienGiamRow" style="display: none;">
                                    <span>Tiền giảm (Voucher):</span>
                                    <strong class="text-success" id="tienGiam">- 0 đ</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2" id="phuThuRow" style="display: none;">
                                    <span>Phụ thu:</span>
                                    <strong class="text-danger" id="phuThu">+ 0 đ</strong>
                                </div>
                                <hr class="my-3" style="border-color: rgba(255,255,255,0.3);">
                                <div class="d-flex justify-content-between fs-4 fw-bold">
                                    <span>Phải thanh toán:</span>
                                    <span id="phaiThanhToan">{{ number_format($tongTienOrder - $tienCoc) }} đ</span>
                                </div>
                            </div>

                            {{-- Nút xác nhận --}}
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('nhanVien.order.page', $order->id) }}" class="btn btn-secondary btn-lg px-5">
                                    <i class="bi bi-arrow-left me-2"></i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-check-circle me-2"></i>Xác nhận thanh toán
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tongTienOrder = {{ $tongTienOrder }};
    const tienCoc = {{ $tienCoc }};
    const phuongThucTT = document.querySelectorAll('input[name="phuong_thuc_tt"]');
    const tienKhachDuaGroup = document.getElementById('tienKhachDuaGroup');
    const tienKhachDuaInput = document.getElementById('tien_khach_dua');
    const tienTraLaiDiv = document.getElementById('tienTraLai');
    const voucherSelect = document.getElementById('voucher_id');
    const phuThuInput = document.getElementById('phu_thu');
    
    // Hiển thị/ẩn tiền khách đưa
    phuongThucTT.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'tien_mat') {
                tienKhachDuaGroup.style.display = 'block';
            } else {
                tienKhachDuaGroup.style.display = 'none';
                tienKhachDuaInput.value = '';
                tienTraLaiDiv.style.display = 'none';
            }
            tinhToan();
        });
    });

    // Tính tiền trả lại
    tienKhachDuaInput.addEventListener('input', function() {
        tinhToan();
    });

    // Tính toán khi thay đổi voucher
    voucherSelect.addEventListener('change', function() {
        tinhToan();
    });

    // Tính toán khi thay đổi phụ thu
    phuThuInput.addEventListener('input', function() {
        tinhToan();
    });

    function tinhToan() {
        let tongTien = tongTienOrder;
        let tienGiam = 0;
        let phuThu = parseFloat(phuThuInput.value) || 0;
        
        // Tính tiền giảm từ voucher
        const selectedVoucher = voucherSelect.options[voucherSelect.selectedIndex];
        if (selectedVoucher.value) {
            const loaiGiam = selectedVoucher.dataset.loai;
            const giaTri = parseFloat(selectedVoucher.dataset.giaTri);
            const giaTriToiDa = parseFloat(selectedVoucher.dataset.giaTriToiDa) || null;
            
            if (loaiGiam === 'phan_tram') {
                tienGiam = tongTien * (giaTri / 100);
                if (giaTriToiDa && tienGiam > giaTriToiDa) {
                    tienGiam = giaTriToiDa;
                }
            } else {
                tienGiam = giaTri;
            }
            
            if (tienGiam > tongTien) {
                tienGiam = tongTien;
            }
            
            document.getElementById('tienGiamRow').style.display = 'flex';
            document.getElementById('tienGiam').textContent = '- ' + tienGiam.toLocaleString('vi-VN') + ' đ';
        } else {
            document.getElementById('tienGiamRow').style.display = 'none';
        }
        
        // Hiển thị phụ thu
        if (phuThu > 0) {
            document.getElementById('phuThuRow').style.display = 'flex';
            document.getElementById('phuThu').textContent = '+ ' + phuThu.toLocaleString('vi-VN') + ' đ';
        } else {
            document.getElementById('phuThuRow').style.display = 'none';
        }
        
        // Tính phải thanh toán
        const phaiThanhToan = tongTien - tienGiam + phuThu - tienCoc;
        const phaiThanhToanFinal = phaiThanhToan < 0 ? 0 : phaiThanhToan;
        document.getElementById('phaiThanhToan').textContent = phaiThanhToanFinal.toLocaleString('vi-VN') + ' đ';
        
        // Tính tiền trả lại (nếu thanh toán tiền mặt)
        if (document.getElementById('tien_mat').checked) {
            const tienKhachDua = parseFloat(tienKhachDuaInput.value) || 0;
            if (tienKhachDua > 0) {
                const tienTraLai = tienKhachDua - phaiThanhToanFinal;
                if (tienTraLai > 0) {
                    tienTraLaiDiv.style.display = 'block';
                    tienTraLaiDiv.textContent = 'Tiền trả lại: ' + tienTraLai.toLocaleString('vi-VN') + ' đ';
                } else if (tienTraLai < 0) {
                    tienTraLaiDiv.style.display = 'block';
                    tienTraLaiDiv.className = 'mt-2 text-danger fw-bold';
                    tienTraLaiDiv.textContent = 'Thiếu: ' + Math.abs(tienTraLai).toLocaleString('vi-VN') + ' đ';
                } else {
                    tienTraLaiDiv.style.display = 'none';
                }
            } else {
                tienTraLaiDiv.style.display = 'none';
            }
        }
    }
    
    // Tính toán ban đầu
    tinhToan();
});
</script>

<style>
.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-check {
    cursor: pointer;
    transition: all 0.3s;
}

.form-check:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.form-check-input:checked ~ .form-check-label {
    color: #0d6efd;
}
</style>
@endsection