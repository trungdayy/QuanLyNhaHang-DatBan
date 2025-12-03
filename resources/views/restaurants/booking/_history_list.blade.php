{{-- File: resources/views/restaurants/booking/_history_list.blade.php --}}

<div class="d-flex align-items-center justify-content-between mb-4">
    <h5 class="fw-bold text-dark m-0 d-flex align-items-center">
        <span class="badge bg-primary rounded-pill me-2">{{ count($datBans) }}</span>
        Lịch sử đơn hàng
    </h5>
    @if($sdt) <span class="text-muted small">SĐT: {{ $sdt }}</span> @endif
</div>

@if($datBans->isEmpty())
<div class="text-center py-5 rounded-4 bg-light border border-dashed">
    <div class="mb-3">
        <i class="fa fa-clipboard-list fa-3x text-muted opacity-25"></i>
    </div>
    <p class="text-muted fw-bold mb-1">Không tìm thấy đơn đặt bàn nào.</p>
    <small class="text-muted">Nhập số điện thoại chính xác để tra cứu.</small>
</div>
@else
<div class="booking-list-container">
    @foreach($datBans as $datBan)
    @php
    $statusConfig = [
    'cho_xac_nhan' => ['bg' => 'bg-warning-subtle', 'text' => 'text-warning', 'label' => 'Chờ xác nhận', 'icon' =>
    'fa-hourglass-half'],
    'da_xac_nhan' => ['bg' => 'bg-info-subtle', 'text' => 'text-info', 'label' => 'Đã xác nhận', 'icon' =>
    'fa-check-circle'],
    'khach_da_den' => ['bg' => 'bg-primary-subtle', 'text' => 'text-primary', 'label' => 'Khách đã đến', 'icon' =>
    'fa-walking'],
    'hoan_tat' => ['bg' => 'bg-success-subtle', 'text' => 'text-success', 'label' => 'Hoàn thành', 'icon' => 'fa-star'],
    'huy' => ['bg' => 'bg-danger-subtle', 'text' => 'text-danger', 'label' => 'Đã hủy', 'icon' => 'fa-times-circle'],
    ];
    $st = $statusConfig[$datBan->trang_thai] ?? ['bg' => 'bg-secondary-subtle', 'text' => 'text-secondary', 'label' =>
    'Không rõ', 'icon' => 'fa-question'];
    @endphp

    <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden transition-hover">
        <div class="card-body p-4">
            {{-- Header Card --}}
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h6 class="fw-bold mb-1 text-dark">{{ $datBan->ten_khach }}</h6>
                    <div class="text-muted small">
                        <i class="far fa-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($datBan->gio_den)->format('H:i - d/m/Y') }}
                    </div>
                </div>
                <span
                    class="badge {{ $st['bg'] }} {{ $st['text'] }} d-flex align-items-center height-fit px-3 py-2 rounded-pill">
                    <i class="fa {{ $st['icon'] }} me-1"></i> {{ $st['label'] }}
                </span>
            </div>

            {{-- Info Grid --}}
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-2 bg-light rounded-3 text-center">
                        <small class="d-block text-muted">Người lớn</small>
                        <span class="fw-bold text-dark">{{ $datBan->nguoi_lon }}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 bg-light rounded-3 text-center">
                        <small class="d-block text-muted">Trẻ em</small>
                        <span class="fw-bold text-dark">{{ $datBan->tre_em }}</span>
                    </div>
                </div>
            </div>

            {{-- Menu --}}
            @if($datBan->chiTietDatBan && $datBan->chiTietDatBan->count() > 0)
            <div class="border-top pt-3">
                <small class="text-uppercase text-muted fw-bold mb-2 d-block" style="font-size: 0.7rem;">Thực đơn đã
                    chọn</small>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($datBan->chiTietDatBan as $ct)
                    <span class="badge bg-white border text-dark fw-normal py-2 px-3 rounded-pill">
                        {{ $ct->comboBuffet->ten_combo ?? 'Món ăn' }}
                        <span class="fw-bold text-primary ms-1">x{{ $ct->so_luong }}</span>
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Actions --}}
            @if($datBan->trang_thai == 'cho_xac_nhan')
            <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                <a href="{{ route('booking.edit', $datBan->id) }}"
                    class="btn btn-sm btn-light fw-bold rounded-pill px-3">
                    Sửa đơn
                </a>
                <form action="{{ route('booking.destroy', $datBan->id) }}" method="POST"
                    onsubmit="return confirm('Bạn chắc chắn muốn hủy?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger fw-bold rounded-pill px-3">Hủy đơn</button>
                </form>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif