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
                    <td class="text-right"><strong>{{ $chiTiet ? $chiTiet->so_khach : ($hoaDon->datBan->so_khach ?? 'N/A') }}</strong></td>
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
                    <th style="width: 40%;">Tên món</th>
                    <th style="width: 15%;" class="text-center">Số lượng</th>
                    <th style="width: 20%;" class="text-end">Đơn giá</th>
                    <th style="width: 20%;" class="text-end">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $chiTiet = $hoaDon->chiTietHoaDon;
                    
                    if ($chiTiet && $chiTiet->danh_sach_mon) {
                        // Sử dụng dữ liệu từ chi_tiet_hoa_don
                        $stt = 1;
                        $tongTienCombo = $chiTiet->tong_tien_combo ?? 0;
                        $tongTienMonGoiThem = 0;
                        foreach($chiTiet->danh_sach_mon as $mon) {
                            $tongTienMonGoiThem += $mon['thanh_tien'];
                        }
                        $tongTienComboMon = $tongTienCombo + $tongTienMonGoiThem;
                    } else {
                        // Fallback cho hóa đơn cũ
                        $stt = 1;
                        $combo = $hoaDon->datBan->comboBuffet;
                        $soKhach = $hoaDon->datBan->so_khach;
                    }
                @endphp
                
                @if($chiTiet && $chiTiet->danh_sach_mon)
                    {{-- Hiển thị từ chi_tiet_hoa_don --}}
                    {{-- Combo chính --}}
                    @if($chiTiet->tong_tien_combo > 0)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td><strong>{{ $chiTiet->ten_combo }}</strong> (Combo chính)</td>
                        <td class="text-center">{{ $chiTiet->so_khach }} khách</td>
                        <td class="text-end">{{ number_format($chiTiet->gia_combo_per_person) }} đ</td>
                        <td class="text-end"><strong>{{ number_format($chiTiet->tong_tien_combo) }} đ</strong></td>
                    </tr>
                    @endif

                    {{-- Danh sách món --}}
                    @foreach($chiTiet->danh_sach_mon as $mon)
                    <tr>
                        <td class="text-center">{{ $mon['stt'] }}</td>
                        <td>
                            {{ $mon['ten_mon'] }}
                            @if($mon['la_mon_combo'])
                                <span style="font-size: 11px; color: #856404;">(Món combo)</span>
                                @if($mon['vuot_gioi_han'])
                                    <span style="font-size: 11px; color: #dc3545;">(Vượt giới hạn)</span>
                                @endif
                            @else
                                <span style="font-size: 11px; color: #0c5460;">(Gọi thêm)</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $mon['so_luong'] }}
                            @if($mon['gioi_han'] !== null)
                                <br><small style="font-size: 10px;">(Giới hạn: {{ $mon['gioi_han'] }})</small>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($mon['don_gia'] > 0)
                                @if($mon['phu_phi'] > 0)
                                    <div style="font-size: 11px; line-height: 1.4;">
                                        <div>{{ number_format($mon['don_gia']) }} đ</div>
                                        <div style="color: #dc3545;">+ {{ number_format($mon['phu_phi']) }} đ (phụ phí)</div>
                                    </div>
                                @else
                                    {{ number_format($mon['don_gia']) }} đ
                                @endif
                            @else
                                <span style="color: #28a745;">0 đ</span>
                                <br><small style="font-size: 10px;">(Đã bao gồm)</small>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($mon['thanh_tien'] > 0)
                                <strong>{{ number_format($mon['thanh_tien']) }} đ</strong>
                            @else
                                <span style="color: #28a745;">0 đ</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    
                    {{-- Tổng kết --}}
                    @if($tongTienCombo > 0)
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td colspan="4" class="text-end">Tổng tiền combo chính:</td>
                        <td class="text-end">{{ number_format($tongTienCombo) }} đ</td>
                    </tr>
                    @endif
                    @if($tongTienMonGoiThem > 0)
                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                        <td colspan="4" class="text-end">Tổng tiền món gọi thêm:</td>
                        <td class="text-end">{{ number_format($tongTienMonGoiThem) }} đ</td>
                    </tr>
                    @endif
                    <tr style="background-color: #e0e0e0; font-weight: bold; font-size: 16px;">
                        <td colspan="4" class="text-end">TỔNG CỘNG:</td>
                        <td class="text-end">{{ number_format($tongTienComboMon) }} đ</td>
                    </tr>
                @else
                    {{-- Fallback cho hóa đơn cũ --}}
                    @php
                        $combo = $hoaDon->datBan->comboBuffet;
                        $soKhach = $hoaDon->datBan->so_khach;
                        $tienComboChinh = isset($tienComboChinh) ? $tienComboChinh : ($combo ? ($combo->gia_co_ban * $soKhach) : 0);
                        $tongTienMonGoiThemTinhLai = isset($tongTienMonGoiThem) ? $tongTienMonGoiThem : 0;
                        $tongTienThucTeTinhLai = $tienComboChinh + $tongTienMonGoiThemTinhLai;
                    @endphp
                    {{-- Combo chính --}}
                    @if($combo && $tienComboChinh > 0)
                    <tr>
                        <td class="text-center">{{ $stt++ }}</td>
                        <td><strong>{{ $combo->ten_combo }}</strong> (Combo chính)</td>
                        <td class="text-center">{{ $soKhach }} khách</td>
                        <td class="text-end">{{ number_format($combo->gia_co_ban) }} đ</td>
                        <td class="text-end"><strong>{{ number_format($tienComboChinh) }} đ</strong></td>
                    </tr>
                    @endif
                    <tr style="background-color: #e0e0e0; font-weight: bold; font-size: 16px;">
                        <td colspan="4" class="text-end">TỔNG CỘNG:</td>
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
                        // Sử dụng dữ liệu từ chi_tiet_hoa_don
                        $tongTienComboMon = $tongTienComboMon ?? ($chiTiet->tong_tien_combo_mon ?? 0);
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
                @endif
                <tr class="total-row">
                    <td>PHẢI THANH TOÁN:</td>
                    <td>{{ number_format($phaiThanhToan) }} đ</td>
                </tr>
                <tr>
                    <td>Đã thanh toán:</td>
                    <td style="color: #28a745; font-size: 16px;">{{ number_format($phaiThanhToan) }} đ</td>
                </tr>
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
