@extends('layouts.restaurants.layout-shop')
@section('title', 'Đặt bàn online')
@section('content')

<div class="container py-5">
    <h1 class="mb-4">Đặt bàn online</h1>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('booking.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <!-- Họ tên khách -->
            <div class="col-md-6">
                <label for="ten_khach" class="form-label">Họ tên</label>
                <input type="text" name="ten_khach" id="ten_khach"
                    class="form-control @error('ten_khach') is-invalid @enderror" value="{{ old('ten_khach') }}">
                @error('ten_khach')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- SĐT khách -->
            <div class="col-md-6">
                <label for="sdt_khach" class="form-label">SĐT</label>
                <input type="text" name="sdt_khach" id="sdt_khach"
                    class="form-control @error('sdt_khach') is-invalid @enderror" value="{{ old('sdt_khach') }}">
                @error('sdt_khach')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Số khách -->
            <div class="col-md-6">
                <label for="so_khach" class="form-label">Số khách</label>
                <input type="number" name="so_khach" id="so_khach"
                    class="form-control @error('so_khach') is-invalid @enderror" value="{{ old('so_khach') }}" min="1">
                @error('so_khach')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Ngày giờ đến -->
            <div class="col-md-6">
                <label for="gio_den" class="form-label">Ngày & giờ đến</label>
                <input type="datetime-local" name="gio_den" id="gio_den"
                    class="form-control @error('gio_den') is-invalid @enderror" value="{{ old('gio_den') }}">
                @error('gio_den')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Combo -->
            <div class="col-md-6">
                <label for="combo_id" class="form-label">Chọn Combo (tùy chọn)</label>
                <select name="combo_id" id="combo_id" class="form-select @error('combo_id') is-invalid @enderror">
                    <option value="">-- Không chọn --</option>
                    @foreach($combos as $combo)
                    <option value="{{ $combo->id }}" {{ old('combo_id') == $combo->id ? 'selected' : '' }}>
                        {{ $combo->ten_combo }}
                    </option>
                    @endforeach
                </select>
                @error('combo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Khu vực -->
            <div class="col-md-6">
                <label for="khu_vuc" class="form-label">Chọn Khu vực</label>
                <select id="khu_vuc" class="form-select">
                    <option value="">-- Chọn khu vực --</option>
                    @php
                    $khuVucs = $banAns->map(fn($b)=>$b->khuVuc)->unique('id')->filter();
                    @endphp
                    @foreach($khuVucs as $kv)
                    <option value="{{ $kv->id }}">{{ $kv->ten_khu_vuc }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Bàn -->
            <div class="col-md-6">
                <label for="ban_id" class="form-label">Chọn Bàn</label>
                <select name="ban_id" id="ban_id" class="form-select @error('ban_id') is-invalid @enderror">
                    <option value="">-- Chọn bàn --</option>
                    @foreach($banAns as $ban)
                    <option value="{{ $ban->id }}" data-khu="{{ $ban->khu_vuc_id }}" style="display:none">
                        Bàn {{ $ban->so_ban }}
                    </option>
                    @endforeach
                </select>
                @error('ban_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Ghi chú -->
            <div class="col-12">
                <label for="ghi_chu" class="form-label">Ghi chú (tùy chọn)</label>
                <textarea name="ghi_chu" id="ghi_chu" rows="4"
                    class="form-control @error('ghi_chu') is-invalid @enderror">{{ old('ghi_chu') }}</textarea>
                @error('ghi_chu')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <!-- Submit -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">Đặt bàn ngay</button>
            </div>
        </div>
    </form>
</div>

<script>
    const khuSelect = document.getElementById('khu_vuc');
    const banSelect = document.getElementById('ban_id');

    function filterBans() {
        const khuId = khuSelect.value;
        Array.from(banSelect.options).forEach(opt => {
            if(!opt.value) return; // giữ option mặc định
            opt.style.display = opt.dataset.khu == khuId ? 'block' : 'none';
        });
        banSelect.value = ""; // reset chọn bàn
    }

    khuSelect.addEventListener('change', filterBans);

    // load lại bàn nếu có old value
    window.addEventListener('DOMContentLoaded', () => {
        if(khuSelect.value) filterBans();
        @if(old('ban_id'))
            banSelect.value = "{{ old('ban_id') }}";
        @endif
    });
</script>

@endsection