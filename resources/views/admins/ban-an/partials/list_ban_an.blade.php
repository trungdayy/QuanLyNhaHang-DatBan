{{-- Đây là file chứa nội dung danh sách để load lại bằng AJAX --}}
@forelse ($khuVucs as $kv)
<div class="col-lg-12 mb-4">
    <div class="card shadow border-left-primary">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-building mr-2"></i> {{ $kv->ten_khu_vuc }} (Tầng {{ $kv->tang }})
                @php
                    $trangThaiKhuVuc = trim(strtolower($kv->trang_thai ?? 'dang_su_dung'));
                    $isKhuVucOff = ($trangThaiKhuVuc === 'khong_su_dung');
                @endphp
                @if($isKhuVucOff)
                    <span class="badge bg-secondary text-white ml-2">Đã tắt</span>
                @endif
            </h5>
            <div>
                <a href="{{ route('admin.khu-vuc.edit', $kv->id) }}" class="btn btn-sm btn-info" title="Sửa"><i class="fas fa-edit"></i></a>
                @php
                    $trangThaiKhuVuc = trim(strtolower($kv->trang_thai ?? 'dang_su_dung'));
                    $isKhuVucOff = ($trangThaiKhuVuc === 'khong_su_dung');
                    
                    // Kiểm tra xem có bàn đang phục vụ hoặc đã đặt không
                    $hasBanDangPhucVu = false;
                    $hasBanDaDat = false;
                    if (!$isKhuVucOff && isset($kv->banAns)) {
                        foreach ($kv->banAns as $ban) {
                            $trangThaiBan = trim(strtolower($ban->trang_thai ?? ''));
                            if ($trangThaiBan === 'dang_phuc_vu') {
                                $hasBanDangPhucVu = true;
                            }
                            if ($trangThaiBan === 'da_dat') {
                                $hasBanDaDat = true;
                            }
                        }
                    }
                    $canToggleOff = !$hasBanDangPhucVu && !$hasBanDaDat;
                @endphp
                <form style="display:inline;" method="POST" action="{{ route('admin.khu-vuc.toggle-status', $kv->id) }}">
                    @csrf
                    @if($isKhuVucOff)
                        {{-- Nút Bật (khi khu vực đang tắt) --}}
                        <button type="submit" class="btn btn-sm btn-success" title="Bật khu vực">
                            <i class="fas fa-power-off"></i>
                        </button>
                    @else
                        {{-- Nút Tắt (khi khu vực đang bật) --}}
                        <button type="submit" class="btn btn-sm btn-secondary {{ !$canToggleOff ? 'disabled' : '' }}" 
                                title="{{ !$canToggleOff ? 'Có bàn đang phục vụ/đã đặt, không thể tắt khu vực. Vui lòng chờ khi tất cả bàn trống.' : 'Tắt khu vực' }}"
                                {{ !$canToggleOff ? 'disabled' : '' }}>
                            <i class="fas fa-power-off"></i>
                        </button>
                    @endif
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
                            @php
                                $trangThaiNormalized = trim(strtolower($ban->trang_thai));
                                $isDisabled = ($trangThaiNormalized === 'dang_phuc_vu' || $trangThaiNormalized === 'da_dat');
                                $isOff = ($trangThaiNormalized === 'khong_su_dung');
                            @endphp
                            <form style="display:inline;" method="POST" action="{{ route('admin.ban-an.toggle-status', $ban->id) }}">
                                @csrf
                                @if($isOff)
                                    {{-- Nút Bật (khi bàn đang tắt) --}}
                                    <button type="submit" class="btn btn-xs btn-outline-success" title="Bật bàn">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                @else
                                    {{-- Nút Tắt (khi bàn đang trống) --}}
                                    <button type="submit" class="btn btn-xs btn-outline-secondary {{ $isDisabled ? 'disabled' : '' }}" 
                                            title="{{ $isDisabled ? 'Bàn đang phục vụ/đã đặt, không thể tắt' : 'Tắt bàn' }}"
                                            {{ $isDisabled ? 'disabled' : '' }}>
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                @endif
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