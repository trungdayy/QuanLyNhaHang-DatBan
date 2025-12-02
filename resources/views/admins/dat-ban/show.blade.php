@extends('layouts.admins.layout-admin')

@section('title', 'Chi Tiết Đặt Bàn')

@section('style')
<style>
    .info-box {
        padding: 20px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: #fff;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        height: 100%;
    }
    .info-box h5 {
        font-weight: 700;
        color: #009688;
        margin-bottom: 20px;
        border-bottom: 2px solid #f2f2f2;
        padding-bottom: 10px;
        text-transform: uppercase;
        font-size: 16px;
    }
    .info-row {
        display: flex;
        margin-bottom: 12px;
        border-bottom: 1px dashed #eee;
        padding-bottom: 8px;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        width: 140px;
        font-weight: 600;
        color: #555;
        flex-shrink: 0;
    }
    .info-value {
        flex-grow: 1;
        color: #333;
        font-weight: 500;
    }
    
    /* Combo Detail Styling */
    .combo-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 8px 12px;
        border-radius: 6px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .combo-name {
        font-weight: bold;
        color: #009688;
    }
    .combo-price {
        font-size: 0.9em;
        color: #666;
    }

    /* Status Form */
    .status-update-box {
        background-color: #e3f2fd;
        border: 1px solid #90caf9;
        border-radius: 8px;
        padding: 20px;
    }
</style>
@endsection

