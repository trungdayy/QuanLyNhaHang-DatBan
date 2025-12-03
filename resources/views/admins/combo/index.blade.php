@extends('layouts.admins.layout-admin')

@section('title', 'Combo Buffet')

@section('style')
    <style>
        .combo-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        th {
            vertical-align: middle !important;
        }
    </style>
@endsection

@section('content')
    <main class="app-content">
        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb side">
                <li class="breadcrumb-item active">
                    <a href="{{ route('admin.combo-buffet.index') }}"><b>Combo Buffet</b></a>
                </li>
            </ul>
            <div id="clock"></div>
        </div>

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-title-w-btn d-flex justify-content-between align-items-center">
                        <h3 class="tile-title mb-0">Danh sách Combo Buffet</h3>
                        <a class="btn btn-add btn-sm" href="{{ route('admin.combo-buffet.create') }}" title="Thêm">
                            <i class="fas fa-plus"></i> Thêm combo mới
                        </a>
                    </div>

                    <div class="tile-body">
                        <div class="rounded overflow-hidden">
                            <table class="table table-hover table-bordered align-middle text-center mb-0" id="sampleTable">
                                <thead style="background-color: #002b5b; color: white;">
                                    <tr>
                                        <th>ID</th>
                                        <th>Hình ảnh</th>
                                        <th class="text-start">Tên Combo</th>
                                        <th class="text-start">Mô tả</th> {{-- thêm cột mô tả --}}
                                        <th>Loại</th>
                                        <th>Giá cơ bản</th>
                                        <th>Thời lượng (phút)</th>
                                        <th>Bắt đầu</th>
                                        <th>Kết thúc</th>
                                        <th>Trạng thái</th>
                                        <th style="width: 100px;">Hành động</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($combos as $combo)
                                        <tr>
                                            <td>{{ $combo->id }}</td>

                                            {{-- 🖼️ Hiển thị hình ảnh combo buffet --}}
                                            <td>
                                                @php
                                                    $imagePath = asset('images/no-image.png');
                                                    if ($combo->anh) {
                                                        $fullPath = public_path('uploads/' . $combo->anh);
                                                        if (file_exists($fullPath)) {
                                                            $imagePath = asset('uploads/' . $combo->anh);
                                                        }
                                                    }
                                                @endphp
                                                <img src="{{ $imagePath }}" alt="Ảnh combo buffet" class="combo-img">
                                            </td>

                                            <td class="text-start">{{ $combo->ten_combo }}</td>
                                            <td class="text-start">
                                                {{ Str::limit($combo->mo_ta, 50) }} {{-- hiển thị mô tả, rút gọn 50 ký tự --}}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge 
                                                @if ($combo->loai_combo == 'nguoi_lon') bg-primary 
                                                @elseif($combo->loai_combo == 'tre_em') bg-info 
                                                @elseif($combo->loai_combo == 'vip') bg-warning 
                                                @elseif($combo->loai_combo == 'khuyen_mai') bg-success 
                                                @else bg-secondary @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $combo->loai_combo)) }}
                                                </span>
                                            </td>
                                            <td class="text-end">{{ number_format($combo->gia_co_ban, 0, ',', '.') }} đ</td>
                                            <td>{{ $combo->thoi_luong_phut }}</td>
                                            <td>{{ $combo->thoi_gian_bat_dau }}</td>
                                            <td>{{ $combo->thoi_gian_ket_thuc }}</td>
                                            <td>
                                                <span
                                                    class="badge 
                                                @if ($combo->trang_thai == 'dang_ban') bg-success 
                                                @else bg-danger @endif">
                                                    {{ $combo->trang_thai == 'dang_ban' ? 'Đang bán' : 'Ngừng bán' }}
                                                </span>
                                            </td>

                                            <td>
                                                <a href="{{ route('admin.combo-buffet.edit', $combo->id) }}"
                                                    class="btn btn-warning btn-sm" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                <form action="{{ route('admin.combo-buffet.destroy', $combo->id) }}"
                                                    method="POST" class="d-inline-block"
                                                    onsubmit="return confirm('Xác nhận xóa combo này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">Chưa có combo buffet nào.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
    <script>
        $('#sampleTable').dataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json"
            },
            order: [
                [0, "asc"]
            ],
            columnDefs: [{
                orderable: false,
                targets: [1, 9]
            }]
        });
    </script>
@endsection