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
                                    // Tính số khách từ chiTietDatBan dựa trên loại combo
                                    $soNguoiLon = 0;
                                    $soTreEm = 0;
                                    if($datBan->chiTietDatBan && $datBan->chiTietDatBan->count() > 0) {
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
                                    } else {
                                        // Fallback: lấy từ database nếu không có chiTietDatBan
                                        $soNguoiLon = $datBan->nguoi_lon ?? 0;
                                        $soTreEm = $datBan->tre_em ?? 0;
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
                                    @foreach($datBan->chiTietDatBan as $chiTiet)
                                        @if($chiTiet->combo)
                                        <div class="mb-3 p-3 bg-light rounded border border-warning">
                                            <h6 class="fw-bold text-primary mb-2">
                                                <i class="bi bi-star-fill me-1 text-warning"></i>{{ $chiTiet->combo->ten_combo }}
                                                <span class="badge bg-success ms-2">Combo chính</span>
                                            </h6>
                                            <p class="mb-1"><strong>Giá combo:</strong> {{ number_format($chiTiet->combo->gia_co_ban) }} đ/người</p>
                                            <p class="mb-1"><strong>Số lượng:</strong> {{ $chiTiet->so_luong ?? 1 }} người</p>
                                            <p class="mb-0 mt-2"><strong>Thành tiền combo:</strong> 
                                                <span class="text-danger fw-bold fs-5">{{ number_format($chiTiet->combo->gia_co_ban * ($chiTiet->so_luong ?? 1)) }} đ</span>
                                            </p>
                                        </div>
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
                                    
                                    // Lấy tất cả món từ các order
                                    foreach($datBan->orderMon as $order) {
                                        foreach($order->chiTietOrders as $ct) {
                                            if($ct->trang_thai != 'huy_mon') {
                                                $hasMonAn = true;
                                                $monAnList->push($ct);
                                            }
                                        }
                                    }
                                    
                                    // Tính tổng tiền combo: tính từng combo với số lượng tương ứng
                                    $tongTienComboChinh = 0;
                                    foreach($datBan->chiTietDatBan as $chiTiet) {
                                        if($chiTiet->combo) {
                                            $tongTienComboChinh += ($chiTiet->combo->gia_co_ban ?? 0) * ($chiTiet->so_luong ?? 1);
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
                                                <th class="text-end">Đơn giá</th>
                                                <th class="text-end">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php 
                                                $stt = 1;
                                                $tongTienGoiThem = 0;
                                                
                                                // Lấy danh sách món trong tất cả các combo với giới hạn
                                                // Key: mon_an_id, Value: tổng giới hạn từ tất cả các combo
                                                $tongGioiHanMon = [];
                                                foreach($datBan->chiTietDatBan as $chiTiet) {
                                                    if($chiTiet->combo) {
                                                        $monTrongCombo = \App\Models\MonTrongCombo::where('combo_id', $chiTiet->combo->id)->get();
                                                        foreach($monTrongCombo as $mtc) {
                                                            $monAnId = $mtc->mon_an_id;
                                                            $gioiHan = $mtc->gioi_han_so_luong ?? null;
                                                            if($gioiHan !== null && $gioiHan > 0) {
                                                                // Nhân giới hạn với số lượng combo
                                                                $soLuongCombo = $chiTiet->so_luong ?? 1;
                                                                if(!isset($tongGioiHanMon[$monAnId])) {
                                                                    $tongGioiHanMon[$monAnId] = 0;
                                                                }
                                                                $tongGioiHanMon[$monAnId] += $gioiHan * $soLuongCombo;
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                // Tính tổng số lượng đã order cho từng món (cả combo và goi_them)
                                                $tongSoLuongMon = [];
                                                foreach($monAnList as $ct) {
                                                    $monAnId = $ct->mon_an_id;
                                                    if(!isset($tongSoLuongMon[$monAnId])) {
                                                        $tongSoLuongMon[$monAnId] = 0;
                                                    }
                                                    $tongSoLuongMon[$monAnId] += $ct->so_luong;
                                                }
                                                
                                                // Tính số lượng vượt quá cho từng món
                                                $soLuongVuotMon = [];
                                                foreach($tongSoLuongMon as $monAnId => $tongSoLuong) {
                                                    $tongGioiHan = $tongGioiHanMon[$monAnId] ?? null;
                                                    if($tongGioiHan !== null && $tongGioiHan > 0) {
                                                        $soLuongVuotMon[$monAnId] = max(0, $tongSoLuong - $tongGioiHan);
                                                    } else {
                                                        // Món không có trong combo hoặc không có giới hạn: không tính vượt quá
                                                        $soLuongVuotMon[$monAnId] = 0;
                                                    }
                                                }
                                                
                                                // Đếm số lượng đã phân bổ cho phần vượt quá
                                                $daPhanBoVuot = [];
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
                                                    
                                                    // Kiểm tra xem món có trong combo nào không
                                                    $coTrongCombo = false;
                                                    $tongGioiHan = $tongGioiHanMon[$monAnId] ?? null;
                                                    $phuPhiMon = 0;
                                                    
                                                    // Tìm phụ phí từ combo có chứa món này (lấy từ combo đầu tiên tìm thấy)
                                                    foreach($datBan->chiTietDatBan as $chiTiet) {
                                                        if($chiTiet->combo) {
                                                            $monTrongComboItem = \App\Models\MonTrongCombo::where('combo_id', $chiTiet->combo->id)
                                                                ->where('mon_an_id', $monAnId)
                                                                ->first();
                                                            if($monTrongComboItem) {
                                                                $coTrongCombo = true;
                                                                $phuPhiMon = $monTrongComboItem->phu_phi_goi_them ?? 0;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    
                                                    $tienMon = 0;
                                                    $donGiaHienThi = 0;
                                                    $soLuongHienThi = $tongSoLuong;
                                                    $coPhuPhi = false;
                                                    $soLuongVuot = $soLuongVuotMon[$monAnId] ?? 0;
                                                    $tienPhuPhi = 0; // Khởi tạo biến phụ phí
                                                    
                                                    if($coTrongCombo) {
                                                        // Món thuộc combo
                                                        if($tongGioiHan !== null && $tongGioiHan > 0) {
                                                            if($soLuongVuot > 0) {
                                                                // Vượt giới hạn: tính tiền cho phần vượt + phụ phí nhân theo số lượng vượt
                                                                $donGiaHienThi = $ctFirst->monAn->gia ?? 0;
                                                                // Tiền món = giá * số lượng vượt
                                                                $tienMon = $donGiaHienThi * $soLuongVuot;
                                                                // Phụ phí = phụ phí * số lượng vượt
                                                                $tienPhuPhi = $phuPhiMon * $soLuongVuot;
                                                                // Tổng tiền = tiền món + phụ phí
                                                                $tienMon = $tienMon + $tienPhuPhi;
                                                                $coPhuPhi = $phuPhiMon > 0;

                                                                $tongTienGoiThem += $tienMon;
                                                            }       
                                                            else {
                                                                // Trong giới hạn: giá = 0 (combo đã bao gồm)
                                                                $donGiaHienThi = 0;
                                                                $tienMon = 0;
                                                                $tienPhuPhi = 0;
                                                            }
                                                        } else {
                                                            // Không có giới hạn: giá = 0 (combo đã bao gồm)
                                                            $donGiaHienThi = 0;
                                                            $tienMon = 0;
                                                            $tienPhuPhi = 0;
                                                        }
                                                    } else {
                                                        // Món không thuộc combo: tính tiền bình thường
                                                        $donGiaHienThi = $ctFirst->monAn->gia ?? 0;
                                                        $tienMon = $donGiaHienThi * $tongSoLuong;
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
                                                            @if($soLuongVuot > 0)
                                                                <span class="badge bg-danger">Vượt giới hạn</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-info">Gọi thêm</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $soLuongHienThi }}
                                                        @if($coTrongCombo && $tongGioiHan !== null && $tongGioiHan > 0)
                                                            <br>
                                                            <small class="text-muted">(Giới hạn: {{ $tongGioiHan }})</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if($donGiaHienThi > 0)
                                                            {{ number_format($donGiaHienThi) }} đ
                                                            @if($coPhuPhi && $tienPhuPhi > 0)
                                                                <br>
                                                                <small class="text-danger">
                                                                    + Phụ phí: {{ number_format($tienPhuPhi) }} đ
                                                                    @if($soLuongVuot > 1)
                                                                        <br><small class="text-muted">({{ number_format($phuPhiMon) }} đ × {{ $soLuongVuot }})</small>
                                                                    @endif
                                                                </small>
                                                            @endif
                                                        @else
                                                            @if($coPhuPhi && $tienPhuPhi > 0)
                                                                <span class="text-success">0 đ</span>
                                                                <br>
                                                                <small class="text-danger">
                                                                    + Phụ phí: {{ number_format($tienPhuPhi) }} đ
                                                                    @if($soLuongVuot > 1)
                                                                        <br><small class="text-muted">({{ number_format($phuPhiMon) }} đ × {{ $soLuongVuot }})</small>
                                                                    @endif
                                                                </small>
                                                            @else
                                                                <span class="text-success">0 đ</span>
                                                                <br><small class="text-muted">(Đã bao gồm trong combo)</small>
                                                            @endif
                                                        @endif
                                                    </td>

                                                    <td class="text-end fw-bold">
                                                        @if($tienMon > 0)
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
                                                <td colspan="4" class="text-end">
                                                    <i class="bi bi-star-fill text-warning me-1"></i>Tổng tiền combo chính:
                                                </td>
                                                <td class="text-end text-primary fs-5">{{ number_format($tongTienComboChinh) }} đ</td>
                                            </tr>
                                            @endif
                                            @if($tongTienGoiThem > 0)
                                            <tr class="table-secondary fw-bold">
                                                <td colspan="4" class="text-end">Tổng tiền món gọi thêm:</td>
                                                <td class="text-end text-info">{{ number_format($tongTienGoiThem) }} đ</td>
                                            </tr>
                                            @endif
                                            <tr class="table-primary fw-bold fs-5">
                                                <td colspan="4" class="text-end">TỔNG CỘNG:</td>
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
                                    <div class="mb-4" id="tienKhachDuaGroup" style="display: none;">
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
                                        <div class="d-flex justify-content-between align-items-center">
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
