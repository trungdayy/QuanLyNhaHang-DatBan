@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Sửa món trong Order')

@section('content')
<main class="app-content">
    <h3 class="fw-bold mb-4">Sửa món: {{ $ct->monAn->ten_mon }}</h3>

    <form action="{{ route('nhanVien.chi-tiet-order.update', $ct->id) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="hidden" name="order_id" value="{{ $order->id }}">

        {{-- Nếu là món gọi thêm mới cho sửa số lượng --}}
        @if($ct->loai_mon === 'goi_them')
        <div class="mb-4">
            <label class="form-label fw-semibold">Số lượng</label>
            <input type="number" name="so_luong" class="form-control rounded-3 shadow-sm" value="{{ old('so_luong', $ct->so_luong) }}" min="1" required>
        </div>
        @else
        {{-- Combo chỉ hiển thị số lượng nhưng không cho sửa --}}
        <div class="mb-4">
            <label class="form-label fw-semibold">Số lượng</label>
            <input type="number" class="form-control rounded-3 shadow-sm" value="{{ $ct->so_luong }}" readonly>
            <small class="text-muted">Lưu ý: Không thể thay đổi số lượng khi là món trong combo</small>
        </div>
        @endif

        <div class="mb-4">
            <label class="form-label fw-semibold">Ghi chú</label>
            <input type="text" name="ghi_chu" class="form-control rounded-3 shadow-sm" value="{{ old('ghi_chu', $ct->ghi_chu) }}" placeholder="Nhập ghi chú nếu cần">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success rounded-pill shadow-sm px-4">Cập nhật</button>
            <a href="{{ route('nhanVien.chi-tiet-order.show', $order->id) }}" class="btn btn-secondary rounded-pill shadow-sm px-4">Hủy</a>
        </div>
    </form>
</main>

{{-- CSS bổ sung nhẹ để UI thêm sang --}}
<style>
    .form-control:focus {
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
        border-color: #0d6efd;
    }

    button.btn:hover,
    a.btn:hover {
        transform: translateY(-2px);
        transition: all 0.2s;
    }
</style>
@endsection