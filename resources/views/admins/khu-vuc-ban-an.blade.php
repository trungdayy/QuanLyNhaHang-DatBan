@extends('layouts.admins.layout-admin')

@section('title', 'Quản lý Khu Vực & Bàn Ăn')

@section('content')
    <main class="app-content">

        {{-- PHẦN HIỂN THỊ THÔNG BÁO (GIỮ NGUYÊN) --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (isset($errorMessage) && $errorMessage)
            <div class="alert alert-danger" role="alert">
                <strong>LỖI TRUY VẤN DB:</strong> {{ $errorMessage }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                Vui lòng kiểm tra lại dữ liệu nhập.
            </div>
        @endif

        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb side">
                <li class="breadcrumb-item active"><a href="#"><b>Quản lý Khu Vực & Bàn Ăn</b></a></li>
            </ul>
            <div id="clock"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">

                        {{-- PHẦN NÚT CHỨC NĂNG (GIỮ NGUYÊN) --}}
                        <div class="row element-button">
                            <div class="col-sm-2">
                                <a class="btn btn-add btn-sm" href="{{ route('admin.khu-vuc.create') }}"
                                    title="Thêm Khu Vực">
                                    <i class="fas fa-building"></i> Tạo mới Khu vực
                                </a>
                            </div>
                            <div class="col-sm-2">
                                <a class="btn btn-add btn-sm" href="{{ route('admin.ban-an.create') }}" title="Thêm Bàn Ăn">
                                    <i class="fas fa-chair"></i> Tạo mới Bàn ăn
                                </a>
                            </div>
                        </div>

                        <div class="container-fluid mt-4">
                            <h4 class="mb-3 text-primary">Danh Sách Bàn Ăn Theo Khu Vực</h4>

                            {{-- --- CHỖ NÀY ĐÃ ĐƯỢC SỬA --- --}}
                            {{-- Thay vì viết code dài dòng ở đây, ta gọi file partial vào --}}
                            <div id="danh-sach-khu-vuc" class="row">
                                @include('admins.ban-an.partials.list_ban_an')
                            </div>
                            {{-- --------------------------- --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

{{-- --- THÊM SCRIPT AJAX VÀO ĐÂY --- --}}
@section('script')
    <script>
        $(document).ready(function() {
            // Hàm này sẽ chạy mỗi 3 giây
            setInterval(function() {
                $.ajax({
                    url: "{{ route('admin.ban-an.ajax') }}",
                    type: "GET",
                    success: function(data) {
                        // Cập nhật lại nội dung bên trong thẻ div #danh-sach-khu-vuc
                        $('#danh-sach-khu-vuc').html(data);
                    },
                    error: function(err) {
                        console.error('Lỗi tự động cập nhật:', err);
                    }
                });
            }, 3000); // 3000ms = 3 giây
        });
    </script>
@endsection
