@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Danh sách bàn')

@section('content')
<main class="app-content">
    @if(session('success'))
    <div class="alert alert-success text-center fw-semibold rounded-3 shadow-sm" id="flashMsg">
        {{ session('success') }}
    </div>
    @endif

    <div class="app-title d-flex justify-content-between align-items-center mb-4 p-3 rounded-4 shadow-sm"
        style="background: linear-gradient(135deg, #ffffff, #f0f2f5);">
        <h2 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-columns-gap me-2 text-primary"></i>
            Danh sách bàn
        </h2>
    </div>

    {{-- Phân chia theo khu vực --}}
    @foreach($khuVucs as $khu)
    <div class="mb-4">
        <h4 class="fw-bold mb-3">
            <i class="bi bi-building me-2"></i> {{ $khu->ten_khu_vuc }} (Tầng {{ $khu->tang }})
        </h4>
        <div class="row g-3 justify-content-start">
            @foreach($bans->where('khu_vuc_id', $khu->id) as $ban)
            @php
            $order = $orders->has($ban->id) ? $orders[$ban->id] : null;

            $datBanMoiNhat = \App\Models\DatBan::where('ban_id', $ban->id)->latest()->first();

            if($ban->trang_thai == 'trong') {
            $bgHeader = 'linear-gradient(135deg, #28a745, #7be495)';
            $icon = 'bi-person-check';
            } elseif($ban->trang_thai == 'dang_phuc_vu') {
            $bgHeader = 'linear-gradient(135deg, #dc3545, #ff6b6b)';
            $icon = 'bi-people-fill';
            } else {
            $bgHeader = 'linear-gradient(135deg, #ffc107, #ffe58a)';
            $icon = 'bi-tools';
            }
            @endphp

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 d-flex">
                <div class="card table-card shadow-sm rounded-4 border-0 position-relative overflow-hidden flex-fill d-flex flex-column">
                    {{-- Header trạng thái --}}
                    <div class="table-card-header text-center text-white fw-bold py-2 rounded-top"
                        style="background: {{ $bgHeader }};">
                        <h5 class="mb-1"><i class="bi {{ $icon }}"></i> Bàn {{ $ban->so_ban }}</h5>
                        <div class="mt-2 w-100 text-center">
                            @php
                            $trangThaiText = 'Trống';
                            $trangThaiClass = 'bg-secondary';

                            if(isset($datBanMoiNhat)) {
                            switch($datBanMoiNhat->trang_thai) {
                            case 'da_xac_nhan':
                            $trangThaiText = 'Đã đặt';
                            $trangThaiClass = 'bg-warning text-dark';
                            break;
                            case 'khach_da_den':
                            if(isset($order)) {
                            $trangThaiText = 'Đang phục vụ';
                            $trangThaiClass = 'bg-success text-white';
                            } else {
                            $trangThaiText = 'Khách đã đến (chưa mở order)';
                            $trangThaiClass = 'bg-info text-white';
                            }
                            break;
                            default:
                            $trangThaiText = 'Trống';
                            $trangThaiClass = 'bg-secondary';
                            break;
                            }
                            }
                            @endphp
                            <span class="badge {{ $trangThaiClass }}">
                                {{ $trangThaiText }}
                            </span>
                        </div>

                        <small class="opacity-75">
                            @if($order)
                            ID: {{ $order->id }} | {{ $order->tong_mon }} món
                            @elseif($ban->trang_thai == 'san_sang')
                            Đã đặt bàn
                            @else
                            Trống
                            @endif
                        </small>
                    </div>

                    {{-- Thân card --}}
                    <div class="card-body d-flex flex-column align-items-center justify-content-center p-3">
                        @if($order)
                        <p class="mb-1 text-truncate"><i class="bi bi-receipt"></i> <b>Order số: {{ $order->id }}</b></p>
                        @if($order && $order->datBan)
                        <p class="mb-1"><i class="bi bi-person-fill"></i> Khách: <b>{{ $order->datBan->ten_khach }}</b></p>
                        <p class="mb-1"><i class="bi bi-telephone-fill"></i> SĐT: <b>{{ $order->datBan->sdt_khach }}</b></p>
                        @endif
                        <p class="mb-1"><i class="bi bi-basket3"></i> Tổng món: <b>{{ $order->tong_mon }}</b></p>
                        <p class="mb-2"><i class="bi bi-currency-dollar"></i> <b>{{ number_format($order->tong_tien) }} đ</b></p>

                        {{-- Icon chi tiết --}}
                        <a href="{{ route('nhanVien.order.page', $order->id) }}"
                            class="btn btn-warning btn-lg rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                            style="width:50px; height:50px; padding:0;">
                            <i class="bi bi-card-checklist fs-5"></i>
                        </a>
                        @else
                        {{-- Icon mở order --}}
                        <form action="{{ route('nhanVien.order.mo-order') }}" method="POST" class="d-flex">
                            @csrf
                            <input type="hidden" name="ban_id" value="{{ $ban->id }}">
                            <button class="btn btn-success btn-lg rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                type="submit" style="width:50px; height:50px; padding:0;">
                                <i class="bi bi-plus-circle fs-5"></i>
                            </button>
                        </form>
                        @endif
                    </div>

                    {{-- Footer trạng thái --}}
                    <div class="table-card-footer position-absolute bottom-0 start-0 w-100 text-center py-1 text-white opacity-75"
                        style="font-size: 0.75rem;">
                        @if($order)
                        ID: {{ $order->id }} | {{ $order->tong_mon }} món
                        @else
                        Sẵn sàng phục vụ
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</main>

{{-- CSS --}}
<style>
    .table-card {
        background: linear-gradient(145deg, #ffffff, #f0f2f5);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .table-card:hover {
        transform: translateY(-5px) scale(1.02);
        /* giảm scale tránh lệch */
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
    }

    .btn-outline-primary {
        transition: all 0.3s;
    }

    .btn-outline-primary:hover {
        background-color: #0d6efd;
        color: #fff;
    }

    #flashMsg {
        animation: fadeOut 5s forwards;
    }

    @keyframes fadeOut {

        0%,
        80% {
            opacity: 1;
        }

        100% {
            opacity: 0;
        }
    }

    .table-card-header i {
        margin-right: 5px;
    }

    .btn-lg i {
        pointer-events: none;
    }
</style>

@endsection