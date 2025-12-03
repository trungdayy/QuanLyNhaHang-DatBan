<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #{{ $hoaDon->ma_hoa_don }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-page {
                page-break-after: always;
            }
            @page {
                size: A4;
                margin: 1cm;
            }
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            padding: 10px;
        }
        
        .invoice-header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .invoice-header h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .invoice-header p {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .invoice-info {
            margin-bottom: 30px;
        }
        
        .invoice-info table {
            width: 100%;
        }
        
        .invoice-info td {
            padding: 5px 10px;
            vertical-align: top;
        }
        
        .invoice-info td:first-child {
            font-weight: bold;
            width: 150px;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 12px;
        }
        
        .invoice-table th,
        .invoice-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        .invoice-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .invoice-table td.text-center {
            text-align: center;
        }
        
        .invoice-table td.text-end {
            text-align: right;
        }
        
        .invoice-summary {
            margin-top: 30px;
            margin-left: auto;
            width: 400px;
        }
        
        .invoice-summary table {
            width: 100%;
        }
        
        .invoice-summary td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        
        .invoice-summary td:last-child {
            text-align: right;
            font-weight: bold;
        }
        
        .invoice-summary .total-row {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            font-size: 18px;
            font-weight: bold;
        }
        
        .invoice-footer {
            margin-top: 50px;
            text-align: center;
        }
        
        .invoice-footer .signature {
            margin-top: 60px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }

        /* Mobile Responsive */
        @media screen and (max-width: 768px) {
            body {
                font-size: 12px;
                padding: 5px;
            }
            
            .container-fluid {
                padding: 0 !important;
            }
            
            .invoice-header {
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            
            .invoice-header h1 {
                font-size: 18px;
                margin-bottom: 5px;
            }
            
            .invoice-header p {
                font-size: 10px;
                margin: 2px 0;
            }
            
            .invoice-info {
                margin-bottom: 20px;
            }
            
            .invoice-info table {
                font-size: 11px;
            }
            
            .invoice-info td {
                padding: 4px 5px;
                display: block;
                width: 100% !important;
            }
            
            .invoice-info td:first-child {
                font-weight: bold;
                margin-top: 8px;
                border-bottom: 1px solid #eee;
                padding-bottom: 4px;
                width: 100% !important;
            }
            
            .invoice-info tr {
                display: block;
                margin-bottom: 8px;
                border-bottom: 1px solid #ddd;
                padding-bottom: 6px;
            }
            
            .invoice-info td.text-right {
                text-align: left !important;
            }
            
            .invoice-table {
                font-size: 10px;
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                width: 100%;
            }
            
            .invoice-table thead {
                display: none;
            }
            
            .invoice-table tbody {
                display: block;
            }
            
            .invoice-table tr {
                display: block;
                border: 1px solid #ddd;
                margin-bottom: 10px;
                padding: 8px;
                background-color: #f9f9f9;
            }
            
            .invoice-table td {
                display: block;
                width: 100% !important;
                border: none;
                padding: 4px 0;
                text-align: left !important;
                position: relative;
                padding-left: 35%;
            }
            
            .invoice-table td:before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 30%;
                font-weight: bold;
                color: #333;
            }
            
            .invoice-table td.text-center,
            .invoice-table td.text-end {
                text-align: left !important;
            }
            
            .invoice-summary {
                width: 100%;
                margin-left: 0;
                margin-top: 20px;
            }
            
            .invoice-summary table {
                font-size: 11px;
            }
            
            .invoice-summary td {
                padding: 6px 4px;
            }
            
            .invoice-summary .total-row {
                font-size: 14px;
            }
            
            .invoice-footer {
                margin-top: 30px;
                font-size: 11px;
            }
            
            .invoice-footer .signature {
                margin-top: 30px;
            }
            
            .invoice-footer .signature table {
                font-size: 10px;
            }
        }
        
        /* Tablet */
        @media screen and (min-width: 769px) and (max-width: 1024px) {
            body {
                font-size: 13px;
            }
            
            .invoice-header h1 {
                font-size: 24px;
            }
            
            .invoice-table {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        {{-- Nút in (chỉ hiện khi không in) --}}
        <div class="no-print text-center mb-3">
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                <i class="bi bi-printer me-2"></i>In hóa đơn
            </button>
            <a href="javascript:window.close()" class="btn btn-secondary btn-lg ms-2">
                <i class="bi bi-x-circle me-2"></i>Đóng
            </a>
        </div>

        {{-- Header hóa đơn --}}
        <div class="invoice-header">
            <h1>HÓA ĐƠN THANH TOÁN</h1>
            <p><strong>Nhà hàng Buffet Ocean</strong></p>
            <p>Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM</p>
            <p>Điện thoại: 0123 456 789 | Email: info@buffetocean.com</p>
        </div>

        {{-- Thông tin hóa đơn --}}
        <div class="invoice-info">
            <table>
                @php
                    $chiTiet = $hoaDon->chiTietHoaDon;
                @endphp
                <tr>
                    <td>Mã hóa đơn:</td>
                    <td><strong>{{ $hoaDon->ma_hoa_don }}</strong></td>
                    <td class="text-right">Ngày tạo:</td>
                    <td class="text-right"><strong>{{ $hoaDon->created_at->format('d/m/Y H:i:s') }}</strong></td>
                </tr>
                <tr>
                    <td>Khách hàng:</td>
                    <td>{{ $chiTiet ? $chiTiet->ten_khach : ($hoaDon->datBan->ten_khach ?? 'N/A') }}</td>
                    <td class="text-right">Bàn số:</td>
                    <td class="text-right"><strong>{{ $chiTiet ? $chiTiet->ban_so : ($hoaDon->datBan->banAn->so_ban ?? 'N/A') }}</strong></td>
                </tr>
                <tr>
                    <td>SĐT:</td>
                    <td>{{ $chiTiet ? $chiTiet->sdt_khach : ($hoaDon->datBan->sdt_khach ?? 'N/A') }}</td>
                    <td class="text-right">Số khách:</td>
                    <td class="text-right">
                        <strong>{{ $chiTiet ? $chiTiet->so_khach : ($hoaDon->datBan->so_khach ?? 'N/A') }}</strong>
                        <br><small style="font-size: 11px;">
                            (Người lớn: {{ $hoaDon->datBan->nguoi_lon ?? 0 }}, Trẻ em: {{ $hoaDon->datBan->tre_em ?? 0 }})
                        </small>
                    </td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>{{ $chiTiet ? $chiTiet->email_khach : ($hoaDon->datBan->email_khach ?? 'N/A') }}</td>
                    <td class="text-right">Khu vực:</td>
                    <td class="text-right"><strong>{{ $chiTiet ? ($chiTiet->khu_vuc . ($chiTiet->tang ? ' - Tầng ' . $chiTiet->tang : '')) : ($hoaDon->datBan->banAn->khuVuc->ten_khu_vuc ?? 'N/A') }}</strong></td>
                </tr>
                @if($chiTiet && $chiTiet->gio_vao)
                <tr>
                    <td>Giờ vào:</td>
                    <td><strong>{{ $chiTiet->gio_vao->format('d/m/Y H:i') }}</strong></td>
                    <td class="text-right">Giờ ra:</td>
                    <td class="text-right"><strong>{{ $chiTiet->gio_ra->format('d/m/Y H:i') }}</strong></td>
                </tr>
                <tr>
                    <td>Thời gian phục vụ:</td>
                    <td><strong>{{ floor($chiTiet->thoi_gian_phuc_vu_phut / 60) }} giờ {{ $chiTiet->thoi_gian_phuc_vu_phut % 60 }} phút</strong></td>
                    <td class="text-right">Mã đặt bàn:</td>
                    <td class="text-right"><strong>{{ $chiTiet->ma_dat_ban ?? 'N/A' }}</strong></td>
                </tr>
                @if($chiTiet->thoi_gian_quy_dinh_phut)
                <tr>
                    <td>Thời gian quy định:</td>
                    <td><strong>{{ floor($chiTiet->thoi_gian_quy_dinh_phut / 60) }} giờ {{ $chiTiet->thoi_gian_quy_dinh_phut % 60 }} phút</strong></td>
                    <td class="text-right">Thời gian vượt quá:</td>
                    <td class="text-right">
                        <strong>
                            @if($chiTiet->thoi_gian_vuot_phut > 0)
                                <span style="color: #dc3545;">{{ floor($chiTiet->thoi_gian_vuot_phut / 60) }} giờ {{ $chiTiet->thoi_gian_vuot_phut % 60 }} phút</span>
                            @else
                                <span style="color: #28a745;">0 phút</span>
                            @endif
                        </strong>
                    </td>
                </tr>
                @endif
                @elseif(isset($gioVao) && $gioVao)
                <tr>
                    <td>Giờ vào:</td>
                    <td><strong>{{ $gioVao->format('d/m/Y H:i') }}</strong></td>
                    <td class="text-right">Giờ ra:</td>
                    <td class="text-right"><strong>{{ $gioRa->format('d/m/Y H:i') }}</strong></td>
                </tr>
                <tr>
                    <td>Thời gian phục vụ:</td>
                    <td><strong>{{ floor($thoiGianPhucVu / 60) }} giờ {{ $thoiGianPhucVu % 60 }} phút</strong></td>
                    <td class="text-right">Mã đặt bàn:</td>
                    <td class="text-right"><strong>{{ $hoaDon->datBan->ma_dat_ban ?? 'N/A' }}</strong></td>
                </tr>
                @endif
                <tr>
                    <td>Phương thức TT:</td>
                    <td>
                        <strong>
                            @if($hoaDon->phuong_thuc_tt == 'tien_mat')
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
                        </strong>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>

        {{-- Chi tiết món ăn --}}
        <table class="invoice-table">
            <thead>
                <tr>
                    <th style="width: 5%;">STT</th>
                    <th style="width: 35%;">Tên món</th>
                    <th style="width: 10%;" class="text-center">Số lượng</th>
                    <th style="width: 15%;" class="text-center">Trạng thái</th>
                    <th style="width: 17.5%;" class="text-end">Đơn giá</th>
                    <th style="width: 17.5%;" class="text-end">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Lấy tất cả món từ các order để tính trạng thái
                    $monAnList = collect();
                    foreach($hoaDon->datBan->orderMon as $order) {
                        foreach($order->chiTietOrders as $ct) {
                            // Bao gồm cả món đã hủy
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
                @php
                    $chiTiet = $hoaDon->chiTietHoaDon;
                    
                    if ($chiTiet && $chiTiet->danh_sach_mon) {
                        // Sử dụng dữ liệu từ chi_tiet_hoa_don
                        $stt = 1;
                        // Tính tổng tiền combo từ tất cả các combo
                        // Giảm 50% cho từng người đầu tiên tương ứng với số trẻ em
                        $tongTienCombo = 0;
                        $soTreEm = $hoaDon->datBan->tre_em ?? 0;
                        $soNguoiDaXuLy = 0; // Đếm số người đã xử lý
                        if($hoaDon->datBan->chiTietDatBan && $hoaDon->datBan->chiTietDatBan->count() > 0) {
                            foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo) {
                                if($chiTietCombo->combo) {
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
                                    
                                    // Tính thành tiền: người được giảm giá + người không giảm giá
                                    $thanhTienCombo = ($giaComboGoc * 0.5 * $soNguoiDuocGiam) + ($giaComboGoc * $soNguoiKhongGiam);
                                    
                                    $tongTienCombo += $thanhTienCombo;
                                    $soNguoiDaXuLy += $soLuongCombo; // Tăng số người đã xử lý
                                }
                            }
                        } else {
                            $tongTienCombo = $chiTiet->tong_tien_combo ?? 0;
                        }
                        // Tổng tiền món gọi thêm sẽ được tính lại trong vòng lặp hiển thị
                        $tongTienMonGoiThem = 0;
                    } else {
                        // Fallback cho hóa đơn cũ
                        $stt = 1;
                        $combo = $hoaDon->datBan->comboBuffet;
                        $soKhach = $hoaDon->datBan->so_khach;
                    }
                @endphp
                
                @if($chiTiet && $chiTiet->danh_sach_mon)
                    {{-- Hiển thị từ chi_tiet_hoa_don --}}
                    {{-- Combo chính - Hiển thị tất cả các combo --}}
                    @if($hoaDon->datBan->chiTietDatBan && $hoaDon->datBan->chiTietDatBan->count() > 0)
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
                            <tr>
                                <td class="text-center" data-label="STT">{{ $stt++ }}</td>
                                <td data-label="Tên món">
                                    <strong>{{ $chiTietCombo->combo->ten_combo }}</strong> (Combo chính)
                                    <span style="background-color: #17a2b8; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 5px;">Trẻ em (Giảm 50%)</span>
                                </td>
                                <td class="text-center" data-label="Số lượng">{{ $soNguoiDuocGiam }} khách</td>
                                <td class="text-center" data-label="Trạng thái">-</td>
                                <td class="text-end" data-label="Đơn giá">
                                    <span style="text-decoration: line-through; color: #6c757d; font-size: 11px;">{{ number_format($giaComboGoc) }} đ</span><br>
                                    <span style="color: #dc3545; font-weight: bold;">{{ number_format($giaComboGiam) }} đ</span>
                                    <small style="color: #28a745; font-size: 10px;">(Giảm 50%)</small>
                                </td>
                                <td class="text-end" data-label="Thành tiền"><strong>{{ number_format($thanhTienGiam) }} đ</strong></td>
                            </tr>
                            @endif
                            
                            {{-- Hiển thị combo với giá gốc (nếu có) --}}
                            @if($soNguoiKhongGiam > 0)
                            @php
                                $thanhTienGoc = $giaComboGoc * $soNguoiKhongGiam;
                            @endphp
                            <tr>
                                <td class="text-center" data-label="STT">{{ $stt++ }}</td>
                                <td data-label="Tên món">
                                    <strong>{{ $chiTietCombo->combo->ten_combo }}</strong> (Combo chính)
                                </td>
                                <td class="text-center" data-label="Số lượng">{{ $soNguoiKhongGiam }} khách</td>
                                <td class="text-center" data-label="Trạng thái">-</td>
                                <td class="text-end" data-label="Đơn giá">
                                    {{ number_format($giaComboGoc) }} đ
                                </td>
                                <td class="text-end" data-label="Thành tiền"><strong>{{ number_format($thanhTienGoc) }} đ</strong></td>
                            </tr>
                            @endif
                            @endif
                        @endforeach
                    @elseif($chiTiet->tong_tien_combo > 0)
                    <tr>
                        <td class="text-center" data-label="STT">{{ $stt++ }}</td>
                        <td data-label="Tên món"><strong>{{ $chiTiet->ten_combo }}</strong> (Combo chính)</td>
                        <td class="text-center" data-label="Số lượng">{{ $chiTiet->so_khach }} khách</td>
                        <td class="text-center" data-label="Trạng thái">-</td>
                        <td class="text-end" data-label="Đơn giá">{{ number_format($chiTiet->gia_combo_per_person) }} đ</td>
                        <td class="text-end" data-label="Thành tiền"><strong>{{ number_format($chiTiet->tong_tien_combo) }} đ</strong></td>
                    </tr>
                    @endif

                    {{-- Danh sách món --}}
                    @php
                        $tongTienMonGoiThemTinhLai = 0;
                        $sttMon = 1;
                    @endphp
                    @foreach($monAnGrouped as $monAnId => $monAnGroup)
                    @php
                        $ctFirst = $monAnGroup->first();
                        $tongSoLuong = $monAnGroup->sum('so_luong');
                        
                        // Lấy thông tin từ danh_sach_mon nếu có (để lấy thông tin như gioi_han, la_mon_combo)
                        $monInfo = null;
                        foreach($chiTiet->danh_sach_mon as $mon) {
                            if($ctFirst->monAn && $ctFirst->monAn->ten_mon == $mon['ten_mon']) {
                                $monInfo = $mon;
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
                        $soLuongHuy = 0; // Đã hủy
                        
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
                        $coTrongCombo = $monInfo['la_mon_combo'] ?? false;
                        $soLuongVuot = $monInfo['so_luong_vuot'] ?? 0;
                        $tongSoLuongHienThi = $monAnGroup ? $monAnGroup->sum('so_luong') : $mon['so_luong'];
                        
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
                                // Tính tiền cho phần đang chế biến trong vượt (30%)
                                $tienMonDangCheBienTrongVuot = $donGiaGoc * 0.3 * $soLuongDangCheBienTrongVuot;
                                // Phần chờ bếp trong vượt: 0 đồng (miễn phí)
                                $tienMonChoBepTrongVuot = 0;
                                // Tổng tiền = tiền món đã nấu xong + tiền món đang chế biến + phụ phí
                                $thanhTienTinhLai = $tienMonDaLenTrongVuot + $tienMonDangCheBienTrongVuot + $tienMonChoBepTrongVuot + $tienPhuPhiTinhLai;
                            }
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
                            $thanhTienTinhLai = $tienMonDaLen + $tienMonDangCheBien + $tienMonChoBep + $tienMonHuy;
                        }
                    @endphp
                    <tr>
                        <td class="text-center" data-label="STT">{{ $sttMon++ }}</td>
                        <td data-label="Tên món">
                            {{ $ctFirst->monAn->ten_mon ?? 'N/A' }}
                            @if($coTrongCombo)
                                <span style="font-size: 11px; color: #856404;">(Món combo)</span>
                                @if($monInfo && ($monInfo['vuot_gioi_han'] ?? false))
                                    <span style="font-size: 11px; color: #dc3545;">(Vượt giới hạn)</span>
                                @endif
                            @else
                                <span style="font-size: 11px; color: #0c5460;">(Gọi thêm)</span>
                            @endif
                        </td>
                        <td class="text-center" data-label="Số lượng">
                            {{ $tongSoLuong }}
                            @if($monInfo && $monInfo['gioi_han'] !== null)
                                <br><small style="font-size: 10px;">(Giới hạn: {{ $monInfo['gioi_han'] }})</small>
                            @endif
                        </td>
                        <td class="text-center" data-label="Trạng thái">
                            @if($monAnGroup)
                                @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuongHienThi)
                                    <span style="font-size: 11px; color: #dc3545; font-weight: bold;">✗ Đã hủy: {{ $soLuongHuy }}/{{ $tongSoLuongHienThi }}</span>
                                @elseif($soLuongHuy > 0)
                                    <span style="font-size: 11px; color: #856404; font-weight: bold;">⏱ Đã lên: {{ $soLuongDaLen }}/{{ $tongSoLuongHienThi - $soLuongHuy }}</span>
                                    <br><small style="font-size: 10px; color: #dc3545;">Đã hủy: {{ $soLuongHuy }}</small>
                                @elseif($soLuongDaLen == $tongSoLuongHienThi)
                                    <span style="font-size: 11px; color: #28a745; font-weight: bold;">✓ Đã lên: {{ $soLuongDaLen }}/{{ $tongSoLuongHienThi }}</span>
                                @elseif($soLuongDaLen > 0)
                                    <span style="font-size: 11px; color: #856404; font-weight: bold;">⏱ Đã lên: {{ $soLuongDaLen }}/{{ $tongSoLuongHienThi }}</span>
                                @else
                                    <span style="font-size: 11px; color: #6c757d;">⏳ Chưa lên: 0/{{ $tongSoLuongHienThi }}</span>
                                @endif
                            @else
                                <span style="font-size: 11px; color: #6c757d;">N/A</span>
                            @endif
                        </td>
                        <td class="text-end" data-label="Đơn giá">
                            @if($soLuongHuy > 0 && $soLuongHuy == $tongSoLuongHienThi)
                                {{-- Tất cả món đã hủy --}}
                                <span style="color: #dc3545;">0 đ</span>
                                <br><small style="font-size: 11px; color: #6c757d;">(Đã hủy)</small>
                            @elseif($coTrongCombo && $soLuongVuot == 0)
                                {{-- Món combo chưa vượt giới hạn: hiển thị 0 đ --}}
                                <span style="color: #28a745;">0 đ</span>
                                <br><small style="font-size: 11px; color: #6c757d;">(Đã bao gồm trong combo)</small>
                                @if($soLuongHuy > 0)
                                    <br><small style="font-size: 10px; color: #dc3545;">Đã hủy ({{ $soLuongHuy }}): 0 đ</small>
                                @endif
                            @elseif($mon['don_gia'] > 0 || $coMonChuaNauXong || $soLuongVuot > 0)
                                {{-- Món vượt giới hạn hoặc món gọi thêm: hiển thị chi tiết --}}
                                <div style="font-size: 11px; line-height: 1.4;">
                                    <div><small style="color: #6c757d;">Giá gốc: {{ number_format($donGiaGoc) }} đ</small></div>
                                    @if($soLuongHuy > 0)
                                        <div><small style="color: #dc3545;">Đã hủy ({{ $soLuongHuy }}): 0 đ</small></div>
                                    @endif
                                    @if($coMonChuaNauXong)
                                        <div style="color: #856404; margin-top: 4px;">
                                            @if($coTrongCombo && $soLuongVuot > 0)
                                                @if($soLuongDaLenTrongVuot > 0)
                                                    Đã nấu xong ({{ $soLuongDaLenTrongVuot }}): 100% = {{ number_format($donGiaGoc * $soLuongDaLenTrongVuot) }} đ
                                                    @if($soLuongChuaNauXongTrongVuot > 0)
                                                        <br>
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
                                                            Đang nấu dở ({{ $soLuongDangCheBien }}): 30% = {{ number_format($donGiaGoc * 0.3 * $soLuongDangCheBien) }} đ
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
                                        </div>
                                    @endif
                                    @if($tienPhuPhiTinhLai > 0)
                                        <div style="color: #dc3545; margin-top: 4px;">
                                            + {{ number_format($tienPhuPhiTinhLai) }} đ (phụ phí)
                                            @if($soLuongDaLenTrongVuot > 1 && $phuPhiDonVi > 0)
                                                <br><small style="font-size: 10px;">({{ number_format($phuPhiDonVi) }} đ × {{ $soLuongDaLenTrongVuot }})</small>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @else
                                @if($tienPhuPhiTinhLai > 0)
                                    <span style="color: #28a745;">0 đ</span>
                                    <br><small style="color: #dc3545; font-size: 11px;">
                                        + {{ number_format($tienPhuPhiTinhLai) }} đ (phụ phí)
                                        @if($soLuongDaLenTrongVuot > 1 && $phuPhiDonVi > 0)
                                            <br><small style="font-size: 10px;">({{ number_format($phuPhiDonVi) }} đ × {{ $soLuongDaLenTrongVuot }})</small>
                                        @endif
                                    </small>
                                @else
                                    <span style="color: #28a745;">0 đ</span>
                                    <br><small style="font-size: 11px; color: #6c757d;">(Đã bao gồm trong combo)</small>
                                @endif
                            @endif
                        </td>
                        <td class="text-end" data-label="Thành tiền">
                            @if($soLuongHuy > 0 && isset($tongSoLuongHienThi) && $soLuongHuy == $tongSoLuongHienThi)
                                {{-- Tất cả món đã hủy --}}
                                <span style="color: #dc3545; font-weight: bold;">0 đ</span>
                            @elseif($thanhTienTinhLai > 0)
                                <strong>{{ number_format($thanhTienTinhLai) }} đ</strong>
                                @php
                                    $tongTienMonGoiThemTinhLai += $thanhTienTinhLai;
                                @endphp
                            @else
                                <span style="color: #28a745;">0 đ</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @php
                        $tongTienMonGoiThem = $tongTienMonGoiThemTinhLai;
                        $tongTienComboMon = $tongTienCombo + $tongTienMonGoiThem;
                    @endphp
                    
                    {{-- Tổng kết --}}
                    @if($tongTienCombo > 0)
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td colspan="5" class="text-end">Tổng tiền combo chính:</td>
                        <td class="text-end">{{ number_format($tongTienCombo) }} đ</td>
                    </tr>
                    @endif
                    @if($tongTienMonGoiThem > 0)
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td colspan="5" class="text-end">Tổng tiền món gọi thêm:</td>
                        <td class="text-end">{{ number_format($tongTienMonGoiThem) }} đ</td>
                    </tr>
                    @endif
                    <tr style="background-color: #e0e0e0; font-weight: bold; font-size: 16px;">
                        <td colspan="5" class="text-end">TỔNG CỘNG:</td>
                        <td class="text-end">{{ number_format($tongTienComboMon) }} đ</td>
                    </tr>
                @else
                    {{-- Fallback cho hóa đơn cũ --}}
                    @php
                        $tienComboChinh = 0;
                        // Tính từ chiTietDatBan nếu có
                        // Giảm 50% cho từng người đầu tiên tương ứng với số trẻ em
                        $soTreEm = $hoaDon->datBan->tre_em ?? 0;
                        $soNguoiDaXuLy = 0; // Đếm số người đã xử lý
                        if($hoaDon->datBan->chiTietDatBan && $hoaDon->datBan->chiTietDatBan->count() > 0) {
                            foreach($hoaDon->datBan->chiTietDatBan as $chiTietCombo) {
                                if($chiTietCombo->combo) {
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
                                    
                                    // Tính thành tiền: người được giảm giá + người không giảm giá
                                    $thanhTienCombo = ($giaComboGoc * 0.5 * $soNguoiDuocGiam) + ($giaComboGoc * $soNguoiKhongGiam);
                                    
                                    $tienComboChinh += $thanhTienCombo;
                                    $soNguoiDaXuLy += $soLuongCombo; // Tăng số người đã xử lý
                                }
                            }
                        } else {
                            // Fallback cũ
                            $combo = $hoaDon->datBan->comboBuffet;
                            $soKhach = $hoaDon->datBan->so_khach;
                            $tienComboChinh = isset($tienComboChinh) ? $tienComboChinh : ($combo ? ($combo->gia_co_ban * $soKhach) : 0);
                        }
                        $tongTienMonGoiThemTinhLai = isset($tongTienMonGoiThem) ? $tongTienMonGoiThem : 0;
                        $tongTienThucTeTinhLai = $tienComboChinh + $tongTienMonGoiThemTinhLai;
                    @endphp
                    {{-- Combo chính - Hiển thị tất cả các combo --}}
                    @if($hoaDon->datBan->chiTietDatBan && $hoaDon->datBan->chiTietDatBan->count() > 0)
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
                            <tr>
                                <td class="text-center" data-label="STT">{{ $stt++ }}</td>
                                <td data-label="Tên món">
                                    <strong>{{ $chiTietCombo->combo->ten_combo }}</strong> (Combo chính)
                                    <span style="background-color: #17a2b8; color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 5px;">Trẻ em (Giảm 50%)</span>
                                </td>
                                <td class="text-center" data-label="Số lượng">{{ $soNguoiDuocGiam }} khách</td>
                                <td class="text-center" data-label="Trạng thái">-</td>
                                <td class="text-end" data-label="Đơn giá">
                                    <span style="text-decoration: line-through; color: #6c757d; font-size: 11px;">{{ number_format($giaComboGoc) }} đ</span><br>
                                    <span style="color: #dc3545; font-weight: bold;">{{ number_format($giaComboGiam) }} đ</span>
                                    <small style="color: #28a745; font-size: 10px;">(Giảm 50%)</small>
                                </td>
                                <td class="text-end" data-label="Thành tiền"><strong>{{ number_format($thanhTienGiam) }} đ</strong></td>
                            </tr>
                            @endif
                            
                            {{-- Hiển thị combo với giá gốc (nếu có) --}}
                            @if($soNguoiKhongGiam > 0)
                            @php
                                $thanhTienGoc = $giaComboGoc * $soNguoiKhongGiam;
                            @endphp
                            <tr>
                                <td class="text-center" data-label="STT">{{ $stt++ }}</td>
                                <td data-label="Tên món">
                                    <strong>{{ $chiTietCombo->combo->ten_combo }}</strong> (Combo chính)
                                </td>
                                <td class="text-center" data-label="Số lượng">{{ $soNguoiKhongGiam }} khách</td>
                                <td class="text-center" data-label="Trạng thái">-</td>
                                <td class="text-end" data-label="Đơn giá">
                                    {{ number_format($giaComboGoc) }} đ
                                </td>
                                <td class="text-end" data-label="Thành tiền"><strong>{{ number_format($thanhTienGoc) }} đ</strong></td>
                            </tr>
                            @endif
                            @endif
                        @endforeach
                    @else
                        @php
                            $combo = $hoaDon->datBan->comboBuffet;
                            $soKhach = $hoaDon->datBan->so_khach;
                        @endphp
                        @if($combo && $tienComboChinh > 0)
                        <tr>
                            <td class="text-center" data-label="STT">{{ $stt++ }}</td>
                            <td data-label="Tên món"><strong>{{ $combo->ten_combo }}</strong> (Combo chính)</td>
                            <td class="text-center" data-label="Số lượng">{{ $soKhach }} khách</td>
                            <td class="text-center" data-label="Trạng thái">-</td>
                            <td class="text-end" data-label="Đơn giá">{{ number_format($combo->gia_co_ban) }} đ</td>
                            <td class="text-end" data-label="Thành tiền"><strong>{{ number_format($tienComboChinh) }} đ</strong></td>
                        </tr>
                        @endif
                    @endif
                    <tr style="background-color: #e0e0e0; font-weight: bold; font-size: 16px;">
                        <td colspan="5" class="text-end">TỔNG CỘNG:</td>
                        <td class="text-end">{{ number_format($tongTienThucTeTinhLai) }} đ</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Tóm tắt thanh toán --}}
        <div class="invoice-summary">
            <table>
                @php
                    if ($chiTiet) {
                        // Sử dụng tổng tiền đã tính lại từ phần bảng trên
                        // $tongTienComboMon đã được tính lại ở dòng 754 từ $tongTienCombo + $tongTienMonGoiThem (đã tính lại)
                        // Nếu chưa có, sử dụng giá trị từ database (fallback)
                        if (!isset($tongTienComboMon)) {
                            $tongTienComboMon = $chiTiet->tong_tien_combo_mon ?? 0;
                        }
                        $tongTienSauVoucher = $tongTienComboMon - ($chiTiet->tien_giam_voucher ?? 0);
                        $phaiThanhToan = $tongTienSauVoucher - ($chiTiet->tien_coc ?? 0) + ($chiTiet->tong_phu_thu ?? 0);
                        if($phaiThanhToan < 0) $phaiThanhToan = 0;
                    } else {
                        // Fallback cho hóa đơn cũ
                        $tongTienComboMon = $tongTienThucTeTinhLai ?? 0;
                        $tongTienSauVoucher = $tongTienComboMon - ($hoaDon->tien_giam ?? 0);
                        $phaiThanhToan = $tongTienSauVoucher - ($hoaDon->datBan->tien_coc ?? 0) + ($hoaDon->phu_thu ?? 0);
                        if($phaiThanhToan < 0) $phaiThanhToan = 0;
                    }
                @endphp
                <tr>
                    <td>Tổng tiền (Combo + Món):</td>
                    <td>{{ number_format($tongTienComboMon) }} đ</td>
                </tr>
                @if(($chiTiet && $chiTiet->tien_giam_voucher > 0) || (!$chiTiet && $hoaDon->voucher))
                <tr>
                    <td>(-) Tiền giảm (Voucher {{ $chiTiet ? $chiTiet->ma_voucher : ($hoaDon->voucher->ma_voucher ?? '') }}):</td>
                    <td>- {{ number_format($chiTiet ? $chiTiet->tien_giam_voucher : ($hoaDon->tien_giam ?? 0)) }} đ</td>
                </tr>
                <tr style="background-color: #fffacd;">
                    <td style="font-weight: bold;">Tổng tiền sau voucher:</td>
                    <td style="font-weight: bold;">{{ number_format($tongTienSauVoucher) }} đ</td>
                </tr>
                @endif
                @if(($chiTiet && $chiTiet->tien_coc > 0) || (!$chiTiet && $hoaDon->datBan->tien_coc > 0))
                <tr>
                    <td>(-) Tiền cọc:</td>
                    <td>- {{ number_format($chiTiet ? $chiTiet->tien_coc : ($hoaDon->datBan->tien_coc ?? 0)) }} đ</td>
                </tr>
                @endif
                @if(($chiTiet && $chiTiet->tong_phu_thu > 0) || (!$chiTiet && $hoaDon->phu_thu > 0))
                <tr>
                    <td>(+) Phụ thu:</td>
                    <td>+ {{ number_format($chiTiet ? $chiTiet->tong_phu_thu : ($hoaDon->phu_thu ?? 0)) }} đ</td>
                </tr>
                @if($chiTiet && $chiTiet->phu_thu_tu_dong > 0)
                <tr style="background-color: #f8f9fa;">
                    <td style="padding-left: 20px; font-size: 12px;">- Phụ thu tự động:</td>
                    <td style="font-size: 12px;">+ {{ number_format($chiTiet->phu_thu_tu_dong) }} đ</td>
                </tr>
                @if($chiTiet->phu_thu_thoi_gian > 0)
                <tr style="background-color: #f8f9fa;">
                    <td style="padding-left: 30px; font-size: 11px; color: #6c757d;">• Thời gian vượt quá:</td>
                    <td style="font-size: 11px; color: #6c757d;">{{ number_format($chiTiet->phu_thu_thoi_gian) }} đ</td>
                </tr>
                @endif
                @if($chiTiet->phu_thu_tu_dong > ($chiTiet->phu_thu_thoi_gian ?? 0))
                <tr style="background-color: #f8f9fa;">
                    <td style="padding-left: 30px; font-size: 11px; color: #6c757d;">• Món gọi quá giới hạn:</td>
                    <td style="font-size: 11px; color: #6c757d;">{{ number_format($chiTiet->phu_thu_tu_dong - ($chiTiet->phu_thu_thoi_gian ?? 0)) }} đ</td>
                </tr>
                @endif
                @endif
                @if($chiTiet && $chiTiet->phu_thu_thu_cong > 0)
                <tr style="background-color: #f8f9fa;">
                    <td style="padding-left: 20px; font-size: 12px;">- Phụ thu tự nhập tay:</td>
                    <td style="font-size: 12px;">+ {{ number_format($chiTiet->phu_thu_thu_cong) }} đ</td>
                </tr>
                @endif
                @endif
                <tr class="total-row">
                    <td>PHẢI THANH TOÁN:</td>
                    <td>{{ number_format($phaiThanhToan) }} đ</td>
                </tr>
                <tr>
                    <td>Đã thanh toán:</td>
                    <td style="color: #28a745; font-size: 16px;">{{ number_format($phaiThanhToan) }} đ</td>
                </tr>
                @php
                    // Lấy tiền khách đưa và tính lại tiền trả lại
                    $tienKhachDua = null;
                    $tienTraLai = 0;
                    if($chiTiet) {
                        $tienKhachDua = $chiTiet->tien_khach_dua ?? null;
                        // Tính lại tiền trả lại từ tiền khách đưa và phải thanh toán
                        if($tienKhachDua && $tienKhachDua > 0) {
                            $tienTraLai = max(0, $tienKhachDua - $phaiThanhToan);
                        }
                    }
                    // Kiểm tra phương thức thanh toán
                    $phuongThucTT = $chiTiet ? ($chiTiet->phuong_thuc_tt ?? null) : ($hoaDon->phuong_thuc_tt ?? null);
                @endphp
                @if($tienKhachDua && $tienKhachDua > 0 && $phuongThucTT == 'tien_mat')
                <tr>
                    <td>Tiền khách đưa:</td>
                    <td>{{ number_format($tienKhachDua) }} đ</td>
                </tr>
                @if($tienTraLai > 0)
                <tr>
                    <td style="color: #28a745; font-weight: bold;">Tiền trả lại:</td>
                    <td style="color: #28a745; font-weight: bold;">{{ number_format($tienTraLai) }} đ</td>
                </tr>
                @elseif($tienKhachDua < $phaiThanhToan)
                <tr>
                    <td style="color: #dc3545; font-weight: bold;">Thiếu:</td>
                    <td style="color: #dc3545; font-weight: bold;">{{ number_format($phaiThanhToan - $tienKhachDua) }} đ</td>
                </tr>
                @endif
                @endif
            </table>
        </div>

        {{-- Footer --}}
        <div class="invoice-footer">
            <p><em>Cảm ơn quý khách đã sử dụng dịch vụ của chúng tôi!</em></p>
            <p><em>Hẹn gặp lại quý khách lần sau.</em></p>
            
            <div class="signature">
                <table style="width: 100%; margin-top: 40px;">
                    <tr>
                        <td style="text-align: center; width: 50%;">
                            <p><strong>Người lập</strong></p>
                            <p style="margin-top: 50px;">(Ký, ghi rõ họ tên)</p>
                        </td>
                        <td style="text-align: center; width: 50%;">
                            <p><strong>Khách hàng</strong></p>
                            <p style="margin-top: 50px;">(Ký, ghi rõ họ tên)</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Tự động in khi tải trang (chỉ khi mở trong cửa sổ mới)
        window.onload = function() {
            // Chỉ tự động in nếu được mở từ window.open
            if (window.opener) {
                setTimeout(function() {
                    window.print();
                }, 500);
            }
        };
    </script>
</body>
</html>
