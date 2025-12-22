@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Hóa đơn chưa thanh toán')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                {{-- Header --}}
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary mb-2">
                        <i class="bi bi-receipt-cutoff me-2"></i>HÓA ĐƠN CHƯA THANH TOÁN
                    </h2>
                    <p class="text-muted">Danh sách các hóa đơn đang chờ thanh toán</p>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                {{-- Bảng danh sách --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh sách hóa đơn</h5>
                    </div>
                    <div class="card-body">
                        @if($hoaDons->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>STT</th>
                                        <th>Mã hóa đơn</th>
                                        <th>Bàn</th>
                                        <th>Khách hàng</th>
                                        <th>Giá tiền</th>
                                        <th>Ngày tạo</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hoaDons as $index => $hoaDon)
                                    @php
                                        $chiTiet = $hoaDon->chiTietHoaDon;
                                        $tenKhach = $chiTiet ? $chiTiet->ten_khach : ($hoaDon->datBan->ten_khach ?? 'N/A');
                                        $soBan = $chiTiet ? $chiTiet->ban_so : ($hoaDon->datBan->banAn->so_ban ?? 'N/A');
                                        $phaiThanhToan = $chiTiet ? ($chiTiet->phai_thanh_toan ?? 0) : ($hoaDon->da_thanh_toan ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ ($hoaDons->currentPage() - 1) * $hoaDons->perPage() + $index + 1 }}</td>
                                        <td>
                                            <strong class="text-primary">{{ $hoaDon->ma_hoa_don }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $soBan }}</span>
                                            @if($chiTiet && $chiTiet->khu_vuc)
                                                <br><small class="text-muted">{{ $chiTiet->khu_vuc }}</small>
                                            @elseif($hoaDon->datBan && $hoaDon->datBan->banAn && $hoaDon->datBan->banAn->khuVuc)
                                                <br><small class="text-muted">{{ $hoaDon->datBan->banAn->khuVuc->ten_khu_vuc }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $tenKhach }}</strong>
                                            @if($chiTiet && $chiTiet->sdt_khach)
                                                <br><small class="text-muted">{{ $chiTiet->sdt_khach }}</small>
                                            @elseif($hoaDon->datBan && $hoaDon->datBan->sdt_khach)
                                                <br><small class="text-muted">{{ $hoaDon->datBan->sdt_khach }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-danger">{{ number_format($phaiThanhToan) }} ₫</strong>
                                        </td>
                                        <td>
                                            {{ $hoaDon->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('nhanVien.thanh-toan.xac-nhan-thanh-toan', $hoaDon->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Bạn có chắc muốn xác nhận đã thanh toán hóa đơn {{ $hoaDon->ma_hoa_don }}?')">
                                                    <i class="bi bi-check-circle me-1"></i>Xác nhận đã thanh toán
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Phân trang --}}
                        <div class="d-flex justify-content-center mt-4">
                            {{ $hoaDons->links() }}
                        </div>
                        @else
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle me-2"></i>Không có hóa đơn nào chưa thanh toán.
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Nút quay lại --}}
                <div class="text-center mt-4">
                    <a href="{{ route('nhanVien.ban-an.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>Quay lại trang bàn ăn
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

