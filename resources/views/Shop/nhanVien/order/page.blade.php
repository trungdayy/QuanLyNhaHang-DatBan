@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Order cho khách')

@section('content')
<main class="app-content">

    {{-- Tiêu đề --}}
    <div class="app-title mb-4 d-flex align-items-center gap-2">
        <i class="bi bi-journal-text fs-3 text-primary"></i>
        <h2 class="fw-bold mb-0">Order bàn {{ $order->banAn->so_ban }}</h2>
    </div>

    {{-- Thông tin bàn & order --}}
    <div class="card shadow-sm mb-4 p-4 rounded-4 border-0" style="background: #f8f9fa;">
        <h5 class="fw-semibold"><i class="bi bi-table"></i> Bàn số: {{ $order->banAn->so_ban }}</h5>
        <p class="mb-1"><i class="bi bi-receipt"></i> Order ID: <b>{{ $order->id }}</b></p>
        <p class="mb-1"><i class="bi bi-basket3"></i> Tổng món: <b>{{ $order->tong_mon }}</b></p>
        <p class="mb-0"><i class="bi bi-currency-dollar"></i> Tổng tiền: <b>{{ number_format($order->tong_tien) }} đ</b></p>
    </div>

    {{-- Nút chức năng --}}
    <div class="mb-4 d-flex flex-wrap gap-2">
        <a href="{{ route('nhanVien.chi-tiet-order.create', ['order_id' => $order->id]) }}"
            class="btn btn-primary fw-semibold rounded-pill shadow-sm">
            ➕ Thêm món
        </a>

        <form action="{{ route('nhanVien.order.gui-bep', $order->id) }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-warning fw-semibold rounded-pill shadow-sm">📤 Gửi bếp</button>
        </form>
    </div>

    {{-- Danh sách món --}}
    <div class="card p-3 rounded-4 shadow-sm border-0" style="background: #ffffff;">
        <h5 class="mb-3 fw-semibold">Danh sách món đã chọn</h5>

        @if($order->chiTietOrders->isEmpty())
        <p class="text-muted">Chưa có món nào.</p>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Món ăn</th>
                        <th>Số lượng</th>
                        <th>Ghi chú</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->chiTietOrders as $ct)
                    <tr>
                        <td>{{ $ct->monAn->ten_mon }}</td>
                        <td>{{ $ct->so_luong_hien_thi }}</td>
                        <td>{{ $ct->ghi_chu }}</td>
                        <td>
                            <a href="{{ route('nhanVien.chi-tiet-order.edit', [$order->id, $ct->id]) }}"
                                class="btn btn-sm btn-warning rounded-pill shadow-sm me-1 mb-1">Sửa</a>

                            <form action="{{ route('nhanVien.chi-tiet-order.destroy', $ct->id) }}"
                                method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa món này không?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger rounded-pill shadow-sm mb-1">Xóa</button>
                            </form>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</main>

{{-- CSS nâng cấp --}}
<style>
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .btn {
        transition: all 0.3s;
    }

    .btn:hover {
        transform: translateY(-2px);
    }

    table th,
    table td {
        vertical-align: middle !important;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
    }
</style>
@endsection