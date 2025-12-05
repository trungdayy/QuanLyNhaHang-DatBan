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
                            @if($chiTiet->danh_sach_mon && count($chiTiet->danh_sach_mon) > 0)
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
                                        foreach($chiTiet->danh_sach_mon as $mon) {
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
                                                    <th class="text-center">Trạng thái</th>
                                                    <th class="text-end">Đơn giá</th>
                                                    <th class="text-end">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $tongTienMonGoiThemTinhLai = 0;
                                                    $stt = 1;
                                                @endphp
                                                @foreach($chiTiet->danh_sach_mon as $mon)
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
                                                    
                                                    // Tính trạng thái từ quan hệ (nếu có)
                                                    $soLuongDaLen = 0;
                                                    $soLuongChoBep = 0;
                                                    $soLuongDangCheBien = 0;
                                                    $soLuongChuaNauXong = 0;
                                                    $soLuongDaLenTrongVuot = 0;
                                                    $soLuongChuaNauXongTrongVuot = 0;
                                                    $soLuongDangCheBienTrongVuot = 0;
                                                    $soLuongChoBepTrongVuot = 0;
                                                    $soLuongHuy = 0;
                                                    
                                                    if($monAnGroup) {
                                                        foreach($monAnGroup as $ct) {
                                                            if($ct->trang_thai == 'da_len_mon') {
                                                                $soLuongDaLen += $ct->so_luong;
                                                            } elseif($ct->trang_thai == 'cho_bep') {
                                                                $soLuongChoBep += $ct->so_luong;
                                                                $soLuongChuaNauXong += $ct->so_luong;
                                                            } elseif($ct->trang_thai == 'dang_che_bien') {
                                                                $soLuongDangCheBien += $ct->so_luong;
                                                                $soLuongChuaNauXong += $ct->so_luong;
                                                            } elseif($ct->trang_thai == 'huy_mon') {
                                                                $soLuongHuy += $ct->so_luong;
                                                            }
                                                        }
                                                        
                                                        if($soLuongVuot > 0 && $tongGioiHan !== null) {
                                                            $soLuongDaLenTrongVuot = max(0, $soLuongDaLen - $tongGioiHan);
                                                            $soLuongChuaNauXongTrongVuot = $soLuongVuot - $soLuongDaLenTrongVuot;
                                                            $soLuongConLaiTrongVuot = $soLuongChuaNauXongTrongVuot;
                                                            $soLuongDangCheBienTrongVuot = min($soLuongDangCheBien, $soLuongConLaiTrongVuot);
                                                            $soLuongChoBepTrongVuot = $soLuongConLaiTrongVuot - $soLuongDangCheBienTrongVuot;
                                                        }
                                                        
                                                        // Tính tổng số lượng từ order (bao gồm cả món đã hủy)
                                                        $tongSoLuongHienThi = $monAnGroup->sum('so_luong');
                                                    } else {
                                                        // Nếu không có monAnGroup (món đã bị xóa), sử dụng số lượng từ danh_sach_mon
                                                        $tongSoLuongHienThi = $soLuong;
                                                    }
                                                    
                                                    // Chỉ hiển thị món nếu có đã lên hoặc đang nấu
                                                    // Nếu chỉ có đã hủy thì không hiển thị
                                                    if($soLuongDaLen == 0 && $soLuongDangCheBien == 0) {
                                                        continue;
                                                    }
                                                    
                                                    $coMonChuaNauXong = $soLuongChuaNauXong > 0 || $soLuongChuaNauXongTrongVuot > 0;
                                                    
                                                    // Lấy phụ phí từ danh_sach_mon đã lưu
                                                    $tienPhuPhiTinhLai = $mon['phu_phi_tong'] ?? 0;
                                                    $phuPhiDonVi = $mon['phu_phi'] ?? 0;
                                                    
                                                    // Tính lại thành tiền từ trạng thái nếu cần hiển thị chi tiết
                                                    // Nhưng ưu tiên sử dụng $thanhTien từ danh_sach_mon đã lưu
                                                    $thanhTienTinhLai = $thanhTien; // Sử dụng giá trị đã lưu
                                                @endphp
                                                <tr>
                                                    <td>{{ $mon['stt'] ?? $stt++ }}</td>
                                                    <td>
                                                        {{ $tenMon }}
                                                        @if($coTrongCombo)
                                                            <span class="badge bg-warning">Món combo</span>
                                                            @if($mon['vuot_gioi_han'] ?? false)
                                                                <span class="badge bg-danger">Vượt giới hạn</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-info">Gọi thêm</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $tongSoLuongHienThi }}
                                                        @if($tongGioiHan !== null && $tongGioiHan > 0)
                                                            <br><small class="text-muted">(Giới hạn: {{ $tongGioiHan }})</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($monAnGroup)
                                                            @php
                                                                $tongSoLuongKhongHuy = $tongSoLuongHienThi - $soLuongHuy;
                                                            @endphp
                                                            @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuongHienThi)
                                                                {{-- Tất cả đã hủy --}}
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <span class="badge bg-danger mb-1">Đã hủy: {{ $soLuongHuy }}/{{ $tongSoLuongHienThi }}</span>
                                                                </div>
                                                            @elseif($soLuongDaLen == $tongSoLuongKhongHuy && $soLuongHuy == 0 && $soLuongDangCheBien == 0 && $soLuongChoBep == 0)
                                                                {{-- Tất cả đã lên, không có trạng thái khác --}}
                                                                <div class="d-flex flex-column align-items-center">
                                                                    <span class="badge bg-success">Đã lên: {{ $soLuongDaLen }}/{{ $tongSoLuongHienThi }}</span>
                                                                </div>
                                                            @else
                                                                {{-- Có nhiều trạng thái - hiển thị chi tiết --}}
                                                                <div class="d-flex flex-column align-items-center gap-1">
                                                                    @if($soLuongDaLen > 0)
                                                                        <span class="badge bg-success">Đã lên: {{ $soLuongDaLen }}</span>
                                                                    @endif
                                                                    @if($soLuongDangCheBien > 0)
                                                                        <span class="badge bg-warning text-dark">Đang nấu: {{ $soLuongDangCheBien }}</span>
                                                                    @endif
                                                                    @if($soLuongChoBep > 0)
                                                                        <span class="badge bg-info text-white">Chờ bếp: {{ $soLuongChoBep }}</span>
                                                                    @endif
                                                                    @if($soLuongHuy > 0)
                                                                        <span class="badge bg-danger">Đã hủy: {{ $soLuongHuy }}</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuongHienThi)
                                                            {{-- Tất cả món đã hủy --}}
                                                            <span class="text-danger">0 đ</span>
                                                            <br><small class="text-muted">(Đã hủy)</small>
                                                        @elseif($coTrongCombo && $soLuongVuot == 0)
                                                            {{-- Món combo chưa vượt giới hạn: hiển thị 0 đ --}}
                                                            <span class="text-success">0 đ</span>
                                                            <br><small class="text-muted">(Đã bao gồm trong combo)</small>
                                                            @if($soLuongHuy > 0)
                                                                <br><small class="text-danger">Đã hủy ({{ $soLuongHuy }}): 0 đ</small>
                                                            @endif
                                                        @elseif($donGiaGoc > 0 || $coMonChuaNauXong || $soLuongVuot > 0)
                                                            {{-- Món vượt giới hạn hoặc món gọi thêm: hiển thị chi tiết --}}
                                                            <div>
                                                                <small class="text-muted">Giá gốc: {{ number_format($donGiaGoc) }} đ</small>
                                                                @if($soLuongHuy > 0)
                                                                    <br><small class="text-danger">Đã hủy ({{ $soLuongHuy }}): 0 đ</small>
                                                                @endif
                                                                @if($coMonChuaNauXong)
                                                                    <br>
                                                                    <small class="text-warning">
                                                                        <i class="bi bi-info-circle me-1"></i>
                                                                        @if($coTrongCombo && $soLuongVuot > 0)
                                                                            @if($soLuongDaLenTrongVuot > 0)
                                                                                Đã nấu xong ({{ $soLuongDaLenTrongVuot }}): 100% = {{ number_format($donGiaGoc * $soLuongDaLenTrongVuot) }} đ
                                                                                @if($soLuongChuaNauXongTrongVuot > 0)
                                                                                    <br>
                                                                                    @if($soLuongDangCheBienTrongVuot > 0)
                                                                                        Đang nấu dở ({{ $soLuongDangCheBienTrongVuot }}): 100% = {{ number_format($donGiaGoc * $soLuongDangCheBienTrongVuot) }} đ
                                                                                        @if($soLuongChoBepTrongVuot > 0)
                                                                                            <br>
                                                                                            Chờ bếp ({{ $soLuongChoBepTrongVuot }}): 0 đ
                                                                                        @endif
                                                                                    @elseif($soLuongChoBepTrongVuot > 0)
                                                                                        Chờ bếp ({{ $soLuongChoBepTrongVuot }}): 0 đ
                                                                                    @endif
                                                                                @endif
                                                                            @elseif($soLuongChuaNauXongTrongVuot > 0)
                                                                                @if($soLuongDangCheBienTrongVuot > 0)
                                                                                    Đang nấu dở ({{ $soLuongDangCheBienTrongVuot }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongDangCheBienTrongVuot) }} đ
                                                                                    @if($soLuongChoBepTrongVuot > 0)
                                                                                        <br>
                                                                                        Chờ bếp ({{ $soLuongChoBepTrongVuot }}): 0 đ
                                                                                    @endif
                                                                                @elseif($soLuongChoBepTrongVuot > 0)
                                                                                    Chờ bếp ({{ $soLuongChoBepTrongVuot }}): 0 đ
                                                                                @endif
                                                                            @endif
                                                                        @elseif(!$coTrongCombo)
                                                                            @if($soLuongDaLen > 0)
                                                                                Đã nấu xong ({{ $soLuongDaLen }}): 100% = {{ number_format($donGiaGoc * $soLuongDaLen) }} đ
                                                                                @if($soLuongChuaNauXong > 0)
                                                                                    <br>
                                                                                    @if($soLuongDangCheBien > 0)
                                                                                        Đang nấu dở ({{ $soLuongDangCheBien }}): 100% = {{ number_format($donGiaGoc * $soLuongDangCheBien) }} đ
                                                                                        @if($soLuongChoBep > 0)
                                                                                            <br>
                                                                                            Chờ bếp ({{ $soLuongChoBep }}): 0 đ
                                                                                        @endif
                                                                                    @elseif($soLuongChoBep > 0)
                                                                                        Chờ bếp ({{ $soLuongChoBep }}): 0 đ
                                                                                    @endif
                                                                                @endif
                                                                            @elseif($soLuongChuaNauXong > 0)
                                                                                @if($soLuongDangCheBien > 0)
                                                                                    Đang nấu dở ({{ $soLuongDangCheBien }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongDangCheBien) }} đ
                                                                                    @if($soLuongChoBep > 0)
                                                                                        <br>
                                                                                        Chờ bếp ({{ $soLuongChoBep }}): 0 đ
                                                                                    @endif
                                                                                @elseif($soLuongChoBep > 0)
                                                                                    Chờ bếp ({{ $soLuongChoBep }}): 0 đ
                                                                                @endif
                                                                            @endif
                                                                        @endif
                                                                    </small>
                                                                @endif
                                                            </div>
                                                            @if($tienPhuPhiTinhLai > 0)
                                                                <br>
                                                                <small class="text-danger">
                                                                    + Phụ phí: {{ number_format($tienPhuPhiTinhLai) }} đ
                                                                    @if($soLuongDaLenTrongVuot > 1 && $phuPhiDonVi > 0)
                                                                        <br><small class="text-muted">({{ number_format($phuPhiDonVi) }} đ × {{ $soLuongDaLenTrongVuot }})</small>
                                                                    @endif
                                                                </small>
                                                            @endif
                                                        @else
                                                            <span class="text-success">0 đ</span>
                                                            <br><small class="text-muted">(Đã bao gồm trong combo)</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        @if($monAnGroup && $soLuongHuy > 0 && isset($tongSoLuongHienThi) && $soLuongHuy == $tongSoLuongHienThi)
                                                            {{-- Tất cả món đã hủy --}}
                                                            <span class="text-danger">0 đ</span>
                                                        @elseif($thanhTien > 0)
                                                            {{ number_format($thanhTien) }} đ
                                                            @php
                                                                $tongTienMonGoiThemTinhLai += $thanhTien;
                                                            @endphp
                                                        @else
                                                            <span class="text-success">0 đ</span>
                                                        @endif
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
                                                    <td colspan="5" class="text-end">
                                                        <i class="bi bi-star-fill text-warning me-1"></i>Tổng tiền combo chính:
                                                    </td>
                                                    <td class="text-end text-primary fs-5">{{ number_format($tongTienComboTinhLai) }} đ</td>
                                                </tr>
                                                @endif
                                                @if($tongTienMonGoiThem > 0)
                                                <tr class="table-secondary fw-bold">
                                                    <td colspan="5" class="text-end">Tổng tiền món gọi thêm:</td>
                                                    <td class="text-end text-info">{{ number_format($tongTienMonGoiThem) }} đ</td>
                                                </tr>
                                                @endif
                                                <tr class="table-primary fw-bold fs-5">
                                                    <td colspan="5" class="text-end">TỔNG CỘNG:</td>
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
                            <p>Số khách: {{ $hoaDon->datBan->so_khach ?? 'N/A' }} 
                                <small class="text-muted">(Người lớn: {{ $hoaDon->datBan->nguoi_lon ?? 0 }}, Trẻ em: {{ $hoaDon->datBan->tre_em ?? 0 }})</small>
                            </p>
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