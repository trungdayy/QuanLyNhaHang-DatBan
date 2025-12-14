@extends('layouts.admins.layout-admin')
@section('title', 'Danh sách chi tiết order')
@section('content')
    <main class="app-content">
        @if (session('success'))
            <div class="alert alert-success text-center fw-semibold rounded-3 shadow-sm" id="flashMsg">
                {{ session('success') }}
            </div>
        @endif

        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb side">
                <li class="breadcrumb-item active"><a href="{{ route('admin.chi-tiet-order.index') }}"><b>Chi tiết
                            order</b></a></li>
            </ul>
            <div id="clock"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="tile-title">Chi tiết order</h3>

                        {{-- 🧩 Form chọn đơn hàng --}}
                        <form action="{{ route('admin.chi-tiet-order.index') }}" method="GET"
                            class="d-flex align-items-center gap-2">
                            <select name="order_id" id="order_id" class="form-select form-select-sm" style="width:200px;">
                                <option value="">-- Chọn đơn hàng --</option>
                                @foreach ($orders as $orderOption)
                                    <option value="{{ $orderOption->id }}">
                                        Đơn #{{ $orderOption->id }} - {{ $orderOption->datBan->ten_khach ?? 'Khách' }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">
                                Xem chi tiết
                            </button>
                        </form>
                    </div>

                    <div class="tile-body">
                        <div class="rounded overflow-hidden">
                            <table class="table table-bordered table-hover align-middle text-center mb-0" id="">
                                <thead style="background-color: #002b5b; color: white;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Mã order</th>
                                        <th>Mã đặt bàn</th>
                                        <th>Tên khách</th>
                                        <th>Ngày tạo</th>
                                        <th>Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>

                                            {{-- ✅ Mã order: dùng ID order_mon --}}
                                            <td>ORDER-{{ str_pad($order->id, 0, '0', STR_PAD_LEFT) }}</td>
                                            <td>{{ $order->datBan->ma_dat_ban }}</td>


                                            <td>{{ $order->datBan->ten_khach ?? 'N/A' }}</td>
                                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a  href="{{ route('admin.chi-tiet-order.index', ['order_id' => $order->id]) }} "
                                                    class="btn btn-primary btn-sm">
                                                    Xem chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        document.getElementById('order_id').addEventListener('change', function() {
            if (this.value) {
                window.location.href = "{{ route('admin.chi-tiet-order.index') }}?order_id=" + this.value;
            }
        });
    </script>
@endsection