@extends('layouts.Shop.layout-nhanvien')
@section('title', 'Hóa đơn #' . $hoaDon->ma_hoa_don)

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="text-center mb-4">
                    <h1 class="display-4 fw-bold text-primary mb-2">HÓA ĐƠN THANH TOÁN</h1>
                    <p class="text-muted fs-5">Mã hóa đơn: <strong>{{ $hoaDon->ma_hoa_don }}</strong></p>
                    <p class="text-muted">Nhà hàng Buffet Ocean</p>
                </div>

                @if(isset($chiTiet) && $chiTiet)
                    {{-- Hiển thị từ chi tiết hóa đơn --}}
                    <div class="row g-4">
                        {{-- Cột trái: Thông tin khách và bàn --}}
                        <div class="col-lg-4">
                            {{-- Thông tin khách hàng --}}
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Thông tin khách hàng</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>Tên khách:</strong><br>{{ $chiTiet->ten_khach ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong>SĐT:</strong><br>{{ $chiTiet->sdt_khach ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong>Email:</strong><br>{{ $chiTiet->email_khach ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Số khách:</strong><br><span class="badge bg-info">{{ $chiTiet->so_khach ?? 'N/A' }} người</span></p>
                                </div>
                            </div>

                            {{-- Thông tin bàn --}}
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Thông tin bàn</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2"><strong>Bàn số:</strong><br><span class="fs-4 fw-bold text-primary">{{ $chiTiet->ban_so ?? 'N/A' }}</span></p>
                                    <p class="mb-2"><strong>Khu vực:</strong><br>{{ $chiTiet->khu_vuc ?? 'N/A' }}@if($chiTiet->tang) - Tầng {{ $chiTiet->tang }}@endif</p>
                                    <p class="mb-2"><strong>Sức chứa:</strong><br>{{ $chiTiet->so_ghe ?? 'N/A' }} chỗ</p>
                                    <p class="mb-0"><strong>Mã đặt bàn:</strong><br><code>{{ $chiTiet->ma_dat_ban ?? 'N/A' }}</code></p>
                                </div>
                            </div>

                            {{-- Thời gian phục vụ --}}
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="bi bi-clock me-2"></i>Thời gian phục vụ</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-2">
                                        <strong>Giờ vào:</strong><br>
                                        <span class="badge bg-light text-dark fs-6">{{ $chiTiet->gio_vao ? $chiTiet->gio_vao->format('d/m/Y H:i') : 'N/A' }}</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Giờ ra:</strong><br>
                                        <span class="badge bg-light text-dark fs-6">{{ $chiTiet->gio_ra ? $chiTiet->gio_ra->format('d/m/Y H:i') : 'N/A' }}</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Thời gian phục vụ:</strong><br>
                                        <span class="badge bg-success fs-6">{{ floor($chiTiet->thoi_gian_phuc_vu_phut / 60) }} giờ {{ $chiTiet->thoi_gian_phuc_vu_phut % 60 }} phút</span>
                                    </p>
                                    @if($chiTiet->thoi_gian_quy_dinh_phut)
                                    <p class="mb-2">
                                        <strong>Thời gian quy định:</strong><br>
                                        <span class="badge bg-info fs-6">{{ floor($chiTiet->thoi_gian_quy_dinh_phut / 60) }} giờ {{ $chiTiet->thoi_gian_quy_dinh_phut % 60 }} phút</span>
                                    </p>
                                    @endif
                                    @if($chiTiet->thoi_gian_vuot_phut > 0)
                                    <p class="mb-0">
                                        <strong>Thời gian vượt quá:</strong><br>
                                        <span class="badge bg-danger fs-6">{{ floor($chiTiet->thoi_gian_vuot_phut / 60) }} giờ {{ $chiTiet->thoi_gian_vuot_phut % 60 }} phút</span>
                                        <small class="text-muted d-block mt-1">({{ $chiTiet->so_lan_10_phut }} × 10 phút = {{ number_format($chiTiet->phu_thu_thoi_gian) }} đ)</small>
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
                        </div>

                        {{-- Cột phải: Thông tin combo & món --}}
                        <div class="col-lg-8">
                            {{-- Thông tin combo --}}
                            @if($chiTiet->ten_combo)
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="bi bi-basket3 me-2"></i>Thông tin combo & món đã gọi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 p-3 bg-light rounded border border-warning">
                                        <h6 class="fw-bold text-primary mb-2">
                                            <i class="bi bi-star-fill me-1 text-warning"></i>{{ $chiTiet->ten_combo }}
                                            <span class="badge bg-success ms-2">Combo chính</span>
                                        </h6>
                                        <p class="mb-1"><strong>Giá combo:</strong> {{ number_format($chiTiet->gia_combo_per_person) }} đ/người</p>
                                        <p class="mb-1"><strong>Số khách:</strong> {{ $chiTiet->so_khach }} người</p>
                                        <p class="mb-0 mt-2"><strong>Thành tiền combo:</strong> 
                                            <span class="text-danger fw-bold fs-5">{{ number_format($chiTiet->tong_tien_combo) }} đ</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Danh sách món đã gọi --}}
                            @if($chiTiet->danh_sach_mon && count($chiTiet->danh_sach_mon) > 0)
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh sách món đã gọi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Tên món</th>
                                                    <th class="text-center">SL</th>
                                                    <th class="text-end">Đơn giá</th>
                                                    <th class="text-end">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($chiTiet->danh_sach_mon as $mon)
                                                <tr>
                                                    <td>{{ $mon['stt'] }}</td>
                                                    <td>
                                                        {{ $mon['ten_mon'] }}
                                                        @if($mon['la_mon_combo'])
                                                            <span class="badge bg-warning">Món combo</span>
                                                            @if($mon['vuot_gioi_han'])
                                                                <span class="badge bg-danger">Vượt giới hạn</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-info">Gọi thêm</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $mon['so_luong'] }}
                                                        @if($mon['gioi_han'] !== null)
                                                            <br><small class="text-muted">(Giới hạn: {{ $mon['gioi_han'] }})</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($mon['don_gia'] > 0)
                                                            {{ number_format($mon['don_gia']) }} đ
                                                            @if($mon['phu_phi'] > 0)
                                                                <br><small class="text-danger">+ Phụ phí: {{ number_format($mon['phu_phi']) }} đ</small>
                                                            @endif
                                                        @else
                                                            <span class="text-success">0 đ</span>
                                                            <br><small class="text-muted">(Đã bao gồm trong combo)</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        @if($mon['thanh_tien'] > 0)
                                                            {{ number_format($mon['thanh_tien']) }} đ
                                                        @else
                                                            <span class="text-success">0 đ</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                                
                                                {{-- Tổng kết --}}
                                                @php
                                                    // Tính tổng tiền món gọi thêm từ danh sách món
                                                    $tongTienMonGoiThem = 0;
                                                    foreach($chiTiet->danh_sach_mon as $mon) {
                                                        $tongTienMonGoiThem += $mon['thanh_tien'];
                                                    }
                                                    // Tổng cộng = combo chính + món gọi thêm
                                                    $tongCong = ($chiTiet->tong_tien_combo ?? 0) + $tongTienMonGoiThem;
                                                @endphp
                                                @if($chiTiet->tong_tien_combo > 0)
                                                <tr class="table-warning fw-bold">
                                                    <td colspan="4" class="text-end">
                                                        <i class="bi bi-star-fill text-warning me-1"></i>Tổng tiền combo chính:
                                                    </td>
                                                    <td class="text-end text-primary fs-5">{{ number_format($chiTiet->tong_tien_combo) }} đ</td>
                                                </tr>
                                                @endif
                                                @if($tongTienMonGoiThem > 0)
                                                <tr class="table-secondary fw-bold">
                                                    <td colspan="4" class="text-end">Tổng tiền món gọi thêm:</td>
                                                    <td class="text-end text-info">{{ number_format($tongTienMonGoiThem) }} đ</td>
                                                </tr>
                                                @endif
                                                <tr class="table-primary fw-bold fs-5">
                                                    <td colspan="4" class="text-end">TỔNG CỘNG:</td>
                                                    <td class="text-end text-danger">{{ number_format($tongCong) }} đ</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Tóm tắt thanh toán --}}
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-danger text-white">
                                    <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Tóm tắt thanh toán</h5>
                                </div>
                                <div class="card-body">
                                    <div class="p-4 bg-white rounded-3 border shadow-sm">
                                        @php
                                            // Tính lại tổng tiền từ danh sách món
                                            $tongTienMonGoiThem = 0;
                                            foreach($chiTiet->danh_sach_mon as $mon) {
                                                $tongTienMonGoiThem += $mon['thanh_tien'];
                                            }
                                            $tongTienComboMon = ($chiTiet->tong_tien_combo ?? 0) + $tongTienMonGoiThem;
                                            
                                            // Tính lại phải thanh toán: Tổng tiền - Voucher - Tiền cọc + Phụ thu
                                            $tongTienSauVoucher = $tongTienComboMon - ($chiTiet->tien_giam_voucher ?? 0);
                                            $phaiThanhToan = $tongTienSauVoucher - ($chiTiet->tien_coc ?? 0) + ($chiTiet->tong_phu_thu ?? 0);
                                            if ($phaiThanhToan < 0) $phaiThanhToan = 0;
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">Tổng tiền (Combo + Món):</span>
                                            <strong class="text-primary fs-5">{{ number_format($tongTienComboMon) }} đ</strong>
                                        </div>
                                        @if($chiTiet->tien_giam_voucher > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">(-) Tiền giảm (Voucher):</span>
                                            <strong class="text-success">- {{ number_format($chiTiet->tien_giam_voucher) }} đ</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark fw-bold">Tổng tiền sau voucher:</span>
                                            <strong class="text-warning fs-5">{{ number_format($tongTienSauVoucher) }} đ</strong>
                                        </div>
                                        @endif
                                        @if($chiTiet->tien_coc > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">(-) Tiền cọc:</span>
                                            <strong class="text-success">- {{ number_format($chiTiet->tien_coc) }} đ</strong>
                                        </div>
                                        @endif
                                        @if($chiTiet->tong_phu_thu > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">(+) Phụ thu:</span>
                                            <strong class="text-danger">+ {{ number_format($chiTiet->tong_phu_thu) }} đ</strong>
                                            @if($chiTiet->phu_thu_tu_dong > 0 || $chiTiet->phu_thu_thoi_gian > 0)
                                            <div class="text-end">
                                                <small class="text-muted d-block">
                                                    - Thời gian vượt quá: {{ number_format($chiTiet->phu_thu_thoi_gian) }} đ
                                                </small>
                                                @if($chiTiet->phu_thu_tu_dong > $chiTiet->phu_thu_thoi_gian)
                                                <small class="text-muted d-block">- Món gọi quá giới hạn: {{ number_format($chiTiet->phu_thu_tu_dong - $chiTiet->phu_thu_thoi_gian) }} đ</small>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between fs-4 fw-bold">
                                            <span class="text-dark">Phải thanh toán:</span>
                                            <span class="text-danger">{{ number_format($phaiThanhToan) }} đ</span>
                                        </div>
                                        @if($chiTiet->tien_khach_dua > 0)
                                        @php
                                            $tienTraLai = max(0, $chiTiet->tien_khach_dua - $phaiThanhToan);
                                        @endphp
                                        <div class="d-flex justify-content-between mt-2">
                                            <span class="text-dark">Tiền khách đưa:</span>
                                            <strong>{{ number_format($chiTiet->tien_khach_dua) }} đ</strong>
                                        </div>
                                        @if($tienTraLai > 0)
                                        <div class="d-flex justify-content-between mt-2">
                                            <span class="text-success">Tiền trả lại:</span>
                                            <strong class="text-success">{{ number_format($tienTraLai) }} đ</strong>
                                        </div>
                                        @elseif($chiTiet->tien_khach_dua < $phaiThanhToan)
                                        <div class="d-flex justify-content-between mt-2">
                                            <span class="text-danger">Thiếu:</span>
                                            <strong class="text-danger">{{ number_format($phaiThanhToan - $chiTiet->tien_khach_dua) }} đ</strong>
                                        </div>
                                        @endif
                                        @endif
                                        <div class="mt-3 pt-3 border-top">
                                            <p class="mb-0"><strong>Phương thức thanh toán:</strong> 
                                                @if($chiTiet->phuong_thuc_tt == 'tien_mat')
                                                    <span class="badge bg-success">Tiền mặt</span>
                                                @elseif($chiTiet->phuong_thuc_tt == 'chuyen_khoan')
                                                    <span class="badge bg-primary">Chuyển khoản</span>
                                                @elseif($chiTiet->phuong_thuc_tt == 'the_ATM')
                                                    <span class="badge bg-info">Thẻ ATM</span>
                                                @elseif($chiTiet->phuong_thuc_tt == 'vnpay')
                                                    <span class="badge bg-warning">VNPay</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $chiTiet->phuong_thuc_tt }}</span>
                                                @endif
                                            </p>
                                            @if($chiTiet->ma_voucher)
                                            <p class="mb-0 mt-2"><strong>Voucher:</strong> <span class="badge bg-info">{{ $chiTiet->ma_voucher }}</span></p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Fallback: Hiển thị từ dữ liệu cũ --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Thông tin khách hàng</h5>
                            <p>Tên: {{ $hoaDon->datBan->ten_khach ?? 'N/A' }}</p>
                            <p>SĐT: {{ $hoaDon->datBan->sdt_khach ?? 'N/A' }}</p>
                            <p>Email: {{ $hoaDon->datBan->email_khach ?? 'N/A' }}</p>
                            <p>Số khách: {{ $hoaDon->datBan->so_khach ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Thông tin bàn</h5>
                            <p>Bàn số: {{ $hoaDon->datBan->banAn->so_ban ?? 'N/A' }}</p>
                            <p>Khu vực: {{ $hoaDon->datBan->banAn->khuVuc->ten_khu_vuc ?? 'N/A' }}</p>
                            <p>Ngày tạo: {{ $hoaDon->created_at->format('d/m/Y H:i:s') }}</p>
                            <p>Phương thức TT: {{ $hoaDon->phuong_thuc_tt }}</p>
                        </div>
                    </div>
                @endif

                {{-- Nút in hóa đơn --}}
                <div class="d-flex justify-content-center gap-3 mt-4">
                    <a href="{{ route('nhanVien.ban-an.index') }}" class="btn btn-secondary btn-lg">Quay lại</a>
                    <a href="{{ route('nhanVien.thanh-toan.in-hoa-don', $hoaDon->id) }}" target="_blank" class="btn btn-primary btn-lg">In hóa đơn</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection