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
                                    <p class="mb-1"><strong>Số khách:</strong><br><span class="badge bg-info">{{ $chiTiet->so_khach ?? 'N/A' }} người</span></p>
                                    <p class="mb-0">
                                        <small class="text-muted">
                                            - Người lớn: <strong>{{ $hoaDon->datBan->nguoi_lon ?? 0 }}</strong> người<br>
                                            - Trẻ em: <strong>{{ $hoaDon->datBan->tre_em ?? 0 }}</strong> người
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
                            @if($hoaDon->datBan->chiTietDatBan && $hoaDon->datBan->chiTietDatBan->count() > 0)
                            <div class="card shadow-sm border-0 mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="bi bi-basket3 me-2"></i>Thông tin combo & món đã gọi</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $soTreEm = $hoaDon->datBan->tre_em ?? 0;
                                        $comboIndex = 0; // Đếm số combo đã xử lý
                                    @endphp
                                    @foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo)
                                        @if($chiTietCombo->combo)
                                        @php
                                            // Tính giá combo: giảm 50% cho số combo đầu tiên tương ứng với số trẻ em
                                            $giaCombo = $chiTietCombo->combo->gia_co_ban;
                                            $soLuongCombo = $chiTietCombo->so_luong ?? 1;
                                            $isTreEm = false;
                                            
                                            // Giảm 50% cho số combo đầu tiên tương ứng với số trẻ em
                                            if($soTreEm > 0 && $comboIndex < $soTreEm) {
                                                $isTreEm = true;
                                                $giaCombo = $giaCombo * 0.5; // Giảm 50% cho trẻ em
                                            }
                                            
                                            $thanhTien = $giaCombo * $soLuongCombo;
                                            $comboIndex += $soLuongCombo; // Tăng index theo số lượng combo
                                        @endphp
                                        <div class="mb-3 p-3 bg-light rounded border border-warning">
                                            <h6 class="fw-bold text-primary mb-2">
                                                <i class="bi bi-star-fill me-1 text-warning"></i>{{ $chiTietCombo->combo->ten_combo }}
                                                <span class="badge bg-success ms-2">Combo chính</span>
                                                @if($isTreEm)
                                                    <span class="badge bg-info ms-2">Trẻ em (Giảm 50%)</span>
                                                @endif
                                            </h6>
                                            <p class="mb-1">
                                                <strong>Giá combo:</strong> 
                                                @if($isTreEm)
                                                    <span class="text-decoration-line-through text-muted">{{ number_format($chiTietCombo->combo->gia_co_ban) }} đ</span>
                                                    <span class="text-danger fw-bold"> {{ number_format($giaCombo) }} đ</span>
                                                    <small class="text-success">(Giảm 50%)</small>
                                                @else
                                                    {{ number_format($giaCombo) }} đ
                                                @endif
                                                /người
                                            </p>
                                            <p class="mb-1"><strong>Số lượng:</strong> {{ $chiTietCombo->so_luong ?? 1 }} người</p>
                                            <p class="mb-0 mt-2"><strong>Thành tiền combo:</strong> 
                                                <span class="text-danger fw-bold fs-5">{{ number_format($thanhTien) }} đ</span>
                                            </p>
                                        </div>
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
                                            <small class="text-muted">(Người lớn: {{ $hoaDon->datBan->nguoi_lon ?? 0 }}, Trẻ em: {{ $hoaDon->datBan->tre_em ?? 0 }})</small>
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
                                        // Lấy tất cả món từ các order để tính trạng thái
                                        $monAnList = collect();
                                        foreach($hoaDon->datBan->orderMon as $order) {
                                            foreach($order->chiTietOrders as $ct) {
                                                if($ct->trang_thai != 'huy_mon') {
                                                    $monAnList->push($ct);
                                                }
                                            }
                                        }
                                        
                                        // Tính tổng giới hạn cho từng món
                                        $tongGioiHanMon = [];
                                        $phuPhiMon = [];
                                        foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo) {
                                            if($chiTietCombo->combo) {
                                                $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $chiTietCombo->combo->id)->get();
                                                foreach($monTrongCombo as $mtc) {
                                                    $monAnId = $mtc->mon_an_id;
                                                    $gioiHan = $mtc->gioi_han_so_luong ?? null;
                                                    if($gioiHan !== null && $gioiHan > 0) {
                                                        $soLuongCombo = $chiTietCombo->so_luong ?? 1;
                                                        if(!isset($tongGioiHanMon[$monAnId])) {
                                                            $tongGioiHanMon[$monAnId] = 0;
                                                            $phuPhiMon[$monAnId] = $mtc->phu_phi_goi_them ?? 0;
                                                        }
                                                        $tongGioiHanMon[$monAnId] += $gioiHan * $soLuongCombo;
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
                                                @endphp
                                                @foreach($chiTiet->danh_sach_mon as $mon)
                                                @php
                                                    // Tìm mon_an_id từ tên món
                                                    $monAnId = null;
                                                    $monAnGroup = null;
                                                    foreach($monAnGrouped as $id => $group) {
                                                        $first = $group->first();
                                                        if($first->monAn && $first->monAn->ten_mon == $mon['ten_mon']) {
                                                            $monAnId = $id;
                                                            $monAnGroup = $group;
                                                            break;
                                                        }
                                                    }
                                                    
                                                    // Tính trạng thái
                                                    $soLuongDaLen = 0;
                                                    $soLuongChoBep = 0;
                                                    $soLuongDangCheBien = 0;
                                                    $soLuongChuaNauXong = 0;
                                                    $soLuongDaLenTrongVuot = 0;
                                                    $soLuongChuaNauXongTrongVuot = 0;
                                                    $soLuongDangCheBienTrongVuot = 0;
                                                    $soLuongChoBepTrongVuot = 0;
                                                    
                                                    if($monAnGroup) {
                                                        $tongSoLuong = $monAnGroup->sum('so_luong');
                                                        $tongGioiHan = $tongGioiHanMon[$monAnId] ?? null;
                                                        $soLuongVuot = 0;
                                                        if($tongGioiHan !== null && $tongGioiHan > 0) {
                                                            $soLuongVuot = max(0, $tongSoLuong - $tongGioiHan);
                                                        }
                                                        
                                                        foreach($monAnGroup as $ct) {
                                                            if($ct->trang_thai == 'da_len_mon') {
                                                                $soLuongDaLen += $ct->so_luong;
                                                            } elseif($ct->trang_thai == 'cho_bep') {
                                                                $soLuongChoBep += $ct->so_luong;
                                                                $soLuongChuaNauXong += $ct->so_luong;
                                                            } elseif($ct->trang_thai == 'dang_che_bien') {
                                                                $soLuongDangCheBien += $ct->so_luong;
                                                                $soLuongChuaNauXong += $ct->so_luong;
                                                            }
                                                        }
                                                        
                                                        if($soLuongVuot > 0) {
                                                            $soLuongDaLenTrongVuot = max(0, $soLuongDaLen - $tongGioiHan);
                                                            $soLuongChuaNauXongTrongVuot = $soLuongVuot - $soLuongDaLenTrongVuot;
                                                            $soLuongConLaiTrongVuot = $soLuongChuaNauXongTrongVuot;
                                                            $soLuongDangCheBienTrongVuot = min($soLuongDangCheBien, $soLuongConLaiTrongVuot);
                                                            $soLuongChoBepTrongVuot = $soLuongConLaiTrongVuot - $soLuongDangCheBienTrongVuot;
                                                        }
                                                    }
                                                    
                                                    $donGiaGoc = 0;
                                                    if($monAnGroup && $monAnGroup->first()->monAn) {
                                                        $donGiaGoc = $monAnGroup->first()->monAn->gia ?? 0;
                                                    }
                                                    $coMonChuaNauXong = $soLuongChuaNauXong > 0 || $soLuongChuaNauXongTrongVuot > 0;
                                                    $coTrongCombo = $mon['la_mon_combo'] ?? false;
                                                    $soLuongVuot = $mon['so_luong_vuot'] ?? 0;
                                                    
                                                    // Tính lại phụ phí dựa trên số lượng đã lên bàn (giống trang thanh toán)
                                                    $tienPhuPhiTinhLai = 0;
                                                    $phuPhiDonVi = $phuPhiMon[$monAnId] ?? 0;
                                                    if($coTrongCombo && $soLuongVuot > 0 && $phuPhiDonVi > 0) {
                                                        // Phụ phí chỉ tính cho số lượng đã lên bàn trong phần vượt
                                                        $tienPhuPhiTinhLai = $phuPhiDonVi * $soLuongDaLenTrongVuot;
                                                    }
                                                    
                                                    // Tính lại thành tiền: đã nấu xong + chờ bếp/đang nấu dở + phụ phí
                                                    $thanhTienTinhLai = 0;
                                                    if($coTrongCombo) {
                                                        // Món thuộc combo
                                                        if($soLuongVuot > 0) {
                                                            // Tính tiền cho phần đã nấu xong trong vượt (100%)
                                                            $tienMonDaLenTrongVuot = $donGiaGoc * $soLuongDaLenTrongVuot;
                                                            // Tính tiền cho phần chưa nấu xong trong vượt (30%)
                                                            $tienMonChuaNauXongTrongVuot = $donGiaGoc * 0.3 * $soLuongChuaNauXongTrongVuot;
                                                            // Tổng tiền = tiền món đã nấu xong + tiền món chưa nấu xong + phụ phí
                                                            $thanhTienTinhLai = $tienMonDaLenTrongVuot + $tienMonChuaNauXongTrongVuot + $tienPhuPhiTinhLai;
                                                        }
                                                    } else {
                                                        // Món không thuộc combo: tính tiền theo trạng thái nấu
                                                        // Phần đã nấu xong: 100% giá
                                                        $tienMonDaLen = $donGiaGoc * $soLuongDaLen;
                                                        // Phần chưa nấu xong: 30% giá
                                                        $tienMonChuaNauXong = $donGiaGoc * 0.3 * $soLuongChuaNauXong;
                                                        // Tổng tiền
                                                        $thanhTienTinhLai = $tienMonDaLen + $tienMonChuaNauXong;
                                                    }
                                                @endphp
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
                                                    <td class="text-center">
                                                        @if($monAnGroup)
                                                            @php
                                                                $tongSoLuongHienThi = $monAnGroup->sum('so_luong');
                                                            @endphp
                                                            @if($soLuongDaLen == $tongSoLuongHienThi)
                                                                <span class="badge bg-success">
                                                                    <i class="bi bi-check-circle me-1"></i>Đã lên: {{ $soLuongDaLen }}/{{ $tongSoLuongHienThi }}
                                                                </span>
                                                            @elseif($soLuongDaLen > 0)
                                                                <span class="badge bg-warning text-dark">
                                                                    <i class="bi bi-clock-history me-1"></i>Đã lên: {{ $soLuongDaLen }}/{{ $tongSoLuongHienThi }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">
                                                                    <i class="bi bi-hourglass-split me-1"></i>Chưa lên: 0/{{ $tongSoLuongHienThi }}
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($coTrongCombo && $soLuongVuot == 0)
                                                            {{-- Món combo chưa vượt giới hạn: hiển thị 0 đ --}}
                                                            <span class="text-success">0 đ</span>
                                                            <br><small class="text-muted">(Đã bao gồm trong combo)</small>
                                                        @elseif($mon['don_gia'] > 0 || $coMonChuaNauXong || $soLuongVuot > 0)
                                                            {{-- Món vượt giới hạn hoặc món gọi thêm: hiển thị chi tiết --}}
                                                            <div>
                                                                <small class="text-muted">Giá gốc: {{ number_format($donGiaGoc) }} đ</small>
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
                                                                                        Đang nấu dở ({{ $soLuongDangCheBienTrongVuot }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongDangCheBienTrongVuot) }} đ
                                                                                        @if($soLuongChoBepTrongVuot > 0)
                                                                                            <br>
                                                                                            Chờ bếp ({{ $soLuongChoBepTrongVuot }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongChoBepTrongVuot) }} đ
                                                                                        @endif
                                                                                    @elseif($soLuongChoBepTrongVuot > 0)
                                                                                        Chờ bếp ({{ $soLuongChoBepTrongVuot }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongChoBepTrongVuot) }} đ
                                                                                    @endif
                                                                                @endif
                                                                            @elseif($soLuongChuaNauXongTrongVuot > 0)
                                                                                @if($soLuongDangCheBienTrongVuot > 0)
                                                                                    Đang nấu dở ({{ $soLuongDangCheBienTrongVuot }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongDangCheBienTrongVuot) }} đ
                                                                                    @if($soLuongChoBepTrongVuot > 0)
                                                                                        <br>
                                                                                        Chờ bếp ({{ $soLuongChoBepTrongVuot }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongChoBepTrongVuot) }} đ
                                                                                    @endif
                                                                                @elseif($soLuongChoBepTrongVuot > 0)
                                                                                    Chờ bếp ({{ $soLuongChoBepTrongVuot }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongChoBepTrongVuot) }} đ
                                                                                @endif
                                                                            @endif
                                                                        @elseif(!$coTrongCombo)
                                                                            @if($soLuongDaLen > 0)
                                                                                Đã nấu xong ({{ $soLuongDaLen }}): 100% = {{ number_format($donGiaGoc * $soLuongDaLen) }} đ
                                                                                @if($soLuongChuaNauXong > 0)
                                                                                    <br>
                                                                                    @if($soLuongDangCheBien > 0)
                                                                                        Đang nấu dở ({{ $soLuongDangCheBien }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongDangCheBien) }} đ
                                                                                        @if($soLuongChoBep > 0)
                                                                                            <br>
                                                                                            Chờ bếp ({{ $soLuongChoBep }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongChoBep) }} đ
                                                                                        @endif
                                                                                    @elseif($soLuongChoBep > 0)
                                                                                        Chờ bếp ({{ $soLuongChoBep }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongChoBep) }} đ
                                                                                    @endif
                                                                                @endif
                                                                            @elseif($soLuongChuaNauXong > 0)
                                                                                @if($soLuongDangCheBien > 0)
                                                                                    Đang nấu dở ({{ $soLuongDangCheBien }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongDangCheBien) }} đ
                                                                                    @if($soLuongChoBep > 0)
                                                                                        <br>
                                                                                        Chờ bếp ({{ $soLuongChoBep }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongChoBep) }} đ
                                                                                    @endif
                                                                                @elseif($soLuongChoBep > 0)
                                                                                    Chờ bếp ({{ $soLuongChoBep }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongChoBep) }} đ
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
                                                        @if($thanhTienTinhLai > 0)
                                                            {{ number_format($thanhTienTinhLai) }} đ
                                                            @php
                                                                $tongTienMonGoiThemTinhLai += $thanhTienTinhLai;
                                                            @endphp
                                                        @else
                                                            <span class="text-success">0 đ</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                                
                                                {{-- Tổng kết --}}
                                                @php
                                                    // Tính lại tổng tiền combo từ chiTietDatBan với logic giảm 50% cho trẻ em
                                                    $tongTienComboTinhLai = 0;
                                                    $soTreEm = $hoaDon->datBan->tre_em ?? 0;
                                                    $comboIndex = 0;
                                                    if($hoaDon->datBan->chiTietDatBan && $hoaDon->datBan->chiTietDatBan->count() > 0) {
                                                        foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo) {
                                                            if($chiTietCombo->combo) {
                                                                $giaCombo = $chiTietCombo->combo->gia_co_ban ?? 0;
                                                                $soLuongCombo = $chiTietCombo->so_luong ?? 1;
                                                                
                                                                // Giảm 50% cho số combo đầu tiên tương ứng với số trẻ em
                                                                if($soTreEm > 0 && $comboIndex < $soTreEm) {
                                                                    $giaCombo = $giaCombo * 0.5;
                                                                }
                                                                
                                                                $tongTienComboTinhLai += $giaCombo * $soLuongCombo;
                                                                $comboIndex += $soLuongCombo;
                                                            }
                                                        }
                                                    } else {
                                                        // Fallback: dùng giá trị từ database nếu không có chiTietDatBan
                                                        $tongTienComboTinhLai = $chiTiet->tong_tien_combo ?? 0;
                                                    }
                                                    
                                                    // Sử dụng tổng tiền món gọi thêm đã tính lại
                                                    $tongTienMonGoiThem = $tongTienMonGoiThemTinhLai;
                                                    // Tổng cộng = combo chính (đã tính lại) + món gọi thêm
                                                    $tongCong = $tongTienComboTinhLai + $tongTienMonGoiThem;
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
                                            // Sử dụng tổng tiền đã tính lại từ phần bảng trên
                                            // $tongTienMonGoiThem và $tongCong đã được tính lại trong phần tổng kết của bảng
                                            $tongTienComboMon = $tongCong; // $tongCong đã bao gồm combo + món gọi thêm đã tính lại
                                            
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