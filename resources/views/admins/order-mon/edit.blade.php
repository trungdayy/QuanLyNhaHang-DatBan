@extends('layouts.admins.layout-admin')

@section('title', 'Sửa Order món')

@section('content')
<div class="app-content">
    <div class="app-title">
        <h4>Sửa Order món</h4>
    </div>

    <form action="{{ route('admin.order-mon.update', $orderMon->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <label>Đặt bàn:</label>
                <input type="text" class="form-control" value="{{ $orderMon->datBan->ma_dat_ban ?? 'Không rõ' }}" readonly>
                <input type="hidden" name="dat_ban_id" value="{{ $orderMon->dat_ban_id }}">

                <label class="mt-3">Tổng món:</label>
                <input type="number" class="form-control" value="{{ $orderMon->tong_mon }}" readonly>

                <label class="mt-3">Tổng tiền:</label>
                <input type="text" class="form-control" value="{{ number_format($orderMon->tong_tien,0,',','.') }} đ" readonly>
            </div>

            <div class="col-md-6">
                <label>Trạng thái</label>
                <select name="trang_thai" class="form-control" required>
                    @foreach($allowedStatus as $key => $label)
                    <option value="{{ $key }}"
                        {{ old('trang_thai', $orderMon->trang_thai) === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>

                @if(session('error'))
                <div class="text-danger mt-2">{{ session('error') }}</div>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
            <a href="{{ route('admin.order-mon.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection