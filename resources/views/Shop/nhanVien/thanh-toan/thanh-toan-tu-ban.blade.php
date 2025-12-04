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
                                    // Ưu tiên lấy từ datBan->nguoi_lon và datBan->tre_em (giá trị đã lưu khi đặt bàn)
                                    $soNguoiLon = $datBan->nguoi_lon ?? 0;
                                    $soTreEm = $datBan->tre_em ?? 0;
                                    
                                    // Nếu không có giá trị trong datBan, tính từ chiTietDatBan dựa trên loại combo
                                    if($soNguoiLon == 0 && $soTreEm == 0 && $datBan->chiTietDatBan && $datBan->chiTietDatBan->count() > 0) {
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
                                                    // Nếu là vip hoặc khuyen_mai, tính vào người lớn
                                                    $soNguoiLon += $soLuong;
                                                }
                                            }
                                        }
                                    }
                                    $tongSoKhach = $soNguoiLon + $soTreEm;
                                @endphp
                                <p class="mb-1"><strong>Số khách:</strong><br><span class="badge bg-info">{{ $tongSoKhach }} người</span></p>
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
                                <p class="mb-2"><strong>Bàn số:</strong><br><span class="fs-4 fw-bold text-primary">{{ $ban->so_ban }}</span></p>
                                <p class="mb-2"><strong>Khu vực:</strong><br>{{ $ban->khuVuc->ten_khu_vuc ?? 'N/A' }} - Tầng {{ $ban->khuVuc->tang ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Sức chứa:</strong><br>{{ $ban->so_ghe }} chỗ</p>
                                <p class="mb-0"><strong>Mã đặt bàn:</strong><br><code>{{ $datBan->ma_dat_ban ?? 'N/A' }}</code></p>
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
                                    <span class="badge bg-light text-dark fs-6">{{ $gioVao->format('d/m/Y H:i') }}</span>
                                </p>
                                <p class="mb-2">
                                    <strong>Giờ ra:</strong><br>
                                    <span class="badge bg-light text-dark fs-6">{{ $gioRa->format('d/m/Y H:i') }}</span>
                                </p>
                                <p class="mb-2">
                                    <strong>Thời gian phục vụ:</strong><br>
                                    <span class="badge bg-success fs-6">{{ floor($thoiGianPhucVu / 60) }} giờ {{ $thoiGianPhucVu % 60 }} phút</span>
                                </p>
                                @if($datBan->comboBuffet && $datBan->comboBuffet->thoi_luong_phut)
                                    @php
                                        $thoiGianQuyDinh = $datBan->comboBuffet->thoi_luong_phut;
                                        $thoiGianMienPhi = $thoiGianQuyDinh + 10; // Thời gian quy định + 10 phút miễn phí
                                        $thoiGianVuot = max(0, $thoiGianPhucVu - $thoiGianMienPhi);
                                    @endphp
                                    <p class="mb-2">
                                        <strong>Thời gian quy định:</strong><br>
                                        <span class="badge bg-info fs-6">{{ floor($thoiGianQuyDinh / 60) }} giờ {{ $thoiGianQuyDinh % 60 }} phút</span>
                                    </p>
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
                                @endif
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
                                        // Lấy số trẻ em từ datBan
                                        $soTreEm = $datBan->tre_em ?? 0;
                                        $soNguoiDaXuLy = 0; // Đếm số người đã xử lý
                                    @endphp
                                    @foreach($datBan->chiTietDatBan as $chiTiet)
                                        @if($chiTiet->combo)
                                        @php
                                            // Tính giá combo: giảm 50% cho từng người đầu tiên tương ứng với số trẻ em
                                            $giaComboGoc = $chiTiet->combo->gia_co_ban;
                                            $soLuongCombo = $chiTiet->so_luong ?? 1;
                                            
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
                                                <i class="bi bi-star-fill me-1 text-warning"></i>{{ $chiTiet->combo->ten_combo }}
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
                                                <i class="bi bi-star-fill me-1 text-warning"></i>{{ $chiTiet->combo->ten_combo }}
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
                                @else
                                <div class="alert alert-warning mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Chưa chọn combo cho bàn này
                                </div>
                                @endif

                                {{-- Danh sách món đã gọi --}}
                                @php
                                    $hasMonAn = false;
                                    $monAnList = collect();
                                    
                                    // Lấy tất cả món từ các order (bao gồm cả món đã hủy)
                                    foreach($datBan->orderMon as $order) {
                                        foreach($order->chiTietOrders as $ct) {
                                            $hasMonAn = true;
                                            $monAnList->push($ct);
                                        }
                                    }
                                    
                                    // Tính tổng tiền combo: tính từng combo với số lượng tương ứng
                                    // Giảm 50% cho từng người đầu tiên tương ứng với số trẻ em
                                    $tongTienComboChinh = 0;
                                    $soTreEm = $datBan->tre_em ?? 0;
                                    $soNguoiDaXuLy = 0; // Đếm số người đã xử lý
                                    foreach($datBan->chiTietDatBan as $chiTiet) {
                                        if($chiTiet->combo) {
                                            $giaComboGoc = $chiTiet->combo->gia_co_ban ?? 0;
                                            $soLuongCombo = $chiTiet->so_luong ?? 1;
                                            
                                            // Tính số người được giảm giá trong combo này
                                            $soNguoiDuocGiam = 0;
                                            if($soTreEm > 0 && $soNguoiDaXuLy < $soTreEm) {
                                                // Số người được giảm = min(số trẻ em còn lại, số lượng combo này)
                                                $soTreEmConLai = $soTreEm - $soNguoiDaXuLy;
                                                $soNguoiDuocGiam = min($soTreEmConLai, $soLuongCombo);
                                            }
                                            
                                            // Tính thành tiền: người được giảm giá + người không giảm giá
                                            $soNguoiKhongGiam = $soLuongCombo - $soNguoiDuocGiam;
                                            $thanhTienCombo = ($giaComboGoc * 0.5 * $soNguoiDuocGiam) + ($giaComboGoc * $soNguoiKhongGiam);
                                            
                                            $tongTienComboChinh += $thanhTienCombo;
                                            $soNguoiDaXuLy += $soLuongCombo; // Tăng số người đã xử lý
                                        }
                                    }
                                    
                                    // Tính tiền món gọi thêm và món combo vượt giới hạn (sẽ tính trong vòng lặp hiển thị)
                                    $tongTienGoiThem = 0;
                                @endphp
                                
                                @if($hasMonAn && $monAnList->isNotEmpty())
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
                                                
                                                // Món combo không có giới hạn nữa, chỉ cần kiểm tra xem món có trong combo không
                                                // Tính tổng số lượng đã order cho từng món (cả combo và goi_them)
                                                $tongSoLuongMon = [];
                                                foreach($monAnList as $ct) {
                                                    $monAnId = $ct->mon_an_id;
                                                    if(!isset($tongSoLuongMon[$monAnId])) {
                                                        $tongSoLuongMon[$monAnId] = 0;
                                                    }
                                                    $tongSoLuongMon[$monAnId] += $ct->so_luong;
                                                }
                                            @endphp
                                            {{-- Hiển thị TẤT CẢ các món đã gọi --}}
                                            @php
                                                // Nhóm món theo mon_an_id để hiển thị gộp
                                                $monAnGrouped = $monAnList->groupBy('mon_an_id');
                                            @endphp
                                            @foreach($monAnGrouped as $monAnId => $monAnGroup)
                                                @php
                                                    $ctFirst = $monAnGroup->first();
                                                    $tongSoLuong = $monAnGroup->sum('so_luong');
                                                    
                                                    // Tính số lượng món theo trạng thái
                                                    $soLuongDaLen = 0; // Đã lên món (da_len_mon) - tính 100%
                                                    $soLuongChoBep = 0; // Chờ bếp (cho_bep) - tính 0%
                                                    $soLuongDangCheBien = 0; // Đang chế biến (dang_che_bien) - tính 30%
                                                    $soLuongChuaNauXong = 0; // Tổng chưa nấu xong (cho_bep + dang_che_bien)
                                                    $soLuongHuy = 0; // Đã hủy (huy_mon) - tính 0%
                                                    
                                                    // Món combo không có giới hạn nữa
                                                    $tongGioiHan = null;
                                                    $soLuongVuot = 0;
                                                    
                                                    // Tính số lượng đã lên và chưa nấu xong (tổng)
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
                                                    
                                                // Món combo không có giới hạn nữa, không cần tính phần vượt
                                                $soLuongDaLenTrongVuot = 0;
                                                $soLuongChoBepTrongVuot = 0;
                                                $soLuongDangCheBienTrongVuot = 0;
                                                $soLuongChuaNauXongTrongVuot = 0;
                                                    
                                                    // Kiểm tra xem món có trong combo nào không
                                                    $coTrongCombo = false;
                                                    
                                                    // Tìm món trong combo
                                                    foreach($datBan->chiTietDatBan as $chiTiet) {
                                                        if($chiTiet->combo) {
                                                            $monTrongComboItem = \App\Models\MonTrongCombo::where('combo_id', $chiTiet->combo->id)
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
                                                    $soLuongHienThi = $tongSoLuong;
                                                    $coPhuPhi = false;
                                                    $tienPhuPhi = 0; // Khởi tạo biến phụ phí
                                                    $donGiaGoc = $ctFirst->monAn->gia ?? 0;
                                                    $coMonChuaNauXong = $soLuongChuaNauXong > 0 || $soLuongChuaNauXongTrongVuot > 0;
                                                    
                                                    if($coTrongCombo) {
                                                        // Món thuộc combo: luôn miễn phí (không có giới hạn, không có phụ phí)
                                                        $donGiaHienThi = 0;
                                                        $tienMon = 0;
                                                        $tienPhuPhi = 0;
                                                        $coPhuPhi = false;
                                                    } else {
                                                        // Món không thuộc combo: tính tiền theo trạng thái nấu
                                                        // Phần đã nấu xong: 100% giá
                                                        $tienMonDaLen = $donGiaGoc * $soLuongDaLen;
                                                        
                                                        // Phần đang chế biến: 30% giá
                                                        $tienMonDangCheBien = $donGiaGoc * 0.3 * $soLuongDangCheBien;
                                                        
                                                        // Phần chờ bếp: 0 đồng (miễn phí)
                                                        $tienMonChoBep = 0;
                                                        
                                                        // Phần đã hủy: 0 đồng (không tính tiền)
                                                        $tienMonHuy = 0;
                                                        
                                                        // Tổng tiền (không tính món hủy)
                                                        $tienMon = $tienMonDaLen + $tienMonDangCheBien + $tienMonChoBep + $tienMonHuy;
                                                        
                                                        // Hiển thị đơn giá trung bình (chỉ tính cho món không hủy)
                                                        $soLuongKhongHuy = $tongSoLuong - $soLuongHuy;
                                                        if($soLuongKhongHuy > 0) {
                                                            $donGiaHienThi = $tienMon / $soLuongKhongHuy;
                                                        } else {
                                                            $donGiaHienThi = 0; // Tất cả đã hủy
                                                        }
                                                        
                                                        $tongTienGoiThem += $tienMon;
                                                        $tienPhuPhi = 0;
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
                                                    <td class="text-center">
                                                        {{ $soLuongHienThi }}
                                                    </td>
                                                    <td class="text-center">
                                                        @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuong)
                                                            <span class="badge bg-danger">
                                                                <i class="bi bi-x-circle me-1"></i>Đã hủy: {{ $soLuongHuy }}/{{ $tongSoLuong }}
                                                            </span>
                                                        @elseif($soLuongHuy > 0)
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="bi bi-clock-history me-1"></i>Đã lên: {{ $soLuongDaLen }}/{{ $tongSoLuong - $soLuongHuy }}
                                                                <br><small class="text-danger">Đã hủy: {{ $soLuongHuy }}</small>
                                                            </span>
                                                        @elseif($soLuongDaLen == $tongSoLuong)
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-check-circle me-1"></i>Đã lên: {{ $soLuongDaLen }}/{{ $tongSoLuong }}
                                                            </span>
                                                        @elseif($soLuongDaLen > 0)
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="bi bi-clock-history me-1"></i>Đã lên: {{ $soLuongDaLen }}/{{ $tongSoLuong }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary">
                                                                <i class="bi bi-hourglass-split me-1"></i>Chưa lên: 0/{{ $tongSoLuong }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuong)
                                                            {{-- Tất cả món đã hủy --}}
                                                            <span class="text-danger">0 đ</span>
                                                            <br><small class="text-muted">(Đã hủy)</small>
                                                        @elseif($donGiaHienThi > 0)
                                                            @php
                                                                $donGiaGocHienThi = $ctFirst->monAn->gia ?? 0;
                                                            @endphp
                                                            <div>
                                                                <strong>Giá gốc: {{ number_format($donGiaGocHienThi) }} đ</strong>
                                                                @if($soLuongHuy > 0)
                                                                    <br><small class="text-danger">Đã hủy ({{ $soLuongHuy }}): 0 đ</small>
                                                                @endif
                                                                @if($coMonChuaNauXong)
                                                                    <br>
                                                                    <small class="text-warning">
                                                                        <i class="bi bi-info-circle me-1"></i>
                                                                        @if(!$coTrongCombo)
                                                                            {{-- Món gọi thêm --}}
                                                                            @if($soLuongDaLen > 0)
                                                                                Đã nấu xong ({{ $soLuongDaLen }}): 100% = {{ number_format($donGiaGocHienThi * $soLuongDaLen) }} đ
                                                                                @if($soLuongChuaNauXong > 0)
                                                                                    <br>
                                                                                    @if($soLuongDangCheBien > 0)
                                                                                        Đang nấu dở ({{ $soLuongDangCheBien }}): 30% = {{ number_format($donGiaGocHienThi * 0.3 * $soLuongDangCheBien) }} đ
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
                                                                                    Đang nấu dở ({{ $soLuongDangCheBien }}): 30% = {{ number_format($donGiaGocHienThi * 0.3 * $soLuongDangCheBien) }} đ
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
                                                        @else
                                                            <span class="text-success">0 đ</span>
                                                            <br><small class="text-muted">(Đã bao gồm trong combo)</small>
                                                        @endif
                                                    </td>

                                                    <td class="text-end fw-bold">
                                                        @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuong)
                                                            {{-- Tất cả món đã hủy --}}
                                                            <span class="text-danger">0 đ</span>
                                                        @elseif($tienMon > 0)
                                                            {{ number_format($tienMon) }} đ
                                                        @else
                                                            <span class="text-success">0 đ</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            
                                            {{-- Tổng kết --}}
                                            @php
                                                // Tính lại tổng tiền từ bảng để đảm bảo chính xác
                                                $tongTienThucTe = $tongTienComboChinh + $tongTienGoiThem;
                                            @endphp
                                            @if($tongTienComboChinh > 0)
                                            <tr class="table-warning fw-bold">
                                                <td colspan="5" class="text-end">
                                                    <i class="bi bi-star-fill text-warning me-1"></i>Tổng tiền combo chính:
                                                </td>
                                                <td class="text-end text-primary fs-5">{{ number_format($tongTienComboChinh) }} đ</td>
                                            </tr>
                                            @endif
                                            @if($tongTienGoiThem > 0)
                                            <tr class="table-secondary fw-bold">
                                                <td colspan="5" class="text-end">Tổng tiền món gọi thêm:</td>
                                                <td class="text-end text-info">{{ number_format($tongTienGoiThem) }} đ</td>
                                            </tr>
                                            @endif
                                            <tr class="table-primary fw-bold fs-5">
                                                <td colspan="5" class="text-end">TỔNG CỘNG:</td>
                                                <td class="text-end text-danger" id="tongTienTuBang" data-tong-tien="{{ $tongTienThucTe }}">{{ number_format($tongTienThucTe) }} đ</td>
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
                                <form action="{{ route('nhanVien.thanh-toan.luu-ban', $ban->id) }}" method="POST" id="thanhToanForm">
                                    @csrf

                                    {{-- Phương thức thanh toán --}}
                                    <div class="mb-4">
                                        <label class="form-label fw-bold" style="color: #333 !important;"><i class="bi bi-credit-card me-2"></i>Phương thức thanh toán <span class="text-danger">*</span></label>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="form-check border rounded p-3 h-100 payment-method-card">
                                                    <input class="form-check-input" type="radio" name="phuong_thuc_tt" id="tien_mat" value="tien_mat" checked>
                                                    <label class="form-check-label w-100" for="tien_mat">
                                                        <i class="bi bi-cash-stack text-success fs-3 d-block mb-2"></i>
                                                        <strong>Tiền mặt</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check border rounded p-3 h-100 payment-method-card">
                                                    <input class="form-check-input" type="radio" name="phuong_thuc_tt" id="chuyen_khoan" value="chuyen_khoan">
                                                    <label class="form-check-label w-100" for="chuyen_khoan">
                                                        <i class="bi bi-bank text-primary fs-3 d-block mb-2"></i>
                                                        <strong>Chuyển khoản</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check border rounded p-3 h-100 payment-method-card">
                                                    <input class="form-check-input" type="radio" name="phuong_thuc_tt" id="the_ATM" value="the_ATM">
                                                    <label class="form-check-label w-100" for="the_ATM">
                                                        <i class="bi bi-credit-card-2-front text-info fs-3 d-block mb-2"></i>
                                                        <strong>Thẻ ATM</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check border rounded p-3 h-100 payment-method-card">
                                                    <input class="form-check-input" type="radio" name="phuong_thuc_tt" id="vnpay" value="vnpay">
                                                    <label class="form-check-label w-100" for="vnpay">
                                                        <i class="bi bi-credit-card text-warning fs-3 d-block mb-2"></i>
                                                        <strong>VNPay</strong>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tiền khách đưa (chỉ hiện khi chọn tiền mặt) --}}
                                    <div class="mb-4" id="tienKhachDuaGroup">
                                        <label for="tien_khach_dua" class="form-label fw-bold" style="color: #333 !important;">Tiền khách đưa</label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" class="form-control" id="tien_khach_dua" name="tien_khach_dua" min="0" step="1000" placeholder="Nhập số tiền">
                                            <span class="input-group-text">đ</span>
                                        </div>
                                        <div id="tienTraLai" class="mt-2 fw-bold" style="display: none;"></div>
                                    </div>

                                    {{-- Voucher --}}
                                    <div class="mb-4">
                                        <label for="voucher_id" class="form-label fw-bold" style="color: #333 !important;"><i class="bi bi-ticket-perforated me-2"></i>Voucher (nếu có)</label>
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

                                    {{-- Phụ thu tự động --}}
                                    @if($phuThuTuDong > 0)
                                    <div class="mb-3 p-3 bg-warning bg-opacity-10 rounded border border-warning">
                                        <label class="form-label fw-bold text-dark">
                                            <i class="bi bi-clock-history me-2"></i>Phụ thu tự động:
                                        </label>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                @if($phuThuThoiGian > 0)
                                                    <small class="text-muted d-block">
                                                        - Thời gian vượt quá: {{ floor($thoiGianVuot / 60) }} giờ {{ $thoiGianVuot % 60 }} phút
                                                        ({{ $soLan10Phut }} × 10 phút = {{ number_format($phuThuThoiGian) }} đ)
                                                    </small>
                                                @endif
                                                @if($phuThuTuDong > $phuThuThoiGian)
                                                    <small class="text-muted d-block">- Món gọi quá giới hạn: {{ number_format($phuThuTuDong - $phuThuThoiGian) }} đ</small>
                                                @endif
                                            </div>
                                            <strong class="text-danger fs-5" id="phuThuTuDong">{{ number_format($phuThuTuDong) }} đ</strong>
                                        </div>
                                        @if($phuThuThoiGian > 0 && isset($thoiGianQuyDinh) && isset($thoiGianMienPhi))
                                        <div class="mt-2 p-2 bg-light rounded border-start border-3 border-info">
                                            <small class="text-dark d-block mb-2">
                                                <i class="bi bi-info-circle me-1 text-info"></i><strong>Công thức tính phụ thu:</strong>
                                            </small>
                                            <small class="text-muted d-block mb-1">
                                                <strong>1.</strong> Thời gian miễn phí = Thời gian quy định + 10 phút
                                                @if($thoiGianQuyDinh > 0)
                                                    <br><span class="ms-3 text-primary">= {{ floor($thoiGianQuyDinh / 60) }} giờ {{ $thoiGianQuyDinh % 60 }} phút + 10 phút = {{ floor($thoiGianMienPhi / 60) }} giờ {{ $thoiGianMienPhi % 60 }} phút</span>
                                                @endif
                                            </small>
                                            <small class="text-muted d-block mb-1">
                                                <strong>2.</strong> Thời gian vượt quá = Thời gian phục vụ - Thời gian miễn phí
                                                <br><span class="ms-3 text-primary">= {{ floor($thoiGianPhucVu / 60) }} giờ {{ $thoiGianPhucVu % 60 }} phút - {{ floor($thoiGianMienPhi / 60) }} giờ {{ $thoiGianMienPhi % 60 }} phút = {{ floor($thoiGianVuot / 60) }} giờ {{ $thoiGianVuot % 60 }} phút</span>
                                            </small>
                                            <small class="text-muted d-block mb-1">
                                                <strong>3.</strong> Số lần 10 phút = Làm tròn lên (Thời gian vượt quá ÷ 10 phút)
                                                <br><span class="ms-3 text-primary">= Làm tròn lên ({{ $thoiGianVuot }} phút ÷ 10) = {{ $soLan10Phut }} lần</span>
                                            </small>
                                            <small class="text-muted d-block mb-0">
                                                <strong>4.</strong> Phụ thu = Số lần 10 phút × 30,000 đ
                                                <br><span class="ms-3 text-primary">= {{ $soLan10Phut }} × 30,000 đ = {{ number_format($phuThuThoiGian) }} đ</span>
                                            </small>
                                        </div>
                                        @endif
                                    </div>
                                    @endif
                                    
                                    {{-- Phụ thu thủ công (nếu cần thêm) --}}
                                    <div class="mb-4">
                                        <label for="phu_thu" class="form-label fw-bold" style="color: #333 !important;">
                                            <i class="bi bi-plus-circle me-2"></i>Phụ thu thêm (nếu có)
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <input type="number" class="form-control" id="phu_thu" name="phu_thu" value="0" min="0" step="1000">
                                            <span class="input-group-text">đ</span>
                                        </div>
                                        <small class="text-muted">Phụ thu tự động: <strong id="phuThuTuDongText">{{ number_format($phuThuTuDong ?? 0) }} đ</strong></small>
                                    </div>

                                    {{-- Tóm tắt thanh toán --}}
                                    <div class="mb-4 p-4 bg-white rounded-3 border shadow-sm">
                                        <h5 class="mb-3 text-dark fw-bold"><i class="bi bi-calculator me-2"></i>Tóm tắt thanh toán</h5>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">Tổng tiền (Combo + Món):</span>
                                            <strong id="tongTien" class="text-primary fs-5">{{ number_format($tongTienThucTe ?? $tongTienOrder) }} đ</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2" id="tienGiamRow" style="display: none;">
                                            <span class="text-dark">(-) Tiền giảm (Voucher):</span>
                                            <strong class="text-success" id="tienGiam">- 0 đ</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2" id="tongTienSauVoucherRow" style="display: none;">
                                            <span class="text-dark fw-bold">Tổng tiền sau voucher:</span>
                                            <strong id="tongTienSauVoucher" class="text-warning fs-5">0 đ</strong>
                                        </div>
                                        @if($tienCoc > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-dark">(-) Tiền cọc:</span>
                                            <strong class="text-success" id="tienCoc">- {{ number_format($tienCoc) }} đ</strong>
                                        </div>
                                        @endif
                                        <div class="d-flex justify-content-between mb-2" id="phuThuRow" style="display: none;">
                                            <span class="text-dark">(+) Phụ thu:</span>
                                            <strong class="text-danger" id="phuThu">+ {{ number_format($phuThuTuDong ?? 0) }} đ</strong>
                                        </div>
                                        <hr class="my-3">
                                        <div class="d-flex justify-content-between fs-4 fw-bold">
                                            <span class="text-dark">Phải thanh toán:</span>
                                            <span id="phaiThanhToan" class="text-danger">{{ number_format($tongTienOrder - $tienCoc) }} đ</span>
                                        </div>
                                    </div>

                                    {{-- Nút xác nhận --}}
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="{{ route('nhanVien.ban-an.index') }}" class="btn btn-secondary btn-lg px-5">
                                            <i class="bi bi-arrow-left me-2"></i>Quay lại
                                        </a>
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
    // Lấy tổng tiền từ bảng (từ element có id tongTienTuBang) - đây là giá trị TỔNG CỘNG chính xác nhất
    const tongTienTuBangElement = document.getElementById('tongTienTuBang');
    let tongTienOrder = 0;
    
    // Ưu tiên lấy từ data attribute (chính xác nhất) - giá trị TỔNG CỘNG từ bảng
    if (tongTienTuBangElement) {
        const tongTienFromData = tongTienTuBangElement.getAttribute('data-tong-tien');
        if (tongTienFromData) {
            tongTienOrder = parseFloat(tongTienFromData);
        } else {
            // Fallback: lấy từ text content
            const tongTienText = tongTienTuBangElement.textContent.replace(/[^\d]/g, '');
            if (tongTienText && tongTienText.length > 0) {
                tongTienOrder = parseInt(tongTienText);
            }
        }
    }
    
    // Nếu không lấy được từ bảng, dùng giá trị từ PHP (fallback)
    if (!tongTienOrder || tongTienOrder === 0 || isNaN(tongTienOrder)) {
        @php
            // Lấy giá trị TỔNG CỘNG đã tính trong bảng
            $tongTienThucTeValue = isset($tongTienThucTe) ? $tongTienThucTe : ($tongTienOrder ?? 0);
        @endphp
        tongTienOrder = {{ $tongTienThucTeValue }};
    }
    
    // Cập nhật hiển thị tổng tiền ban đầu từ giá trị đã lấy (từ TỔNG CỘNG trong bảng)
    const tongTienElement = document.getElementById('tongTien');
    if (tongTienElement) {
        tongTienElement.textContent = tongTienOrder.toLocaleString('vi-VN') + ' đ';
    }
    
    const tienCoc = {{ $tienCoc }};
    const phuThuTuDong = {{ $phuThuTuDong ?? 0 }};
    const phuongThucTT = document.querySelectorAll('input[name="phuong_thuc_tt"]');
    const tienKhachDuaGroup = document.getElementById('tienKhachDuaGroup');
    const tienKhachDuaInput = document.getElementById('tien_khach_dua');
    const tienTraLaiDiv = document.getElementById('tienTraLai');
    const voucherSelect = document.getElementById('voucher_id');
    const phuThuInput = document.getElementById('phu_thu');
    
    // Kiểm tra phương thức thanh toán mặc định và hiển thị input tiền khách đưa nếu là tiền mặt
    const tienMatRadio = document.getElementById('tien_mat');
    if (tienMatRadio && tienMatRadio.checked) {
        tienKhachDuaGroup.style.display = 'block';
    } else {
        tienKhachDuaGroup.style.display = 'none';
    }
    
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
        // Lấy lại tổng tiền từ bảng để đảm bảo chính xác
        let tongTien = tongTienOrder;
        const tongTienTuBangElement = document.getElementById('tongTienTuBang');
        if (tongTienTuBangElement) {
            const tongTienFromData = tongTienTuBangElement.getAttribute('data-tong-tien');
            if (tongTienFromData) {
                tongTien = parseFloat(tongTienFromData);
            } else {
                const tongTienText = tongTienTuBangElement.textContent.replace(/[^\d]/g, '');
                if (tongTienText && tongTienText.length > 0) {
                    tongTien = parseInt(tongTienText);
                }
            }
        }
        
        let tienGiam = 0;
        let phuThu = parseFloat(phuThuInput.value) || 0;
        
        // Tính tiền giảm từ voucher (trừ vào tổng tiền)
        const selectedVoucher = voucherSelect.options[voucherSelect.selectedIndex];
        if (selectedVoucher && selectedVoucher.value) {
            const loaiGiam = selectedVoucher.getAttribute('data-loai');
            const giaTri = parseFloat(selectedVoucher.getAttribute('data-gia-tri')) || 0;
            const giaTriToiDa = parseFloat(selectedVoucher.getAttribute('data-gia-tri-toi-da')) || null;
            
            console.log('Voucher selected:', {
                loaiGiam: loaiGiam,
                giaTri: giaTri,
                giaTriToiDa: giaTriToiDa,
                tongTien: tongTien
            });
            
            if (loaiGiam === 'phan_tram') {
                tienGiam = tongTien * (giaTri / 100);
                if (giaTriToiDa && tienGiam > giaTriToiDa) {
                    tienGiam = giaTriToiDa;
                }
            } else if (loaiGiam === 'tien_mat' || loaiGiam === 'tien') {
                tienGiam = giaTri;
            } else {
                tienGiam = giaTri;
            }
            
            // Không được giảm quá tổng tiền
            if (tienGiam > tongTien) {
                tienGiam = tongTien;
            }
            
            if (tienGiam > 0) {
                document.getElementById('tienGiamRow').style.display = 'flex';
                document.getElementById('tienGiam').textContent = '- ' + tienGiam.toLocaleString('vi-VN') + ' đ';
                document.getElementById('tienGiam').style.color = '#28a745';
            } else {
                document.getElementById('tienGiamRow').style.display = 'none';
            }
        } else {
            document.getElementById('tienGiamRow').style.display = 'none';
        }
        
        // Tính tổng tiền sau voucher
        const tongTienSauVoucher = tongTien - tienGiam;
        
        // Hiển thị tổng tiền sau voucher (luôn hiển thị nếu có voucher)
        if (selectedVoucher && selectedVoucher.value) {
            document.getElementById('tongTienSauVoucherRow').style.display = 'flex';
            document.getElementById('tongTienSauVoucher').textContent = tongTienSauVoucher.toLocaleString('vi-VN') + ' đ';
            document.getElementById('tongTienSauVoucher').style.color = '#FFD700';
        } else {
            document.getElementById('tongTienSauVoucherRow').style.display = 'none';
        }
        
        // Tính phụ thu (tự động + thủ công)
        const phuThuThucCong = parseFloat(phuThuInput.value) || 0;
        const phuThuTotal = phuThuTuDong + phuThuThucCong;
        
        // Hiển thị phụ thu
        if (phuThuTotal > 0) {
            document.getElementById('phuThuRow').style.display = 'flex';
            document.getElementById('phuThu').textContent = '+ ' + phuThuTotal.toLocaleString('vi-VN') + ' đ';
            document.getElementById('phuThu').style.color = '#FFB6C1';
        } else {
            document.getElementById('phuThuRow').style.display = 'none';
        }
        
        // Tính phải thanh toán: Tổng tiền - Voucher - Tiền cọc + Phụ thu
        // Thứ tự: Trừ voucher trước, sau đó trừ tiền cọc, rồi cộng phụ thu
        const phaiThanhToan = tongTienSauVoucher - tienCoc + phuThuTotal;
        const phaiThanhToanFinal = phaiThanhToan < 0 ? 0 : phaiThanhToan;
        document.getElementById('phaiThanhToan').textContent = phaiThanhToanFinal.toLocaleString('vi-VN') + ' đ';
        document.getElementById('phaiThanhToan').style.color = '#dc3545';
        
        // Tính tiền trả lại (nếu thanh toán tiền mặt)
        if (document.getElementById('tien_mat').checked) {
            const tienKhachDua = parseFloat(tienKhachDuaInput.value) || 0;
            if (tienKhachDua > 0) {
                const tienTraLai = tienKhachDua - phaiThanhToanFinal;
                if (tienTraLai > 0) {
                    tienTraLaiDiv.style.display = 'block';
                    tienTraLaiDiv.className = 'mt-2 text-success fw-bold';
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


    document.getElementById('thanhToanForm').addEventListener('submit', function(e){
        const vnpayRadio = document.getElementById('vnpay');
        if(vnpayRadio.checked){
            e.preventDefault();
            // Gửi form qua route VNPay
            this.action = '{{ route("nhanVien.thanh-toan.vnpay.payment", $ban->id) }}';
            this.submit();
        }
    });

});
</script>

<style>
.payment-method-card {
    cursor: pointer;
    transition: all 0.3s;
}

.payment-method-card:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.form-check-input:checked ~ .form-check-label {
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