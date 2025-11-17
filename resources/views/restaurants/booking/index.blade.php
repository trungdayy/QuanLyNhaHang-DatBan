@extends('layouts.restaurants.layout-shop')
@section('title', 'Book a Table Online')
@section('content')

<div class="container-xxl py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <h2 class="mb-4 text-center">Book a Table Online</h2>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
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

            <form action="{{ route('booking.store') }}" method="POST">
                @csrf
                <div class="row g-3">

                    <!-- Tên khách -->
                    <div class="col-md-6">
                        <input type="text" name="ten_khach" class="form-control" placeholder="Your Name"
                               value="{{ old('ten_khach') }}" required>
                    </div>

                    <!-- SĐT khách -->
                    <div class="col-md-6">
                        <input type="text" name="sdt_khach" class="form-control" placeholder="Phone Number"
                               value="{{ old('sdt_khach') }}" required>
                    </div>

                    <!-- Số người -->
                    <div class="col-md-6">
                        <input type="number" name="so_khach" class="form-control" placeholder="Number of People"
                               value="{{ old('so_khach', 1) }}" min="1" required>
                    </div>

                    <!-- Combo -->
                    <div class="col-md-6">
                        <select name="combo_id" class="form-select">
                            <option value="">-- Choose Combo --</option>
                            @foreach($combos as $combo)
                                <option value="{{ $combo->id }}" {{ old('combo_id') == $combo->id ? 'selected' : '' }}>
                                    {{ $combo->ten_combo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Khu vực -->
                    <div class="col-md-6">
                        <select name="khu_vuc_id" id="khu_vuc_id" class="form-select">
                            <option value="">-- Chọn khu vực --</option>
                            @foreach($khuVucs as $kv)
                                <option value="{{ $kv->id }}" {{ old('khu_vuc_id') == $kv->id ? 'selected' : '' }}>
                                    {{ $kv->ten_khu_vuc }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Bàn -->
                    <div class="col-md-6">
                        <select name="ban_id" id="ban_id" class="form-select">
                            <option value="">-- Chọn bàn (theo khu vực) --</option>
                            @foreach($banAns as $ban)
                                <option value="{{ $ban->id }}" data-khu="{{ $ban->khu_vuc_id }}"
                                    {{ old('ban_id') == $ban->id ? 'selected' : '' }}>
                                    Bàn {{ $ban->so_ban }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Ngày giờ -->
                    <div class="col-md-6">
                        <input type="datetime-local" name="gio_den" class="form-control" value="{{ old('gio_den') }}" required>
                    </div>

                    <!-- Ghi chú -->
                    <div class="col-12">
                        <textarea name="ghi_chu" class="form-control" placeholder="Special Request">{{ old('ghi_chu') }}</textarea>
                    </div>

                    <!-- Submit -->
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100 py-3">Book Now</button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const khuVucSelect = document.getElementById('khu_vuc_id');
    const banSelect = document.getElementById('ban_id');

    function filterBans() {
        const selectedKhu = khuVucSelect.value;
        Array.from(banSelect.options).forEach(option => {
            if(option.value === "") return; // luôn hiển thị option mặc định
            option.style.display = (option.dataset.khu === selectedKhu || selectedKhu === "") ? 'block' : 'none';
        });
        banSelect.value = ""; // reset chọn bàn khi đổi khu
    }

    khuVucSelect.addEventListener('change', filterBans);
    filterBans();
});
</script>

@endsection
