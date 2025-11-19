@extends('layouts.admins.layout-admin')

@section('title', 'Quản lý Đặt Bàn')

@section('style')
<style>
    .cell-gio-den { min-width: 120px; }
    .cell-gio-den .gio { font-size: 1.1em; font-weight: bold; color: #000; }
    .cell-gio-den .ngay { font-size: 0.9em; color: #666; }

    /* --- Bộ lọc --- */
    .filter-card {
        background: #fdfdfd;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 16px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .filter-card label { font-weight: 600; font-size: 0.85rem; color: #333; margin-bottom: 4px; }
    .filter-card .form-control { font-size: 0.9rem; }
    .filter-actions {
        display: flex; 
        align-items: flex-end; 
        justify-content: flex-start; 
        gap: 8px;
        flex-wrap: wrap;
    }
    .filter-card .btn {
        border-radius: 5px;
        padding: 6px 12px;
    }
    .quick-filters a {
        margin-left: 6px;
        font-size: 0.85rem;
    }
    @media (max-width: 768px) {
        .filter-card .row > div { margin-bottom: 10px; }
    }
</style>
@endsection

@section('content')
<main class="app-content">
    <div class="app-title d-flex justify-content-between align-items-center flex-wrap">
        <ul class="app-breadcrumb breadcrumb side mb-2 mb-md-0">
            <li class="breadcrumb-item active">
                <a href="#"><b>Quản lý Đặt Bàn (Đơn hàng)</b></a>
            </li>
        </ul>
        <div id="clock"></div>
    </div>

    @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="tile">
        {{-- Nút thêm + Bộ lọc --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <a class="btn btn-add btn-sm" href="{{ route('admin.dat-ban.create') }}">
                <i class="fas fa-plus"></i> Tạo Đặt Bàn Mới
            </a>

            <button class="btn btn-outline-secondary btn-sm d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#filterBox">
                <i class="fas fa-filter"></i> Bộ lọc
            </button>
        </div>

        {{-- Bộ lọc --}}
        <div id="filterBox" class="collapse show filter-card mb-3">
            <form method="GET" action="{{ route('admin.dat-ban.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label>Tìm nhanh</label>
                        <input type="text" name="q" class="form-control" placeholder="Tên, SĐT, mã..." value="{{ request('q') }}">
                    </div>

                    <div class="col-md-2">
                        <label>Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="">-- Tất cả --</option>
                            <option value="cho_xac_nhan" {{ request('status')=='cho_xac_nhan' ? 'selected':'' }}>Chờ xác nhận</option>
                            <option value="da_xac_nhan" {{ request('status')=='da_xac_nhan' ? 'selected':'' }}>Đã xác nhận</option>
                            <option value="khach_da_den" {{ request('status')=='khach_da_den' ? 'selected':'' }}>Khách đã đến</option>
                            <option value="hoan_tat" {{ request('status')=='hoan_tat' ? 'selected':'' }}>Hoàn tất</option>
                            <option value="huy" {{ request('status')=='huy' ? 'selected':'' }}>Đã hủy</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Combo</label>
                        <select name="combo_id" class="form-control">
                            <option value="">-- Tất cả --</option>
                            @foreach($combosAll ?? [] as $c)
                                <option value="{{ $c->id }}" {{ request('combo_id') == $c->id ? 'selected':'' }}>{!! $c->ten_combo !!}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Bàn ăn</label>
                        <select name="ban_id" class="form-control">
                            <option value="">-- Tất cả --</option>
                            @foreach($banAnsAll ?? [] as $b)
                                <option value="{{ $b->id }}" {{ request('ban_id') == $b->id ? 'selected':'' }}>
                                    Bàn {{ $b->so_ban }} @if($b->khuVuc) ({{ $b->khuVuc->ten_khu_vuc }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex justify-content-between">
                            <div style="width:48%;">
                                <label>Từ ngày</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div style="width:48%;">
                                <label>Đến ngày</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Áp dụng</button>
                        <a href="{{ route('admin.dat-ban.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eraser"></i> Xóa lọc
                        </a>
                    </div>
                    <div class="quick-filters">
                        <a href="{{ route('admin.dat-ban.index', ['status' => 'khach_da_den']) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-user-check"></i> Khách đã đến
                        </a>
                        <a href="{{ route('admin.dat-ban.index', ['status' => 'cho_xac_nhan']) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-clock"></i> Chờ xác nhận
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Bảng danh sách --}}
        <div class="tile-body">
            <table class="table table-bordered table-hover align-middle text-center mb-3" id="monAnTable">
                <thead style="background-color: #002b5b; color: white;">
                    <tr>
                        <th>Mã</th>
                        <th>Khách hàng</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Combo</th>
                        <th>Bàn / Khu vực</th> <th>Giờ đến</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($danhSachDatBan as $datBan)
                        <tr>
                            <td>{{ $datBan->ma_dat_ban ?? $datBan->id }}</td>
                            <td>{{ $datBan->ten_khach }}</td>
                            <td>{{ $datBan->sdt_khach }}</td>
                            <td>{{ $datBan->email_khach }}</td>
                            <td>{!! $datBan->comboBuffet->ten_combo ?? 'N/A' !!}</td>
                            
                            <td>
                                <div class="fw-bold">{{ $datBan->banAn->so_ban ?? 'N/A' }}</div>
                                @if($datBan->banAn && $datBan->banAn->khuVuc)
                                    <div class="small text-muted" style="font-size: 0.85rem;">
                                        {{ $datBan->banAn->khuVuc->ten_khu_vuc }}
                                    </div>
                                @endif
                            </td>
                            <td class="cell-gio-den">
                                @if($datBan->gio_den)
                                    <div class="gio">{{ \Carbon\Carbon::parse($datBan->gio_den)->format('H:i') }}</div>
                                    <div class="ngay">{{ \Carbon\Carbon::parse($datBan->gio_den)->format('d/m/Y') }}</div>
                                @else N/A @endif
                            </td>
                            <td>
                                @php
                                    $statusColor = [
                                        'cho_xac_nhan' => 'info',
                                        'da_xac_nhan' => 'primary',
                                        'khach_da_den' => 'success',
                                        'hoan_tat' => 'secondary',
                                        'huy' => 'danger'
                                    ][$datBan->trang_thai] ?? 'light';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $datBan->trang_thai)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form style="display:inline;" method="POST" action="{{ route('admin.dat-ban.destroy', $datBan->id) }}" onsubmit="return confirm('Xóa đơn này?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                                </form>
                                <a href="{{ route('admin.dat-ban.edit', $datBan->id) }}" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('admin.dat-ban.show', $datBan->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">Không có đơn đặt bàn nào.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {{ $danhSachDatBan->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</main>
@endsection