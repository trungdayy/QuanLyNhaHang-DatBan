@extends('layouts.admins.layout-admin')

@section('title', 'Danh sách Order món')

@section('content')
    <div class="app-content">
        <div class="app-title">
            <h4>Danh sách Order món</h4>
        </div>

        <a href="{{ route('admin.order-mon.create') }}" class="btn btn-success mb-3">+ Tạo Order mới</a>

        <form method="GET" action="{{ route('admin.order-mon.index') }}" class="mb-3 row g-2">
            <div class="col-md-2">
                <input type="text" name="ma_dat_ban" class="form-control" placeholder="Mã đặt bàn"
                    value="{{ request('ma_dat_ban') }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="ten_khach" class="form-control" placeholder="Tên khách"
                    value="{{ request('ten_khach') }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="so_ban" class="form-control" placeholder="Bàn" value="{{ request('so_ban') }}">
            </div>
            <div class="col-md-2">
                <select name="trang_thai" class="form-control">
                    <option value="">Tất cả trạng thái</option>
                    <option value="dang_xu_li" {{ request('trang_thai') == 'dang_xu_li' ? 'selected' : '' }}>Đang xử lí
                    </option>
                    <option value="hoan_thanh" {{ request('trang_thai') == 'hoan_thanh' ? 'selected' : '' }}>Hoàn Thành
                    </option>
                    <option value="huy_mon" {{ request('trang_thai') == 'huy_mon' ? 'selected' : '' }}>Hủy món</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter me-1"></i> Lọc
                </button>
                <a href="{{ route('admin.order-mon.index') }}" class="btn btn-secondary flex-grow-1">
                    <i class="fas fa-undo me-1"></i> Reset
                </a>
            </div>
        </form>

        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Mã Đặt bàn</th>
                    <th>Tên khách</th>
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
                        <td>{{ $order->datBan->ma_dat_ban ?? 'N/A' }}</td>
                        <td>{{ $order->datBan->ten_khach ?? 'N/A' }}</td>
                        <td>{{ $order->datBan->banAn->so_ban ?? 'N/A' }}</td>
                        <td>{{ $order->tong_mon }}</td>
                        <td>{{ number_format($order->tong_tien, 0, ',', '.') }} đ</td>
                        <td>
                            @switch($order->trang_thai)
                                @case('dang_xu_li')
                                    <span class="badge bg-info">Đang xử lí</span>
                                @break

                                @case('hoan_thanh')
                                    <span class="badge bg-success">Hoàn Thành</span>
                                @break

                                @case('huy_mon')
                                    <span class="badge bg-danger">Hủy món</span>
                                @break
                            @endswitch
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td> 
                            <a href="{{ route('admin.chi-tiet-order.index', ['order_id' => $order->id]) }} "
                                class="btn btn-primary btn-sm">
                                Xem chi tiết
                            </a>
                            <a href="{{ route('admin.order-mon.edit', $order->id) }}" class="btn btn-warning btn-sm"><i
                                    class="fas fa-edit"></i></a>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $orders->links() }}
    </div>
@endsection
