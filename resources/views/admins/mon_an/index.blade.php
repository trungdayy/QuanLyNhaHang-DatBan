@extends('layouts.admins.layout-admin')

@section('title', 'Danh sách món ăn')

@section('content')
    <main class="app-content">

        @if (session('success'))
            <div class="alert alert-success text-center fw-semibold rounded-3 shadow-sm" id="flashMsg">
                {{ session('success') }}
            </div>
        @endif

        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb side">
                <li class="breadcrumb-item active"><a href="{{ route('admin.san-pham.index') }}"><b>Danh sách món ăn</b></a>
                </li>
            </ul>
            <div id="clock"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-title-w-btn">
                        <h3 class="tile-title">Danh sách món ăn</h3>
                        <a href="{{ route('admin.san-pham.create') }}" class="btn btn-add btn-sm">
                            <i class="fas fa-plus me-1"></i> Thêm món ăn
                        </a>
                    </div>

                    <div class="tile-body">

                        <!-- KHU VỰC LỌC VÀ TÌM KIẾM  -->
                        <div class="mb-4 p-3 border rounded shadow-sm bg-light">
                            <form action="{{ route('admin.san-pham.index') }}" method="GET"
                                class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="search_keyword" class="form-label fw-semibold mb-1">Tìm kiếm</label>
                                    <input type="text" name="keyword" id="search_keyword" class="form-control"
                                        placeholder="Nhập tên món, mô tả..." value="{{ request('keyword') }}">
                                </div>

                                <div class="col-md-3">
                                    <label for="filter_danh_muc" class="form-label fw-semibold mb-1">Danh mục</label>
                                    <select name="danh_muc_id" id="filter_danh_muc" class="form-control">
                                        <option value="">— Tất cả Danh mục —</option>
                                        @foreach ($danhMucs as $dm)
                                            <option value="{{ $dm->id }}"
                                                {{ request('danh_muc_id') == $dm->id ? 'selected' : '' }}>
                                                {{ $dm->ten_danh_muc }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- ĐÃ THÊM: Lọc theo Loại Món -->
                                <div class="col-md-3">
                                    <label for="filter_loai_mon" class="form-label fw-semibold mb-1">Loại món</label>
                                    <select name="loai_mon" id="filter_loai_mon" class="form-control">
                                        <option value="">— Tất cả Loại món —</option>
                                        @php
                                            // Danh sách này phải khớp 100% với cột ENUM trong MySQL
                                            $loaiMonDB = [
                                                'Sống',
                                                'Chín',
                                                'Nướng',
                                                'Xào / Luộc',
                                                'Bánh ngọt',
                                                'Trái cây',
                                                'Nước có ga',
                                                'Nước không ga',
                                                'Trà / Cà phê',
                                            ];
                                        @endphp
                                        @foreach ($loaiMonDB as $loai)
                                            <option value="{{ $loai }}"
                                                {{ request('loai_mon') == $loai ? 'selected' : '' }}>
                                                {{ $loai }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label for="filter_trang_thai" class="form-label fw-semibold mb-1">Trạng thái</label>
                                    <select name="trang_thai" id="filter_trang_thai" class="form-control">
                                        <option value="">— Tất cả Trạng thái —</option>
                                        @foreach (['con' => 'Còn món', 'het' => 'Hết món', 'an' => 'Ẩn'] as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ request('trang_thai') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-1 d-flex align-self-end">
                                    <button type="submit" class="btn btn-primary w-100" title="Tìm kiếm và Lọc">
                                        <i class="fas fa-search me-1"></i> Lọc
                                    </button>
                                </div>
                            </form>
                        </div>
                        <!-- KẾT THÚC KHU VỰC LỌC -->

                        <div class="rounded overflow-hidden">
                            <table class="table table-bordered table-hover align-middle text-center mb-0" id="monAnTable">
                                <thead style="background-color: #002b5b; color: white;">
                                    <tr>
                                        <th>#</th>
                                        <th class="text-start">Tên món</th>
                                        <th>Danh mục</th>
                                        <th>Loại món</th>
                                        <th>Giá</th>
                                        <th>Ảnh</th>
                                        <th>Thời gian</th>
                                        <th>Trạng thái</th>
                                        <th style="width: 120px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dsMonAn as $index => $monAn)
                                        <tr>
                                            <td>{{ $dsMonAn->firstItem() + $index }}</td>
                                            <td class="text-start">
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $monAn->ten_mon }}</span>
                                                    @if ($monAn->mo_ta)
                                                        <small
                                                            class="text-muted fst-italic">{{ Str::limit($monAn->mo_ta, 60) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $monAn->danh_muc_badge }}">
                                                    {{ $monAn->danhMuc->ten_danh_muc ?? '-' }}
                                                </span>
                                            </td>
                                            <td>{{ $monAn->loai_mon ?? '—' }}</td>
                                            <td class="text-danger fw-semibold">
                                                {{ number_format($monAn->gia, 0, ',', '.') }} đ
                                            </td>
                                            <td>
                                                @if ($monAn->hinh_anh)
                                                    <img src="{{ asset($monAn->hinh_anh) }}" width="60" height="60"
                                                        class="rounded border shadow-sm object-fit-cover" alt="Ảnh món"
                                                        onerror="this.src='https://placehold.co/60x60/eee/ccc?text=No+Img'">
                                                @else
                                                    <span class="text-muted fst-italic">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $monAn->thoi_gian_che_bien }} phút</td>
                                            <td>
                                                <span class="badge {{ $monAn->trang_thai_badge }}">
                                                    {{ $monAn->trang_thai_display }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.san-pham.show', $monAn->id) }}"
                                                        class="btn btn-sm btn-info" title="Xem">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.san-pham.edit', $monAn->id) }}"
                                                        class="btn btn-sm btn-warning" title="Sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.san-pham.destroy', $monAn->id) }}"
                                                        method="POST" onsubmit="return confirm('Xác nhận xóa món ăn này?')"
                                                        class="d-inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger" title="Xóa">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">Chưa có món ăn nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $dsMonAn->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Script tự động ẩn thông báo
            setTimeout(() => {
                const msg = document.getElementById('flashMsg');
                if (msg) $(msg).fadeOut(500, () => msg.remove());
            }, 3000);
        });
    </script>
@endpush
