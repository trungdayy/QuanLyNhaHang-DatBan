{{-- resources/views/restaurants/booking/_form.blade.php --}}
<form action="{{ $action }}" method="POST">
    @csrf
    @if(isset($method) && $method === 'PUT')
    @method('PUT')
    @endif

    <div class="row g-3">

        {{-- Họ và tên --}}
        <div class="col-md-6">
            <label class="form-label fw-semibold">Họ và tên</label>
            <input type="text" name="ten_khach" class="form-control" placeholder="Họ và tên"
                value="{{ old('ten_khach', $datBan->ten_khach ?? '') }}" required>
        </div>

        {{-- Số điện thoại --}}
        <div class="col-md-6">
            <label class="form-label fw-semibold">Số điện thoại</label>
            <input type="text" name="sdt_khach" class="form-control" placeholder="Số điện thoại"
                value="{{ old('sdt_khach', $datBan->sdt_khach ?? '') }}" required>
        </div>

        {{-- Giờ đến --}}
        <div class="col-md-6">
            <label class="form-label fw-semibold">Giờ đến</label>
            <input type="datetime-local" name="gio_den" class="form-control"
                value="{{ old('gio_den', isset($datBan->gio_den) ? \Carbon\Carbon::parse($datBan->gio_den)->format('Y-m-d\TH:i') : '') }}"
                required>
        </div>

        {{-- Người lớn --}}
        <div class="col-md-3">
            <label class="form-label fw-semibold">Người lớn</label>
            <input type="number" name="nguoi_lon" class="form-control"
                value="{{ old('nguoi_lon', $datBan->nguoi_lon ?? 1) }}" min="0" required>
        </div>

        {{-- Trẻ em --}}
        <div class="col-md-3">
            <label class="form-label fw-semibold">Trẻ em</label>
            <input type="number" name="tre_em" class="form-control" value="{{ old('tre_em', $datBan->tre_em ?? 0) }}"
                min="0" required>
        </div>

        {{-- Submit --}}
        <div class="col-12 mt-3">
            <button type="submit" class="btn btn-primary w-100">
                {{ isset($method) && $method === 'PUT' ? 'Cập nhật' : 'Đặt ngay' }}
            </button>
        </div>

    </div>
</form>