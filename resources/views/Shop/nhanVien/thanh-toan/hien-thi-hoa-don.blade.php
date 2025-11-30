@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Hóa đơn #' . $hoaDon->ma_hoa_don)

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                {{-- Header hóa đơn --}}
                <div class="text-center mb-4">
                    <h1 class="display-4 fw-bold text-primary mb-2">
                        <i class="bi bi-receipt-cutoff me-2"></i>HÓA ĐƠN THANH TOÁN
                    </h1>
                    <p class="text-muted fs-5">Mã hóa đơn: <strong>{{ $hoaDon->ma_hoa_don }}</strong></p>
                    <p class="text-muted">Nhà hàng Buffet Ocean</p>
                </div>

                <div class="card shadow-lg border-0 rounded-4 mb-4">
                    <div class="card-header bg-gradient text-white py-4 rounded-top-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0 text-white">
                                <i class="bi bi-receipt-cutoff me-2"></i>Chi tiết hóa đơn
                            </h3>
                            <div>
                                <a href="{{ route('nhanVien.thanh-toan.in-hoa-don', $hoaDon->id) }}" target="_blank" class="btn btn-light btn-lg">
                                    <i class="bi bi-printer me-1"></i>In hóa đơn
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        {{-- Thông tin khách hàng và bàn --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3"><i class="bi bi-person me-2"></i>Thông tin khách hàng</h5>
                                <p class="mb-2"><strong>Tên khách:</strong> {{ $hoaDon->datBan->ten_khach ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>SĐT:</strong> {{ $hoaDon->datBan->sdt_khach ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Email:</strong> {{ $hoaDon->datBan->email_khach ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>Số khách:</strong> {{ $hoaDon->datBan->so_khach ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3"><i class="bi bi-table me-2"></i>Thông tin bàn</h5>
                                <p class="mb-2"><strong>Bàn số:</strong> {{ $hoaDon->datBan->banAn->so_ban ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Khu vực:</strong> {{ $hoaDon->datBan->banAn->khuVuc->ten_khu_vuc ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Mã đặt bàn:</strong> <code>{{ $hoaDon->datBan->ma_dat_ban ?? 'N/A' }}</code></p>
                                <p class="mb-2"><strong>Ngày tạo:</strong> {{ $hoaDon->created_at->format('d/m/Y H:i:s') }}</p>
                                <p class="mb-0"><strong>Phương thức TT:</strong> 
                                    <span class="badge bg-primary">
                                        @if($hoaDon->phuong_thuc_tt == 'tien_mat')
                                            Tiền mặt
                                        @elseif($hoaDon->phuong_thuc_tt == 'chuyen_khoan')
                                            Chuyển khoản
                                        @elseif($hoaDon->phuong_thuc_tt == 'the_ATM')
                                            Thẻ ATM
                                        @else
                                            {{ $hoaDon->phuong_thuc_tt }}
                                        @endif
                                    </span>
                                </p>
                            </div>
                        </div>

                        {{-- Thời gian phục vụ --}}
                        @if($gioVao)
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-clock me-2"></i>Thời gian phục vụ</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <p class="mb-2">
                                            <strong>Giờ vào:</strong><br>
                                            <span class="badge bg-light text-dark fs-6">{{ $gioVao->format('d/m/Y H:i') }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-2">
                                            <strong>Giờ ra:</strong><br>
                                            <span class="badge bg-light text-dark fs-6">{{ $gioRa->format('d/m/Y H:i') }}</span>
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-2">
                                            <strong>Thời gian phục vụ:</strong><br>
                                            <span class="badge bg-success fs-6">{{ floor($thoiGianPhucVu / 60) }} giờ {{ $thoiGianPhucVu % 60 }} phút</span>
                                        </p>
                                    </div>
                                    @if($hoaDon->datBan->comboBuffet && $hoaDon->datBan->comboBuffet->thoi_luong_phut)
                                        @php
                                            $thoiGianQuyDinh = $hoaDon->datBan->comboBuffet->thoi_luong_phut;
                                            $thoiGianMienPhi = $thoiGianQuyDinh + 10; // Thời gian quy định + 10 phút miễn phí
                                            $thoiGianVuot = max(0, $thoiGianPhucVu - $thoiGianMienPhi);
                                        @endphp
                                        <div class="col-md-3">
                                            <p class="mb-2">
                                                <strong>Thời gian quy định:</strong><br>
                                                <span class="badge bg-info fs-6">{{ floor($thoiGianQuyDinh / 60) }} giờ {{ $thoiGianQuyDinh % 60 }} phút</span>
                                            </p>
                                        </div>
                                    @endif
                                </div>
                                @if($hoaDon->datBan->comboBuffet && $hoaDon->datBan->comboBuffet->thoi_luong_phut)
                                    @php
                                        $thoiGianQuyDinh = $hoaDon->datBan->comboBuffet->thoi_luong_phut;
                                        $thoiGianMienPhi = $thoiGianQuyDinh + 10;
                                        $thoiGianVuot = max(0, $thoiGianPhucVu - $thoiGianMienPhi);
                                    @endphp
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            @if($thoiGianVuot > 0)
                                                <p class="mb-0">
                                                    <strong>Thời gian vượt quá:</strong><br>
                                                    <span class="badge bg-danger fs-6">{{ floor($thoiGianVuot / 60) }} giờ {{ $thoiGianVuot % 60 }} phút</span>
                                                    <small class="text-muted d-block mt-1">(Sau {{ floor($thoiGianMienPhi / 60) }} giờ {{ $thoiGianMienPhi % 60 }} phút miễn phí)</small>
                                                </p>
                                            @else
                                                <p class="mb-0">
                                                    <strong>Thời gian vượt quá:</strong><br>
                                                    <span class="badge bg-success fs-6">0 phút</span>
                                                    <small class="text-muted d-block mt-1">(Trong thời gian cho phép)</small>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <hr>
                        </div>

                        <hr>

                        {{-- Chi tiết món ăn --}}
                        <h5 class="fw-bold mb-3"><i class="bi bi-list-ul me-2"></i>Chi tiết món ăn</h5>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên món</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $stt = 1; @endphp
                                    @foreach($monGoiThemList as $monId => $monData)
                                        <tr>
                                            <td>{{ $stt++ }}</td>
                                            <td>
                                                {{ $monData['monAn']->ten_mon ?? 'N/A' }}
                                                @if($monData['soLuongVuot'] > 0)
                                                    <span class="badge bg-danger">Vượt giới hạn</span>
                                                @else
                                                    <span class="badge bg-info">Gọi thêm</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $monData['soLuongVuot'] }}</td>
                                            <td class="text-end">
                                                @if($monData['phuPhi'] > 0)
                                                    <div class="small">
                                                        <div>{{ number_format($monData['giaGoc']) }} đ × {{ $monData['soLuongVuot'] }}</div>
                                                        <div class="text-danger">+ {{ number_format($monData['phuPhi']) }} đ × {{ $monData['soLuongVuot'] }} (phụ phí)</div>
                                                        <div class="fw-bold">= {{ number_format($monData['thanhTien']) }} đ</div>
                                                    </div>
                                                @else
                                                    {{ number_format($monData['giaGoc']) }} đ
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format($monData['thanhTien']) }} đ</td>
                                        </tr>
                                    @endforeach

                                    {{-- Tổng combo chính --}}
                                    @if($tienComboChinh > 0)
                                        <tr class="table-warning fw-bold">
                                            <td colspan="4" class="text-end">Tổng tiền combo chính:</td>
                                            <td class="text-end text-primary fs-5">{{ number_format($tienComboChinh) }} đ</td>
                                        </tr>
                                    @endif

                                    {{-- Tổng món gọi thêm --}}
                                    @if($tongTienMonGoiThem > 0)
                                        <tr class="table-secondary fw-bold">
                                            <td colspan="4" class="text-end">Tổng tiền món gọi thêm:</td>
                                            <td class="text-end text-info">{{ number_format($tongTienMonGoiThem) }} đ</td>
                                        </tr>
                                    @endif

                                    {{-- Tổng cộng --}}
                                    <tr class="table-primary fw-bold fs-5">
                                        <td colspan="4" class="text-end">TỔNG CỘNG:</td>
                                        <td class="text-end text-danger">{{ number_format($tongTienThucTe) }} đ</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>


                        <hr>

                        {{-- Tóm tắt thanh toán --}}
                        <div class="row">
                            <div class="col-md-8 offset-md-4">
                                <div class="p-4 bg-white rounded-3 border shadow-sm">
                                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-calculator me-2"></i>Tóm tắt thanh toán</h5>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-dark">Tổng tiền (Combo + Món):</span>
                                        <strong class="text-primary fs-5">{{ number_format($tongTienThucTe) }} đ</strong>
                                    </div>
                                    @if($hoaDon->voucher)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span class="text-dark">(-) Tiền giảm (Voucher {{ $hoaDon->voucher->ma_voucher }}):</span>
                                        <strong>- {{ number_format($hoaDon->tien_giam) }} đ</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-dark fw-bold">Tổng tiền sau voucher:</span>
                                        <strong class="text-warning fs-5">{{ number_format($tongTienThucTe - ($hoaDon->tien_giam ?? 0)) }} đ</strong>
                                    </div>
                                    @endif
                                    @if($hoaDon->datBan->tien_coc > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span class="text-dark">(-) Tiền cọc:</span>
                                        <strong>- {{ number_format($hoaDon->datBan->tien_coc) }} đ</strong>
                                    </div>
                                    @endif
                                    @if($hoaDon->phu_thu > 0)
                                    <div class="d-flex justify-content-between mb-2 text-danger">
                                        <span class="text-dark">(+) Phụ thu:</span>
                                        <strong>+ {{ number_format($hoaDon->phu_thu) }} đ</strong>
                                    </div>
                                    @endif
                                    @php
                                        // Tính lại phải thanh toán
                                        $tongTienThucTeTinhLai = $tongTienThucTe; // dùng biến có sẵn
                                        $tongTienSauVoucherTinhLai = $tongTienThucTeTinhLai - ($hoaDon->tien_giam ?? 0);
                                        $phaiThanhToanTinhLai = $tongTienSauVoucherTinhLai - ($hoaDon->datBan->tien_coc ?? 0) + ($hoaDon->phu_thu ?? 0);
                                        if($phaiThanhToanTinhLai < 0) $phaiThanhToanTinhLai = 0;
                                    @endphp
                                    <hr class="my-3">
                                    <div class="d-flex justify-content-between fs-4 fw-bold">
                                        <span class="text-dark">Phải thanh toán:</span>
                                        <span class="text-danger">{{ number_format($phaiThanhToanTinhLai) }} đ</span>
                                    </div>
                                    <div class="d-flex justify-content-between fs-5 fw-bold text-success mt-2">
                                        <span class="text-dark">Đã thanh toán:</span>
                                        <span>{{ number_format($phaiThanhToanTinhLai) }} đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- Nút hành động --}}
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="{{ route('nhanVien.ban-an.index') }}" class="btn btn-secondary btn-lg px-5">
                                <i class="bi bi-arrow-left me-2"></i>Quay lại quản lý bàn
                            </a>
                            <a href="{{ route('nhanVien.thanh-toan.in-hoa-don', $hoaDon->id) }}" target="_blank" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-printer me-2"></i>In hóa đơn
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
