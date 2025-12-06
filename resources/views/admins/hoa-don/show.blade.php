@extends('layouts.admins.layout-admin')

@section('title', 'Chi tiết hóa đơn')

@section('content')
<main class="app-content">
    <div class="app-title d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fa fa-file-invoice-dollar"></i> Hóa đơn #{{ $hoaDon->ma_hoa_don }}</h1>
        <a href="{{ route('admin.hoa-don.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    @php
        // ----- SỬ DỤNG DỮ LIỆU ĐÃ LƯU TRONG CHI_TIET_HOA_DON -----
        $chiTiet = $hoaDon->chiTietHoaDon;
        
        // Thông tin khách hàng - ưu tiên từ chi_tiet_hoa_don
        $tenKhach = $chiTiet->ten_khach ?? $hoaDon->datBan->ten_khach ?? 'N/A';
        $sdtKhach = $chiTiet->sdt_khach ?? $hoaDon->datBan->sdt_khach ?? 'N/A';
        $emailKhach = $chiTiet->email_khach ?? $hoaDon->datBan->email_khach ?? 'N/A';
        $soKhach = $chiTiet->so_khach ?? $hoaDon->datBan->so_khach ?? 0;
        $nguoiLon = $chiTiet->nguoi_lon ?? $hoaDon->datBan->nguoi_lon ?? 0;
        $treEm = $chiTiet->tre_em ?? $hoaDon->datBan->tre_em ?? 0;
        
        // Thông tin bàn - ưu tiên từ chi_tiet_hoa_don
        $banSo = $chiTiet->ban_so ?? $hoaDon->datBan->banAn->so_ban ?? 'N/A';
        $khuVuc = $chiTiet->khu_vuc ?? $hoaDon->datBan->banAn->khuVuc->ten_khu_vuc ?? 'N/A';
        $tang = $chiTiet->tang ?? $hoaDon->datBan->banAn->khuVuc->tang ?? null;
        $soGhe = $chiTiet->so_ghe ?? $hoaDon->datBan->banAn->so_ghe ?? 'N/A';
        $maDatBan = $chiTiet->ma_dat_ban ?? $hoaDon->datBan->ma_dat_ban ?? 'N/A';
        
        // Thời gian phục vụ - ưu tiên từ chi_tiet_hoa_don
        $gioVao = $chiTiet->gio_vao ?? ($hoaDon->datBan->gio_den ? \Carbon\Carbon::parse($hoaDon->datBan->gio_den) : null);
        $gioRa = $chiTiet->gio_ra ?? $hoaDon->created_at;
        $thoiGianPhucVuPhut = $chiTiet->thoi_gian_phuc_vu_phut ?? ($gioVao ? $gioVao->diffInMinutes($gioRa) : 0);
        
        // Tổng tiền - sử dụng từ chi_tiet_hoa_don
        $tongTienCombo = $chiTiet->tong_tien_combo ?? 0;
        $tongTienMonGoiThem = $chiTiet->tong_tien_mon_goi_them ?? 0;
        $tongTienMonHienThi = $chiTiet->tong_tien_combo_mon ?? $hoaDon->tong_tien ?? 0;
        
        // Phải thanh toán - sử dụng từ chi_tiet_hoa_don
        $phaiThanhToanTinhLai = $chiTiet->phai_thanh_toan ?? null;
        if($phaiThanhToanTinhLai === null) {
            // Fallback: tính từ hoa_don
            $phaiThanhToanTinhLai = $tongTienMonHienThi - ($hoaDon->tien_giam ?? 0) + ($hoaDon->phu_thu ?? 0) - ($hoaDon->datBan->tien_coc ?? 0);
            if($phaiThanhToanTinhLai < 0) $phaiThanhToanTinhLai = 0;
        }
        
        // Danh sách món - sử dụng từ chi_tiet_hoa_don nếu có
        $danhSachMon = $chiTiet->danh_sach_mon ?? null;
    @endphp

    <div class="row">
        <div class="col-md-5">
            {{-- Thông tin khách hàng --}}
            <div class="tile mb-4">
                <h3 class="tile-title">Thông tin khách hàng</h3>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <strong>Tên khách:</strong>
                        <span>{{ $tenKhach }}</span>
                    </div>
                    <div class="list-group-item">
                        <strong>SĐT:</strong>
                        <span>{{ $sdtKhach }}</span>
                    </div>
                    <div class="list-group-item">
                        <strong>Email:</strong>
                        <span>{{ $emailKhach }}</span>
                    </div>
                    <div class="list-group-item">
                        <strong>Số khách:</strong><br>
                        <span class="badge bg-info">{{ $soKhach }} người</span>
                        @if($nguoiLon || $treEm)
                        <br><small class="text-muted">
                            - Người lớn: <strong>{{ $nguoiLon }}</strong> người<br>
                            - Trẻ em: <strong>{{ $treEm }}</strong> người
                        </small>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Thông tin bàn --}}
            <div class="tile mb-4">
                <h3 class="tile-title">Thông tin bàn</h3>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <strong>Bàn số:</strong>
                        <span class="fs-4 fw-bold text-primary">{{ $banSo }}</span>
                    </div>
                    <div class="list-group-item">
                        <strong>Khu vực:</strong>
                        <span>{{ $khuVuc }}</span>
                        @if($tang)
                        - Tầng {{ $tang }}
                        @endif
                    </div>
                    <div class="list-group-item">
                        <strong>Sức chứa:</strong>
                        <span>{{ $soGhe }} chỗ</span>
                    </div>
                    <div class="list-group-item">
                        <strong>Mã đặt bàn:</strong><br>
                        <code>{{ $maDatBan }}</code>
                    </div>
                </div>
            </div>

            {{-- Thời gian phục vụ --}}
            <div class="tile mb-4">
                <h3 class="tile-title">Thời gian phục vụ</h3>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <strong>Giờ vào:</strong><br>
                        <span class="badge bg-light text-dark fs-6">
                            {{ $gioVao ? $gioVao->format('d/m/Y H:i') : 'N/A' }}
                        </span>
                    </div>
                    <div class="list-group-item">
                        <strong>Giờ ra:</strong><br>
                        <span class="badge bg-light text-dark fs-6">
                            {{ $gioRa ? $gioRa->format('d/m/Y H:i') : 'N/A' }}
                        </span>
                    </div>
                    <div class="list-group-item">
                        <strong>Thời gian phục vụ:</strong><br>
                        <span class="badge bg-success fs-6">
                            {{ floor($thoiGianPhucVuPhut / 60) }} giờ {{ $thoiGianPhucVuPhut % 60 }} phút
                        </span>
                    </div>
                </div>
            </div>

            {{-- Phương thức thanh toán --}}
            <div class="tile mb-4">
                <h3 class="tile-title">Phương thức thanh toán</h3>
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <strong>Phương thức:</strong><br>
                        <span class="badge bg-primary">
                            @if($hoaDon->phuong_thuc_tt == 'tien_mat')
                                Tiền mặt
                            @elseif($hoaDon->phuong_thuc_tt == 'chuyen_khoan')
                                Chuyển khoản
                            @else
                                {{ $hoaDon->phuong_thuc_tt ?? 'N/A' }}
                            @endif
                        </span>
                    </div>
                    <div class="list-group-item">
                        <strong>Ngày tạo hóa đơn:</strong><br>
                        <span>{{ $hoaDon->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="tile">
                <h3 class="tile-title">Chi tiết Thanh toán</h3>
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between">
                        <span>Tổng tiền món:</span>
                        <span>{{ number_format($tongTienMonHienThi ?? 0) }}₫</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between text-muted">
                        <span>(-) Tiền cọc:</span>
                        <span>{{ number_format($hoaDon->datBan->tien_coc ?? 0) }}₫</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between text-success">
                        <span>(-) Voucher:</span>
                        <span>
                            @if($hoaDon->voucher)
                                {{ $hoaDon->voucher->ma_voucher }}
                            @else
                                Không áp dụng
                            @endif
                        </span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between text-success">
                        <span>(-) Tiền giảm:</span>
                        <span>{{ number_format($hoaDon->tien_giam ?? 0) }}₫</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between text-danger">
                        <span>(+) Phụ thu:</span>
                        <span>{{ number_format($hoaDon->phu_thu ?? 0) }}₫</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between fw-bold fs-5 bg-light">
                        <span>Phải thanh toán:</span>
                        <span>
                            @php
                                // Tính lại phải thanh toán dựa trên tổng tiền món đã tính lại
                                $phaiThanhToanTinhLai = $tongTienMonHienThi - ($hoaDon->tien_giam ?? 0) + ($hoaDon->phu_thu ?? 0) - ($hoaDon->datBan->tien_coc ?? 0);
                                if($phaiThanhToanTinhLai < 0) $phaiThanhToanTinhLai = 0;
                            @endphp
                            {{ number_format($phaiThanhToanTinhLai) }}₫
                        </span>
                    </div>
                    @php
                        // Lấy tiền khách đưa và tiền trả lại từ chi tiết hóa đơn
                        $tienKhachDua = $chiTiet->tien_khach_dua ?? null;
                        $tienTraLai = $chiTiet->tien_tra_lai ?? 0;
                    @endphp
                    @if($tienKhachDua && $tienKhachDua > 0)
                    <div class="list-group-item d-flex justify-content-between">
                        <span>Tiền khách đưa:</span>
                        <span>{{ number_format($tienKhachDua) }}₫</span>
                    </div>
                    @if($tienTraLai > 0)
                    <div class="list-group-item d-flex justify-content-between text-success">
                        <span>Tiền trả lại:</span>
                        <span>{{ number_format($tienTraLai) }}₫</span>
                    </div>
                    @endif
                    @endif
                    <div class="list-group-item d-flex justify-content-between fw-bold fs-5 text-success">
                        <span>Đã thanh toán:</span>
                        <span>{{ number_format($phaiThanhToanTinhLai) }}₫</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="tile">
                <h3 class="tile-title">Giải thích "Tổng tiền món" ({{ number_format($hoaDon->tong_tien ?? 0) }}₫)</h3>
                
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
                @endphp
                
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
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
                            @foreach($monAnGrouped as $monAnId => $monAnGroup)
                            @php
                                $ctFirst = $monAnGroup->first();
                                $tongSoLuong = $monAnGroup->sum('so_luong');
                                
                                // Xác định món có thuộc combo không
                                $coTrongCombo = false;
                                $soLuongVuot = 0;
                                $tongGioiHan = $tongGioiHanMon[$monAnId] ?? null;
                                if($tongGioiHan !== null && $tongGioiHan > 0) {
                                    $coTrongCombo = true;
                                    $soLuongVuot = max(0, $tongSoLuong - $tongGioiHan);
                                } else {
                                    // Kiểm tra xem món có trong chi tiết đặt bàn không
                                    foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo) {
                                        if($chiTietCombo->combo) {
                                            $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $chiTietCombo->combo->id)
                                                ->where('mon_an_id', $monAnId)
                                                ->first();
                                            if($monTrongCombo) {
                                                $coTrongCombo = true;
                                                break;
                                            }
                                        }
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
                                
                                // Tính lại phụ phí dựa trên số lượng đã lên bàn
                                $tienPhuPhiTinhLai = 0;
                                $phuPhiDonVi = $phuPhiMon[$monAnId] ?? 0;
                                if($coTrongCombo && $soLuongVuot > 0 && $phuPhiDonVi > 0) {
                                    $tienPhuPhiTinhLai = $phuPhiDonVi * $soLuongDaLenTrongVuot;
                                }
                                
                                // Tính lại thành tiền theo trạng thái (giống logic trong controller)
                                // Logic: đã lên/đang nấu = 100%, chờ bếp = 0%, đã hủy = 0%
                                $thanhTienTinhLai = 0;
                                
                                if($coTrongCombo) {
                                    // Món combo: chỉ tính tiền cho phần vượt giới hạn
                                    if($soLuongVuot > 0) {
                                        // Tính tiền cho phần đã lên và đang nấu trong vượt (100%)
                                        $tienMonDaLenTrongVuot = $donGiaGoc * $soLuongDaLenTrongVuot;
                                        $tienMonDangCheBienTrongVuot = $donGiaGoc * $soLuongDangCheBienTrongVuot;
                                        // Chờ bếp: 0 đồng
                                        $tienMonChoBepTrongVuot = 0;
                                        $thanhTienTinhLai = $tienMonDaLenTrongVuot + $tienMonDangCheBienTrongVuot + $tienMonChoBepTrongVuot + $tienPhuPhiTinhLai;
                                    }
                                } else {
                                    // Món gọi thêm: tính theo trạng thái
                                    // Đã lên: 100% giá
                                    $tienMonDaLen = $donGiaGoc * $soLuongDaLen;
                                    // Đang nấu: 100% giá
                                    $tienMonDangCheBien = $donGiaGoc * $soLuongDangCheBien;
                                    // Chờ bếp: 0 đồng
                                    $tienMonChoBep = 0;
                                    // Đã hủy: 0 đồng
                                    $tienMonHuy = 0;
                                    $thanhTienTinhLai = $tienMonDaLen + $tienMonDangCheBien + $tienMonChoBep + $tienMonHuy;
                                }
                            @endphp
                            <tr>
                                <td>{{ $stt++ }}</td>
                                <td>
                                    {{ $ctFirst->monAn->ten_mon ?? 'N/A' }}
                                    @if($coTrongCombo)
                                        <span class="badge bg-warning">Món combo</span>
                                        @if($soLuongVuot > 0)
                                            <span class="badge bg-danger">Vượt giới hạn</span>
                                        @endif
                                    @else
                                        <span class="badge bg-info">Gọi thêm</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $tongSoLuong }}
                                    @if($tongGioiHan !== null && $tongGioiHan > 0)
                                        <br><small class="text-muted">(Giới hạn: {{ $tongGioiHan }})</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $tongSoLuongHienThi = $monAnGroup->sum('so_luong');
                                        $tongSoLuongKhongHuy = $tongSoLuongHienThi - $soLuongHuy;
                                    @endphp
                                    @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuongHienThi)
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                            <span style="font-size: 11px; color: #dc3545; font-weight: bold; padding: 2px 6px; background-color: #f8d7da; border-radius: 3px;">Đã hủy: {{ $soLuongHuy }}/{{ $tongSoLuongHienThi }}</span>
                                        </div>
                                    @elseif($soLuongDaLen == $tongSoLuongKhongHuy && $soLuongHuy == 0 && $soLuongDangCheBien == 0 && $soLuongChoBep == 0)
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                            <span style="font-size: 11px; color: #28a745; font-weight: bold; padding: 2px 6px; background-color: #d4edda; border-radius: 3px;">Đã lên: {{ $soLuongDaLen }}/{{ $tongSoLuongHienThi }}</span>
                                        </div>
                                    @else
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                            @if($soLuongDaLen > 0)
                                                <span style="font-size: 11px; color: #28a745; font-weight: bold; padding: 2px 6px; background-color: #d4edda; border-radius: 3px;">Đã lên: {{ $soLuongDaLen }}</span>
                                            @endif
                                            @if($soLuongDangCheBien > 0)
                                                <span style="font-size: 11px; color: #856404; font-weight: bold; padding: 2px 6px; background-color: #fff3cd; border-radius: 3px;">Đang nấu: {{ $soLuongDangCheBien }}</span>
                                            @endif
                                            @if($soLuongChoBep > 0)
                                                <span style="font-size: 11px; color: #0c5460; font-weight: bold; padding: 2px 6px; background-color: #d1ecf1; border-radius: 3px;">Chờ bếp: {{ $soLuongChoBep }}</span>
                                            @endif
                                            @if($soLuongHuy > 0)
                                                <span style="font-size: 11px; color: #dc3545; font-weight: bold; padding: 2px 6px; background-color: #f8d7da; border-radius: 3px;">Đã hủy: {{ $soLuongHuy }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuongHienThi)
                                        <span class="text-danger">0 đ</span>
                                        <br><small class="text-muted">(Đã hủy)</small>
                                    @elseif($coTrongCombo && $soLuongVuot == 0)
                                        <span class="text-success">0 đ</span>
                                        <br><small class="text-muted">(Đã bao gồm trong combo)</small>
                                        @if($soLuongHuy > 0)
                                            <br><small class="text-danger">Đã hủy ({{ $soLuongHuy }}): 0 đ</small>
                                        @endif
                                    @elseif($donGiaGoc > 0 || $coMonChuaNauXong || $soLuongVuot > 0)
                                        <div>
                                            <small class="text-muted">Giá gốc: {{ number_format($donGiaGoc) }} đ</small>
                                            @if($soLuongHuy > 0)
                                                <br><small class="text-danger">Đã hủy ({{ $soLuongHuy }}): 0 đ</small>
                                            @endif
                                            @if($coMonChuaNauXong)
                                                <br>
                                                <small class="text-warning">
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
                                                                Đang nấu dở ({{ $soLuongDangCheBien }}): 100% = {{ number_format($donGiaGoc * $soLuongDangCheBien) }} đ
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
                                    @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuongHienThi)
                                        <span class="text-danger">0 đ</span>
                                    @elseif($thanhTienTinhLai > 0)
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
                                // Sử dụng dữ liệu đã lưu từ chi_tiet_hoa_don
                                $tongTienComboTinhLai = $tongTienCombo;
                                $tongTienMonGoiThemHienThi = $tongTienMonGoiThem;
                                
                                // Tổng cộng = combo + món gọi thêm (từ database)
                                $tongCong = $tongTienMonHienThi;
                            @endphp
                            @if($tongTienComboTinhLai > 0)
                            <tr class="table-warning fw-bold">
                                <td colspan="5" class="text-end">
                                    <i class="fa fa-star text-warning me-1"></i>Tổng tiền combo chính:
                                </td>
                                <td class="text-end text-primary fs-5">{{ number_format($tongTienComboTinhLai) }} đ</td>
                            </tr>
                            @endif
                            @if($tongTienMonGoiThemHienThi > 0)
                            <tr class="table-secondary fw-bold">
                                <td colspan="5" class="text-end">Tổng tiền món gọi thêm:</td>
                                <td class="text-end text-info">{{ number_format($tongTienMonGoiThemHienThi) }} đ</td>
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
    </div>
</main>
@endsection