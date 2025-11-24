@extends('layouts.Shop.layout-nhanvien')
@section('title', 'Quản lý Đặt Bàn')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Danh sách đặt bàn</h4>
        <a href="{{ route('nhanVien.datban.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tạo đặt bàn
        </a>
    </div>

    {{-- Hiển thị thông báo (nếu có) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 50px;">Stt</th>
                            <th>Mã Đặt bàn</th>
                            <th>Bàn</th>
                            <th>Khách hàng</th>
                            <th style="width: 80px;">Số khách</th>
                            <th>Giờ đến</th>
                            <th>Combo</th>
                            <th style="width: 150px;">Thời gian còn lại</th>
                            <th>Ghi chú</th>
                            <th>Trạng thái</th>
                            <th style="width: 180px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ds as $index => $d)
                            <tr class="{{ $d->trang_thai == 'huy' ? 'table-danger' : '' }} {{ $d->trang_thai == 'hoan_tat' ? 'table-light' : '' }}">
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-bold text-primary">{{ $d->ma_dat_ban }}</td>
                                <td class="text-center">
                                    @if ($d->banAn)
                                        {{-- Hiển thị Bàn và Khu vực --}}
                                        <span class="badge bg-dark">Bàn {{ $d->banAn->so_ban }}</span>
                                        <br><small class="text-muted">{{ $d->banAn->khuVuc->ten_khu_vuc ?? '' }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Hiển thị tên, SĐT và Email khách hàng --}}
                                    <div class="fw-bold">{{ $d->ten_khach }}</div>
                                    <small class="text-muted">{{ $d->sdt_khach }}</small>
                                    @if ($d->email_khach)
                                        <br><small class="text-info">{{ $d->email_khach }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $d->so_khach }}</td>
                                <td>
                                    {{ $d->gio_den ? \Carbon\Carbon::parse($d->gio_den)->format('H:i d/m/Y') : '-' }}
                                </td>
                                <td>
                                    @if ($d->comboBuffet)
                                        {{ $d->comboBuffet->ten_combo }} <br>
                                        <small class="text-muted">({{ $d->thoi_luong_phut ?? $d->comboBuffet->thoi_luong_phut }}p)</small>
                                    @else
                                        <span class="text-muted">Mặc định</span>
                                    @endif
                                </td>

                                {{-- ĐẾM NGƯỢC THỜI GIAN BUFFET --}}
<td class="text-center fw-bold">
    @if ($d->trang_thai == 'khach_da_den')
        @if ($d->comboBuffet)
            @php
                $orderDau = \App\Models\OrderMon::where('dat_ban_id', $d->id)
                            ->orderBy('created_at', 'asc')->first();
                $thoiLuong = $d->comboBuffet->thoi_luong_phut ?? 120;
                $endTime = $orderDau ? \Carbon\Carbon::parse($orderDau->created_at)
                            ->addMinutes($thoiLuong)->timestamp * 1000 : null;
            @endphp

            @if ($endTime)
                <span class="countdown-timer" data-endtime="{{ $endTime }}">Tính giờ...</span>
            @else
                <span class="text-muted small">Chưa gọi món</span>
            @endif
        @else
            <span class="text-muted small">Chưa chọn combo</span>
        @endif
    @elseif(in_array($d->trang_thai, ['cho_xac_nhan', 'da_xac_nhan']))
        <span class="text-muted small">Chưa đến</span>
    @else
        <span class="text-muted">-</span>
    @endif
</td>

{{-- Script đồng hồ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateCountdowns() {
        const timers = document.querySelectorAll('.countdown-timer');
        const now = new Date().getTime();
        const fiveMinutes = 5 * 60 * 1000;

        timers.forEach(timer => {
            const endTime = parseInt(timer.getAttribute('data-endtime'));
            if (isNaN(endTime)) return;
            const distance = endTime - now;

            if (distance < 0) {
                timer.innerHTML = "HẾT GIỜ";
                timer.className = "countdown-timer text-danger fw-bold blink-fast";
                return;
            }

            const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((distance % (1000 * 60)) / 1000);
            
            const timeString = `${h < 10 ? "0"+h : h}:${m < 10 ? "0"+m : m}:${s < 10 ? "0"+s : s}`;

            if (distance <= fiveMinutes) {
                timer.innerHTML = `${timeString} <br> <span class="badge bg-warning text-dark">Sắp hết</span>`;
                timer.className = "countdown-timer text-warning fw-bold blink-slow";
            } else {
                timer.innerHTML = timeString;
                timer.className = "countdown-timer text-success fw-bold";
            }
        });
    }
    setInterval(updateCountdowns, 1000);
    updateCountdowns();
});
</script>

