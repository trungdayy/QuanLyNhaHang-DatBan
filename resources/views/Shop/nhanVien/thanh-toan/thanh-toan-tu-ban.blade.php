@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Thanh toán bàn ' . $ban->so_ban)

@section('content')

{{-- 
    =========================================================
    1. KHỐI XỬ LÝ LOGIC PHP (TÍNH TOÁN DỮ LIỆU ĐẦU VÀO)
    =========================================================
--}}
@php
// --- Khởi tạo biến ---
$tienCoc = $datBan->tien_coc ?? 0;
$soNguoiLon = $datBan->nguoi_lon ?? 0;
$soTreEm = $datBan->tre_em ?? 0;

// Đếm khách từ chi tiết nếu data bàn chưa có
if ($soNguoiLon == 0 && $soTreEm == 0 && $datBan->chiTietDatBan) {
foreach ($datBan->chiTietDatBan as $ct) {
$sl = $ct->so_luong ?? 1;
if ($ct->combo && $ct->combo->loai_combo == 'tre_em') $soTreEm += $sl;
else $soNguoiLon += $sl;
}
}
$tongSoKhach = $soNguoiLon + $soTreEm;

// --- Tính tiền Combo (Ưu tiên giảm giá trẻ em) ---
$tongTienCombo = 0;
$danhSachComboDisplay = [];
if ($datBan->chiTietDatBan) {
$soTreEmConLai = $soTreEm;
foreach ($datBan->chiTietDatBan as $ct) {
if ($ct->combo) {
$giaGoc = $ct->combo->gia_co_ban ?? 0;
$sl = $ct->so_luong ?? 1;

$slGiam = 0;
if ($soTreEmConLai > 0) {
$slGiam = min($soTreEmConLai, $sl);
$soTreEmConLai -= $slGiam;
}
$slNguyen = $sl - $slGiam;
$thanhTien = ($slGiam * $giaGoc * 0.5) + ($slNguyen * $giaGoc);
$tongTienCombo += $thanhTien;

$danhSachComboDisplay[] = (object)[
'ten' => $ct->combo->ten_combo,
'gia_goc' => $giaGoc,
'sl_giam' => $slGiam,
'sl_nguyen' => $slNguyen,
'thanh_tien' => $thanhTien
];
}
}
}

// --- Tính tiền Món ăn (Theo trạng thái) ---
$tongTienMon = 0;
$danhSachMonDisplay = [];
$idsMonCombo = [];

// Lấy ID món trong combo để miễn phí
if ($datBan->chiTietDatBan) {
foreach ($datBan->chiTietDatBan as $ct) {
if ($ct->combo && $ct->combo->monAn) {
foreach($ct->combo->monAn as $m) $idsMonCombo[] = $m->id;
}
}
}

if ($datBan->orderMon) {
$allCt = collect();
foreach ($datBan->orderMon as $ord) {
foreach ($ord->chiTietOrders as $ct) $allCt->push($ct);
}

foreach ($allCt->groupBy('mon_an_id') as $monId => $group) {
$first = $group->first();
$tenMon = $first->monAn->ten_mon ?? 'N/A';
$donGia = $first->monAn->gia ?? 0;
$isCombo = in_array($monId, $idsMonCombo);

$slDaLen = $group->where('trang_thai', 'da_len_mon')->sum('so_luong');
$slDangNau = $group->where('trang_thai', 'dang_che_bien')->sum('so_luong');
$slHuy = $group->where('trang_thai', 'huy_mon')->sum('so_luong');
$tongSl = $group->sum('so_luong');
$slChoBep = $tongSl - $slDaLen - $slDangNau - $slHuy;

$thanhTienItem = 0;
if (!$isCombo) {
// Chỉ tính tiền món Đã lên (100%) và Đang nấu (30%)
$thanhTienItem = ($slDaLen * $donGia) + ($slDangNau * $donGia * 0.3);
}
$tongTienMon += $thanhTienItem;

$danhSachMonDisplay[] = (object)[
'ten_mon' => $tenMon,
'is_combo' => $isCombo,
'tong_sl' => $tongSl,
'sl_hien_thi' => $tongSl - $slHuy,
'sl_da_len' => $slDaLen,
'sl_dang_nau' => $slDangNau,
'sl_huy' => $slHuy,
'sl_cho_bep' => $slChoBep,
'don_gia' => $donGia,
'thanh_tien' => $thanhTienItem
];
}
}

// --- Phụ thu thời gian ---
$thoiGianPhucVu = $thoiGianPhucVu ?? 0;
$phuThuThoiGian = 0;
$thoiGianVuot = 0;
$soLan10Phut = 0;
$tgQuyDinh = 0; $tgMienPhi = 0;

if ($datBan->comboBuffet && $datBan->comboBuffet->thoi_luong_phut) {
$tgQuyDinh = $datBan->comboBuffet->thoi_luong_phut;
$tgMienPhi = $tgQuyDinh + 10;
$thoiGianVuot = max(0, $thoiGianPhucVu - $tgMienPhi);
if ($thoiGianVuot > 0) {
$soLan10Phut = ceil($thoiGianVuot / 10);
$phuThuThoiGian = $soLan10Phut * 30000;
}
}
$phuThuTuDong = $phuThuThoiGian;

// TỔNG TIỀN CHUẨN
$tongTienThucTe = $tongTienCombo + $tongTienMon;
@endphp

{{-- 
    =========================================================
    2. GIAO DIỆN HTML
    =========================================================
--}}

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
                    {{-- CỘT TRÁI: THÔNG TIN KHÁCH & BÀN --}}
                    <div class="col-lg-4">
                        {{-- Khách hàng --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Thông tin khách hàng</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Tên khách:</strong> {{ $datBan->ten_khach ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>SĐT:</strong> {{ $datBan->sdt_khach ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Email:</strong> {{ $datBan->email_khach ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Số khách:</strong> <span
                                        class="badge bg-info">{{ $tongSoKhach }} người</span></p>
                                <small class="text-muted">- Người lớn: <strong>{{ $soNguoiLon }}</strong> | Trẻ em:
                                    <strong>{{ $soTreEm }}</strong></small>
                            </div>
                        </div>

                        {{-- Bàn --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-table me-2"></i>Thông tin bàn</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Bàn số:</strong> <span
                                        class="fs-4 fw-bold text-primary">{{ $ban->so_ban }}</span></p>
                                <p class="mb-2"><strong>Khu vực:</strong> {{ $ban->khuVuc->ten_khu_vuc ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>Mã đặt:</strong> <code>{{ $datBan->ma_dat_ban ?? 'N/A' }}</code>
                                </p>
                            </div>
                        </div>

                        {{-- Thời gian --}}
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-clock me-2"></i>Thời gian phục vụ</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Vào:</strong> <span
                                        class="badge bg-light text-dark">{{ $gioVao->format('H:i d/m') }}</span></p>
                                <p class="mb-2"><strong>Ra:</strong> <span
                                        class="badge bg-light text-dark">{{ $gioRa->format('H:i d/m') }}</span></p>
                                <p class="mb-2"><strong>Thời lượng:</strong> <span
                                        class="badge bg-success">{{ floor($thoiGianPhucVu / 60) }}h
                                        {{ $thoiGianPhucVu % 60 }}p</span></p>

                                @if($tgQuyDinh > 0)
                                <p class="mb-2"><strong>Quy định:</strong> <span
                                        class="badge bg-info">{{ floor($tgQuyDinh / 60) }}h
                                        {{ $tgQuyDinh % 60 }}p</span></p>
                                @if($thoiGianVuot > 0)
                                <div class="alert alert-warning py-1 px-2 mb-0">
                                    <small class="text-danger fw-bold">Quá giờ: {{ floor($thoiGianVuot / 60) }}h
                                        {{ $thoiGianVuot % 60 }}p</small><br>
                                    <small class="text-muted">(Phạt: {{ number_format($phuThuThoiGian) }}đ)</small>
                                </div>
                                @else
                                <span class="badge bg-success">Trong giờ cho phép</span>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- CỘT PHẢI: CHI TIẾT & THANH TOÁN --}}
                    <div class="col-lg-8">
                        {{-- Card Chi tiết Combo & Món --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-basket3 me-2"></i>Thông tin combo & món đã gọi</h5>
                            </div>
                            <div class="card-body">
                                {{-- Combo --}}
                                @if(count($danhSachComboDisplay) > 0)
                                @foreach($danhSachComboDisplay as $combo)
                                <div class="mb-3 p-3 bg-light rounded border border-warning">
                                    <h6 class="fw-bold text-primary mb-2"><i
                                            class="bi bi-star-fill me-1 text-warning"></i>{{ $combo->ten }}</h6>
                                    @if($combo->sl_giam > 0) <p class="mb-1"><strong>Trẻ em (50%):</strong>
                                        {{ $combo->sl_giam }} x {{ number_format($combo->gia_goc * 0.5) }} đ</p> @endif
                                    @if($combo->sl_nguyen > 0) <p class="mb-1"><strong>Người lớn:</strong>
                                        {{ $combo->sl_nguyen }} x {{ number_format($combo->gia_goc) }} đ</p> @endif
                                    <p class="mb-0 mt-2 text-end"><strong>Thành tiền:</strong> <span
                                            class="text-danger fw-bold fs-5">{{ number_format($combo->thanh_tien) }}
                                            đ</span></p>
                                </div>
                                @endforeach
                                @else
                                <div class="alert alert-warning mb-3"><i class="bi bi-exclamation-triangle"></i> Chưa
                                    chọn combo</div>
                                @endif

                                {{-- Món --}}
                                @if(count($danhSachMonDisplay) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tên món</th>
                                                <th class="text-center">SL</th>
                                                <th class="text-center">Trạng thái</th>
                                                <th class="text-end">Đơn giá</th>
                                                <th class="text-end">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($danhSachMonDisplay as $mon)
                                            <tr>
                                                <td>{{ $mon->ten_mon }} @if($mon->is_combo) <span
                                                        class="badge bg-warning text-dark">Combo</span> @else <span
                                                        class="badge bg-info">Gọi thêm</span> @endif</td>
                                                <td class="text-center fw-bold">{{ $mon->sl_hien_thi }}</td>
                                                <td class="text-center small">
                                                    @if($mon->sl_huy > 0) <div class="text-danger">Hủy:
                                                        {{ $mon->sl_huy }}</div> @endif
                                                    @if($mon->sl_da_len > 0) <div class="text-success">Đã lên:
                                                        {{ $mon->sl_da_len }}</div> @endif
                                                    @if($mon->sl_dang_nau > 0) <div class="text-warning text-dark">Đang
                                                        nấu: {{ $mon->sl_dang_nau }}</div> @endif
                                                    @if($mon->sl_cho_bep > 0) <div class="text-muted">Chờ bếp:
                                                        {{ $mon->sl_cho_bep }}</div> @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($mon->is_combo) <span class="text-success">0 đ</span>
                                                    @else {{ number_format($mon->don_gia) }} đ @endif
                                                </td>
                                                <td class="text-end fw-bold">{{ number_format($mon->thanh_tien) }} đ
                                                </td>
                                            </tr>
                                            @endforeach
                                            {{-- Tổng cộng dùng cho JS lấy --}}
                                            <tr class="table-primary fw-bold fs-5">
                                                <td colspan="4" class="text-end">TỔNG CỘNG:</td>
                                                <td class="text-end text-danger" id="tongTienTuBang"
                                                    data-tong-tien="{{ $tongTienThucTe }}">
                                                    {{ number_format($tongTienThucTe) }} đ</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <p class="text-center text-muted">Chưa gọi món</p>
                                @endif
                            </div>
                        </div>

                        {{-- Card Form Thanh Toán --}}
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Thanh toán</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('nhanVien.thanh-toan.luu-ban', $ban->id) }}" method="POST"
                                    id="thanhToanForm">
                                    @csrf

                                    {{-- !!! INPUT ẨN CHỨA TỔNG TIỀN !!! --}}
                                    <input type="hidden" name="tong_tien" id="hidden_tong_tien_payos" value="0">

                                    {{-- Phương thức thanh toán --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Phương thức thanh toán <span
                                                class="text-danger">*</span></label>
                                        <div class="row g-3">
                                            @foreach(['tien_mat' => ['Tiền mặt', 'cash-stack', 'success'],
                                            'chuyen_khoan' => ['Chuyển khoản', 'bank', 'primary'], 'the_ATM' => ['Thẻ
                                            ATM', 'credit-card-2-front', 'info'], 'payos' => ['PayOS (QR)', 'qr-code',
                                            'warning']] as $val => $info)
                                            <div class="col-md-3 col-6">
                                                <div class="form-check border rounded p-3 h-100 payment-method-card">
                                                    <input class="form-check-input" type="radio" name="phuong_thuc_tt"
                                                        id="{{ $val }}" value="{{ $val }}"
                                                        {{ $val == 'tien_mat' ? 'checked' : '' }}>
                                                    <label class="form-check-label w-100" for="{{ $val }}">
                                                        <i
                                                            class="bi bi-{{ $info[1] }} text-{{ $info[2] }} fs-3 d-block mb-2"></i>
                                                        <strong>{{ $info[0] }}</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Tiền khách đưa --}}
                                    <div class="mb-4" id="tienKhachDuaGroup">
                                        <label class="form-label fw-bold">Tiền khách đưa</label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" class="form-control" id="tien_khach_dua"
                                                name="tien_khach_dua" min="0" placeholder="Nhập số tiền...">
                                            <span class="input-group-text">đ</span>
                                        </div>
                                        <div id="tienTraLai" class="mt-2 fw-bold" style="display: none;"></div>
                                    </div>

                                    {{-- Voucher & Phụ thu --}}
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold"><i
                                                    class="bi bi-ticket-perforated me-2"></i>Voucher</label>
                                            <select class="form-select form-select-lg" id="voucher_id"
                                                name="voucher_id">
                                                <option value="" data-giam="0">-- Không sử dụng --</option>
                                                @foreach($vouchers as $voucher)
                                                {{-- [QUAN TRỌNG] Làm sạch dữ liệu Voucher: Loại bỏ dấu phẩy --}}
                                                @php
                                                $giaTriClean = intval(preg_replace('/[^0-9]/', '', $voucher->gia_tri));
                                                $maxClean = intval(preg_replace('/[^0-9]/', '',
                                                $voucher->gia_tri_toi_da));
                                                @endphp
                                                <option value="{{ $voucher->id }}" data-loai="{{ $voucher->loai_giam }}"
                                                    data-gia-tri="{{ $giaTriClean }}" data-max="{{ $maxClean }}">
                                                    {{ $voucher->ma_voucher }} - Giảm
                                                    {{ $voucher->loai_giam == 'phan_tram' ? $voucher->gia_tri.'%' : number_format($giaTriClean).'đ' }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label fw-bold"><i class="bi bi-plus-circle me-2"></i>Phụ
                                                thu thêm</label>
                                            <div class="input-group input-group-lg">
                                                <input type="number" class="form-control" id="phu_thu" name="phu_thu"
                                                    value="0" min="0" step="1000">
                                                <span class="input-group-text">đ</span>
                                            </div>
                                            <small class="text-muted">Phụ thu tự động: <strong
                                                    id="phuThuTuDongText">{{ number_format($phuThuTuDong) }}
                                                    đ</strong></small>
                                        </div>
                                    </div>

                                    {{-- Tóm tắt thanh toán --}}
                                    <div class="mb-4 p-4 bg-white rounded-3 border shadow-sm">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">Tổng tiền hàng:</span>
                                            <strong class="text-primary fs-5">{{ number_format($tongTienThucTe) }}
                                                đ</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2" id="tienGiamRow"
                                            style="display: none;">
                                            <span class="text-dark">(-) Voucher giảm:</span>
                                            <strong class="text-success" id="tienGiam">- 0 đ</strong>
                                        </div>
                                        @if($tienCoc > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">(-) Tiền cọc:</span>
                                            <strong class="text-success">- {{ number_format($tienCoc) }} đ</strong>
                                        </div>
                                        @endif
                                        <div class="d-flex justify-content-between mb-2" id="phuThuRow"
                                            style="display: none;">
                                            <span class="text-dark">(+) Phụ thu:</span>
                                            <strong class="text-danger" id="phuThuText">+ 0 đ</strong>
                                        </div>
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between fs-4 fw-bold align-items-center">
                                            <span class="text-dark">PHẢI THANH TOÁN:</span>
                                            <span id="phaiThanhToan"
                                                class="text-danger">{{ number_format(max(0, $tongTienThucTe - $tienCoc + $phuThuTuDong)) }}
                                                đ</span>
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="{{ route('nhanVien.ban-an.index') }}"
                                            class="btn btn-secondary btn-lg px-4">Quay lại</a>
                                        <button type="submit" class="btn btn-success btn-lg px-5 fw-bold">
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

{{-- 
    =========================================================
    3. JAVASCRIPT: LOGIC TÍNH TOÁN & PAYOS
    =========================================================
--}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // 1. Lấy dữ liệu gốc an toàn
    const tongTienElement = document.getElementById('tongTienTuBang');
    // ParseFloat để lấy số thực từ data attribute
    const tongTienGoc = tongTienElement ? (parseFloat(tongTienElement.getAttribute('data-tong-tien')) || 0) : 0;
    const tienCoc = {{ $tienCoc }};
    const phuThuTuDong = {{ $phuThuTuDong }};
    
    // 2. DOM Elements
    const elTienKhach = document.getElementById('tien_khach_dua');
    const elTienTraLai = document.getElementById('tienTraLai');
    const elVoucher = document.getElementById('voucher_id');
    const elPhuThuInput = document.getElementById('phu_thu');
    const elPhaiThanhToan = document.getElementById('phaiThanhToan');
    const elTienGiamRow = document.getElementById('tienGiamRow');
    const elTienGiamText = document.getElementById('tienGiam');
    const elPhuThuRow = document.getElementById('phuThuRow');
    const elPhuThuText = document.getElementById('phuThuText');
    const radioPhuongThuc = document.getElementsByName('phuong_thuc_tt');
    const groupTienKhach = document.getElementById('tienKhachDuaGroup');
    const elHiddenTien = document.getElementById('hidden_tong_tien_payos');

    // Hàm format tiền tệ đẹp (VD: 70,000 đ)
    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }

    // 3. Hàm tính toán chính
    function tinhToan() {
        // --- A. Tính Voucher ---
        let giamGia = 0;
        const opt = elVoucher.options[elVoucher.selectedIndex];
        
        if (opt && opt.value) {
            const loai = opt.getAttribute('data-loai');
            const val = parseFloat(opt.getAttribute('data-gia-tri')) || 0;
            const max = parseFloat(opt.getAttribute('data-max')) || 0;

            if (loai == 'phan_tram') {
                giamGia = tongTienGoc * (val / 100);
                if (max > 0 && giamGia > max) giamGia = max;
            } else {
                giamGia = val;
            }
            
            // Không giảm quá tổng tiền
            if(giamGia > tongTienGoc) giamGia = tongTienGoc;

            elTienGiamRow.style.display = 'flex';
            elTienGiamText.textContent = '- ' + formatMoney(giamGia);
        } else {
            elTienGiamRow.style.display = 'none';
        }

        // --- B. Tính Phụ thu (Auto + Thủ công) ---
        const phuThuThem = parseFloat(elPhuThuInput.value) || 0;
        const tongPhuThu = phuThuTuDong + phuThuThem;

        if (tongPhuThu > 0) {
            elPhuThuRow.style.display = 'flex';
            elPhuThuText.textContent = '+ ' + formatMoney(tongPhuThu);
        } else {
            elPhuThuRow.style.display = 'none';
        }

        // --- C. Tổng kết ---
        let canTra = tongTienGoc - tienCoc - giamGia + tongPhuThu;
        if (canTra < 0) canTra = 0;
        
        // Cập nhật text hiển thị phải thanh toán
        elPhaiThanhToan.textContent = formatMoney(canTra);
        
        // Cập nhật giá trị vào Input ẩn để gửi sang PayOS (Quan trọng!)
        if(elHiddenTien) {
            elHiddenTien.value = canTra;
        }

        // --- D. Tiền thừa trả khách ---
        if (document.getElementById('tien_mat').checked) {
            const khachDua = parseFloat(elTienKhach.value) || 0;
            if (khachDua > 0) {
                const diff = khachDua - canTra;
                elTienTraLai.style.display = 'block';
                if (diff >= 0) {
                    elTienTraLai.className = 'mt-2 text-success fw-bold';
                    elTienTraLai.innerHTML = '<i class="bi bi-check-circle"></i> Trả lại: ' + formatMoney(diff);
                } else {
                    elTienTraLai.className = 'mt-2 text-danger fw-bold';
                    elTienTraLai.innerHTML = '<i class="bi bi-exclamation-circle"></i> Thiếu: ' + formatMoney(Math.abs(diff));
                }
            } else {
                elTienTraLai.style.display = 'none';
            }
        }
    }

    // 4. Lắng nghe sự kiện thay đổi
    elVoucher.addEventListener('change', tinhToan);
    elPhuThuInput.addEventListener('input', tinhToan);
    elTienKhach.addEventListener('input', tinhToan);

    // Ẩn hiện ô nhập tiền mặt
    Array.from(radioPhuongThuc).forEach(r => {
        r.addEventListener('change', function() {
            if (this.value == 'tien_mat') {
                groupTienKhach.style.display = 'block';
            } else {
                groupTienKhach.style.display = 'none';
                elTienKhach.value = '';
                elTienTraLai.style.display = 'none';
            }
            tinhToan();
        });
    });

    // 5. Xử lý Submit Form (Logic PayOS)
    const form = document.getElementById('thanhToanForm');
    if(form){
        form.addEventListener('submit', function(e){
            // Kiểm tra số tiền
            const amountCheck = parseFloat(elHiddenTien ? elHiddenTien.value : 0);
            
            if(document.getElementById('payos').checked){
                if (amountCheck <= 0) {
                    e.preventDefault();
                    alert('Số tiền thanh toán phải lớn hơn 0đ mới tạo được mã QR!');
                    return;
                }
                e.preventDefault();
                // Đổi action sang route PayOS
                this.action = '{{ route("nhanVien.thanh-toan.payos.payment", $ban->id) }}';
                this.submit();
            }
        });
    }

    // Chạy tính toán lần đầu khi load trang
    tinhToan();
});
</script>

<style>
    .payment-method-card {
        cursor: pointer;
        transition: 0.3s;
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