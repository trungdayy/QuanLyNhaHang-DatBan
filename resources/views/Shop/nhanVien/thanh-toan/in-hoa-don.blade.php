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
    @page {
        size: A4;
        margin: 1cm;
    }
}

/* ===== GENERAL ===== */
body {
    font-family: "Times New Roman", serif;
    font-size: 14px;
    color: #222;
    background: #fff;
}

h1, h2, h3, h4 {
    margin: 0;
    padding: 0;
}

/* ===== HEADER ===== */
.invoice-header {
    text-align: center;
    padding-bottom: 18px;
    margin-bottom: 30px;
    border-bottom: 3px solid #000;
}

.invoice-header h1 {
    font-size: 30px;
    letter-spacing: 1px;
    font-weight: bold;
}

.invoice-header p {
    margin: 3px 0;
    font-size: 14px;
}

/* ===== INVOICE INFO ===== */
.invoice-info table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
}

.invoice-info td {
    padding: 6px 8px;
    vertical-align: top;
}

.invoice-info td:first-child {
    font-weight: bold;
    width: 160px;
}

.invoice-info tr td:nth-child(3) {
    text-align: right;
    font-weight: bold;
}

/* ===== TABLE ITEMS ===== */
.invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
    font-size: 14px;
}

.invoice-table th {
    background: #eef1f5;
    font-weight: bold;
    text-align: center;
    padding: 10px;
    border: 1px solid #000;
}

.invoice-table td {
    padding: 8px;
    border: 1px solid #000;
}

.invoice-table tr:nth-child(even) {
    background: #fafafa;
}

.invoice-table td.text-center {
    text-align: center;
}

.invoice-table td.text-end {
    text-align: right;
}

/* ===== SUMMARY ===== */
.invoice-summary {
    width: 380px;
    margin-left: auto;
    font-size: 14px;
}

.invoice-summary table {
    width: 100%;
}

.invoice-summary td {
    padding: 7px 5px;
    border-bottom: 1px solid #ddd;
}

.invoice-summary td:nth-child(1) {
    font-weight: bold;
}

.invoice-summary td:nth-child(2) {
    text-align: right;
}

.invoice-summary tr.total-row td {
    border-top: 2px solid #222;
    border-bottom: 2px solid #222;
    font-size: 18px;
    font-weight: bold;
    padding: 10px 5px;
}

/* ===== FOOTER ===== */
.invoice-footer {
    text-align: center;
    margin-top: 50px;
    font-style: italic;
    font-size: 14px;
}

.signature table {
    width: 100%;
    margin-top: 45px;
}

.signature td {
    text-align: center;
    padding-top: 30px;
    font-size: 14px;
}

.signature strong {
    font-size: 15px;
}

/* ===== PRINT BUTTON ===== */
.no-print button,
.no-print a {
    font-size: 16px;
    padding: 10px 20px;
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
            <p>Địa chỉ: FPT POLYTECHNIC</p>
            <p>Điện thoại: 0123 xxx xxx | Email: info@buffetocean.com</p>
        </div>

        {{-- Thông tin hóa đơn --}}
        <div class="invoice-info">
            <table>
                <tr>
                    <td>Mã hóa đơn:</td>
                    <td><strong>{{ $hoaDon->ma_hoa_don }}</strong></td>
                    <td class="text-right">Ngày tạo:</td>
                    <td class="text-right"><strong>{{ $hoaDon->created_at->format('d/m/Y H:i:s') }}</strong></td>
                </tr>
                <tr>
                    <td>Khách hàng:</td>
                    <td>{{ $hoaDon->datBan->ten_khach ?? 'N/A' }}</td>
                    <td class="text-right">Bàn số:</td>
                    <td class="text-right"><strong>{{ $hoaDon->datBan->banAn->so_ban ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td>SĐT:</td>
                    <td>{{ $hoaDon->datBan->sdt_khach ?? 'N/A' }}</td>
                    <td class="text-right">Số khách:</td>
                    <td class="text-right"><strong>{{ $hoaDon->datBan->so_khach ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>{{ $hoaDon->datBan->email_khach ?? 'N/A' }}</td>
                    <td class="text-right">Khu vực:</td>
                    <td class="text-right"><strong>{{ $hoaDon->datBan->banAn->khuVuc->ten_khu_vuc ?? 'N/A' }}</strong></td>
                </tr>
                @if($gioVao)
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
                @if($hoaDon->datBan->comboBuffet && $hoaDon->datBan->comboBuffet->thoi_luong_phut)
                    @php
                        $thoiGianQuyDinh = $hoaDon->datBan->comboBuffet->thoi_luong_phut;
                        $thoiGianMienPhi = $thoiGianQuyDinh + 10;
                        $thoiGianVuot = max(0, $thoiGianPhucVu - $thoiGianMienPhi);
                    @endphp
                <tr>
                    <td>Thời gian quy định:</td>
                    <td><strong>{{ floor($thoiGianQuyDinh / 60) }} giờ {{ $thoiGianQuyDinh % 60 }} phút</strong></td>
                    <td class="text-right">Thời gian vượt quá:</td>
                    <td class="text-right">
                        <strong>
                            @if($thoiGianVuot > 0)
                                <span style="color: #dc3545;">{{ floor($thoiGianVuot / 60) }} giờ {{ $thoiGianVuot % 60 }} phút</span>
                            @else
                                <span style="color: #28a745;">0 phút</span>
                            @endif
                        </strong>
                    </td>
                </tr>
                @endif
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
                    $stt = 1;
                    $combo = $hoaDon->datBan->comboBuffet;
                    $soKhach = $hoaDon->datBan->so_khach;
                    
                    // Lấy danh sách món trong combo với giới hạn và phụ phí
                    $monTrongCombo = collect();
                    if($combo) {
                        $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $combo->id)
                            ->get()
                            ->keyBy('mon_an_id');
                    }
                    
                    // Tính tổng số lượng đã order cho từng món (cả combo và goi_them)
                    $tongSoLuongMon = [];
                    $monAnData = []; // Lưu thông tin món để hiển thị
                    
                    foreach($hoaDon->datBan->orderMon as $order) {
                        foreach($order->chiTietOrders as $ct) {
                            if($ct->trang_thai != 'huy_mon') {
                                $monAnId = $ct->mon_an_id;
                                if(!isset($tongSoLuongMon[$monAnId])) {
                                    $tongSoLuongMon[$monAnId] = 0;
                                    $monAnData[$monAnId] = $ct->monAn;
                                }
                                $tongSoLuongMon[$monAnId] += $ct->so_luong;
                            }
                        }
                    }
                    
                    // Tính số lượng và tiền cho từng món (gộp lại)
                    $monGoiThemList = [];
                    foreach($tongSoLuongMon as $monAnId => $tongSoLuong) {
                        $monAn = $monAnData[$monAnId];
                        $monTrongComboItem = $monTrongCombo->get($monAnId);
                        $soLuongTinhTien = 0;
                        $giaGoc = $monAn->gia ?? 0;
                        $phuPhi = 0;
                        
                        if($monTrongComboItem) {
                            // Món trong combo: chỉ tính tiền cho phần vượt quá
                            $gioiHan = $monTrongComboItem->gioi_han_so_luong ?? null;
                            $phuPhi = $monTrongComboItem->phu_phi_goi_them ?? 0;
                            
                            if($gioiHan !== null && $gioiHan > 0) {
                                $soLuongVuot = max(0, $tongSoLuong - $gioiHan);
                                if($soLuongVuot > 0) {
                                    $soLuongTinhTien = $soLuongVuot;
                                }
                            } else {
                                // Không có giới hạn: tính toàn bộ
                                $soLuongTinhTien = $tongSoLuong;
                            }
                        } else {
                            // Món không trong combo: tính toàn bộ
                            $soLuongTinhTien = $tongSoLuong;
                        }
                        
                        // Chỉ thêm vào danh sách nếu có số lượng tính tiền > 0
                        if($soLuongTinhTien > 0) {
                            // Phụ phí tính theo số lần gọi món (nhân với số lượng)
                            $tongTienGiaGoc = $giaGoc * $soLuongTinhTien;
                            $tongPhuPhi = $phuPhi * $soLuongTinhTien; // Phụ phí nhân với số lượng
                            $thanhTien = $tongTienGiaGoc + $tongPhuPhi;
                            
                            $monGoiThemList[$monAnId] = [
                                'monAn' => $monAn,
                                'soLuong' => $soLuongTinhTien,
                                'giaGoc' => $giaGoc,
                                'phuPhi' => $phuPhi,
                                'tongTienGiaGoc' => $tongTienGiaGoc,
                                'tongPhuPhi' => $tongPhuPhi,
                                'thanhTien' => $thanhTien
                            ];
                        }
                    }
                    
                    // Tính lại tổng tiền món gọi thêm từ danh sách đã gộp (bao gồm phụ phí)
                    $tongTienMonGoiThemTinhLai = 0;
                    foreach($monGoiThemList as $monData) {
                        $tongTienMonGoiThemTinhLai += $monData['thanhTien'];
                    }
                    
                    // Tính lại tổng tiền thực tế
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

                {{-- Hiển thị món gọi thêm (đã gộp) --}}
                @foreach($monGoiThemList as $monAnId => $monData)
                <tr>
                    <td class="text-center">{{ $stt++ }}</td>
                    <td>{{ $monData['monAn']->ten_mon ?? 'N/A' }} (Gọi thêm)</td>
                    <td class="text-center">{{ $monData['soLuong'] }}</td>
                    <td class="text-end">
                        @if($monData['phuPhi'] > 0)
                            <div style="font-size: 12px; line-height: 1.4;">
                                <div>{{ number_format($monData['giaGoc']) }} đ × {{ $monData['soLuong'] }}</div>
                                <div style="color: #dc3545;">+ {{ number_format($monData['phuPhi']) }} đ (phụ phí) × {{ $monData['soLuong'] }}</div>
                                <div style="font-weight: bold;">= {{ number_format($monData['thanhTien']) }} đ</div>
                            </div>
                        @else
                            {{ number_format($monData['giaGoc']) }} đ
                        @endif
                    </td>
                    <td class="text-end">{{ number_format($monData['thanhTien']) }} đ</td>
                </tr>
                @endforeach
                
                {{-- Tổng kết --}}
                @if($tienComboChinh > 0 || $tongTienMonGoiThemTinhLai > 0)
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="4" class="text-end">Tổng tiền combo chính:</td>
                    <td class="text-end">{{ number_format($tienComboChinh) }} đ</td>
                </tr>
                @if($tongTienMonGoiThemTinhLai > 0)
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="4" class="text-end">Tổng tiền món gọi thêm:</td>
                    <td class="text-end">{{ number_format($tongTienMonGoiThemTinhLai) }} đ</td>
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
                <tr>
                    <td>Tổng tiền (Combo + Món):</td>
                    <td>{{ number_format($tongTienThucTeTinhLai) }} đ</td>
                </tr>
                @if($hoaDon->voucher)
                <tr>
                    <td>(-) Tiền giảm (Voucher {{ $hoaDon->voucher->ma_voucher }}):</td>
                    <td>- {{ number_format($hoaDon->tien_giam) }} đ</td>
                </tr>
                <tr style="background-color: #fffacd;">
                    <td style="font-weight: bold;">Tổng tiền sau voucher:</td>
                    <td style="font-weight: bold;">{{ number_format($tongTienThucTeTinhLai - ($hoaDon->tien_giam ?? 0)) }} đ</td>
                </tr>
                @endif
                @if($hoaDon->datBan->tien_coc > 0)
                <tr>
                    <td>(-) Tiền cọc:</td>
                    <td>- {{ number_format($hoaDon->datBan->tien_coc) }} đ</td>
                </tr>
                @endif
                @if($hoaDon->phu_thu > 0)
                <tr>
                    <td>(+) Phụ thu:</td>
                    <td>+ {{ number_format($hoaDon->phu_thu) }} đ</td>
                </tr>
                @endif
                @php
                    // Tính lại phải thanh toán từ giá trị tính lại trong view
                    $tongTienSauVoucherTinhLai = $tongTienThucTeTinhLai - ($hoaDon->tien_giam ?? 0);
                    $phaiThanhToanTinhLai = $tongTienSauVoucherTinhLai - ($hoaDon->datBan->tien_coc ?? 0) + ($hoaDon->phu_thu ?? 0);
                    if($phaiThanhToanTinhLai < 0) $phaiThanhToanTinhLai = 0;
                @endphp
                <tr class="total-row">
                    <td>PHẢI THANH TOÁN:</td>
                    <td>{{ number_format($phaiThanhToanTinhLai) }} đ</td>
                </tr>
                <tr>
                    <td>Đã thanh toán:</td>
                    <td style="color: #28a745; font-size: 16px;">{{ number_format($phaiThanhToanTinhLai) }} đ</td>
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

