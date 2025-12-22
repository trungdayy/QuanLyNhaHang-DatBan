@extends('layouts.admins.layout-admin')

@section('title', 'Danh sách hóa đơn')

@section('content')
<main class="app-content">
    <div class="app-title d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fa fa-file-invoice"></i> Danh sách hóa đơn</h1>
        <a href="{{ route('admin.hoa-don.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Tạo hóa đơn mới
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- THÊM MỚI: FORM TÌM KIẾM --}}
    <div class="tile mb-4">
        <h3 class="tile-title">Bộ lọc tìm kiếm</h3>
        <form class="row" method="GET" action="{{ route('admin.hoa-don.index') }}">
            <div class="col-md-5">
                <label class="form-label">Từ khóa</label>
                <input class="form-control" type="text" name="search" value="{{ request('search') }}"
                    placeholder="Mã HĐ, Tên khách, Số bàn...">
            </div>
            <div class="col-md-4">
                <label class="form-label">Phương thức thanh toán</label>
                <select class="form-control" name="phuong_thuc_tt">
                    <option value="">Tất cả</option>
                    <option value="Tiền mặt" {{ request('phuong_thuc_tt') == 'Tiền mặt' ? 'selected' : '' }}>Tiền mặt</option>
                    <option value="Chuyển khoản ngân hàng" {{ request('phuong_thuc_tt') == 'Chuyển khoản ngân hàng' ? 'selected' : '' }}>Chuyển khoản ngân hàng</option>
                    <option value="Thẻ Visa/Mastercard" {{ request('phuong_thuc_tt') == 'Thẻ Visa/Mastercard' ? 'selected' : '' }}>Thẻ Visa/Mastercard</option>
                    <option value="Ví điện tử Momo" {{ request('phuong_thuc_tt') == 'Ví điện tử Momo' ? 'selected' : '' }}>Ví điện tử Momo</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary me-2" type="submit"><i class="fa fa-search"></i> Tìm kiếm</button>
                <a href="{{ route('admin.hoa-don.index') }}" class="btn ml-2 btn-secondary"><i class="fa fa-refresh"></i> Reset</a>
            </div>
        </form>
    </div>
    {{-- KẾT THÚC FORM TÌM KIẾM --}}


    @if($hoadons->isEmpty())
    <div class="alert alert-info">Không tìm thấy hóa đơn nào.</div>
    @else
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Mã hóa đơn</th>
                    <th>Bàn / Khách</th>
                    <th>Tổng tiền món</th>
                    <th>Voucher</th>
                    <th>Tiền giảm</th>
                    <th>Thực thu</th>
                    <th>Trạng thái</th>
                    <th>Phương thức</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hoadons as $hd)
                @php
                    // Sử dụng dữ liệu đã lưu trong database thay vì tính toán lại
                    // Ưu tiên lấy từ chi_tiet_hoa_don, fallback về hoa_don
                    $tongTienMonHienThi = $hd->chiTietHoaDon->tong_tien_combo_mon ?? $hd->tong_tien ?? 0;
                    
                    // Lấy phải thanh toán từ chi_tiet_hoa_don nếu có
                    $phaiThanhToan = $hd->chiTietHoaDon->phai_thanh_toan ?? null;
                    if($phaiThanhToan === null) {
                        // Fallback: tính từ hoa_don
                        $phaiThanhToan = $hd->tong_tien - ($hd->tien_giam ?? 0) + ($hd->phu_thu ?? 0) - ($hd->datBan->tien_coc ?? 0);
                        if($phaiThanhToan < 0) $phaiThanhToan = 0;
                    }
                @endphp
                <tr>
                    <td>{{ $hd->id }}</td>
                    <td><span class="badge bg-info">{{ $hd->ma_hoa_don }}</span></td>
                    <td>
                        <strong>{{ $hd->datBan->banAn->so_ban ?? 'N/A' }}</strong><br> 
                        <small>Khách: {{ $hd->datBan->ten_khach ?? 'N/A' }}</small>
                    </td>
                    
                    <td class="text-end">{{ number_format($tongTienMonHienThi) }}₫</td>

                    <td>
                        @if($hd->voucher)
                            <span class="badge bg-primary">{{ $hd->voucher->ma_voucher }}</span>
                        @else
                            <span class="text-muted small">Không có</span>
                        @endif
                    </td>
                    
                    <td class="text-end text-success">{{ number_format($hd->tien_giam ?? 0) }}₫</td>
                    
                    <td class="text-end fw-bold text-primary">{{ number_format($phaiThanhToan) }}₫</td>
                    
                    <td>
                        @if($hd->trang_thai == 'da_thanh_toan')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @else
                            <span class="badge bg-warning">Chưa thanh toán</span>
                        @endif
                    </td>

                    <td>
                        @if($hd->trang_thai == 'da_thanh_toan' && ($hd->phuong_thuc_tt == 'chua_thanh_toan' || !$hd->phuong_thuc_tt))
                            {{-- Chỉ hiển thị "Đã thanh toán" khi đã thanh toán nhưng không biết phương thức (thanh toán sau) --}}
                            <span class="badge bg-success" data-bs-toggle="tooltip" title="Đã thanh toán (không xác định phương thức)">Đã thanh toán</span>
                        @elseif($hd->phuong_thuc_tt == 'tien_mat')
                            <span class="badge bg-primary" data-bs-toggle="tooltip" title="Thanh toán tiền mặt">Tiền mặt</span>
                        @elseif($hd->phuong_thuc_tt == 'chuyen_khoan')
                            <span class="badge bg-secondary" data-bs-toggle="tooltip" title="Thanh toán QR / Chuyển khoản">Chuyển khoản</span>
                        @elseif($hd->phuong_thuc_tt == 'the_ATM')
                            <span class="badge bg-info" data-bs-toggle="tooltip" title="Thanh toán bằng thẻ ATM">Thẻ ATM</span>
                        @elseif($hd->phuong_thuc_tt == 'vnpay')
                            <span class="badge bg-warning" data-bs-toggle="tooltip" title="Thanh toán qua VNPay">VNPay</span>
                        @elseif($hd->phuong_thuc_tt == 'payos')
                            <span class="badge bg-info" data-bs-toggle="tooltip" title="Thanh toán qua PayOS">PayOS</span>
                        @else
                            <span class="badge bg-dark" data-bs-toggle="tooltip" title="Chưa thanh toán">Chưa thanh toán</span>
                        @endif
                    </td>

                    <td>{{ $hd->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</td>
                    
                    <td>
                        <a href="{{ route('admin.hoa-don.show', $hd->id) }}" class="btn btn-info btn-sm mb-1"
                            title="Xem chi tiết">
                            <i class="fa fa-eye"></i>
                        </a>
                        @if($hd->trang_thai != 'da_thanh_toan')
                        <a href="{{ route('admin.hoa-don.edit', $hd->id) }}" class="btn btn-warning btn-sm mb-1"
                            title="Sửa hóa đơn">
                            <i class="fa fa-edit"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $hoadons->links() }}
    </div>
    
    @endif
</main>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endsection