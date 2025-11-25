{{-- Đây là file chứa nội dung danh sách để load lại bằng AJAX --}}
@forelse ($khuVucs as $kv)
<div class="col-lg-12 mb-4">
    <div class="card shadow border-left-primary">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-building mr-2"></i> {{ $kv->ten_khu_vuc }} (Tầng {{ $kv->tang }})
            </h5>
            <div>
                <a href="{{ route('admin.khu-vuc.edit', $kv->id) }}" class="btn btn-sm btn-info" title="Sửa"><i class="fas fa-edit"></i></a>
                <form style="display:inline;" method="POST" action="{{ route('admin.khu-vuc.destroy', $kv->id) }}" onsubmit="return confirm('CẢNH BÁO: Bạn có chắc chắn muốn xóa Khu vực {{ $kv->ten_khu_vuc }}? (Phải xóa hết bàn ăn trước)');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa Khu Vực"><i class="fas fa-trash-alt"></i></button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @forelse ($kv->banAns as $ban)
                @php
                    $badgeClass = '';
                    $trangThaiDisplay = $ban->trang_thai;
                    $trangThaiNormalized = trim(strtolower($ban->trang_thai));
                    switch ($trangThaiNormalized) {
                        case 'trong': $badgeClass = 'bg-success'; $trangThaiDisplay = 'Trống'; break;
                        case 'dang_phuc_vu': $badgeClass = 'bg-danger text-white'; $trangThaiDisplay = 'Đang phục vụ'; break;
                        case 'da_dat': $badgeClass = 'bg-warning'; $trangThaiDisplay = 'Đã đặt'; break;
                        case 'khong_su_dung': $badgeClass = 'bg-secondary text-white'; $trangThaiDisplay = 'Không sử dụng'; break;
                        default: $badgeClass = 'bg-light text-dark';
                    }
                @endphp
                <div class="col-md-2 mb-3">
                    <div class="card p-2 text-center shadow-sm">
                        <i class="fas fa-chair fa-2x mb-2"></i>
                        <strong>{{ $ban->so_ban }}</strong> ({{ $ban->so_ghe }} Ghế)
                        <span class="badge {{ $badgeClass }} text-small">{{ $trangThaiDisplay }}</span>
                        <div class="mt-2">
                            <form style="display:inline;" method="POST" action="{{ route('admin.ban-an.qr', $ban->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline-info" title="Tạo lại QR"><i class="fas fa-qrcode"></i></button>
                            </form>
                            <a href="{{ route('admin.ban-an.edit', $ban->id) }}" class="btn btn-xs btn-outline-warning" title="Sửa bàn"><i class="fas fa-edit"></i></a>
                            <form style="display:inline;" method="POST" action="{{ route('admin.ban-an.destroy', $ban->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Bàn {{ $ban->so_ban }}?');">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Xóa bàn"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12"><p class="text-muted">Chưa có bàn nào trong khu vực này.</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@empty
@if (!isset($errorMessage))
<div class="col-12"><p class="alert alert-info">Chưa có Khu vực nào được tìm thấy trong database.</p></div>
@endif
@endforelse