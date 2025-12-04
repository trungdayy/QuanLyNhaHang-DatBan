@extends('layouts.admins.layout-admin')

@section('title', 'Quản lý Đánh giá')

@section('content')
<main class="app-content">

    {{-- Thông báo Flash Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @elseif (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Breadcrumb & Clock --}}
    <div class="app-title">
        <ul class="app-breadcrumb breadcrumb side">
            <li class="breadcrumb-item active"><a href="{{ route('admin.danh-gia.index') }}"><b>Quản lý đánh giá khách hàng</b></a></li>
        </ul>
        <div id="clock"></div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="tile-title">Danh sách ý kiến & Đánh giá</h3>
                    {{-- Đánh giá thường không có nút "Thêm mới" từ admin, nhưng nếu cần có thể thêm ở đây --}}
                </div>

                <div class="tile-body">
                    <div class="rounded overflow-hidden">
                        <table class="table table-bordered table-hover align-middle text-center mb-0" id="danhGiaTable">
                            
                            {{-- Header bảng màu xanh đậm theo mẫu --}}
                            <thead style="background-color: #002b5b; color: white;">
                                <tr>
                                    <th width="50">#</th>
                                    <th width="250">Thông tin khách</th>
                                    <th>Nội dung đánh giá</th>
                                    <th width="150">Trạng thái</th>
                                    <th width="120">Ngày gửi</th>
                                    <th width="120">Thao tác</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @forelse($danhGias as $index => $item)
                                <tr>
                                    <td>{{ $danhGias->firstItem() + $index }}</td>
                                    
                                    {{-- Cột Thông tin khách --}}
                                    <td class="text-start">
                                        <div class="fw-bold text-primary">{{ $item->ten_khach }}</div>
                                        <div class="small text-muted"><i class="fas fa-phone-alt fa-fw me-1"></i>{{ $item->sdt }}</div>
                                        @if($item->email)
                                            <div class="small text-muted"><i class="fas fa-envelope fa-fw me-1"></i>{{ $item->email }}</div>
                                        @endif
                                        @if($item->nghe_nghiep)
                                            <div class="small text-muted fst-italic"><i class="fas fa-briefcase fa-fw me-1"></i>{{ $item->nghe_nghiep }}</div>
                                        @endif
                                    </td>

                                    {{-- Cột Nội dung & Sao --}}
                                    <td class="text-start">
                                        <div class="mb-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $item->so_sao)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-secondary"></i>
                                                @endif
                                            @endfor
                                            <span class="small fw-bold ms-1">({{ $item->so_sao }}/5)</span>
                                        </div>
                                        <div class="text-muted small text-justify">
                                            {{ $item->noi_dung }}
                                        </div>
                                    </td>

                                    {{-- Cột Trạng thái --}}
                                    <td>
                                        @if($item->trang_thai == 'cho_duyet')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Chờ duyệt</span>
                                        @elseif($item->trang_thai == 'hien_thi')
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Hiển thị</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-eye-slash me-1"></i> Đã ẩn</span>
                                        @endif
                                    </td>

                                    {{-- Cột Ngày gửi --}}
                                    <td>
                                        <span class="small">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '' }}</span><br>
                                        <span class="small text-muted">{{ $item->created_at ? $item->created_at->format('H:i') : '' }}</span>
                                    </td>

                                    {{-- Cột Thao tác --}}
                                    <td>
                                        {{-- Nút Duyệt / Ẩn --}}
                                        @if($item->trang_thai == 'hien_thi')
                                            <a href="{{ route('admin.danh-gia.status', ['id' => $item->id, 'status' => 'an']) }}" 
                                               class="btn btn-sm btn-secondary" title="Ẩn đánh giá">
                                                <i class="fas fa-eye-slash"></i>
                                            </a>
                                        @else
                                            <a href="{{ route('admin.danh-gia.status', ['id' => $item->id, 'status' => 'hien_thi']) }}" 
                                               class="btn btn-sm btn-success" title="Duyệt hiển thị">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        @endif

                                        {{-- Nút Xóa --}}
                                        <form action="{{ route('admin.danh-gia.destroy', $item->id) }}" method="POST" class="d-inline-block"
                                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá của khách {{ $item->ten_khach }}? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Xóa vĩnh viễn">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        Chưa có đánh giá nào từ khách hàng.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Phân trang --}}
                    <div class="d-flex justify-content-center mt-3">
                        {{ $danhGias->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    {{-- Nếu template của bạn cần script riêng cho table (ví dụ DataTables), thêm ở đây --}}
    <script>
        // Ví dụ: Hover effect hoặc JS xử lý phụ
    </script>
@endpush