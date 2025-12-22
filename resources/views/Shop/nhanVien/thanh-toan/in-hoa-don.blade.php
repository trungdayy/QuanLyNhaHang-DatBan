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
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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
            /* Loại bỏ tất cả màu sắc khi in */
            * {
                color: #000 !important;
                background-color: #fff !important;
                border-color: #000 !important;
            }
            .invoice-table th {
                background-color: #f0f0f0 !important;
            }
            .invoice-table td {
                background-color: #fff !important;
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
            @php
                // Kiểm tra xem có phải hóa đơn thanh toán sau không
                $chiTiet = $hoaDon->chiTietHoaDon;
                $phuongThucTT = $chiTiet ? ($chiTiet->phuong_thuc_tt ?? null) : ($hoaDon->phuong_thuc_tt ?? null);
                $laThanhToanSau = $hoaDon->trang_thai == 'da_thanh_toan' && ($phuongThucTT == 'chua_thanh_toan' || !$phuongThucTT);
            @endphp
            @if($laThanhToanSau)
                <a href="{{ route('nhanVien.ban-an.index') }}" class="btn btn-secondary btn-lg ms-2">
                    <i class="bi bi-x-circle me-2"></i>Đóng
                </a>
            @else
                <a href="javascript:window.close()" class="btn btn-secondary btn-lg ms-2">
                    <i class="bi bi-x-circle me-2"></i>Đóng
                </a>
            @endif
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
                            @php
                                // Chỉ hiển thị "Đã thanh toán" khi đã thanh toán nhưng không biết phương thức (thanh toán sau)
                                $chiTiet = $hoaDon->chiTietHoaDon;
                                $phuongThucTT = $chiTiet ? ($chiTiet->phuong_thuc_tt ?? null) : ($hoaDon->phuong_thuc_tt ?? null);
                                
                                if($hoaDon->trang_thai == 'da_thanh_toan' && ($phuongThucTT == 'chua_thanh_toan' || !$phuongThucTT)) {
                                    // Đã thanh toán sau (không xác định phương thức)
                                    $hienThi = 'Đã thanh toán';
                                } elseif($phuongThucTT == 'tien_mat') {
                                    $hienThi = 'Tiền mặt';
                                } elseif($phuongThucTT == 'chuyen_khoan') {
                                    $hienThi = 'Chuyển khoản';
                                } elseif($phuongThucTT == 'the_ATM') {
                                    $hienThi = 'Thẻ ATM';
                                } elseif($phuongThucTT == 'vnpay') {
                                    $hienThi = 'VNPay';
                                } elseif($phuongThucTT == 'payos') {
                                    $hienThi = 'PayOS';
                                } else {
                                    $hienThi = 'Chưa thanh toán';
                                }
                            @endphp
                            {{ $hienThi }}
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
                    <th style="width: 40%;">Tên món</th>
                    <th style="width: 15%;" class="text-center">Số lượng</th>
                    <th style="width: 20%;" class="text-end">Đơn giá</th>
                    <th style="width: 20%;" class="text-end">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Tính và hiển thị combo trước
                    $soTreEm = $hoaDon->datBan->tre_em ?? 0;
                    $soNguoiDaXuLy = 0;
                    
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
                    
                    // Khởi tạo biến đếm STT
                    $stt = 1;
                @endphp
                
                {{-- Hiển thị Combo với giảm giá cho trẻ em --}}
                @php
                    $chiTiet = $hoaDon->chiTietHoaDon;
                @endphp
                @if($chiTiet && $chiTiet->tong_tien_combo > 0 && $hoaDon->datBan->chiTietDatBan)
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
                    <td class="text-center" data-label="STT">{{ $stt++ }}</td>
                    <td data-label="Tên món">
                        <strong>{{ $chiTietCombo->combo->ten_combo }}</strong>
                        <br><small>Combo Buffet - Trẻ em (Giảm 50%)</small>
                    </td>
                    <td class="text-center" data-label="Số lượng">{{ $soNguoiDuocGiam }} người</td>
                    <td class="text-end" data-label="Đơn giá">
                        <span class="text-decoration-line-through" style="font-size: 11px;">{{ number_format($giaComboGoc) }} đ</span>
                        <br>
                        <span class="text-danger fw-bold">{{ number_format($giaComboGiam) }} đ</span>
                        <br><small class="text-success">(Giảm 50%)</small>
                    </td>
                    <td class="text-end" data-label="Thành tiền">
                        <strong>{{ number_format($thanhTienGiam) }} đ</strong>
                    </td>
                </tr>
                @php
                    }
                    
                    // Hiển thị combo giá gốc (người lớn) nếu có
                    if($soNguoiKhongGiam > 0) {
                        $thanhTienGoc = $giaComboGoc * $soNguoiKhongGiam;
                @endphp
                <tr>
                    <td class="text-center" data-label="STT">{{ $stt++ }}</td>
                    <td data-label="Tên món">
                        <strong>{{ $chiTietCombo->combo->ten_combo }}</strong>
                        <br><small>Combo Buffet - Người lớn</small>
                    </td>
                    <td class="text-center" data-label="Số lượng">{{ $soNguoiKhongGiam }} người</td>
                    <td class="text-end" data-label="Đơn giá">{{ number_format($giaComboGoc) }} đ/người</td>
                    <td class="text-end" data-label="Thành tiền">
                        <strong>{{ number_format($thanhTienGoc) }} đ</strong>
                    </td>
                </tr>
                @php
                    }
                    
                    $soNguoiDaXuLy += $soLuongCombo;
                @endphp
                @endif
                @endforeach
                @endif
                
                @php
                    // Sử dụng dữ liệu đã lưu từ chi_tiet_hoa_don
                    if ($chiTiet) {
                        $tongTienCombo = $chiTiet->tong_tien_combo ?? 0;
                        $tongTienMonGoiThem = $chiTiet->tong_tien_mon_goi_them ?? 0;
                        $tongTienComboMon = $chiTiet->tong_tien_combo_mon ?? $hoaDon->tong_tien ?? 0;
                    } else {
                        // Fallback cho hóa đơn cũ
                        $tongTienCombo = 0;
                        $tongTienMonGoiThem = 0;
                        $tongTienComboMon = $hoaDon->tong_tien ?? 0;
                        $combo = $hoaDon->datBan->comboBuffet ?? null;
                        $soKhach = $hoaDon->datBan->so_khach ?? 0;
                    }
                @endphp
                
                @if($chiTiet && $chiTiet->danh_sach_mon)
                    @php
                        // Decode JSON từ danh_sach_mon
                        $danhSachMonArray = is_string($chiTiet->danh_sach_mon) 
                            ? json_decode($chiTiet->danh_sach_mon, true) 
                            : $chiTiet->danh_sach_mon;
                        
                        $tongTienMonGoiThemTinhLai = 0;
                        $sttMon = 1;
                        
                        // Lọc chỉ các món gọi thêm (không phải món combo)
                        $danhSachMonGoiThem = [];
                        if (is_array($danhSachMonArray)) {
                            foreach($danhSachMonArray as $mon) {
                                if(!isset($mon['la_mon_combo']) || !$mon['la_mon_combo']) {
                                    // Chỉ lấy món gọi thêm (la_mon_combo = false hoặc không có)
                                    if(isset($mon['thanh_tien']) && $mon['thanh_tien'] > 0) {
                                        $danhSachMonGoiThem[] = $mon;
                                    }
                                }
                            }
                        }
                    @endphp
                    
                    {{-- Hiển thị món gọi thêm từ danh_sach_mon --}}
                    @foreach($danhSachMonGoiThem as $mon)
                    <tr>
                        <td class="text-center" data-label="STT">{{ $sttMon++ }}</td>
                        <td data-label="Tên món">
                            {{ $mon['ten_mon'] ?? 'N/A' }}
                        </td>
                        <td class="text-center" data-label="Số lượng">
                            {{ $mon['so_luong'] ?? 0 }}
                        </td>
                        <td class="text-end" data-label="Đơn giá">
                            {{ number_format($mon['don_gia'] ?? 0) }} đ
                        </td>
                        <td class="text-end" data-label="Thành tiền">
                            <strong>{{ number_format($mon['thanh_tien'] ?? 0) }} đ</strong>
                            @php
                                $tongTienMonGoiThemTinhLai += ($mon['thanh_tien'] ?? 0);
                            @endphp
                        </td>
                    </tr>
                    @endforeach
                    @php
                        // Sử dụng dữ liệu đã tính từ danh_sach_mon hoặc từ chi tiết
                        if($tongTienMonGoiThemTinhLai > 0) {
                            $tongTienMonGoiThem = $tongTienMonGoiThemTinhLai;
                        } else {
                            $tongTienMonGoiThem = $chiTiet->tong_tien_mon_goi_them ?? 0;
                        }
                    @endphp
                    
                    {{-- Tổng kết món gọi thêm --}}
                    @if($tongTienMonGoiThem > 0)
                    <tr style="background-color: #e0e0e0; font-weight: bold; font-size: 16px;">
                        <td colspan="4" class="text-end">TỔNG CỘNG MÓN GỌI THÊM:</td>
                        <td class="text-end">{{ number_format($tongTienMonGoiThem) }} đ</td>
                    </tr>
                    @else
                    <tr style="background-color: #e0e0e0; font-weight: bold; font-size: 16px;">
                        <td colspan="4" class="text-end">TỔNG CỘNG MÓN GỌI THÊM:</td>
                        <td class="text-end">0 đ</td>
                    </tr>
                    @endif
                @else
                    {{-- Fallback cho hóa đơn cũ (không có chi_tiet_hoa_don) --}}
                    @php
                        $tongTienMonGoiThemTinhLai = 0;
                        $sttMon = 1;
                        $combo = $hoaDon->datBan->comboBuffet ?? null;
                        $soKhach = $hoaDon->datBan->so_khach ?? 0;
                    @endphp
                    
                    {{-- Chỉ hiển thị món gọi thêm (không hiển thị combo) --}}
                    @foreach($monAnGrouped as $monAnId => $monAnGroup)
                    @php
                        $ctFirst = $monAnGroup->first();
                        
                        // Kiểm tra xem có phải món combo không (dựa vào giới hạn)
                        $tongGioiHan = $tongGioiHanMon[$monAnId] ?? null;
                        $tongSoLuong = $monAnGroup->sum('so_luong');
                        $coTrongCombo = false;
                        if($tongGioiHan !== null && $tongGioiHan > 0 && $tongSoLuong <= $tongGioiHan) {
                            $coTrongCombo = true; // Món trong combo, không vượt giới hạn
                        }
                        
                        // Chỉ hiển thị món gọi thêm (không phải món combo)
                        if($coTrongCombo) {
                            continue;
                        }
                        
                        // Tính số lượng theo trạng thái: chỉ tính đã lên + đang nấu + chờ cung ứng (không tính hủy, chờ bếp)
                        $soLuongDaLen = 0;
                        $soLuongDangCheBien = 0;
                        $soLuongChoCungUng = 0;
                        
                        foreach($monAnGroup as $ct) {
                            if($ct->trang_thai == 'da_len_mon') {
                                $soLuongDaLen += $ct->so_luong;
                            } elseif($ct->trang_thai == 'dang_che_bien') {
                                $soLuongDangCheBien += $ct->so_luong;
                            } elseif($ct->trang_thai == 'cho_cung_ung') {
                                $soLuongChoCungUng += $ct->so_luong;
                            }
                            // Bỏ qua: huy_mon, cho_bep
                        }
                        
                        // Số lượng hiển thị = số lượng tính tiền (chỉ tính các trạng thái được tính tiền)
                        $tongSoLuongHienThi = $soLuongDaLen + $soLuongDangCheBien + $soLuongChoCungUng;
                        
                        // Chỉ hiển thị món có số lượng > 0
                        if($tongSoLuongHienThi <= 0) {
                            continue;
                        }
                        
                        // Lấy đơn giá
                        $donGiaGoc = 0;
                        if($monAnGroup && $monAnGroup->first()->monAn) {
                            $donGiaGoc = $monAnGroup->first()->monAn->gia ?? 0;
                        }
                        
                        // Tính thành tiền: đã lên + đang nấu + chờ cung ứng
                        $thanhTienTinhLai = ($donGiaGoc * $soLuongDaLen) + ($donGiaGoc * $soLuongDangCheBien) + ($donGiaGoc * $soLuongChoCungUng);
                    @endphp
                    <tr>
                        <td class="text-center" data-label="STT">{{ $sttMon++ }}</td>
                        <td data-label="Tên món">
                            {{ $ctFirst->monAn->ten_mon ?? 'N/A' }}
                        </td>
                        <td class="text-center" data-label="Số lượng">
                            {{ $tongSoLuongHienThi }}
                        </td>
                        <td class="text-end" data-label="Đơn giá">
                            {{ number_format($donGiaGoc) }} đ
                        </td>
                        <td class="text-end" data-label="Thành tiền">
                            <strong>{{ number_format($thanhTienTinhLai) }} đ</strong>
                            @php
                                $tongTienMonGoiThemTinhLai += $thanhTienTinhLai;
                            @endphp
                        </td>
                    </tr>
                    @endforeach
                    @php
                        // Tính tổng: combo + món gọi thêm
                        $tongTienComboMon = $tongTienCombo + $tongTienMonGoiThemTinhLai;
                    @endphp
                    @if($tongTienCombo > 0)
                    <tr style="background-color: #e0e0e0; font-weight: bold;">
                        <td colspan="4" class="text-end">Tiền combo:</td>
                        <td class="text-end">{{ number_format($tongTienCombo) }} đ</td>
                    </tr>
                    @endif
                    @if($tongTienMonGoiThemTinhLai > 0)
                    <tr style="background-color: #e0e0e0; font-weight: bold;">
                        <td colspan="4" class="text-end">Tiền món:</td>
                        <td class="text-end">{{ number_format($tongTienMonGoiThemTinhLai) }} đ</td>
                    </tr>
                    @endif
                    <tr style="background-color: #e0e0e0; font-weight: bold; font-size: 16px; border-top: 2px solid #000;">
                        <td colspan="4" class="text-end">TỔNG TIỀN HÀNG:</td>
                        <td class="text-end">{{ number_format($tongTienComboMon) }} đ</td>
                    </tr>
                @endif
            </tbody>
        </table>

        {{-- Tóm tắt thanh toán --}}
        <div class="invoice-summary">
            <table>
                @php
                    // Tính tổng tiền: combo + món gọi thêm
                    $tongTienMonGoiThem = $tongTienMonGoiThemTinhLai ?? 0;
                    $tongTienComboMon = $tongTienCombo + $tongTienMonGoiThem;
                    
                    // Tính tổng tiền sau voucher
                    $tongTienSauVoucher = $tongTienComboMon - (($chiTiet && $chiTiet->tien_giam_voucher) ? $chiTiet->tien_giam_voucher : ($hoaDon->tien_giam ?? 0));
                    if($tongTienSauVoucher < 0) $tongTienSauVoucher = 0;
                    
                    // Tính phải thanh toán: tổng tiền sau voucher - tiền cọc
                    $phaiThanhToan = $tongTienSauVoucher - (($chiTiet && $chiTiet->tien_coc) ? $chiTiet->tien_coc : ($hoaDon->datBan->tien_coc ?? 0));
                    if($phaiThanhToan < 0) $phaiThanhToan = 0;
                @endphp
                @if($tongTienCombo > 0)
                <tr>
                    <td>Tiền combo:</td>
                    <td>{{ number_format($tongTienCombo) }} đ</td>
                </tr>
                @endif
                @if($tongTienMonGoiThem > 0)
                <tr>
                    <td>Tiền món:</td>
                    <td>{{ number_format($tongTienMonGoiThem) }} đ</td>
                </tr>
                @endif
                <tr style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
                    <td style="font-weight: bold;">Tổng tiền hàng:</td>
                    <td style="font-weight: bold;">{{ number_format($tongTienComboMon) }} đ</td>
                </tr>
                @if(($chiTiet && $chiTiet->tien_giam_voucher > 0) || (!$chiTiet && $hoaDon->voucher))
                <tr>
                    <td>(-) Tiền giảm (Voucher {{ $chiTiet ? $chiTiet->ma_voucher : ($hoaDon->voucher->ma_voucher ?? '') }}):</td>
                    <td>- {{ number_format($chiTiet ? $chiTiet->tien_giam_voucher : ($hoaDon->tien_giam ?? 0)) }} đ</td>
                </tr>
                <tr style="background-color: #f0f0f0;">
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
                <tr class="total-row">
                    <td>PHẢI THANH TOÁN:</td>
                    <td>{{ number_format($phaiThanhToan) }} đ</td>
                </tr>
                <tr>
                    <td>Đã thanh toán:</td>
                    <td style="font-size: 16px;">
                        @php
                            $chiTiet = $hoaDon->chiTietHoaDon;
                            // Xác định đã thanh toán chưa: kiểm tra phuong_thuc_tt hoặc tien_khach_dua
                            $daThanhToan = false;
                            $soTienDaThanhToan = 0;
                            
                            if($chiTiet) {
                                // Nếu có phuong_thuc_tt và khác 'chua_thanh_toan' thì đã thanh toán
                                if($chiTiet->phuong_thuc_tt && $chiTiet->phuong_thuc_tt != 'chua_thanh_toan') {
                                    $daThanhToan = true;
                                    // Lấy số tiền đã thanh toán từ phai_thanh_toan (đã tính đúng từ chi_tiet_hoa_don)
                                    $soTienDaThanhToan = $chiTiet->phai_thanh_toan ?? $phaiThanhToan;
                                } elseif($chiTiet->tien_khach_dua && $chiTiet->tien_khach_dua > 0) {
                                    // Nếu có tien_khach_dua thì đã thanh toán
                                    $daThanhToan = true;
                                    $soTienDaThanhToan = $chiTiet->phai_thanh_toan ?? $phaiThanhToan;
                                } elseif($hoaDon->trang_thai == 'da_thanh_toan') {
                                    $daThanhToan = true;
                                    // Nếu có da_thanh_toan trong hoa_don thì dùng, nếu không thì dùng phaiThanhToan đã tính
                                    $soTienDaThanhToan = $hoaDon->da_thanh_toan ?? $phaiThanhToan;
                                }
                            } elseif($hoaDon->trang_thai == 'da_thanh_toan') {
                                $daThanhToan = true;
                                $soTienDaThanhToan = $hoaDon->da_thanh_toan ?? $phaiThanhToan;
                            }
                        @endphp
                        {{ number_format($soTienDaThanhToan) }} đ
                    </td>
                </tr>
                @php
                    // Lấy tiền khách đưa và tiền trả lại từ chi_tiet_hoa_don
                    $tienKhachDua = $chiTiet->tien_khach_dua ?? null;
                    $tienTraLai = $chiTiet->tien_tra_lai ?? 0;
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
                    <td style="font-weight: bold;">Tiền trả lại:</td>
                    <td style="font-weight: bold;">{{ number_format($tienTraLai) }} đ</td>
                </tr>
                @elseif($tienKhachDua < $phaiThanhToan)
                <tr>
                    <td style="font-weight: bold;">Thiếu:</td>
                    <td style="font-weight: bold;">{{ number_format($phaiThanhToan - $tienKhachDua) }} đ</td>
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