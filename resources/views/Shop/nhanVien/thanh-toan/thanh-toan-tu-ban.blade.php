@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Thanh toán bàn ' . $ban->so_ban)

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                {{-- Header --}}
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary mb-2">
                        <i class="bi bi-cash-coin me-2"></i>THANH TOÁN BÀN {{ $ban->so_ban }}
                    </h2>
                    <p class="text-muted">Nhà hàng Buffet Ocean</p>
                </div>

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="row g-4">
                    {{-- Cột trái: Thông tin khách và bàn --}}
                    <div class="col-lg-4">
                        {{-- Thông tin khách hàng --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Thông tin khách hàng</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Tên khách:</strong><br>{{ $datBan->ten_khach ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>SĐT:</strong><br>{{ $datBan->sdt_khach ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Email:</strong><br>{{ $datBan->email_khach ?? 'N/A' }}</p>
                                @php
                                // Ưu tiên lấy từ datBan->nguoi_lon và datBan->tre_em
                                $soNguoiLon = $datBan->nguoi_lon ?? 0;
                                $soTreEm = $datBan->tre_em ?? 0;

                                // Nếu không có giá trị trong datBan, tính từ chiTietDatBan
                                if($soNguoiLon == 0 && $soTreEm == 0 && $datBan->chiTietDatBan &&
                                $datBan->chiTietDatBan->count() > 0) {
                                $soNguoiLon = 0;
                                $soTreEm = 0;
                                foreach($datBan->chiTietDatBan as $chiTiet) {
                                if($chiTiet->combo) {
                                $soLuong = $chiTiet->so_luong ?? 1;
                                if($chiTiet->combo->loai_combo == 'nguoi_lon') {
                                $soNguoiLon += $soLuong;
                                } elseif($chiTiet->combo->loai_combo == 'tre_em') {
                                $soTreEm += $soLuong;
                                } else {
                                $soNguoiLon += $soLuong;
                                }
                                }
                                }
                                }
                                $tongSoKhach = $soNguoiLon + $soTreEm;
                                @endphp
                                <p class="mb-1"><strong>Số khách:</strong><br><span
                                        class="badge bg-info">{{ $tongSoKhach }} người</span></p>
                                <p class="mb-0">
                                    <small class="text-muted">
                                        - Người lớn: <strong>{{ $soNguoiLon }}</strong> người<br>
                                        - Trẻ em: <strong>{{ $soTreEm }}</strong> người
                                    </small>
                                </p>
                            </div>
                        </div>

                        {{-- Thông tin bàn --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-table me-2"></i>Thông tin bàn</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Bàn số:</strong><br><span
                                        class="fs-4 fw-bold text-primary">{{ $ban->so_ban }}</span></p>
                                <p class="mb-2"><strong>Khu vực:</strong><br>{{ $ban->khuVuc->ten_khu_vuc ?? 'N/A' }} -
                                    Tầng {{ $ban->khuVuc->tang ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Sức chứa:</strong><br>{{ $ban->so_ghe }} chỗ</p>
                                <p class="mb-0"><strong>Mã đặt
                                        bàn:</strong><br><code>{{ $datBan->ma_dat_ban ?? 'N/A' }}</code></p>
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
                                    <span
                                        class="badge bg-light text-dark fs-6">{{ $gioVao->format('d/m/Y H:i') }}</span>
                                </p>
                                <p class="mb-2">
                                    <strong>Giờ ra:</strong><br>
                                    <span class="badge bg-light text-dark fs-6">{{ $gioRa->format('d/m/Y H:i') }}</span>
                                </p>
                                <p class="mb-0">
                                    <strong>Thời gian phục vụ:</strong><br>
                                    <span class="badge bg-success fs-6">{{ floor($thoiGianPhucVu / 60) }} giờ
                                        {{ $thoiGianPhucVu % 60 }} phút</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Cột phải: Form thanh toán --}}
                    <div class="col-lg-8">
                        {{-- Thông tin combo và món --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-basket3 me-2"></i>Thông tin combo & món đã gọi</h5>
                            </div>
                            <div class="card-body">
                                @if($datBan->chiTietDatBan && $datBan->chiTietDatBan->count() > 0)
                                @php
                                $soTreEm = $datBan->tre_em ?? 0;
                                $soNguoiDaXuLy = 0;
                                @endphp
                                @foreach($datBan->chiTietDatBan as $chiTiet)
                                @if($chiTiet->combo)
                                @php
                                $giaComboGoc = $chiTiet->combo->gia_co_ban;
                                $soLuongCombo = $chiTiet->so_luong ?? 1;
                                $soNguoiDuocGiam = 0;

                                if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) { $soTreEmConLai=$soTreEm -
                                    $soNguoiDaXuLy; $soNguoiDuocGiam=min($soTreEmConLai, $soLuongCombo); }
                                    $soNguoiKhongGiam=$soLuongCombo - $soNguoiDuocGiam; $soNguoiDaXuLy +=$soLuongCombo;
                                    @endphp {{-- Hiển thị combo giảm giá --}} @if($soNguoiDuocGiam> 0)
                                    @php
                                    $giaComboGiam = $giaComboGoc * 0.5;
                                    $thanhTienGiam = $giaComboGiam * $soNguoiDuocGiam;
                                    @endphp
                                    <div class="mb-3 p-3 bg-light rounded border border-warning">
                                        <h6 class="fw-bold text-primary mb-2">
                                            <i
                                                class="bi bi-star-fill me-1 text-warning"></i>{{ $chiTiet->combo->ten_combo }}
                                            <span class="badge bg-success ms-2">Combo chính</span>
                                            <span class="badge bg-info ms-2">Trẻ em (Giảm 50%)</span>
                                        </h6>
                                        <p class="mb-1">
                                            <strong>Giá combo:</strong>
                                            <span
                                                class="text-decoration-line-through text-muted">{{ number_format($giaComboGoc) }}
                                                đ</span>
                                            <span class="text-danger fw-bold"> {{ number_format($giaComboGiam) }}
                                                đ</span>
                                            <small class="text-success">(Giảm 50%)</small> /người
                                        </p>
                                        <p class="mb-1"><strong>Số lượng:</strong> {{ $soNguoiDuocGiam }} người</p>
                                        <p class="mb-0 mt-2"><strong>Thành tiền combo:</strong>
                                            <span class="text-danger fw-bold fs-5">{{ number_format($thanhTienGiam) }}
                                                đ</span>
                                        </p>
                                    </div>
                                    @endif

                                    {{-- Hiển thị combo giá gốc --}}
                                    @if($soNguoiKhongGiam > 0)
                                    @php
                                    $thanhTienGoc = $giaComboGoc * $soNguoiKhongGiam;
                                    @endphp
                                    <div class="mb-3 p-3 bg-light rounded border border-warning">
                                        <h6 class="fw-bold text-primary mb-2">
                                            <i
                                                class="bi bi-star-fill me-1 text-warning"></i>{{ $chiTiet->combo->ten_combo }}
                                            <span class="badge bg-success ms-2">Combo chính</span>
                                        </h6>
                                        <p class="mb-1">
                                            <strong>Giá combo:</strong> {{ number_format($giaComboGoc) }} đ/người
                                        </p>
                                        <p class="mb-1"><strong>Số lượng:</strong> {{ $soNguoiKhongGiam }} người</p>
                                        <p class="mb-0 mt-2"><strong>Thành tiền combo:</strong>
                                            <span class="text-danger fw-bold fs-5">{{ number_format($thanhTienGoc) }}
                                                đ</span>
                                        </p>
                                    </div>
                                    @endif
                                    @endif
                                    @endforeach
                                    @else
                                    <div class="alert alert-warning mb-3">
                                        <i class="bi bi-exclamation-triangle me-2"></i>Chưa chọn combo cho bàn này
                                    </div>
                                    @endif

                                    {{-- Danh sách món đã gọi --}}
                                    @php
                                    $hasMonAn = false;
                                    $monAnList = collect();

                                    foreach($datBan->orderMon as $order) {
                                    foreach($order->chiTietOrders as $ct) {
                                    $hasMonAn = true;
                                    $monAnList->push($ct);
                                    }
                                    }

                                    // Tính lại tổng tiền combo chính xác
                                    $tongTienComboChinh = 0;
                                    $soTreEm = $datBan->tre_em ?? 0;
                                    $soNguoiDaXuLy = 0;

                                    foreach($datBan->chiTietDatBan as $chiTiet) {
                                    if($chiTiet->combo) {
                                    $giaComboGoc = $chiTiet->combo->gia_co_ban ?? 0;
                                    $soLuongCombo = $chiTiet->so_luong ?? 1;
                                    $soNguoiDuocGiam = 0;

                                    if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) { $soTreEmConLai=$soTreEm -
                                        $soNguoiDaXuLy; $soNguoiDuocGiam=min($soTreEmConLai, $soLuongCombo); }
                                        $soNguoiKhongGiam=$soLuongCombo - $soNguoiDuocGiam;
                                        $thanhTienCombo=($giaComboGoc * 0.5 * $soNguoiDuocGiam) + ($giaComboGoc *
                                        $soNguoiKhongGiam); $tongTienComboChinh +=$thanhTienCombo; $soNguoiDaXuLy
                                        +=$soLuongCombo; } } @endphp @if($hasMonAn && $monAnList->isNotEmpty())
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>STT</th>
                                                        <th>Tên món</th>
                                                        <th class="text-center">SL</th>
                                                        <th class="text-center">Trạng thái</th>
                                                        <th class="text-end">Đơn giá</th>
                                                        <th class="text-end">Thành tiền</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                    $stt = 1;
                                                    $tongTienGoiThem = 0;
                                                    $tongSoLuongMon = [];

                                                    // Filter món không chờ bếp
                                                    $monAnListFiltered = $monAnList->filter(function($ct) {
                                                    return $ct->trang_thai != 'cho_bep';
                                                    });

                                                    foreach($monAnListFiltered as $ct) {
                                                    $monAnId = $ct->mon_an_id;
                                                    if(!isset($tongSoLuongMon[$monAnId])) {
                                                    $tongSoLuongMon[$monAnId] = 0;
                                                    }
                                                    $tongSoLuongMon[$monAnId] += $ct->so_luong;
                                                    }

                                                    $monAnGrouped = $monAnListFiltered->groupBy('mon_an_id');
                                                    @endphp

                                                    @foreach($monAnGrouped as $monAnId => $monAnGroup)
                                                    @php
                                                    $ctFirst = $monAnGroup->first();
                                                    $tongSoLuong = $monAnGroup->sum('so_luong');
                                                    $soLuongDaLen = 0;
                                                    $soLuongDangCheBien = 0;
                                                    $soLuongChoCungUng = 0;
                                                    $soLuongHuy = 0;

                                                    foreach($monAnGroup as $ct) {
                                                    if($ct->trang_thai == 'da_len_mon') $soLuongDaLen += $ct->so_luong;
                                                    elseif($ct->trang_thai == 'dang_che_bien') $soLuongDangCheBien +=
                                                    $ct->so_luong;
                                                    elseif($ct->trang_thai == 'cho_cung_ung') $soLuongChoCungUng += $ct->so_luong;
                                                    elseif($ct->trang_thai == 'huy_mon') $soLuongHuy += $ct->so_luong;
                                                    }

                                                    if($soLuongDaLen == 0 && $soLuongDangCheBien == 0 && $soLuongChoCungUng == 0) continue;

                                                    $coTrongCombo = false;
                                                    foreach($datBan->chiTietDatBan as $chiTiet) {
                                                    if($chiTiet->combo) {
                                                    $monTrongComboItem = \App\Models\MonTrongCombo::where('combo_id',
                                                    $chiTiet->combo->id)
                                                    ->where('mon_an_id', $monAnId)
                                                    ->first();
                                                    if($monTrongComboItem) {
                                                    $coTrongCombo = true;
                                                    break;
                                                    }
                                                    }
                                                    }

                                                    $tienMon = 0;
                                                    $donGiaHienThi = 0;
                                                    $donGiaGoc = $ctFirst->monAn->gia ?? 0;

                                                    if($coTrongCombo) {
                                                    $tienMon = 0;
                                                    } else {
                                                    $tienMonDaLen = $donGiaGoc * $soLuongDaLen;
                                                    $tienMonDangCheBien = $donGiaGoc * $soLuongDangCheBien;
                                                    $tienMonChoCungUng = $donGiaGoc * $soLuongChoCungUng;
                                                    $tienMon = $tienMonDaLen + $tienMonDangCheBien + $tienMonChoCungUng;

                                                    $soLuongKhongHuy = $tongSoLuong - $soLuongHuy;
                                                    if($soLuongKhongHuy > 0) {
                                                    $donGiaHienThi = $tienMon / $soLuongKhongHuy;
                                                    }

                                                    $tongTienGoiThem += $tienMon;
                                                    }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $stt++ }}</td>
                                                        <td>
                                                            {{ $ctFirst->monAn->ten_mon ?? 'N/A' }}
                                                            @if($coTrongCombo)
                                                            <span class="badge bg-warning">Món combo</span>
                                                            @else
                                                            <span class="badge bg-info">Gọi thêm</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">{{ $tongSoLuong }}</td>
                                                        <td class="text-center">
                                                            @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuong)
                                                            <span class="badge bg-danger">Đã hủy:
                                                                {{ $soLuongHuy }}</span>
                                                            @else
                                                            <div class="d-flex flex-column align-items-center gap-1">
                                                                @if($soLuongDaLen > 0) <span class="badge bg-success">Đã
                                                                    lên: {{ $soLuongDaLen }}</span> @endif
                                                                @if($soLuongDangCheBien > 0) <span
                                                                    class="badge bg-warning text-dark">Đang nấu:
                                                                    {{ $soLuongDangCheBien }}</span> @endif
                                                                @if($soLuongChoCungUng > 0) <span
                                                                    class="badge bg-info text-white">Chờ cung ứng:
                                                                    {{ $soLuongChoCungUng }}</span> @endif
                                                                @if($soLuongHuy > 0) <span class="badge bg-danger">Đã
                                                                    hủy: {{ $soLuongHuy }}</span> @endif
                                                            </div>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            @if($coTrongCombo)
                                                            <span class="text-success">0 đ</span><br><small
                                                                class="text-muted">(Trong combo)</small>
                                                            @else
                                                            {{ number_format($donGiaGoc) }} đ
                                                            @endif
                                                        </td>
                                                        <td class="text-end fw-bold">
                                                            {{ number_format($tienMon) }} đ
                                                        </td>
                                                    </tr>
                                                    @endforeach

                                                    {{-- Tổng kết --}}
                                                    @php
                                                    $tongTienThucTe = $tongTienComboChinh + $tongTienGoiThem;
                                                    @endphp
                                                    @if($tongTienComboChinh > 0)
                                                    <tr class="table-warning fw-bold">
                                                        <td colspan="5" class="text-end"><i
                                                                class="bi bi-star-fill text-warning me-1"></i>Tổng tiền
                                                            combo chính:</td>
                                                        <td class="text-end text-primary fs-5">
                                                            {{ number_format($tongTienComboChinh) }} đ</td>
                                                    </tr>
                                                    @endif
                                                    @if($tongTienGoiThem > 0)
                                                    <tr class="table-secondary fw-bold">
                                                        <td colspan="5" class="text-end">Tổng tiền món gọi thêm:</td>
                                                        <td class="text-end text-info">
                                                            {{ number_format($tongTienGoiThem) }} đ</td>
                                                    </tr>
                                                    @endif
                                                    <tr class="table-primary fw-bold fs-5">
                                                        <td colspan="5" class="text-end">TỔNG CỘNG:</td>
                                                        <td class="text-end text-danger" id="tongTienTuBang"
                                                            data-tong-tien="{{ $tongTienThucTe }}">
                                                            {{ number_format($tongTienThucTe) }} đ</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        @else
                                        <p class="text-muted text-center">Chưa có món nào được gọi</p>
                                        @endif
                            </div>
                        </div>

                        {{-- Form thanh toán --}}
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Thanh toán</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('nhanVien.thanh-toan.luu-ban', $ban->id) }}" method="POST"
                                    id="thanhToanForm">
                                    @csrf

                                    {{-- Phương thức thanh toán --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold" style="color: #333 !important;"><i
                                                class="bi bi-credit-card me-2"></i>Phương thức thanh toán <span
                                                class="text-danger">*</span></label>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="form-check border rounded p-3 h-100 payment-method-card">
                                                    <input class="form-check-input" type="radio" name="phuong_thuc_tt"
                                                        id="tien_mat" value="tien_mat" checked>
                                                    <label class="form-check-label w-100" for="tien_mat">
                                                        <i class="bi bi-cash-stack text-success fs-3 d-block mb-2"></i>
                                                        <strong>Tiền mặt</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check border rounded p-3 h-100 payment-method-card">
                                                    <input class="form-check-input" type="radio" name="phuong_thuc_tt"
                                                        id="chuyen_khoan" value="chuyen_khoan">
                                                    <label class="form-check-label w-100" for="chuyen_khoan">
                                                        <i class="bi bi-bank text-primary fs-3 d-block mb-2"></i>
                                                        <strong>Chuyển khoản</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check border rounded p-3 h-100 payment-method-card">
                                                    <input class="form-check-input" type="radio" name="phuong_thuc_tt"
                                                        id="the_ATM" value="the_ATM">
                                                    <label class="form-check-label w-100" for="the_ATM">
                                                        <i
                                                            class="bi bi-credit-card-2-front text-info fs-3 d-block mb-2"></i>
                                                        <strong>Thẻ ATM</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check border rounded p-3 h-100 payment-method-card">
                                                    <input class="form-check-input" type="radio" name="phuong_thuc_tt"
                                                        id="vnpay" value="vnpay">
                                                    <label class="form-check-label w-100" for="vnpay">
                                                        <i class="bi bi-credit-card text-warning fs-3 d-block mb-2"></i>
                                                        <strong>VNPay</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            {{-- ĐÃ THÊM LẠI PAYOS --}}
                                            <div class="col-md-4">
                                                <div class="form-check border rounded p-3 h-100 payment-method-card">
                                                    <input class="form-check-input" type="radio" name="phuong_thuc_tt"
                                                        id="payos" value="payos">
                                                    <label class="form-check-label w-100" for="payos">
                                                        <i class="bi bi-qr-code text-primary fs-3 d-block mb-2"></i>
                                                        <strong>PayOS (QR)</strong>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tiền khách đưa (chỉ hiện khi chọn tiền mặt) --}}
                                    <div class="mb-4" id="tienKhachDuaGroup">
                                        <label for="tien_khach_dua" class="form-label fw-bold"
                                            style="color: #333 !important;">Tiền khách đưa</label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" class="form-control" id="tien_khach_dua"
                                                name="tien_khach_dua" min="0" step="1000" placeholder="Nhập số tiền">
                                            <span class="input-group-text">đ</span>
                                        </div>
                                        <div id="tienTraLai" class="mt-2 fw-bold" style="display: none;"></div>
                                    </div>

                                    {{-- Voucher --}}
                                    <div class="mb-4">
                                        <label for="voucher_id" class="form-label fw-bold"
                                            style="color: #333 !important;"><i
                                                class="bi bi-ticket-perforated me-2"></i>Voucher (nếu có)</label>
                                        <select class="form-select form-select-lg" id="voucher_id" name="voucher_id">
                                            <option value="">-- Không sử dụng voucher --</option>
                                            @foreach($vouchers as $voucher)
                                            <option value="{{ $voucher->id }}" data-loai="{{ $voucher->loai_giam }}"
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

                                    {{-- Phụ thu thủ công (nếu cần thêm) --}}
                                    <div class="mb-4">
                                        <label for="phu_thu" class="form-label fw-bold" style="color: #333 !important;">
                                            <i class="bi bi-plus-circle me-2"></i>Phụ thu thêm (nếu có)
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" class="form-control" id="phu_thu" name="phu_thu"
                                                value="0" min="0" step="1000">
                                            <span class="input-group-text">đ</span>
                                        </div>
                                    </div>

                                    {{-- Tóm tắt thanh toán --}}
                                    <div class="mb-4 p-4 bg-white rounded-3 border shadow-sm">
                                        <h5 class="mb-3 text-dark fw-bold"><i class="bi bi-calculator me-2"></i>Tóm tắt
                                            thanh toán</h5>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">Tổng tiền (Combo + Món):</span>
                                            <strong id="tongTien"
                                                class="text-primary fs-5">{{ number_format($tongTienThucTe ?? $tongTienOrder) }}
                                                đ</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2" id="tienGiamRow"
                                            style="display: none;">
                                            <span class="text-dark">(-) Tiền giảm (Voucher):</span>
                                            <strong class="text-success" id="tienGiam">- 0 đ</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2" id="tongTienSauVoucherRow"
                                            style="display: none;">
                                            <span class="text-dark fw-bold">Tổng tiền sau voucher:</span>
                                            <strong id="tongTienSauVoucher" class="text-warning fs-5">0 đ</strong>
                                        </div>
                                        @if($tienCoc > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">(-) Tiền cọc:</span>
                                            <strong class="text-success" id="tienCoc">- {{ number_format($tienCoc) }}
                                                đ</strong>
                                        </div>
                                        @endif
                                        <div class="d-flex justify-content-between mb-2" id="phuThuRow"
                                            style="display: none;">
                                            <span class="text-dark">(+) Phụ thu:</span>
                                            <strong class="text-danger" id="phuThu">+
                                                {{ number_format($phuThuTuDong ?? 0) }} đ</strong>
                                        </div>
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between fs-4 fw-bold">
                                            <span class="text-dark">Phải thanh toán:</span>
                                            <span id="phaiThanhToan"
                                                class="text-danger">{{ number_format($tongTienOrder - $tienCoc) }}
                                                đ</span>
                                        </div>
                                    </div>

                                    {{-- Nút xác nhận --}}
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="{{ route('nhanVien.ban-an.index') }}"
                                            class="btn btn-secondary btn-lg px-5">
                                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                                        </a>
                                        <button type="button" class="btn btn-warning btn-lg px-5" id="btnThanhToanSau"
                                            onclick="xacNhanThanhToanSau()">
                                            <i class="bi bi-clock-history me-2"></i>Thanh toán sau
                                        </button>
                                        <button type="submit" class="btn btn-success btn-lg px-5">
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
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // 1. Lấy tổng tiền chính xác từ giao diện
    const tongTienTuBangElement = document.getElementById('tongTienTuBang');
    let tongTienOrder = 0;
    
    if (tongTienTuBangElement) {
        const tongTienFromData = tongTienTuBangElement.getAttribute('data-tong-tien');
        if (tongTienFromData) {
            tongTienOrder = parseFloat(tongTienFromData);
        } else {
            const tongTienText = tongTienTuBangElement.textContent.replace(/[^\d]/g, '');
            if (tongTienText && tongTienText.length > 0) {
                tongTienOrder = parseInt(tongTienText);
            }
        }
    }
    
    // Fallback
    if (!tongTienOrder || tongTienOrder === 0 || isNaN(tongTienOrder)) {
        @php
            $tongTienThucTeValue = isset($tongTienThucTe) ? $tongTienThucTe : ($tongTienOrder ?? 0);
        @endphp
        tongTienOrder = {{ $tongTienThucTeValue }};
    }
    
    // Cập nhật hiển thị ban đầu
    const tongTienElement = document.getElementById('tongTien');
    if (tongTienElement) {
        tongTienElement.textContent = tongTienOrder.toLocaleString('vi-VN') + ' đ';
    }
    
    // 2. KHAI BÁO BIẾN TOÀN CỤC
    const tienCoc = {{ $tienCoc ?? 0 }}; 
    const phuThuTuDong = {{ $phuThuTuDong ?? 0 }};
    
    // DOM Elements
    const phuongThucTT = document.querySelectorAll('input[name="phuong_thuc_tt"]');
    const tienKhachDuaGroup = document.getElementById('tienKhachDuaGroup');
    const tienKhachDuaInput = document.getElementById('tien_khach_dua');
    const tienTraLaiDiv = document.getElementById('tienTraLai');
    const voucherSelect = document.getElementById('voucher_id');
    const phuThuInput = document.getElementById('phu_thu');
    
    // 3. Xử lý hiển thị nhập tiền mặt
    const tienMatRadio = document.getElementById('tien_mat');
    if (tienMatRadio && tienMatRadio.checked) {
        tienKhachDuaGroup.style.display = 'block';
    } else {
        tienKhachDuaGroup.style.display = 'none';
    }
    
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

    // Events tính toán
    tienKhachDuaInput.addEventListener('input', tinhToan);
    voucherSelect.addEventListener('change', tinhToan);
    phuThuInput.addEventListener('input', tinhToan);

    // 4. Hàm tính toán trung tâm
    function tinhToan() {
        let tongTien = tongTienOrder;
        
        // Cập nhật lại tổng tiền từ DOM nếu có thay đổi
        const tongTienTuBangElement = document.getElementById('tongTienTuBang');
        if (tongTienTuBangElement) {
            const tongTienFromData = tongTienTuBangElement.getAttribute('data-tong-tien');
            if (tongTienFromData) tongTien = parseFloat(tongTienFromData);
        }
        
        let tienGiam = 0;
        let phuThu = parseFloat(phuThuInput.value) || 0;
        
        // Tính Voucher
        const selectedVoucher = voucherSelect.options[voucherSelect.selectedIndex];
        if (selectedVoucher && selectedVoucher.value) {
            const loaiGiam = selectedVoucher.getAttribute('data-loai');
            const giaTri = parseFloat(selectedVoucher.getAttribute('data-gia-tri')) || 0;
            const giaTriToiDa = parseFloat(selectedVoucher.getAttribute('data-gia-tri-toi-da')) || null;
            
            if (loaiGiam === 'phan_tram') {
                tienGiam = tongTien * (giaTri / 100);
                if (giaTriToiDa && tienGiam > giaTriToiDa) {
                    tienGiam = giaTriToiDa;
                }
            } else {
                tienGiam = giaTri;
            }
            
            if (tienGiam > tongTien) tienGiam = tongTien;
            
            if (tienGiam > 0) {
                document.getElementById('tienGiamRow').style.display = 'flex';
                document.getElementById('tienGiam').textContent = '- ' + tienGiam.toLocaleString('vi-VN') + ' đ';
            } else {
                document.getElementById('tienGiamRow').style.display = 'none';
            }
        } else {
            document.getElementById('tienGiamRow').style.display = 'none';
        }
        
        // Tổng tiền sau voucher
        const tongTienSauVoucher = tongTien - tienGiam;
        if (selectedVoucher && selectedVoucher.value) {
            document.getElementById('tongTienSauVoucherRow').style.display = 'flex';
            document.getElementById('tongTienSauVoucher').textContent = tongTienSauVoucher.toLocaleString('vi-VN') + ' đ';
        } else {
            document.getElementById('tongTienSauVoucherRow').style.display = 'none';
        }
        
        // Tổng phụ thu
        const phuThuTotal = phuThuTuDong + phuThu;
        if (phuThuTotal > 0) {
            document.getElementById('phuThuRow').style.display = 'flex';
            document.getElementById('phuThu').textContent = '+ ' + phuThuTotal.toLocaleString('vi-VN') + ' đ';
        } else {
            document.getElementById('phuThuRow').style.display = 'none';
        }
        
        // Phải thanh toán
        let phaiThanhToan = tongTienSauVoucher - tienCoc + phuThuTotal;
        if (phaiThanhToan < 0) phaiThanhToan = 0;
        
        document.getElementById('phaiThanhToan').textContent = phaiThanhToan.toLocaleString('vi-VN') + ' đ';
        
        // Tiền thừa trả khách
        if (document.getElementById('tien_mat').checked) {
            const tienKhachDua = parseFloat(tienKhachDuaInput.value) || 0;
            if (tienKhachDua > 0) {
                const tienTraLai = tienKhachDua - phaiThanhToan;
                tienTraLaiDiv.style.display = 'block';
                if (tienTraLai >= 0) {
                    tienTraLaiDiv.className = 'mt-2 text-success fw-bold';
                    tienTraLaiDiv.textContent = 'Tiền trả lại: ' + tienTraLai.toLocaleString('vi-VN') + ' đ';
                } else {
                    tienTraLaiDiv.className = 'mt-2 text-danger fw-bold';
                    tienTraLaiDiv.textContent = 'Thiếu: ' + Math.abs(tienTraLai).toLocaleString('vi-VN') + ' đ';
                }
            } else {
                tienTraLaiDiv.style.display = 'none';
            }
        }
    }
    
    tinhToan();

    // 5. Xử lý Submit Form (Cập nhật cho cả VNPay và PayOS)
    document.getElementById('thanhToanForm').addEventListener('submit', function(e){
        const vnpayRadio = document.getElementById('vnpay');
        const payosRadio = document.getElementById('payos'); // Đã thêm ID payos

        // Kiểm tra nếu chọn thanh toán online (VNPay hoặc PayOS)
        if(vnpayRadio.checked || (payosRadio && payosRadio.checked)){
            e.preventDefault();
            
            // Lấy lại các giá trị hiện tại
            let tongTien = tongTienOrder;
            const tongTienTuBangElement = document.getElementById('tongTienTuBang');
            if (tongTienTuBangElement) {
                const tongTienFromData = tongTienTuBangElement.getAttribute('data-tong-tien');
                if (tongTienFromData) tongTien = parseFloat(tongTienFromData);
            }
            
            const phaiThanhToanElement = document.getElementById('phaiThanhToan');
            let amountToPay = tongTien;
            
            if (phaiThanhToanElement) {
                const textVal = phaiThanhToanElement.textContent.replace(/[^\d]/g, '');
                if(textVal) amountToPay = parseInt(textVal);
            } else {
                amountToPay = tongTien - tienCoc; 
            }
            
            let tongTienInput = document.getElementById('tong_tien_hidden');
            if (!tongTienInput) {
                tongTienInput = document.createElement('input');
                tongTienInput.type = 'hidden';
                tongTienInput.name = 'tong_tien';
                tongTienInput.id = 'tong_tien_hidden';
                this.appendChild(tongTienInput);
            }
            
            tongTienInput.value = amountToPay > 0 ? amountToPay : 0;
            
            // Điều hướng dựa trên phương thức thanh toán
            if (vnpayRadio.checked) {
                this.action = '{{ route("nhanVien.thanh-toan.vnpay.payment", $ban->id) }}';
            } else if (payosRadio && payosRadio.checked) {
                // Route PayOS đã có trong Controller (createPayOSPayment)
                this.action = '{{ route("nhanVien.thanh-toan.payos.create", $ban->id) }}';
            }

            this.submit();
        }
    });

});

function xacNhanThanhToanSau() {
    if (confirm('Bạn có chắc muốn tạo hóa đơn với trạng thái "Chưa thanh toán"? Hóa đơn sẽ được lưu và có thể thanh toán sau.')) {
        const form = document.getElementById('thanhToanForm');
        const formData = new FormData(form);
        
        const newForm = document.createElement('form');
        newForm.method = 'POST';
        newForm.action = '{{ route("nhanVien.thanh-toan.luu-ban-sau", $ban->id) }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        newForm.appendChild(csrfInput);
        
        const voucherId = formData.get('voucher_id');
        if (voucherId) {
            const voucherInput = document.createElement('input');
            voucherInput.type = 'hidden';
            voucherInput.name = 'voucher_id';
            voucherInput.value = voucherId;
            newForm.appendChild(voucherInput);
        }
        
        document.body.appendChild(newForm);
        newForm.submit();
    }
}
</script>

<style>
    .payment-method-card {
        cursor: pointer;
        transition: all 0.3s;
    }

    .payment-method-card:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-check-input:checked~.form-check-label {
        color: #0d6efd;
    }

    .card {
        border-radius: 12px;
        overflow: hidden;
    }

    .card-header {
        border-bottom: none;
    }
</style>
@endsection