@section('content')
    <main class="app-content">
        
        {{-- Tiêu đề trang --}}
        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dat-ban.index') }}">Quản lý Đặt Bàn</a></li>
                <li class="breadcrumb-item"><a href="#"><b>Chi Tiết Đặt Bàn (#{{ $datBan->id }})</b></a></li>
            </ul>
        </div>

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                        
                        {{-- Nút quay lại và Sửa --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <a href="{{ route('admin.dat-ban.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            
                            @if (!in_array($datBan->trang_thai, ['hoan_tat', 'huy']))
                                <a href="{{ route('admin.dat-ban.edit', $datBan->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Chỉnh sửa thông tin
                                </a>
                            @endif
                        </div>

                        <div class="row">
                            {{-- Cột 1: Thông Tin Khách Hàng --}}
                            <div class="col-md-4">
                                <div class="info-box">
                                    <h5><i class="fas fa-user-circle mr-2"></i> Khách Hàng</h5>
                                    
                                    <div class="info-row">
                                        <span class="info-label">Họ tên:</span>
                                        <span class="info-value">{{ $datBan->ten_khach }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">SĐT:</span>
                                        <span class="info-value">{{ $datBan->sdt_khach }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Email:</span>
                                        <span class="info-value">{{ $datBan->email_khach ?? 'Không có' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Số lượng:</span>
                                        <span class="info-value">
                                            {{ $datBan->nguoi_lon }} người lớn
                                            @if($datBan->tre_em > 0), {{ $datBan->tre_em }} trẻ em @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Cột 2: Thông Tin Đặt Bàn --}}
                            <div class="col-md-4">
                                <div class="info-box">
                                    <h5><i class="fas fa-clock mr-2"></i> Lịch Đặt</h5>
                                    
                                    <div class="info-row">
                                        <span class="info-label">Mã đơn:</span>
                                        <span class="info-value text-primary font-weight-bold">{{ $datBan->ma_dat_ban }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Giờ đến:</span>
                                        <span class="info-value">{{ $datBan->gio_den ? \Carbon\Carbon::parse($datBan->gio_den)->format('H:i - d/m/Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Thời lượng:</span>
                                        <span class="info-value">{{ $datBan->thoi_luong_phut }} phút</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Tiền cọc:</span>
                                        <span class="info-value text-danger font-weight-bold">{{ number_format($datBan->tien_coc ?? 0) }} đ</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Trạng thái:</span>
                                        <span class="info-value">
                                            @if($datBan->trang_thai == 'cho_xac_nhan') <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                            @elseif($datBan->trang_thai == 'da_xac_nhan') <span class="badge bg-info">Đã xác nhận</span>
                                            @elseif($datBan->trang_thai == 'khach_da_den') <span class="badge bg-primary">Khách đã đến</span>
                                            @elseif($datBan->trang_thai == 'hoan_tat') <span class="badge bg-success">Hoàn tất</span>
                                            @else <span class="badge bg-danger">Hủy</span> @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Cột 3: Combo & Bàn --}}
                            <div class="col-md-4">
                                <div class="info-box">
                                    <h5><i class="fas fa-utensils mr-2"></i> Chi Tiết Dịch Vụ</h5>
                                    
                                    <div class="info-row">
                                        <span class="info-label">Vị trí bàn:</span>
                                        <span class="info-value">
                                            {{-- [AN TOÀN] Kiểm tra kỹ banAn trước khi gọi --}}
                                            @if($datBan->banAn)
                                                Bàn {{ $datBan->banAn->so_ban }} 
                                                <small class="text-muted">
                                                    ({{ $datBan->banAn->khuVuc->ten_khu_vuc ?? 'Chưa rõ khu vực' }})
                                                </small>
                                            @else
                                                <span class="text-muted text-italic">Chưa xếp bàn</span>
                                            @endif
                                        </span>
                                    </div>

                                    <div class="mt-3">
                                        <strong><i class="fas fa-list-ul"></i> Combo đã chọn:</strong>
                                        <div class="mt-2">
                                            @php $totalEst = 0; @endphp
                                            {{-- [AN TOÀN] Lặp qua danh sách chi tiết --}}
                                            @forelse ($datBan->chiTietDatBan as $chiTiet)
                                                @php 
                                                    // Kiểm tra Combo có tồn tại không (tránh lỗi nếu đã xóa combo)
                                                    $combo = $chiTiet->combo;
                                                    $name = $combo ? $combo->ten_combo : 'Combo đã xóa';
                                                    $price = $combo ? $combo->gia_co_ban : 0;
                                                    $subtotal = $price * $chiTiet->so_luong;
                                                    $totalEst += $subtotal;
                                                @endphp
                                                <div class="combo-item">
                                                    <div>
                                                        <div class="combo-name">{{ $name }}</div>
                                                        <div class="combo-price">{{ number_format($price) }}đ x {{ $chiTiet->so_luong }} suất</div>
                                                    </div>
                                                    <div class="font-weight-bold text-dark">
                                                        {{ number_format($subtotal) }}đ
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted text-center small">Chưa chọn combo nào</p>
                                            @endforelse
                                            
                                            @if($totalEst > 0)
                                            <div class="text-right mt-2 pt-2 border-top">
                                                <small>Tổng tiền combo dự kiến:</small><br>
                                                <strong class="text-success" style="font-size: 1.2em;">{{ number_format($totalEst) }} đ</strong>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            {{-- Cột Ghi Chú & Nhân viên --}}
                            <div class="col-md-12">
                                <div class="info-box" style="padding: 15px;">
                                    <div class="d-flex justify-content-between">
                                        <span>
                                            <strong><i class="fas fa-user-tag"></i> Nhân viên phụ trách: </strong> 
                                            {{-- [AN TOÀN] Kiểm tra null cho nhân viên --}}
                                            {{ $datBan->nhanVien ? $datBan->nhanVien->ho_ten : 'Chưa phân công' }}
                                        </span>
                                        <span>
                                            <strong><i class="fas fa-globe"></i> Kênh đặt: </strong> 
                                            {{ $datBan->la_dat_online ? 'Online (Website)' : 'Tại quầy (Offline)' }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <strong><i class="fas fa-sticky-note"></i> Ghi chú: </strong>
                                        <span class="text-muted">{{ $datBan->ghi_chu ?? 'Không có' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Cập nhật trạng thái --}}
                        @if (!in_array($datBan->trang_thai, ['hoan_tat', 'huy']))
                        <div class="row justify-content-center mt-3">
                            <div class="col-md-8">
                                <form class="status-update-box text-center" method="POST" action="{{ route('admin.dat-ban.updateStatus', $datBan->id) }}">
                                    @csrf
                                    <h5 class="mb-3 text-primary">Cập nhật trạng thái đơn đặt bàn</h5>
                                    
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <div style="flex-grow: 1; max-width: 300px; margin-right: 10px;">
                                            <select class="form-control" name="trang_thai_moi" required>
                                                <option value="cho_xac_nhan" {{ $datBan->trang_thai == 'cho_xac_nhan' ? 'selected' : '' }}>Chờ xác nhận</option>
                                                <option value="da_xac_nhan" {{ $datBan->trang_thai == 'da_xac_nhan' ? 'selected' : '' }}>Đã xác nhận</option>
                                                <option value="khach_da_den" {{ $datBan->trang_thai == 'khach_da_den' ? 'selected' : '' }}>Khách đã đến (Check-in)</option>
                                                <option value="hoan_tat" {{ $datBan->trang_thai == 'hoan_tat' ? 'selected' : '' }}>Hoàn tất & Thanh toán</option>
                                                <option value="huy" {{ $datBan->trang_thai == 'huy' ? 'selected' : '' }}>Hủy đơn</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-save"></i> Lưu Trạng Thái
                                        </button>
                                    </div>
                                    @if($datBan->trang_thai == 'cho_xac_nhan')
                                        <p class="text-muted small mt-2 mb-0"><i class="fas fa-info-circle"></i> Chuyển sang <b>"Đã xác nhận"</b> sau khi gọi điện xác nhận với khách.</p>
                                    @endif
                                </form>
                            </div>
                        </div>
                        @else
                        <div class="alert alert-secondary text-center mt-3">
                            <i class="fas fa-lock"></i> Đơn đặt bàn này đã kết thúc ({{ $datBan->trang_thai }}), không thể chỉnh sửa trạng thái.
                        </div>
                        @endif
                        
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection