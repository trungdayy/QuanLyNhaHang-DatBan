@extends('layouts.admins.layout-admin')

@section('title', 'Danh sách QR Bàn ăn')

@section('content')

<main class="app-content">
    {{-- Header --}}
    <div class="app-title">
        <ul class="app-breadcrumb breadcrumb side">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item active"><a href="#"><b>Danh sách QR Bàn ăn</b></a></li>
        </ul>
        <div id="clock"></div>
    </div>

    {{-- Thông báo thành công/lỗi --}}
    @if(session('success'))
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> {{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="tile-title"><i class="fas fa-qrcode me-2"></i> Quản lý Mã QR</h3>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> In danh sách QR
                    </button>
                </div>

                <div class="tile-body">
                    
                    {{-- Kiểm tra nếu có dữ liệu --}}
                    @if($banAns->isEmpty())
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle me-2"></i> Chưa có bàn ăn nào được tạo.
                        </div>
                    @else
                        
                        {{-- Nhóm bàn theo Khu Vực --}}
                        @php
                            // Nhóm các bàn theo ID khu vực (hoặc tên khu vực nếu muốn)
                            $banTheoKhuVuc = $banAns->groupBy(function($item) {
                                return $item->khuVuc ? $item->khuVuc->ten_khu_vuc : 'Khu vực khác';
                            });
                        @endphp

                        @foreach($banTheoKhuVuc as $tenKhuVuc => $danhSachBan)
                            
                            {{-- Tiêu đề Khu vực --}}
                            <div class="mb-4 print-break-avoid">
                                <h5 class="text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i> {{ $tenKhuVuc }}
                                    <span class="badge bg-secondary ms-2" style="font-size: 0.7em">{{ $danhSachBan->count() }} bàn</span>
                                </h5>

                                {{-- Grid hiển thị bàn trong khu vực này --}}
                                <div class="row g-3">
                                    @foreach ($danhSachBan as $banAn)
                                        @php
                                            $finalUrl = url('/oderqr/menu/' . $banAn->ma_qr);
                                        @endphp

                                        {{-- Responsive Column --}}
                                        <div class="col-6 col-md-4 col-lg-3 col-xl-2 print-item">
                                            <div class="card qr-card h-100 position-relative">
                                                <span class="badge position-absolute top-0 end-0 m-2 bg-success rounded-pill">Active</span>

                                                <div class="card-body text-center p-3 d-flex flex-column align-items-center">
                                                    {{-- Tên bàn --}}
                                                    <h5 class="card-title text-primary fw-bold mb-1">
                                                         {{ $banAn->so_ban }}
                                                    </h5>
                                                    <small class="text-muted mb-3">ID: #{{ $banAn->id }}</small>

                                                    {{-- Ảnh QR --}}
                                                    <div class="qr-frame mb-3">
                                                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(140)->color(0, 43, 91)->generate($finalUrl) !!}
                                                    </div>

                                                    {{-- Các nút hành động --}}
                                                    <div class="action-buttons d-flex gap-2 w-100 justify-content-center mt-auto no-print">
                                                        
                                                        {{-- 1. Nút Mở Link --}}
                                                        <a href="{{ $finalUrl }}" target="_blank" class="btn btn-sm btn-outline-info" title="Truy cập link">
                                                            <i class="fas fa-external-link-alt"></i>
                                                        </a>
                                                        
                                                        {{-- 2. Nút Copy Link --}}
                                                        <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('{{ $finalUrl }}')" title="Sao chép link">
                                                            <i class="fas fa-copy"></i>
                                                        </button>

                                                        {{-- 3. Nút Reset QR --}}
                                                        <form style="display:inline;" method="POST" action="{{ route('admin.ban-an.qr', $banAn->id) }}" 
                                                              onsubmit="return confirm('CẢNH BÁO: Bạn có chắc muốn tạo lại mã QR cho Bàn {{ $banAn->so_ban }}? Mã QR cũ đã in ra sẽ KHÔNG còn sử dụng được nữa.');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Tạo lại mã QR mới">
                                                                <i class="fas fa-sync-alt"></i>
                                                            </button>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                    @endif

                </div>
            </div>
        </div>
    </div>
</main>

{{-- CSS Tùy chỉnh --}}
<style>
    .qr-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        background: #fff;
        overflow: hidden;
    }
    .qr-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 28px rgba(0,0,0,0.1), 0 10px 10px rgba(0,0,0,0.05);
        border-color: #009688;
    }
    .qr-frame {
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px dashed #ced4da;
        display: inline-block;
    }
    .qr-frame svg {
        width: 100% !important;
        height: auto !important;
        max-width: 140px;
    }
    .card-title {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        letter-spacing: 0.5px;
    }
    
    /* CSS cho chế độ in ấn */
    @media print {
        body * { visibility: hidden; }
        .tile-body, .tile-body * { visibility: visible; }
        .tile-body { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print, .app-title, .tile-title-w-btn { display: none !important; }
        
        /* Ngắt trang thông minh: Không xé lẻ tiêu đề khu vực và hàng đầu tiên */
        .print-break-avoid { break-inside: avoid; margin-bottom: 20px; page-break-inside: avoid; }
        
        .col-xl-2 { width: 25% !important; float: left; }
        .qr-card { box-shadow: none !important; border: 1px solid #000 !important; margin-bottom: 20px; }
    }
</style>

{{-- Script Copy Link --}}
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Đã sao chép link: ' + text); 
        }, function(err) {
            console.error('Lỗi khi sao chép: ', err);
        });
    }
</script>

@endsection