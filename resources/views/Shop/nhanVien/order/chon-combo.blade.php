@extends('layouts.Shop.layout-nhanvien')

@section('title', 'Chọn Combo Buffet')

@section('content')
<main class="app-content">

    <h3 class="mb-3">Chọn Combo Buffet cho Order #{{ $order->id }}</h3>
    <p><b>Bàn:</b> {{ $order->banAn->so_ban ?? 'Không xác định' }}</p>
    <hr>

    <div class="row">
        @foreach ($combos as $combo)
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card combo-card h-100 border-0">
                {{-- Hình ảnh combo --}}
                @php
                $imgPath = 'uploads/combo_buffet/' . $combo->anh;
                @endphp
                <img src="{{ file_exists(public_path($imgPath)) ? asset($imgPath) : asset('images/no-image.png') }}"
                    class="card-img-top combo-img"
                    alt="Hình Combo">

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title font-weight-bold">{{ $combo->ten_combo }}</h5>

                    <p class="text-primary font-weight-bold mb-2">Giá: {{ number_format($combo->gia_co_ban) }} VNĐ</p>

                    <hr>

                    <p class="mb-2 font-weight-bold">Món bao gồm:</p>
                    <ul class="list-unstyled">
                        @foreach ($combo->monTrongCombo as $ct)
                        @php
                        $monImgPath = 'uploads/mon_an/' . $ct->monAn->anh;
                        @endphp
                        <li class="d-flex align-items-center mb-2 combo-item">
                            <img src="{{ file_exists(public_path($monImgPath)) ? asset($monImgPath) : asset('images/no-image.png') }}"
                                alt="{{ $ct->monAn->ten_mon }}"
                                class="combo-item-img">
                            <span class="text-dark">{{ $ct->monAn->ten_mon }}</span>
                            <span class="ml-auto text-muted">(SL: {{ $ct->gioi_han_so_luong }})</span>
                        </li>
                        @endforeach
                    </ul>

                    <form method="POST" action="{{ route('nhanVien.order.luu-combo', $order->id) }}" class="mt-auto">
                        @csrf
                        <input type="hidden" name="combo_id" value="{{ $combo->id }}">
                        <button type="submit" class="btn btn-success btn-block font-weight-bold btn-hover-scale">
                            Chọn Combo Này
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @endforeach
    </div>

</main>

{{-- Thêm CSS nhỏ cải thiện UI/UX --}}
<style>
    .card:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .card-title {
        font-size: 1.2rem;
    }

    ul.list-unstyled li:hover {
        background-color: #f8f9fa;
        border-radius: 6px;
    }

    .combo-card {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #fff;
    }

    .combo-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
    }

    .combo-img {
        border-radius: 20px 20px 0 0;
        border-bottom: 2px solid #f1f1f1;
        box-shadow: inset 0 -3px 6px rgba(0, 0, 0, 0.05);
        object-fit: cover;
        height: 180px;
    }

    .card-title {
        font-size: 1.3rem;
    }

    .combo-item:hover {
        background-color: #f0f8ff;
        border-radius: 8px;
    }

    .combo-item-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #ddd;
        margin-right: 10px;
    }

    .btn-hover-scale {
        transition: all 0.2s ease;
    }

    .btn-hover-scale:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>

</style>

@endsection