<style>
.blink-slow { animation: blinker 1.5s linear infinite; }
.blink-fast { animation: blinker 0.8s linear infinite; }
@keyframes blinker { 50% { opacity: 0.4; } }
</style>


                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 100px;" title="{{ $d->ghi_chu }}">
                                        {{ $d->ghi_chu ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    @php
                                        $statusLabels = [
                                            'cho_xac_nhan' => ['text' => 'Chờ xác nhận', 'class' => 'bg-warning text-dark'],
                                            'da_xac_nhan' => ['text' => 'Đã xác nhận', 'class' => 'bg-info text-dark'],
                                            'khach_da_den' => ['text' => 'Đang phục vụ', 'class' => 'bg-success'],
                                            'huy' => ['text' => 'Đã hủy', 'class' => 'bg-danger'],
                                            'hoan_tat' => ['text' => 'Hoàn tất', 'class' => 'bg-secondary'],
                                        ];
                                        $status = $statusLabels[$d->trang_thai] ?? ['text' => $d->trang_thai, 'class' => 'bg-secondary'];
                                    @endphp
                                    <span class="badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                                </td>

                                {{-- HÀNH ĐỘNG --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        
                                        {{-- TRƯỜNG HỢP 1: CHỜ XÁC NHẬN --}}
                                        @if ($d->trang_thai == 'cho_xac_nhan')
                                            {{-- Nút Xác nhận --}}
                                            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">
                                                @csrf
                                                <input type="hidden" name="trang_thai" value="da_xac_nhan">
                                                <button type="submit" class="btn btn-sm btn-success" title="Xác nhận" onclick="return confirm('Xác nhận đơn này?')">
                                                    <i class="bi bi-check-lg"></i> Xác nhận
                                                </button>
                                            </form>
                                            
                                            {{-- Nút Hủy --}}
                                            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">
                                                @csrf
                                                <input type="hidden" name="trang_thai" value="huy">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hủy đơn" onclick="return confirm('Hủy đơn này?')">
                                                    <i class="bi bi-x-lg"></i> Hủy
                                                </button>
                                            </form>

                                        {{-- TRƯỜNG HỢP 2: ĐÃ XÁC NHẬN (Khách đến) --}}
                                        @elseif($d->trang_thai == 'da_xac_nhan')
                                            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">
                                                @csrf
                                                <input type="hidden" name="trang_thai" value="khach_da_den">
                                                <button type="submit" class="btn btn-sm btn-primary w-100" onclick="return confirm('Khách đã đến?')">
                                                    <i class="bi bi-person-check-fill"></i> Khách tới
                                                </button>
                                            </form>

                                        {{-- TRƯỜNG HỢP 3: ĐANG PHỤC VỤ (Kết thúc) --}}
                                        @elseif($d->trang_thai == 'khach_da_den')
                                            <form method="post" action="{{ route('nhanVien.datban.thaydoitrangthai', $d->id) }}">
                                                @csrf
                                                <input type="hidden" name="trang_thai" value="hoan_tat">
                                                <button type="submit" class="btn btn-sm btn-secondary w-100" onclick="return confirm('Kết thúc bàn này?')">
                                                    <i class="bi bi-flag-fill"></i> Kết thúc
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
                                <td colspan="11" class="text-center py-4 text-muted">Chưa có dữ liệu đặt bàn nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Script đồng hồ (Giữ nguyên logic cũ) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function updateCountdowns() {
                const timers = document.querySelectorAll('.countdown-timer');
                const now = new Date().getTime();
                const fiveMinutes = 5 * 60 * 1000;

                timers.forEach(timer => {
                    const endTime = parseInt(timer.getAttribute('data-endtime'));
                    if (isNaN(endTime)) return;
                    const distance = endTime - now;

                    if (distance < 0) {
                        timer.innerHTML = "HẾT GIỜ";
                        timer.className = "countdown-timer text-danger fw-bold blink-fast";
                        return;
                    }

                    const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((distance % (1000 * 60)) / 1000);
                    
                    const timeString = `${h < 10 ? "0"+h : h}:${m < 10 ? "0"+m : m}:${s < 10 ? "0"+s : s}`;

                    if (distance <= fiveMinutes) {
                        timer.innerHTML = `${timeString} <br> <span class="badge bg-warning text-dark">Sắp hết</span>`;
                        timer.className = "countdown-timer text-warning fw-bold blink-slow";
                    } else {
                        timer.innerHTML = timeString;
                        timer.className = "countdown-timer text-success fw-bold";
                    }
                });
            }
            setInterval(updateCountdowns, 1000);
            updateCountdowns();
        });
    </script>

    <style>
        .blink-slow { animation: blinker 1.5s linear infinite; }
        .blink-fast { animation: blinker 0.8s linear infinite; }
        @keyframes blinker { 50% { opacity: 0.4; } }
    </style>
@endsection