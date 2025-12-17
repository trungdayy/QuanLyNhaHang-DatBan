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
                                            @php
                                            $danhSachMon = is_string($chiTiet->danh_sach_mon) ?
                                            json_decode($chiTiet->danh_sach_mon, true) : $chiTiet->danh_sach_mon;
                                            $tongTienMonTinhLai = 0; // Tính lại tổng tiền món từ danh sách hiển thị
                                            $stt = 1;
                                            
                                            // Tính và hiển thị combo với giảm giá cho trẻ em
                                            $soTreEm = $hoaDon->datBan->tre_em ?? 0;
                                            $soNguoiDaXuLy = 0;
                                            @endphp
                                            
                                            {{-- Hiển thị Combo --}}
                                            @if($chiTiet->tong_tien_combo > 0 && $hoaDon->datBan->chiTietDatBan)
                                            @foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo)
                                            @if($chiTietCombo->combo)
                                            @php
                                                $giaComboGoc = $chiTietCombo->combo->gia_co_ban;
                                                $soLuongCombo = $chiTietCombo->so_luong ?? 1;
                                                $soNguoiDuocGiam = 0;
                                                
                                                // Tính số người được giảm giá (trẻ em)
                                                if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                                                    $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                                                    $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                                                }
                                                $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;
                                                
                                                // Hiển thị combo giảm giá (trẻ em) nếu có
                                                if($soNguoiDuocGiam > 0) {
                                                    $giaComboGiam = $giaComboGoc * 0.5;
                                                    $thanhTienGiam = $giaComboGiam * $soNguoiDuocGiam;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $chiTietCombo->combo->ten_combo }}</strong>
                                                    <br><small class="text-muted">Combo Buffet - Trẻ em (Giảm 50%)</small>
                                                </td>
                                                <td class="text-center">{{ $soNguoiDuocGiam }} người</td>
                                                <td class="text-end">
                                                    <span class="text-decoration-line-through text-muted">{{ number_format($giaComboGoc) }} đ</span>
                                                    <br>
                                                    <span class="text-danger fw-bold">{{ number_format($giaComboGiam) }} đ</span>
                                                    <br><small class="text-success">(Giảm 50%)</small>
                                                </td>
                                                <td class="text-end fw-bold">{{ number_format($thanhTienGiam) }} đ</td>
                                            </tr>
                                            @php
                                                }
                                                
                                                // Hiển thị combo giá gốc (người lớn) nếu có
                                                if($soNguoiKhongGiam > 0) {
                                                    $thanhTienGoc = $giaComboGoc * $soNguoiKhongGiam;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $chiTietCombo->combo->ten_combo }}</strong>
                                                    <br><small class="text-muted">Combo Buffet - Người lớn</small>
                                                </td>
                                                <td class="text-center">{{ $soNguoiKhongGiam }} người</td>
                                                <td class="text-end">{{ number_format($giaComboGoc) }} đ/người</td>
                                                <td class="text-end fw-bold">{{ number_format($thanhTienGoc) }} đ</td>
                                            </tr>
                                            @php
                                                }
                                                
                                                $soNguoiDaXuLy += $soLuongCombo;
                                            @endphp
                                            @endif
                                            @endforeach
                                            @endif
                                            
                                            {{-- Hiển thị Món gọi thêm --}}
                                            @if(is_array($danhSachMon) && count($danhSachMon) > 0)
                                            @foreach($danhSachMon as $mon)
                                            @php
                                                // Chỉ hiển thị món gọi thêm (không phải món combo)
                                                $laMonCombo = isset($mon['la_mon_combo']) && $mon['la_mon_combo'];
                                                if($laMonCombo) {
                                                    continue; // Bỏ qua món combo
                                                }
                                                // Cộng dồn tiền món
                                                $tongTienMonTinhLai += $mon['thanh_tien'] ?? 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{ $mon['ten_mon'] }}
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
                                        @php
                                            $tienCombo = $chiTiet->tong_tien_combo ?? 0;
                                            // Sử dụng tiền món đã tính lại từ danh sách hiển thị, nếu không có thì dùng giá trị lưu
                                            $tienMon = isset($tongTienMonTinhLai) && $tongTienMonTinhLai > 0 ? $tongTienMonTinhLai : ($chiTiet->tong_tien_mon_goi_them ?? 0);
                                            $tongTienHang = $tienCombo + $tienMon;
                                        @endphp
                                        @if($tienCombo > 0)
                                        <p>Tiền combo: <strong>{{ number_format($tienCombo) }} đ</strong></p>
                                        @endif
                                        @if($tienMon > 0)
                                        <p>Tiền món: <strong>{{ number_format($tienMon) }} đ</strong></p>
                                        @endif
                                        <p class="border-top pt-2 mt-2">Tổng tiền hàng: <strong class="fs-5">{{ number_format($tongTienHang) }} đ</strong></p>
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
                                    {{-- Hiển thị Combo với giảm giá cho trẻ em --}}
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
                                    if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                                        $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                                        $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                                    }
                                    $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;
                                    $soNguoiDaXuLy += $soLuongCombo;
                                    @endphp
                                    
                                    {{-- Hiển thị Combo giảm giá (trẻ em) --}}
                                    @if($soNguoiDuocGiam > 0)
                                    @php
                                        $giaComboGiam = $giaComboGoc * 0.5;
                                        $thanhTienGiam = $giaComboGiam * $soNguoiDuocGiam;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $chiTietCombo->combo->ten_combo }}</strong>
                                            <br><small class="text-muted">Combo Buffet - Trẻ em (Giảm 50%)</small>
                                        </td>
                                        <td class="text-center">{{ $soNguoiDuocGiam }} người</td>
                                        <td class="text-end">
                                            <span class="text-decoration-line-through text-muted">{{ number_format($giaComboGoc) }} đ</span>
                                            <br>
                                            <span class="text-danger fw-bold">{{ number_format($giaComboGiam) }} đ</span>
                                            <br><small class="text-success">(Giảm 50%)</small>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($thanhTienGiam) }} đ</td>
                                    </tr>
                                    @endif

                                    {{-- Hiển thị Combo giá gốc (người lớn) --}}
                                    @if($soNguoiKhongGiam > 0)
                                    @php
                                        $thanhTienGoc = $giaComboGoc * $soNguoiKhongGiam;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $chiTietCombo->combo->ten_combo }}</strong>
                                            <br><small class="text-muted">Combo Buffet - Người lớn</small>
                                        </td>
                                        <td class="text-center">{{ $soNguoiKhongGiam }} người</td>
                                        <td class="text-end">{{ number_format($giaComboGoc) }} đ/người</td>
                                        <td class="text-end fw-bold">{{ number_format($thanhTienGoc) }} đ</td>
                                    </tr>
                                    @endif
                                    @endif
                                    @endforeach
                                    @endif

                                        {{-- Hiển thị Món gọi thêm (không có tag) --}}
                                        @php
                                        $tongTienCombo = 0;
                                        $tongTienMon = 0;
                                        
                                        // Tính tiền combo
                                        $soTreEm = $hoaDon->datBan->tre_em ?? 0;
                                        $soNguoiDaXuLy = 0;
                                        foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo) {
                                            if($chiTietCombo->combo) {
                                                $giaComboGoc = $chiTietCombo->combo->gia_co_ban;
                                                $soLuongCombo = $chiTietCombo->so_luong ?? 1;
                                                $soNguoiDuocGiam = 0;
                                                
                                                if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                                                    $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                                                    $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                                                }
                                                $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;
                                                
                                                $tongTienCombo += ($giaComboGoc * 0.5 * $soNguoiDuocGiam) + ($giaComboGoc * $soNguoiKhongGiam);
                                                $soNguoiDaXuLy += $soLuongCombo;
                                            }
                                        }
                                        @endphp
                                        @foreach($hoaDon->datBan->orderMon as $order)
                                        @foreach($order->chiTietOrders as $ct)
                                        @php
                                            // Chỉ tính món gọi thêm với các trạng thái được tính tiền
                                            $trangThaiHopLe = in_array($ct->trang_thai, ['da_len_mon', 'dang_che_bien', 'cho_cung_ung']);
                                        @endphp
                                        @if($ct->loai_mon == 'goi_them' && $ct->trang_thai != 'huy_mon' &&
                                        $ct->trang_thai != 'cho_bep' && $trangThaiHopLe)
                                        @php
                                            $thanhTien = ($ct->monAn->gia ?? 0) * $ct->so_luong;
                                            $tongTienMon += $thanhTien;
                                        @endphp
                                        <tr>
                                            <td>{{ $ct->monAn->ten_mon ?? 'Món gọi thêm' }}</td>
                                            <td class="text-center">{{ $ct->so_luong }}</td>
                                            <td class="text-end">{{ number_format($ct->monAn->gia ?? 0) }} đ</td>
                                            <td class="text-end">{{ number_format($thanhTien) }} đ</td>
                                        </tr>
                                        @endif
                                        @endforeach
                                        @endforeach
                                </tbody>
                                <tfoot class="table-light fw-bold">
                                    @php
                                        $tongTienHang = $tongTienCombo + $tongTienMon;
                                    @endphp
                                    @if($tongTienCombo > 0)
                                    <tr>
                                        <td colspan="3" class="text-end">Tiền combo:</td>
                                        <td class="text-end">{{ number_format($tongTienCombo) }} đ</td>
                                    </tr>
                                    @endif
                                    @if($tongTienMon > 0)
                                    <tr>
                                        <td colspan="3" class="text-end">Tiền món:</td>
                                        <td class="text-end">{{ number_format($tongTienMon) }} đ</td>
                                    </tr>
                                    @endif
                                    <tr class="border-top">
                                        <td colspan="3" class="text-end">Tổng tiền hàng:</td>
                                        <td class="text-end fs-5">{{ number_format($tongTienHang) }} đ</td>
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