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

                {{-- TRƯỜNG HỢP 1: ĐÃ CÓ CHI TIẾT HÓA ĐƠN (LƯU TRONG DB) --}}
                @if(isset($chiTiet) && $chiTiet)
                @php
                $tongCong = $chiTiet->tong_tien_combo_mon ?? $hoaDon->tong_tien ?? 0;
                @endphp
                <div class="row g-4">
                    {{-- Cột trái: Thông tin khách và bàn --}}
                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Thông tin khách hàng</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Tên khách:</strong><br>{{ $chiTiet->ten_khach ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>SĐT:</strong><br>{{ $chiTiet->sdt_khach ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Email:</strong><br>{{ $chiTiet->email_khach ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Số khách:</strong><br><span
                                        class="badge bg-info">{{ $chiTiet->so_khach ?? 'N/A' }} người</span></p>
                                <p class="mb-0">
                                    <small class="text-muted">
                                        - Người lớn:
                                        <strong>{{ $chiTiet->nguoi_lon ?? $hoaDon->datBan->nguoi_lon ?? 0 }}</strong>
                                        người<br>
                                        - Trẻ em:
                                        <strong>{{ $chiTiet->tre_em ?? $hoaDon->datBan->tre_em ?? 0 }}</strong> người
                                    </small>
                                </p>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-table me-2"></i>Thông tin bàn</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Bàn số:</strong><br><span
                                        class="fs-4 fw-bold text-primary">{{ $chiTiet->ban_so ?? 'N/A' }}</span></p>
                                <p class="mb-2"><strong>Khu vực:</strong><br>{{ $chiTiet->khu_vuc ?? 'N/A' }}
                                    @if($chiTiet->tang) - Tầng {{ $chiTiet->tang }}@endif
                                </p>
                                <p class="mb-2"><strong>Sức chứa:</strong><br>{{ $chiTiet->so_ghe ?? 'N/A' }} chỗ</p>
                                <p class="mb-0"><strong>Mã đặt
                                        bàn:</strong><br><code>{{ $chiTiet->ma_dat_ban ?? 'N/A' }}</code></p>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="bi bi-clock me-2"></i>Thời gian phục vụ</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-2">
                                    <strong>Giờ vào:</strong><br>
                                    <span
                                        class="badge bg-light text-dark fs-6">{{ $chiTiet->gio_vao ? \Carbon\Carbon::parse($chiTiet->gio_vao)->format('d/m/Y H:i') : 'N/A' }}</span>
                                </p>
                                <p class="mb-2">
                                    <strong>Giờ ra:</strong><br>
                                    <span
                                        class="badge bg-light text-dark fs-6">{{ $chiTiet->gio_ra ? \Carbon\Carbon::parse($chiTiet->gio_ra)->format('d/m/Y H:i') : 'N/A' }}</span>
                                </p>
                                <p class="mb-0">
                                    <strong>Thời gian phục vụ:</strong><br>
                                    <span class="badge bg-success fs-6">
                                        {{ floor($chiTiet->thoi_gian_phuc_vu_phut / 60) }} giờ
                                        {{ $chiTiet->thoi_gian_phuc_vu_phut % 60 }} phút
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Cột phải: Chi tiết thanh toán --}}
                    <div class="col-lg-8">
                        {{-- Danh sách món/combo --}}
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-basket3 me-2"></i>Chi tiết dịch vụ</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nội dung</th>
                                                <th class="text-center">SL</th>
                                                <th class="text-end">Đơn giá</th>
                                                <th class="text-end">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Hiển thị Combo --}}
                                            @if($chiTiet->ten_combo)
                                            <tr>
                                                <td>
                                                    <strong>{{ $chiTiet->ten_combo }}</strong>
                                                    <br><small class="text-muted">Combo Buffet</small>
                                                </td>
                                                <td class="text-center">{{ $chiTiet->so_khach }}</td>
                                                <td class="text-end">{{ number_format($chiTiet->gia_combo_per_person) }}
                                                    đ</td>
                                                <td class="text-end fw-bold">
                                                    {{ number_format($chiTiet->tong_tien_combo) }} đ</td>
                                            </tr>
                                            @endif

                                            {{-- Hiển thị Món gọi thêm --}}
                                            @php
                                            $danhSachMon = is_string($chiTiet->danh_sach_mon) ?
                                            json_decode($chiTiet->danh_sach_mon, true) : $chiTiet->danh_sach_mon;
                                            @endphp
                                            @if(is_array($danhSachMon) && count($danhSachMon) > 0)
                                            @foreach($danhSachMon as $mon)
                                            <tr>
                                                <td>
                                                    {{ $mon['ten_mon'] }}
                                                    @if(isset($mon['la_mon_combo']) && $mon['la_mon_combo'])
                                                    <span class="badge bg-warning text-dark">Trong combo</span>
                                                    @else
                                                    <span class="badge bg-secondary">Gọi thêm</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $mon['so_luong'] }}</td>
                                                <td class="text-end">{{ number_format($mon['don_gia']) }} đ</td>
                                                <td class="text-end">{{ number_format($mon['thanh_tien']) }} đ</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Tóm tắt thanh toán --}}
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="bi bi-calculator me-2"></i>Tổng kết</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Phương thức thanh toán:</strong>
                                            @if($chiTiet->phuong_thuc_tt == 'tien_mat') Tiền mặt
                                            @elseif($chiTiet->phuong_thuc_tt == 'chuyen_khoan') Chuyển khoản
                                            @elseif($chiTiet->phuong_thuc_tt == 'the_ATM') Thẻ ATM
                                            @elseif($chiTiet->phuong_thuc_tt == 'vnpay') VNPay
                                            @elseif($chiTiet->phuong_thuc_tt == 'payos') PayOS (QR)
                                            @else Chưa thanh toán @endif
                                        </p>
                                        <p><strong>Trạng thái:</strong>
                                            @if($hoaDon->trang_thai == 'da_thanh_toan' || ($hoaDon->da_thanh_toan > 0 &&
                                            $hoaDon->trang_thai != 'chua_thanh_toan'))
                                            <span class="badge bg-success">Đã thanh toán</span>
                                            @else
                                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <p>Tổng tiền hàng: <strong>{{ number_format($tongCong) }} đ</strong></p>
                                        @if($chiTiet->tien_giam_voucher > 0)
                                        <p class="text-success">Voucher giảm:
                                            -{{ number_format($chiTiet->tien_giam_voucher) }} đ</p>
                                        @endif
                                        @if($chiTiet->tien_coc > 0)
                                        <p class="text-primary">Đã cọc: -{{ number_format($chiTiet->tien_coc) }} đ</p>
                                        @endif
                                        @if($chiTiet->tong_phu_thu > 0)
                                        <p class="text-danger">Phụ thu: +{{ number_format($chiTiet->tong_phu_thu) }} đ
                                        </p>
                                        @endif
                                        <h4 class="text-danger mt-3 border-top pt-2">THỰC THU:
                                            {{ number_format($chiTiet->phai_thanh_toan) }} đ</h4>

                                        @if($chiTiet->tien_khach_dua > 0)
                                        <p class="mt-2 text-muted">Khách đưa:
                                            {{ number_format($chiTiet->tien_khach_dua) }} đ</p>
                                        <p class="text-muted">Trả lại: {{ number_format($chiTiet->tien_tra_lai) }} đ</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TRƯỜNG HỢP 2: FALLBACK (HIỂN THỊ TỪ DỮ LIỆU GỐC NẾU CHƯA CÓ CHI TIẾT) --}}
                @else
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="fw-bold mb-3">Thông tin khách hàng</h5>
                        <p>Tên: {{ $hoaDon->datBan->ten_khach ?? 'N/A' }}</p>
                        <p>SĐT: {{ $hoaDon->datBan->sdt_khach ?? 'N/A' }}</p>
                        <p>Số khách: {{ $hoaDon->datBan->so_khach ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5 class="fw-bold mb-3">Thông tin hóa đơn</h5>
                        <p>Bàn: {{ $hoaDon->datBan->banAn->so_ban ?? 'N/A' }}</p>
                        <p>Ngày tạo: {{ $hoaDon->created_at->format('d/m/Y H:i') }}</p>
                        <p>Trạng thái:
                            @if($hoaDon->trang_thai == 'da_thanh_toan' || $hoaDon->da_thanh_toan > 0)
                            <span class="badge bg-success">Đã thanh toán</span>
                            @else
                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Bảng chi tiết tính toán lại --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Chi tiết thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Khoản mục</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Loop Combo Fix Lỗi Cú Pháp --}}
                                    @php
                                    $soTreEm = $hoaDon->datBan->tre_em ?? 0;
                                    $soNguoiDaXuLy = 0;
                                    @endphp

                                    @if($hoaDon->datBan->chiTietDatBan)
                                    @foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo)
                                    @if($chiTietCombo->combo)
                                    @php
                                    $giaComboGoc = $chiTietCombo->combo->gia_co_ban;
                                    $soLuongCombo = $chiTietCombo->so_luong ?? 1;
                                    $soNguoiDuocGiam = 0;

                                    // Logic tính giảm giá
                                    if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) { $soTreEmConLai=$soTreEm -
                                        $soNguoiDaXuLy; $soNguoiDuocGiam=min($soTreEmConLai, $soLuongCombo); }
                                        $soNguoiKhongGiam=$soLuongCombo - $soNguoiDuocGiam; $soNguoiDaXuLy
                                        +=$soLuongCombo; @endphp {{-- Hiển thị Combo giảm --}} @if($soNguoiDuocGiam> 0)
                                        <tr>
                                            <td>{{ $chiTietCombo->combo->ten_combo }} <small class="text-success">(Vé
                                                    trẻ em -50%)</small></td>
                                            <td class="text-center">{{ $soNguoiDuocGiam }}</td>
                                            <td class="text-end">{{ number_format($giaComboGoc * 0.5) }} đ</td>
                                            <td class="text-end">
                                                {{ number_format(($giaComboGoc * 0.5) * $soNguoiDuocGiam) }} đ</td>
                                        </tr>
                                        @endif

                                        {{-- Hiển thị Combo gốc --}}
                                        @if($soNguoiKhongGiam > 0)
                                        <tr>
                                            <td>{{ $chiTietCombo->combo->ten_combo }} <small class="text-muted">(Vé
                                                    người lớn)</small></td>
                                            <td class="text-center">{{ $soNguoiKhongGiam }}</td>
                                            <td class="text-end">{{ number_format($giaComboGoc) }} đ</td>
                                            <td class="text-end">{{ number_format($giaComboGoc * $soNguoiKhongGiam) }} đ
                                            </td>
                                        </tr>
                                        @endif
                                        @endif
                                        @endforeach
                                        @endif

                                        {{-- Hiển thị Món gọi thêm --}}
                                        @foreach($hoaDon->datBan->orderMon as $order)
                                        @foreach($order->chiTietOrders as $ct)
                                        @if($ct->loai_mon == 'goi_them' && $ct->trang_thai != 'huy_mon' &&
                                        $ct->trang_thai != 'cho_bep')
                                        <tr>
                                            <td>{{ $ct->monAn->ten_mon ?? 'Món gọi thêm' }} <span
                                                    class="badge bg-secondary">Gọi thêm</span></td>
                                            <td class="text-center">{{ $ct->so_luong }}</td>
                                            <td class="text-end">{{ number_format($ct->monAn->gia ?? 0) }} đ</td>
                                            <td class="text-end">
                                                {{ number_format(($ct->monAn->gia ?? 0) * $ct->so_luong) }} đ</td>
                                        </tr>
                                        @endif
                                        @endforeach
                                        @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    <tr>
                                        <td colspan="3" class="text-end">Tổng cộng:</td>
                                        <td class="text-end">{{ number_format($hoaDon->tong_tien) }} đ</td>
                                    </tr>
                                    @if($hoaDon->tien_giam > 0)
                                    <tr class="text-success">
                                        <td colspan="3" class="text-end">Voucher giảm:</td>
                                        <td class="text-end">-{{ number_format($hoaDon->tien_giam) }} đ</td>
                                    </tr>
                                    @endif
                                    @if($hoaDon->phu_thu > 0)
                                    <tr class="text-danger">
                                        <td colspan="3" class="text-end">Phụ thu:</td>
                                        <td class="text-end">+{{ number_format($hoaDon->phu_thu) }} đ</td>
                                    </tr>
                                    @endif
                                    @if(isset($hoaDon->datBan->tien_coc) && $hoaDon->datBan->tien_coc > 0)
                                    <tr class="text-primary">
                                        <td colspan="3" class="text-end">Tiền cọc:</td>
                                        <td class="text-end">-{{ number_format($hoaDon->datBan->tien_coc) }} đ</td>
                                    </tr>
                                    @endif
                                    <tr class="table-primary fs-5 text-danger">
                                        <td colspan="3" class="text-end">THỰC THU:</td>
                                        <td class="text-end">{{ number_format($hoaDon->da_thanh_toan) }} đ</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Nút chức năng --}}
                <div class="d-flex justify-content-center gap-3 mt-4 no-print">
                    <a href="{{ route('nhanVien.ban-an.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-house-door me-2"></i>Về trang chủ
                    </a>
                    <a href="{{ route('nhanVien.thanh-toan.in-hoa-don', $hoaDon->id) }}" target="_blank"
                        class="btn btn-primary btn-lg">
                        <i class="bi bi-printer me-2"></i>In hóa đơn
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection