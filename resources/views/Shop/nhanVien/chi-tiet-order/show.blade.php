@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Chi tiết Order')

@section('content')
<main class="app-content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">Chi tiết Order #{{ $order->id }}</h3>
        <span class="badge bg-primary fs-6">Bàn: {{ $order->banAn->so_ban ?? 'Không xác định' }}</span>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Nút thêm món --}}
    <div class="mb-3">
        <a href="{{ route('nhanVien.chi-tiet-order.create', ['order_id' => $order->id]) }}"
            class="btn btn-success rounded-pill shadow-sm fw-semibold">
            <i class="bi bi-plus-circle me-1"></i> Thêm món mới
        </a>
    </div>

    {{-- Bảng món --}}
    <div class="card shadow-sm rounded-4 p-3 border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Món</th>
                        <th>Số lượng</th>
                        <th>Ghi chú</th>
                        <th class="text-end">Tác vụ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->chiTietOrders as $ct)
                    <tr>
                        <td>{{ $ct->monAn->ten_mon ?? 'Không xác định' }}</td>
                        <td>{{ $ct->so_luong_hien_thi }}</td>
                        <td>{{ $ct->ghi_chu ?? '-' }}</td>
                        <td class="text-end">
                            <a href="{{ route('nhanvien.chi-tiet-order.edit', [$order->id, $ct->id]) }}"
                                class="btn btn-sm btn-warning rounded-pill shadow-sm me-1 mb-1" title="Sửa">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            @if ($ct->loai_mon === 'goi_them')
                            <form action="{{ route('nhanVien.chi-tiet-order.destroy', $ct->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger rounded-pill shadow-sm mb-1" title="Xóa">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</main>

{{-- CSS nhỏ nâng cao UI --}}
<style>
    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
        transition: background-color 0.2s;
    }

    .btn {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection