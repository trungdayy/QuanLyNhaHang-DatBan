@extends('layouts.admins.layout-admin')

@section('title', 'Danh sách Order món')

@section('content')
<div class="app-content">
    <div class="app-title">
        <h4>Danh sách Order món</h4>
    </div>

    <a href="{{ route('admin.order-mon.create') }}" class="btn btn-success mb-3">+ Tạo Order mới</a>

    <table class="table table-bordered text-center">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Đặt bàn</th>
                <th>Bàn</th>
                <th>Tổng món</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->datBan->ten_khach ?? 'N/A' }}</td>
                <td>{{ $order->datBan->ban_id ?? 'N/A' }}</td>
                <td>{{ $order->tong_mon }}</td>
                <td>{{ number_format($order->tong_tien, 0, ',', '.') }} đ</td>
                <td>
                    @switch($order->trang_thai)
                    @case('dang_xu_li') <span class="badge bg-info">Đang xử lí</span> @break
                    @case('hoan_thanh') <span class="badge bg-success">Hoàn Thành</span> @break
                    @case('huy_mon') <span class="badge bg-danger">Hủy món</span> @break
                    @endswitch
                </td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.order-mon.edit', $order->id) }}" class="btn btn-warning btn-sm"><i
                            class="fas fa-edit"></i></a>
                    <!-- <form action="{{ route('admin.order-mon.destroy', $order->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa order này?')"><i class="fas fa-trash"></i></button>
                    </form> -->
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $orders->links() }}
</div>
@endsection