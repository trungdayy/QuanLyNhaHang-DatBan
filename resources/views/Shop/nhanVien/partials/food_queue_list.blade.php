@forelse ($monChoPhucVu as $mon)
    <div class="food-item-card d-flex justify-content-between align-items-center" id="item-{{ $mon->id }}">
        <div>
            <p class="mb-1">
                <strong>{{ $mon->monAn->ten_mon }}</strong>
                <span class="badge-suat ms-2">{{ $mon->so_luong }} suất</span>
            </p>
            <small class="text-muted">
                <i class="fas fa-table"></i> Bàn: <strong>{{ optional($mon->orderMon->banAn)->so_ban ?? 'N/A' }}</strong>
                (Hoàn thành: {{ \Carbon\Carbon::parse($mon->updated_at)->format('H:i') }})
            </small>
        </div>
        <div>
            <button class="btn btn-ocean-served btn-sm" onclick="xacNhanDaBung({{ $mon->id }})">
                <i class="fas fa-check"></i> Đã lên món
            </button>
        </div>
    </div>
@empty
    <div class="text-center text-secondary py-4" id="empty-queue-message">
        ✅ Không có món nào chờ phục vụ lúc này.
    </div>
@endforelse