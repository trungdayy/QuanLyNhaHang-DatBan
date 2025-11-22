{{-- resources/views/restaurants/booking/create.blade.php hoặc edit.blade.php --}}
@extends('layouts.restaurants.layout-shop')
@section('title', $method === 'PUT' ? 'Cập nhật đặt bàn' : 'Đặt Bàn Online')
@section('content')

<div class="container py-5">
    <h2 class="mb-4 text-center">{{ $method === 'PUT' ? 'Cập nhật đặt bàn' : 'Đặt Bàn Online' }}</h2>

    {{-- Thông báo --}}
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

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="{{ $action }}" method="POST" class="booking-form">
                @csrf
                @if($method === 'PUT') @method('PUT') @endif

                <div class="row g-3">
                    {{-- Tên khách --}}
                    <div class="col-md-6">
                        <input type="text" name="ten_khach" class="form-control" placeholder="Họ và tên"
                            value="{{ old('ten_khach', $datBan->ten_khach ?? '') }}" required>
                    </div>

                    {{-- Email khách --}}
                    <div class="col-md-6">
                        <input type="email" name="email_khach" class="form-control" placeholder="Email"
                            value="{{ old('email_khach', $datBan->email_khach ?? '') }}">
                    </div>

                    {{-- SĐT khách --}}
                    <div class="col-md-6">
                        <input type="text" name="sdt_khach" class="form-control" placeholder="Số điện thoại"
                            value="{{ old('sdt_khach', $datBan->sdt_khach ?? '') }}" required>
                    </div>

                    {{-- Số khách --}}
                    <div class="col-md-6">
                        <input type="number" name="so_khach" class="form-control" placeholder="Số khách"
                            value="{{ old('so_khach', $datBan->so_khach ?? 1) }}" min="1" required>
                    </div>

                    {{-- Combo --}}
                    <div class="col-md-6">
                        <select name="combo_id" class="form-select">
                            <option value="">-- Chọn Combo --</option>
                            @foreach($combos as $combo)
                            <option value="{{ $combo->id }}"
                                {{ old('combo_id', $datBan->combo_id ?? '') == $combo->id ? 'selected' : '' }}>
                                {{ $combo->ten_combo }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Khu vực --}}
                    <div class="col-md-6">
                        <select name="khu_vuc_id" id="khu_vuc_id" class="form-select" required>
                            <option value="">-- Chọn Khu Vực --</option>
                            @foreach($khuVucs as $kv)
                            <option value="{{ $kv->id }}" data-tang="{{ $kv->tang }}"
                                {{ old('khu_vuc_id', $datBan->ban?->khu_vuc_id ?? '') == $kv->id ? 'selected' : '' }}>
                                {{ $kv->ten_khu_vuc }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Bàn --}}
                    <div class="col-md-6">
                        <select name="ban_id" id="ban_id" class="form-select" required>
                            <option value="">-- Chọn bàn --</option>
                            @foreach($banAns as $ban)
                            @if($ban->trang_thai === 'trong')
                            <option value="{{ $ban->id }}" data-khu="{{ $ban->khu_vuc_id }}"
                                data-tang="{{ $ban->khuVuc->tang ?? 1 }}"
                                {{ old('ban_id', $datBan->ban_id ?? '') == $ban->id ? 'selected' : '' }}>
                                Bàn {{ $ban->so_ban }}
                            </option>
                            @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- Ngày giờ đến --}}
                    <div class="col-md-6">
                        <input type="datetime-local" name="gio_den" class="form-control"
                            value="{{ old('gio_den', isset($datBan->gio_den) ? \Carbon\Carbon::parse($datBan->gio_den)->format('Y-m-d\TH:i') : '') }}"
                            required>
                    </div>

                    {{-- Ghi chú --}}
                    <div class="col-12">
                        <textarea name="ghi_chu" class="form-control"
                            placeholder="Ghi chú">{{ old('ghi_chu', $datBan->ghi_chu ?? '') }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <div class="col-12">
                        <button type="submit"
                            class="btn btn-success w-100 py-3 fw-bold">{{ $method === 'PUT' ? 'Cập nhật' : 'Đặt ngay' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const tangSelect = document.getElementById('tang_id');
    const khuVucSelect = document.getElementById('khu_vuc_id');
    const banSelect = document.getElementById('ban_id');

    function filterBans() {
        const selectedTang = tangSelect.value;
        const selectedKhu = khuVucSelect.value;
        let firstVisible = null;

        Array.from(banSelect.options).forEach(option => {
            if(option.value === "") return;

            if(option.dataset.tang == selectedTang && option.dataset.khu == selectedKhu){
                option.style.display = 'block';
                if(!firstVisible) firstVisible = option.value;
            } else {
                option.style.display = 'none';
            }
        });

        if(banSelect.value && banSelect.options[banSelect.selectedIndex].style.display === 'none') {
            banSelect.value = firstVisible || "";
        }
    }

    tangSelect.addEventListener('change', filterBans);
    khuVucSelect.addEventListener('change', filterBans);
    filterBans();
});
</script>
@endpush

@push('styles')
<style>
    .booking-form .form-control,
    .booking-form .form-select,
    .booking-form textarea {
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .booking-form .form-control:focus,
    .booking-form .form-select:focus,
    .booking-form textarea:focus {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        border-color: #28a745;
    }

    .btn-success {
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background-color: #218838;
        transform: translateY(-2px);
    }
</style>
@endpush

@endsection