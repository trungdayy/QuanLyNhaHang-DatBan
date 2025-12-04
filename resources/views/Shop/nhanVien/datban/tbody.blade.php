<tbody>
@forelse($ds as $index => $d)
<tr style="{{ $d->trang_thai == 'huy' ? 'opacity: 0.6;' : '' }}">
    <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
    <td class="text-center font-heading text-primary fw-bold">{{ $d->ma_dat_ban }}</td>
    <td class="text-center">
        @if ($d->banAn)
        <span class="tag-table">Bàn {{ $d->banAn->so_ban }}</span>
        <div class="small text-muted mt-1" style="font-size: 0.7rem;">{{ $d->banAn->khuVuc->ten_khu_vuc ?? '' }}</div>
        @else
        <span class="text-muted small fst-italic">Chưa xếp</span>
        @endif
    </td>
    <td>
        <div class="fw-bold text-dark">{{ $d->ten_khach }}</div>
        <div class="small text-muted"><i class="fa-solid fa-phone me-1" style="font-size: 0.7rem;"></i>{{ $d->sdt_khach }}</div>
    </td>
    <td class="text-center fw-bold">
        {{ $d->nguoi_lon + $d->tre_em }}
        @if($d->tre_em > 0)
        <span class="small text-muted">(T: {{ $d->tre_em }})</span>
        @endif
    </td>
    <td class="text-center small">
        @if($d->gio_den)
        <div class="fw-bold">{{ \Carbon\Carbon::parse($d->gio_den)->format('H:i') }}</div>
        <div class="text-muted">{{ \Carbon\Carbon::parse($d->gio_den)->format('d/m') }}</div>
        @else - @endif
    </td>
    <td class="text-start">
        @forelse($d->combos as $combo)
        <span class="tag-combo">
            {{ $combo->ten_combo }} (x{{ $combo->pivot->so_luong }})
        </span>
        @empty
        <span class="text-muted small fst-italic">Chưa chọn Combo</span>
        @endforelse
        <div class="small text-muted mt-1">TTL: {{ $d->thoi_luong_phut ?? 120 }}p</div>
    </td>
    <td class="text-center small">
        @if ($d->trang_thai == 'khach_da_den')
        @php
        $thoiLuong = $d->thoi_luong_phut ?? 120;
        $orderDau = $d->orderMon->sortBy('created_at')->first();
        $endTime = $orderDau ? \Carbon\Carbon::parse($orderDau->created_at)->addMinutes($thoiLuong)->timestamp * 1000 : null;
        @endphp
        @if ($endTime)
        <span class="countdown-timer text-primary" data-endtime="{{ $endTime }}">...</span>
        @else
        <span class="text-muted">Chờ gọi món</span>
        @endif
        @elseif(in_array($d->trang_thai, ['cho_xac_nhan', 'da_xac_nhan']))
        <span class="text-muted">Chưa check-in</span>
        @else
        -
        @endif
    </td>
    <td class="text-center">
        @php
        $st = $d->trang_thai;
        $badgeClass = 'bg-secondary text-white';
        $badgeText = $st;
        if($st == 'cho_xac_nhan') { $badgeClass = 'st-cho'; $badgeText = 'Chờ duyệt'; }
        elseif($st == 'da_xac_nhan') { $badgeClass = 'st-xac-nhan'; $badgeText = 'Đã duyệt'; }
        elseif($st == 'khach_da_den') { $badgeClass = 'st-phuc-vu'; $badgeText = 'Đang ăn'; }
        elseif($st == 'hoan_tat') { $badgeClass = 'st-hoan-tat'; $badgeText = 'Hoàn tất'; }
        elseif($st == 'huy') { $badgeClass = 'st-huy'; $badgeText = 'Hủy'; }
        @endphp
        <span class="badge-pill {{ $badgeClass }}">{{ $badgeText }}</span>
    </td>
    <td class="text-center">
        <div class="d-flex justify-content-center flex-wrap gap-1">
            @if ($d->trang_thai == 'cho_xac_nhan')
            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">@csrf
                <input type="hidden" name="trang_thai" value="da_xac_nhan">
                <button class="action-btn btn-accept" onclick="return confirm('Xác nhận đơn này?')">
                    <i class="fa-solid fa-check"></i> Xác nhận
                </button>
            </form>
            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">@csrf
                <input type="hidden" name="trang_thai" value="huy">
                <button class="action-btn btn-reject" onclick="return confirm('Hủy đơn này?')">
                    <i class="fa-solid fa-xmark"></i> Hủy
                </button>
            </form>
            @elseif($d->trang_thai == 'da_xac_nhan')
            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">@csrf
                <input type="hidden" name="trang_thai" value="khach_da_den">
                <button class="action-btn btn-arrived" onclick="return confirm('Khách đã đến?')">
                    <i class="fa-solid fa-person-walking"></i> Check-in
                </button>
            </form>
            @elseif($d->trang_thai == 'khach_da_den')
            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">@csrf
                <input type="hidden" name="trang_thai" value="hoan_tat">
                <button class="action-btn btn-finish" onclick="return confirm('Kết thúc bàn này?')">
                    <i class="fa-solid fa-flag-checkered"></i> Kết thúc
                </button>
            </form>
            @else
            <span class="text-muted">-</span>
            @endif
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center py-5 text-muted">
        <i class="fa-solid fa-inbox fa-2x mb-2 opacity-25"></i>
        <p class="fw-bold mb-0">Không tìm thấy dữ liệu đặt bàn.</p>
    </td>
</tr>
@endforelse
</tbody>