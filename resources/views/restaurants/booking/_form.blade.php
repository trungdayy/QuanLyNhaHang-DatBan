{{-- resources/views/restaurants/booking/create.blade.php --}}
@extends('layouts.restaurants.layout-shop')

@section('title', $method === 'PUT' ? 'Cập nhật đặt bàn' : 'Đặt Bàn Online')

@section('content')
<div class="container py-5">
    <h2 class="mb-5 text-center fw-bold text-primary">
        {{ $method === 'PUT' ? 'Cập nhật đặt bàn' : 'Đặt Bàn Online' }}
    </h2>

    {{-- Thông báo --}}
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
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ $action }}" method="POST" class="booking-form">
                        @csrf
                        @if($method === 'PUT') @method('PUT') @endif

                        <div class="row g-2">

                            {{-- Họ và tên --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ và tên</label>
                                <input type="text" name="ten_khach" class="form-control form-control-lg"
                                    placeholder="Họ và tên" value="{{ old('ten_khach', $datBan->ten_khach ?? '') }}"
                                    required>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email_khach" class="form-control form-control-lg"
                                    placeholder="Email" value="{{ old('email_khach', $datBan->email_khach ?? '') }}">
                            </div>

                            {{-- SĐT --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text" name="sdt_khach" class="form-control form-control-lg"
                                    placeholder="Số điện thoại" value="{{ old('sdt_khach', $datBan->sdt_khach ?? '') }}"
                                    required>
                            </div>

                            {{-- Số khách --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số khách</label>
                                <input type="number" name="so_khach" class="form-control form-control-lg"
                                    placeholder="Số khách" value="{{ old('so_khach', $datBan->so_khach ?? 1) }}" min="1"
                                    required>
                            </div>

                            {{-- Giờ đến --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Giờ đến</label>
                                <input type="datetime-local" name="gio_den" class="form-control form-control-lg"
                                    value="{{ old('gio_den', isset($datBan->gio_den) ? \Carbon\Carbon::parse($datBan->gio_den)->format('Y-m-d\TH:i') : '') }}"
                                    required>
                            </div>

                            {{-- Combo --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Chọn Combo</label>
                                <select name="combo_id" class="form-select form-select-lg" required>
                                    <option value="">-- Chọn Combo --</option>
                                    @foreach($combos as $c)
                                    <option value="{{ $c->id }}"
                                        {{ old('combo_id', $datBan->combo_id ?? $selectedCombo->id ?? '') == $c->id ? 'selected' : '' }}>
                                        {{ $c->ten_combo }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Khu vực --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Chọn Khu Vực</label>
                                <select name="khu_vuc_id" id="khu-vuc-select" class="form-select form-select-lg"
                                    required>
                                    <option value="">-- Chọn Khu Vực --</option>
                                    @foreach($khuVucs as $kv)
                                    <option value="{{ $kv->id }}"
                                        {{ old('khu_vuc_id', $selectedKhuVucId ?? '') == $kv->id ? 'selected' : '' }}>
                                        {{ $kv->ten_khu_vuc }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Bàn --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Chọn Bàn</label>
                                <select name="ban_id" id="ban-select" class="form-select form-select-lg" required>
                                    <option value="">-- Chọn bàn --</option>
                                    @if($selectedKhuVucId && $banAns->count())
                                    @foreach($banAns as $ban)
                                    <option value="{{ $ban->id }}"
                                        {{ old('ban_id', $datBan->ban_id ?? '') == $ban->id ? 'selected' : '' }}>
                                        Bàn {{ $ban->so_ban }}
                                    </option>
                                    @endforeach
                                    @elseif($selectedKhuVucId)
                                    <option disabled>Không có bàn trống trong khu vực này</option>
                                    @else
                                    <option disabled>Chọn khu vực trước để chọn bàn</option>
                                    @endif
                                </select>
                            </div>

                            {{-- Ghi chú --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Ghi chú</label>
                                <textarea name="ghi_chu" class="form-control form-control-lg"
                                    placeholder="Ghi chú">{{ old('ghi_chu', $datBan->ghi_chu ?? '') }}</textarea>
                            </div>

                            {{-- Submit --}}
                            <div class="col-12 d-grid mt-3">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold py-3">
                                    {{ $method === 'PUT' ? 'Cập nhật' : 'Đặt ngay' }}
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- AJAX load bàn theo khu vực + ngày giờ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const khuSelect = document.getElementById('khu-vuc-select');
    const banSelect = document.getElementById('ban-select');
    const gioDenInput = document.querySelector('input[name="gio_den"]');
    const oldBanId = "{{ old('ban_id', $datBan->ban_id ?? '') }}";
    const urlGetBansBase = "{{ url('booking/bans-by-khuvuc') }}";

    function loadBans(khu_vuc_id) {
        banSelect.innerHTML = '<option>Đang tải...</option>';
        if (!khu_vuc_id) {
            banSelect.innerHTML = '<option disabled>Chọn khu vực trước để chọn bàn</option>';
            return;
        }

        const gioDen = gioDenInput ? gioDenInput.value : '';

        fetch(`${urlGetBansBase}/${khu_vuc_id}?gio_den=${encodeURIComponent(gioDen)}`)
            .then(res => res.json())
            .then(bans => {
                if (bans.length > 0) {
                    banSelect.innerHTML = '<option value="">-- Chọn bàn --</option>';
                    bans.forEach(b => {
                        const option = document.createElement('option');
                        option.value = b.id;
                        option.textContent = 'Bàn ' + b.so_ban;
                        if (oldBanId == b.id) option.selected = true;
                        banSelect.appendChild(option);
                    });
                } else {
                    banSelect.innerHTML = '<option disabled>Không có bàn trống trong khu vực này</option>';
                }
            })
            .catch(err => {
                console.error(err);
                banSelect.innerHTML = '<option disabled>Lỗi tải bàn</option>';
            });
    }

    khuSelect.addEventListener('change', function() {
        loadBans(this.value);
    });

    if(gioDenInput) {
        gioDenInput.addEventListener('change', function() {
            if(khuSelect.value) loadBans(khuSelect.value);
        });
    }

    // Load ngay khi trang mở nếu đã chọn khu vực
    if(khuSelect.value) loadBans(khuSelect.value);
});
</script>
@endsection