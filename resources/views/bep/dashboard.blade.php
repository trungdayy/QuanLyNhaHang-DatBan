@extends('layouts.Shop.layout-bep')

@section('title', 'Dashboard Bếp')

@section('style')
<style>
    /* Scrollbar đẹp */
    .card-body::-webkit-scrollbar {
        width: 6px;
    }
    .card-body::-webkit-scrollbar-thumb {
        background: #bbb;
        border-radius: 10px;
    }

    .card {
        border-radius: 14px !important;
        overflow: hidden;
        transition: 0.25s ease !important;
        border: none !important;
        box-shadow: 0 3px 10px rgba(0,0,0,0.12);
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.22) !important;
    }

    .card-header {
        font-size: 1.15rem;
        padding: 10px 16px;
        font-weight: bold;
    }

    .mon-item {
        border-left-width: 6px !important;
        border-radius: 12px !important;
        transition: background 0.2s ease !important;
        background: #fff !important;
    }

    .mon-item:hover {
        background-color: #f7f7f7 !important;
    }

    /* Màu trạng thái */
    .status-cho_bep { border-left-color: #f1c40f !important; background-color: #fff7d1 !important; }
    .status-dang_che_bien { border-left-color: #3498db !important; background-color: #eaf4ff !important; }
    .status-da_len_mon { border-left-color: #2ecc71 !important; background-color: #e9fbea !important; }
    .status-huy_mon { border-left-color: #e74c3c !important; background-color: #fdeaea !important; }

    .mon-img {
        width: 48px !important;
        height: 48px !important;
        border-radius: 8px !important;
        object-fit: cover !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.12) !important;
    }

    /* Badge ưu tiên có hiệu ứng */
    .badge-uu-tien {
        animation: pulse 1.2s infinite !important;
        font-size: 0.7rem !important;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: .55; }
        100% { opacity: 1; }
    }

    .form-select-sm {
        padding: 2px 6px !important;
        font-size: 0.85rem !important;
    }
</style>
@endsection

@section('content')
<main class="app-content">

    @if (session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <div class="tile">
        <div class="tile-title-w-btn d-flex justify-content-between align-items-center mb-2">
            <h3 class="tile-title mb-0">Danh sách món cần chế biến</h3>
        </div>

        <div class="tile-body">

            {{-- Bộ lọc khu bếp --}}
            <form method="GET" action="{{ route('bep.dashboard') }}" class="mb-3">
                <select name="khu_bep" class="form-select w-auto d-inline">
                    <option value="">-- Tất cả khu bếp --</option>
                    <option value="nong" {{ request('khu_bep') == 'nong' ? 'selected' : '' }}>Khu nóng</option>
                    <option value="lanh" {{ request('khu_bep') == 'lanh' ? 'selected' : '' }}>Khu lạnh</option>
                    <option value="nuong" {{ request('khu_bep') == 'nuong' ? 'selected' : '' }}>Khu nướng</option>
                    <option value="nuoc" {{ request('khu_bep') == 'nuoc' ? 'selected' : '' }}>Nước uống</option>
                </select>
                <button type="submit" class="btn btn-sm btn-secondary">Lọc</button>
            </form>

            <div class="row">
                @forelse ($theoBan as $soBan => $danhSachMon)
                    <div class="col-md-4">
                        <div class="card shadow mb-4">

                            {{-- Header bàn --}}
                            <div class="card-header bg-dark text-white">
                                Bàn {{ $soBan }}
                                <span class="text-warning">
                                    — {{ $danhSachMon->first()->orderMon->created_at->format('H:i') }}
                                </span>
                            </div>

                            {{-- Danh sách món --}}
                            <div class="card-body" style="max-height: 430px; overflow-y: auto;">

                                @foreach ($danhSachMon as $mon)
                                    @php
                                        $from = $mon->trang_thai;
                                        $disable = [
                                            'cho_bep' => ['da_len_mon'],
                                            'dang_che_bien' => ['cho_bep'],
                                            'da_len_mon' => ['cho_bep', 'dang_che_bien', 'huy_mon'],
                                            'huy_mon' => ['cho_bep', 'dang_che_bien', 'da_len_mon'],
                                        ];
                                    @endphp

                                    <div class="d-flex align-items-center border mon-item p-2 mb-2 status-{{ $mon->trang_thai }}">

                                        {{-- Ảnh món --}}
                                        @if ($mon->monAn->hinh_anh)
                                            <img src="{{ asset('storage/' . $mon->monAn->hinh_anh) }}" class="mon-img me-2">
                                        @endif

                                        {{-- Thông tin món --}}
                                        <div class="flex-grow-1">
                                            <b>{{ $mon->monAn->ten_mon }}</b>

                                            @if ($mon->uu_tien)
                                                <span class="badge bg-danger badge-uu-tien">Ưu tiên</span>
                                            @endif

                                            <div class="small text-muted">
                                                SL: {{ $mon->so_luong }} — {{ $mon->created_at->diffForHumans() }}
                                            </div>
                                        </div>

                                        {{-- Form cập nhật --}}
                                        <form method="POST" action="{{ route('bep.update-status') }}" class="ms-2" style="width: 100px;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $mon->id }}">

                                            <select name="trang_thai" class="form-select form-select-sm mb-1">

                                                <option value="cho_bep"
                                                    {{ $from === 'cho_bep' ? 'selected' : '' }}
                                                    {{ in_array('cho_bep', $disable[$from]) ? 'disabled' : '' }}>
                                                    Chờ
                                                </option>

                                                <option value="dang_che_bien"
                                                    {{ $from === 'dang_che_bien' ? 'selected' : '' }}
                                                    {{ in_array('dang_che_bien', $disable[$from]) ? 'disabled' : '' }}>
                                                    Nấu
                                                </option>

                                                <option value="da_len_mon"
                                                    {{ $from === 'da_len_mon' ? 'selected' : '' }}
                                                    {{ in_array('da_len_mon', $disable[$from]) ? 'disabled' : '' }}>
                                                    Xong
                                                </option>

                                                <option value="huy_mon"
                                                    {{ $from === 'huy_mon' ? 'selected' : '' }}
                                                    {{ in_array('huy_mon', $disable[$from]) ? 'disabled' : '' }}>
                                                    Hủy
                                                </option>

                                            </select>

                                            <button class="btn btn-primary btn-sm w-100">OK</button>
                                        </form>

                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>

                @empty
                    <div class="col-12 text-center text-muted py-5">
                        Không có món nào cần chế biến
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</main>
@endsection

@section('script')
{{-- Không dùng DataTable nữa, nên xoá toàn bộ script cũ để tránh lỗi --}}
@endsection
