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
                    @php
                        // Định nghĩa biến tổng cộng từ dữ liệu đã lưu
                        $tongCong = $chiTiet->tong_tien_combo_mon ?? $hoaDon->tong_tien ?? 0;
                    @endphp
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
                                    <p class="mb-1"><strong>Số khách:</strong><br><span class="badge bg-info">{{ $chiTiet->so_khach ?? 'N/A' }} người</span></p>
                                    <p class="mb-0">
                                        <small class="text-muted">
                                            - Người lớn: <strong>{{ $chiTiet->nguoi_lon ?? $hoaDon->datBan->nguoi_lon ?? 0 }}</strong> người<br>
                                            - Trẻ em: <strong>{{ $chiTiet->tre_em ?? $hoaDon->datBan->tre_em ?? 0 }}</strong> người
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
                                    <p class="mb-0">
                                        <strong>Thời gian phục vụ:</strong><br>
                                        <span class="badge bg-success fs-6">{{ floor($chiTiet->thoi_gian_phuc_vu_phut / 60) }} giờ {{ $chiTiet->thoi_gian_phuc_vu_phut % 60 }} phút</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Cột phải: Thông tin combo & món --}}
                        <div class="col-lg-8">
                            {{-- Thông tin combo --}}
                            @if($hoaDon->datBan->chiTietDatBan && $hoaDon->datBan->chiTietDatBan->count() > 0)
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="bi bi-basket3 me-2"></i>Thông tin combo & món đã gọi</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $soTreEm = $hoaDon->datBan->tre_em ?? 0;
                                        $soNguoiDaXuLy = 0; // Đếm số người đã xử lý
                                    @endphp
                                    @foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo)
                                        @if($chiTietCombo->combo)
                                        @php
                                            // Tính giá combo: giảm 50% cho từng người đầu tiên tương ứng với số trẻ em
                                            $giaComboGoc = $chiTietCombo->combo->gia_co_ban;
                                            $soLuongCombo = $chiTietCombo->so_luong ?? 1;
                                            
                                            // Tính số người được giảm giá trong combo này
                                            $soNguoiDuocGiam = 0;
                                            if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                                                // Số người được giảm = min(số trẻ em còn lại, số lượng combo này)
                                                $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                                                $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                                            }
                                            
                                            // Tính số người không giảm giá
                                            $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;
                                            
                                            $soNguoiDaXuLy += $soLuongCombo; // Tăng số người đã xử lý
                                        @endphp
                                        
                                        {{-- Hiển thị combo với giá giảm 50% (nếu có) --}}
                                        @if($soNguoiDuocGiam > 0)
                                        @php
                                            $giaComboGiam = $giaComboGoc * 0.5;
                                            $thanhTienGiam = $giaComboGiam * $soNguoiDuocGiam;
                                        @endphp
                                        <div class="mb-3 p-3 bg-light rounded border border-warning">
                                            <h6 class="fw-bold text-primary mb-2">
                                                <i class="bi bi-star-fill me-1 text-warning"></i>{{ $chiTietCombo->combo->ten_combo }}
                                                <span class="badge bg-success ms-2">Combo chính</span>
                                                <span class="badge bg-info ms-2">Trẻ em (Giảm 50%)</span>
                                            </h6>
                                            <p class="mb-1">
                                                <strong>Giá combo:</strong> 
                                                <span class="text-decoration-line-through text-muted">{{ number_format($giaComboGoc) }} đ</span>
                                                <span class="text-danger fw-bold"> {{ number_format($giaComboGiam) }} đ</span>
                                                <small class="text-success">(Giảm 50%)</small>
                                                /người
                                            </p>
                                            <p class="mb-1"><strong>Số lượng:</strong> {{ $soNguoiDuocGiam }} người</p>
                                            <p class="mb-0 mt-2"><strong>Thành tiền combo:</strong> 
                                                <span class="text-danger fw-bold fs-5">{{ number_format($thanhTienGiam) }} đ</span>
                                            </p>
                                        </div>
                                        @endif
                                        
                                        {{-- Hiển thị combo với giá gốc (nếu có) --}}
                                        @if($soNguoiKhongGiam > 0)
                                        @php
                                            $thanhTienGoc = $giaComboGoc * $soNguoiKhongGiam;
                                        @endphp
                                        <div class="mb-3 p-3 bg-light rounded border border-warning">
                                            <h6 class="fw-bold text-primary mb-2">
                                                <i class="bi bi-star-fill me-1 text-warning"></i>{{ $chiTietCombo->combo->ten_combo }}
                                                <span class="badge bg-success ms-2">Combo chính</span>
                                            </h6>
                                            <p class="mb-1">
                                                <strong>Giá combo:</strong> 
                                                {{ number_format($giaComboGoc) }} đ/người
                                            </p>
                                            <p class="mb-1"><strong>Số lượng:</strong> {{ $soNguoiKhongGiam }} người</p>
                                            <p class="mb-0 mt-2"><strong>Thành tiền combo:</strong> 
                                                <span class="text-danger fw-bold fs-5">{{ number_format($thanhTienGoc) }} đ</span>
                                            </p>
                                        </div>
                                        @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @elseif($chiTiet->ten_combo)
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
                                        <p class="mb-1"><strong>Số khách:</strong> {{ $chiTiet->so_khach }} người 
                                            <small class="text-muted">(Người lớn: {{ $chiTiet->nguoi_lon ?? $hoaDon->datBan->nguoi_lon ?? 0 }}, Trẻ em: {{ $chiTiet->tre_em ?? $hoaDon->datBan->tre_em ?? 0 }})</small>
                                        </p>
                                        <p class="mb-0 mt-2"><strong>Thành tiền combo:</strong> 
                                            <span class="text-danger fw-bold fs-5">{{ number_format($chiTiet->tong_tien_combo) }} đ</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- Danh sách món đã gọi --}}
                            @php
                                // Xử lý danh_sach_mon: có thể là array hoặc JSON string
                                $danhSachMonArray = $chiTiet->danh_sach_mon;
                                if (is_string($danhSachMonArray)) {
                                    $danhSachMonArray = json_decode($danhSachMonArray, true) ?? [];
                                }
                                if (!is_array($danhSachMonArray)) {
                                    $danhSachMonArray = [];
                                }
                            @endphp
                            @if(!empty($danhSachMonArray) && is_array($danhSachMonArray) && count($danhSachMonArray) > 0)
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh sách món đã gọi</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        // Lấy tất cả món từ các order để tính trạng thái (bao gồm cả món đã hủy)
                                        $monAnList = collect();
                                        foreach($hoaDon->datBan->orderMon as $order) {
                                            foreach($order->chiTietOrders as $ct) {
                                                $monAnList->push($ct);
                                            }
                                        }
                                        
                                        // Tính tổng giới hạn cho từng món
                                        $tongGioiHanMon = [];
                                        $phuPhiMon = [];
                                        $monTrongComboIds = []; // Danh sách ID món thuộc combo (bất kể có giới hạn hay không)
                                        foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo) {
                                            if($chiTietCombo->combo) {
                                                $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $chiTietCombo->combo->id)->get();
                                                foreach($monTrongCombo as $mtc) {
                                                    $monAnId = $mtc->mon_an_id;
                                                    
                                                    // Đánh dấu món này thuộc combo (bất kể có giới hạn hay không)
                                                    if(!in_array($monAnId, $monTrongComboIds)) {
                                                        $monTrongComboIds[] = $monAnId;
                                                    }
                                                    
                                                    $gioiHan = $mtc->gioi_han_so_luong ?? null;
                                                    if($gioiHan !== null && $gioiHan > 0) {
                                                        $soLuongCombo = $chiTietCombo->so_luong ?? 1;
                                                        if(!isset($tongGioiHanMon[$monAnId])) {
                                                            $tongGioiHanMon[$monAnId] = 0;
                                                            $phuPhiMon[$monAnId] = $mtc->phu_phi_goi_them ?? 0;
                                                        }
                                                        $tongGioiHanMon[$monAnId] += $gioiHan * $soLuongCombo;
                                                    } else {
                                                        // Món trong combo nhưng không có giới hạn, vẫn cần lưu phụ phí
                                                        if(!isset($phuPhiMon[$monAnId])) {
                                                            $phuPhiMon[$monAnId] = $mtc->phu_phi_goi_them ?? 0;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        
                                        // Nhóm món theo mon_an_id
                                        $monAnGrouped = $monAnList->groupBy('mon_an_id');
                                        
                                        // Tạo map từ mon_an_id sang thông tin món trong danh_sach_mon
                                        $monMap = [];
                                        foreach($danhSachMonArray as $mon) {
                                            // Tìm mon_an_id từ tên món (cần tìm từ order)
                                            foreach($monAnGrouped as $monAnId => $group) {
                                                $first = $group->first();
                                                if($first->monAn && $first->monAn->ten_mon == $mon['ten_mon']) {
                                                    $monMap[$monAnId] = $mon;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
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
                                                @php
                                                    $tongTienMonGoiThemTinhLai = 0;
                                                    $stt = 1;
                                                @endphp
                                                @foreach($danhSachMonArray as $mon)
                                                @php
                                                    // Ưu tiên sử dụng dữ liệu từ danh_sach_mon đã lưu
                                                    $tenMon = $mon['ten_mon'] ?? 'N/A';
                                                    $soLuong = $mon['so_luong'] ?? 0;
                                                    $coTrongCombo = $mon['la_mon_combo'] ?? false;
                                                    $soLuongVuot = $mon['so_luong_vuot'] ?? 0;
                                                    $tongGioiHan = $mon['gioi_han'] ?? null;
                                                    $donGiaGoc = $mon['don_gia'] ?? 0;
                                                    $thanhTien = $mon['thanh_tien'] ?? 0;
                                                    
                                                    // Tìm trong monAnGrouped để lấy trạng thái (nếu món chưa bị xóa)
                                                    $monAnGroup = null;
                                                    $ctFirst = null;
                                                    $monAnId = null;
                                                    foreach($monAnGrouped as $id => $group) {
                                                        $first = $group->first();
                                                        if($first->monAn && $first->monAn->ten_mon == $tenMon) {
                                                            $monAnGroup = $group;
                                                            $ctFirst = $first;
                                                            $monAnId = $id;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Fallback: Nếu la_mon_combo không có hoặc false, kiểm tra lại từ quan hệ
                                                    // (Trường hợp hóa đơn cũ được tạo trước khi sửa logic)
                                                    if(!$coTrongCombo && $monAnId !== null) {
                                                        // Kiểm tra xem món có trong danh sách món combo không
                                                        if(in_array($monAnId, $monTrongComboIds)) {
                                                            $coTrongCombo = true;
                                                            // Nếu chưa có giới hạn trong danh_sach_mon, lấy từ tongGioiHanMon
                                                            if($tongGioiHan === null && isset($tongGioiHanMon[$monAnId])) {
                                                                $tongGioiHan = $tongGioiHanMon[$monAnId];
                                                            }
                                                        }
                                                    }
                                                    
                                                    // Chỉ hiển thị món gọi thêm (không phải món combo)
                                                    if($coTrongCombo) {
                                                        continue; // Bỏ qua món combo
                                                    }
                                                    
                                                    // Tính trạng thái từ quan hệ (nếu có): chỉ tính đã lên + đang nấu + chờ cung ứng
                                                    $soLuongDaLen = 0;
                                                    $soLuongDangCheBien = 0;
                                                    $soLuongChoCungUng = 0;
                                                    $soLuongHuy = 0;
                                                    
                                                    if($monAnGroup) {
                                                        foreach($monAnGroup as $ct) {
                                                            if($ct->trang_thai == 'da_len_mon') {
                                                                $soLuongDaLen += $ct->so_luong;
                                                            } elseif($ct->trang_thai == 'dang_che_bien') {
                                                                $soLuongDangCheBien += $ct->so_luong;
                                                            } elseif($ct->trang_thai == 'cho_cung_ung') {
                                                                $soLuongChoCungUng += $ct->so_luong;
                                                            } elseif($ct->trang_thai == 'huy_mon') {
                                                                $soLuongHuy += $ct->so_luong;
                                                            }
                                                            // Bỏ qua: cho_bep
                                                        }
                                                        
                                                        // Số lượng hiển thị = đã lên + đang nấu + chờ cung ứng (không tính hủy, chờ bếp)
                                                        $tongSoLuongHienThi = $soLuongDaLen + $soLuongDangCheBien + $soLuongChoCungUng;
                                                    } else {
                                                        // Nếu không có monAnGroup (món đã bị xóa), sử dụng số lượng từ danh_sach_mon
                                                        $tongSoLuongHienThi = $soLuong;
                                                    }
                                                    
                                                    // Chỉ hiển thị món có số lượng > 0
                                                    if($tongSoLuongHienThi <= 0) {
                                                        continue;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $mon['stt'] ?? $stt++ }}</td>
                                                    <td>
                                                        {{ $tenMon }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $tongSoLuongHienThi }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ number_format($donGiaGoc) }} đ
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        @php
                                                            // Tính thành tiền: đã lên + đang nấu + chờ cung ứng
                                                            $thanhTienTinhLai = ($donGiaGoc * $soLuongDaLen) + ($donGiaGoc * $soLuongDangCheBien) + ($donGiaGoc * $soLuongChoCungUng);
                                                            $tongTienMonGoiThemTinhLai += $thanhTienTinhLai;
                                                        @endphp
                                                        {{ number_format($thanhTienTinhLai) }} đ
                                                    </td>
                                                </tr>
                                                @endforeach
                                                
                                                {{-- Tổng kết --}}
                                                @php
                                                    // Sử dụng dữ liệu đã lưu từ chi_tiet_hoa_don
                                                    $tongTienComboTinhLai = $chiTiet->tong_tien_combo ?? 0;
                                                    $tongTienMonGoiThem = $tongTienMonGoiThemTinhLai; // Tính từ bảng để hiển thị chi tiết
                                                    $tongCong = $chiTiet->tong_tien_combo_mon ?? $hoaDon->tong_tien ?? 0;
                                                @endphp
                                                @if($tongTienComboTinhLai > 0)
                                                <tr class="table-warning fw-bold">
                                                    <td colspan="4" class="text-end">
                                                        <i class="bi bi-star-fill text-warning me-1"></i>Tổng tiền combo chính:
                                                    </td>
                                                    <td class="text-end text-primary fs-5">{{ number_format($tongTienComboTinhLai) }} đ</td>
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
                                            // Sử dụng dữ liệu đã lưu từ chi_tiet_hoa_don
                                            $tongTienComboMon = $chiTiet->tong_tien_combo_mon ?? $hoaDon->tong_tien ?? 0;
                                            $tongTienSauVoucher = $chiTiet->tong_tien_sau_voucher ?? ($tongTienComboMon - ($chiTiet->tien_giam_voucher ?? 0));
                                            if($tongTienSauVoucher < 0) $tongTienSauVoucher = 0;
                                            
                                            // Phải thanh toán từ chi_tiet_hoa_don
                                            $phaiThanhToan = $chiTiet->phai_thanh_toan ?? ($tongTienSauVoucher - ($chiTiet->tien_coc ?? 0));
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
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between fs-4 fw-bold">
                                            <span class="text-dark">Phải thanh toán:</span>
                                            <span class="text-danger">{{ number_format($phaiThanhToan) }} đ</span>
                                        </div>
                                        <div class="d-flex justify-content-between mt-2 fs-5">
                                            <span class="text-dark">Đã thanh toán:</span>
                                            @php
                                                // Xác định đã thanh toán chưa: kiểm tra phuong_thuc_tt hoặc tien_khach_dua
                                                $daThanhToan = false;
                                                $soTienDaThanhToan = 0;
                                                
                                                if($chiTiet) {
                                                    // Nếu có phuong_thuc_tt và khác 'chua_thanh_toan' thì đã thanh toán
                                                    if($chiTiet->phuong_thuc_tt && $chiTiet->phuong_thuc_tt != 'chua_thanh_toan') {
                                                        $daThanhToan = true;
                                                        $soTienDaThanhToan = $chiTiet->phai_thanh_toan ?? $phaiThanhToan;
                                                    } elseif($chiTiet->tien_khach_dua && $chiTiet->tien_khach_dua > 0) {
                                                        // Nếu có tien_khach_dua thì đã thanh toán
                                                        $daThanhToan = true;
                                                        $soTienDaThanhToan = $chiTiet->phai_thanh_toan ?? $phaiThanhToan;
                                                    } elseif($hoaDon->trang_thai == 'da_thanh_toan') {
                                                        $daThanhToan = true;
                                                        $soTienDaThanhToan = $hoaDon->da_thanh_toan ?? $phaiThanhToan;
                                                    }
                                                } elseif($hoaDon->trang_thai == 'da_thanh_toan') {
                                                    $daThanhToan = true;
                                                    $soTienDaThanhToan = $hoaDon->da_thanh_toan ?? $phaiThanhToan;
                                                }
                                            @endphp
                                            <span class="text-{{ $daThanhToan ? 'success' : 'warning' }}">
                                                {{ number_format($soTienDaThanhToan) }} đ
                                            </span>
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
                                                @php
                                                    // Ưu tiên lấy từ chi_tiet_hoa_don, sau đó từ hoa_don
                                                    $phuongThucTT = $chiTiet->phuong_thuc_tt ?? $hoaDon->phuong_thuc_tt ?? null;
                                                    
                                                    // Nếu có tien_khach_dua hoặc phuong_thuc_tt khác 'chua_thanh_toan' thì đã thanh toán
                                                    $daThanhToan = false;
                                                    if($chiTiet) {
                                                        if($chiTiet->tien_khach_dua && $chiTiet->tien_khach_dua > 0) {
                                                            $daThanhToan = true;
                                                        } elseif($phuongThucTT && $phuongThucTT != 'chua_thanh_toan') {
                                                            $daThanhToan = true;
                                                        }
                                                    } elseif($hoaDon->trang_thai == 'da_thanh_toan') {
                                                        $daThanhToan = true;
                                                    }
                                                    
                                                    // Nếu chưa thanh toán hoặc phuong_thuc_tt là 'chua_thanh_toan' hoặc null
                                                    if(!$daThanhToan || !$phuongThucTT || $phuongThucTT == 'chua_thanh_toan') {
                                                        $phuongThucTT = 'chua_thanh_toan';
                                                    }
                                                @endphp
                                                @if($phuongThucTT == 'chua_thanh_toan')
                                                    <span class="badge bg-warning">Chưa thanh toán</span>
                                                @elseif($phuongThucTT == 'tien_mat')
                                                    <span class="badge bg-success">Tiền mặt</span>
                                                @elseif($phuongThucTT == 'chuyen_khoan')
                                                    <span class="badge bg-primary">Chuyển khoản</span>
                                                @elseif($phuongThucTT == 'the_ATM')
                                                    <span class="badge bg-info">Thẻ ATM</span>
                                                @elseif($phuongThucTT == 'vnpay')
                                                    <span class="badge bg-warning">VNPay</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $phuongThucTT }}</span>
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
                            <p>Số khách: {{ $hoaDon->datBan->so_khach ?? 'N/A' }} 
                                <small class="text-muted">(Người lớn: {{ $hoaDon->datBan->nguoi_lon ?? 0 }}, Trẻ em: {{ $hoaDon->datBan->tre_em ?? 0 }})</small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">Thông tin bàn</h5>
                            <p>Bàn số: {{ $hoaDon->datBan->banAn->so_ban ?? 'N/A' }}</p>
                            <p>Khu vực: {{ $hoaDon->datBan->banAn->khuVuc->ten_khu_vuc ?? 'N/A' }}</p>
                            <p>Ngày tạo: {{ $hoaDon->created_at->format('d/m/Y H:i:s') }}</p>
                            <p>Phương thức TT: 
                                @if($hoaDon->trang_thai == 'chua_thanh_toan' || $hoaDon->phuong_thuc_tt == 'chua_thanh_toan')
                                    Chưa thanh toán
                                @elseif($hoaDon->phuong_thuc_tt == 'tien_mat')
                                    Tiền mặt
                                @elseif($hoaDon->phuong_thuc_tt == 'chuyen_khoan')
                                    Chuyển khoản
                                @elseif($hoaDon->phuong_thuc_tt == 'the_ATM')
                                    Thẻ ATM
                                @elseif($hoaDon->phuong_thuc_tt == 'vnpay')
                                    VNPay
                                @else
                                    {{ $hoaDon->phuong_thuc_tt }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Nút in hóa đơn --}}
                <div class="d-flex justify-content-center gap-3 mt-4">
                    @php
                        $user = Auth::user();
                        $backRoute = ($user && $user->vai_tro === 'le_tan') 
                            ? route('nhanVien.ban-an.index') 
                            : route('nhanVien.order.index');
                    @endphp
                    <a href="{{ $backRoute }}" class="btn btn-secondary btn-lg">Quay lại</a>
                    <a href="{{ route('nhanVien.thanh-toan.in-hoa-don', $hoaDon->id) }}" target="_blank" class="btn btn-primary btn-lg">In hóa đơn</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection