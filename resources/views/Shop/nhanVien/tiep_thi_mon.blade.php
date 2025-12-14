@extends('layouts.shop.layout-nhanvien')

{{-- SỬA CÚ PHÁP: Sử dụng phép nối chuỗi PHP --}}
@section('title', 'Hàng chờ - ' . Auth::user()->ho_ten)

@section('content')
{{-- NHÚNG CÁC STYLE TÙY CHỈNH TỪ TRANG QUẢN LÝ BÀN --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
    /* CSS Variables (Lấy từ trang quản lý bàn) */
    :root { 
        --primary: #fea116; 
        --primary-hover: #db8a10; 
        --dark: #0f172b; 
        --light: #F1F8FF; 
        --text-main: #1e293b; 
        --white: #ffffff; 
        --radius: 4px; 
        --shadow-card: 0 0 45px rgba(0, 0, 0, 0.08); 
    }
    
    /* Header Style */
    .page-header-title { 
        font-family: 'Heebo', sans-serif; 
        font-weight: 800; 
        color: var(--dark); 
        text-transform: uppercase; 
        position: relative; 
        padding-left: 15px; 
        font-size: 1.5rem; 
    }
    .page-header-title::before { 
        content: ''; 
        position: absolute; 
        left: 0; 
        top: 50%; 
        transform: translateY(-50%); 
        height: 80%; 
        width: 5px; 
        background-color: var(--primary); 
        border-radius: 2px; 
    }

    /* Card Zone Style */
    .card-zone { 
        border: none; 
        border-radius: var(--radius); 
        box-shadow: var(--shadow-card); 
        background: var(--white); 
        margin-bottom: 24px; 
        overflow: hidden; 
    }
    .card-zone-header { 
        background: var(--dark); 
        color: var(--primary); 
        padding: 15px 20px; 
        font-family: 'Heebo', sans-serif; 
        font-weight: 700; 
        text-transform: uppercase; 
        border-bottom: 3px solid var(--primary); 
    }
    
    /* List Item Style (Món ăn chờ bưng) */
    .food-item-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        /* TĂNG PADDING CHO KHOẢNG CÁCH RỘNG HƠN */
        padding: 20px; 
        transition: all 0.2s ease-in-out;
        margin-bottom: 12px; /* Tăng khoảng cách giữa các món */
        box-shadow: 0 4px 8px rgba(0,0,0,0.05); /* Shadow rõ hơn */
    }
    .food-item-card:hover {
        border-color: #10b981; /* Xanh lá */
        background: #f0fff0;
    }
    
    /* TĂNG KÍCH THƯỚC CHỮ CHO TÊN MÓN */
    .food-item-card strong {
        font-size: 1.15rem; 
    }

    /* TĂNG KÍCH THƯỚC BADGE SUẤT */
    .badge-suat {
        background: #ef4444; /* Màu đỏ cho số suất */
        color: #fff;
        font-weight: 700;
        padding: 6px 10px; /* Tăng padding */
        border-radius: 4px;
        font-size: 0.95rem; /* Tăng cỡ chữ */
    }
    .btn-ocean-served {
        background: #10b981;
        color: #fff;
        border: none;
        font-weight: 700;
        text-transform: uppercase;
        padding: 10px 20px; /* Tăng padding nút */
        border-radius: 4px;
        transition: 0.3s;
    }
    .btn-ocean-served:hover {
        background: #059669;
        color: #fff;
    }
    
    /* Loading animation for refresh button */
    .fa-spin-slow {
        animation: fa-spin 3s linear infinite;
    }
    
    /* ======================================= */
    /* === BỔ SUNG: TỐI ƯU GIAO DIỆN MOBILE === */
    /* ======================================= */
    
    @media (max-width: 767px) { /* Áp dụng cho màn hình <= 767px (sm/xs) */
        
        /* Thay đổi container chính để tăng không gian (Tối ưu hóa padding chung) */
        .container-fluid.py-4.px-4 {
            padding: 15px !important; 
        }
        
        .card-body {
            padding: 15px !important; 
        }
        
        /* Chuyển item sang chế độ xếp chồng và căn trái */
        .food-item-card {
            flex-direction: column; 
            align-items: flex-start !important;
            padding: 15px; 
        }
        
        /* TĂNG KÍCH THƯỚC CHỮ CHO TÊN MÓN TRÊN MOBILE */
        .food-item-card strong {
            font-size: 1.25rem; 
        }

        /* Đảm bảo nội dung chính chiếm toàn bộ chiều rộng */
        .food-item-card > div:first-child {
            width: 100%;
        }

        /* Đẩy nút "Đã lên món" xuống dòng mới và căn trái */
        .food-item-card > div:last-child {
            width: 100%; 
            margin-top: 15px; 
        }

        /* Chuyển nút "Đã lên món" thành khối rộng */
        .btn-ocean-served {
            width: 100%;
            padding: 12px 15px; 
        }
        
        /* Điều chỉnh hiển thị header trên mobile */
        .page-header-title {
            font-size: 1.4rem; 
            padding-left: 10px;
        }
        
        /* Xử lý khối trạng thái tải */
        #status-indicator {
            font-size: 0.75rem; 
            padding: 6px 10px;
        }
    }
</style>

<div class="container-fluid py-4 px-4">
    
    {{-- HEADER TRANG --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-header-title m-0">Hàng chờ phục vụ</h4>
            {{-- Ẩn/Hiện mô tả theo kích thước màn hình --}}
            <p class="text-muted small ms-3 mb-0 mt-1 d-none d-sm-block">Các món ăn đã được bếp hoàn thành</p>
            <p class="text-muted small ms-3 mb-0 mt-1 d-block d-sm-none">Món ăn đã hoàn thành</p>
        </div>
        <div class="d-flex gap-2">
            
            <span id="status-indicator" class="btn btn-white shadow-sm border fw-bold text-dark disabled" style="cursor: default;">
                <i class="fa-solid fa-rotate text-primary fa-spin-slow me-2"></i> Đang tải dữ liệu...
            </span>
            
        </div>
    </div>

    <div class="card-zone">
        <div class="card-zone-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-utensils me-2"></i> Danh sách món ăn chờ bưng (Bàn của bạn)</span>
        </div>
        
        <div class="card-body p-4 bg-light-subtle">
            <div id="food-queue-container">
                
                {{-- KHU VỰC CẦN TẠO FILE: resources/views/Shop/nhanVien/partials/food_queue_list.blade.php --}}
                @include('Shop.nhanVien.partials.food_queue_list', ['monChoPhucVu' => $monChoPhucVu])
                
            </div>
            
            <div id="loading-indicator" class="text-center py-4" style="display: none;">
                <i class="fas fa-spinner fa-spin text-primary fa-2x"></i>
                <p class="text-muted small mt-2">Đang tải dữ liệu mới...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Lấy ID nhân viên đang đăng nhập (đã fix lỗi)
    const nhanVienId = {{ Auth::id() }}; 
    const queueContainer = $('#food-queue-container');
    const loadingIndicator = $('#loading-indicator');
    const statusIndicator = $('#status-indicator'); // Khai báo hằng số status indicator
    
    // --- Hàm render HTML cho từng món ăn (Dùng cho AJAX) ---
    function renderFoodItem(mon) {
        // Cần format thời gian (Giả định Carbon/Moment không dùng được trong JS pure, nên dùng JS Date)
        const updateTime = mon.updated_at ? new Date(mon.updated_at).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) : 'Vừa xong';
        const banSo = mon.order_mon && mon.order_mon.ban_an ? mon.order_mon.ban_an.so_ban : 'N/A';
        
        return `
            <div class="food-item-card d-flex justify-content-between align-items-center" id="item-${mon.id}">
                <div>
                    <p class="mb-1">
                        <strong>${mon.mon_an.ten_mon}</strong>
                        <span class="badge-suat ms-2">${mon.so_luong} suất</span>
                    </p>
                    <small class="text-muted">
                        <i class="fas fa-table"></i> Bàn: <strong>${banSo}</strong>
                        (Hoàn thành: ${updateTime})
                    </small>
                </div>
                <div>
                    <button class="btn btn-ocean-served btn-sm" onclick="xacNhanDaBung(${mon.id})">
                        <i class="fas fa-check"></i> Đã lên món
                    </button>
                </div>
            </div>
        `;
    }

    // --- 1. HÀM XỬ LÝ AJAX LẤY DỮ LIỆU MỚI (Tự động tải lại) ---
    function fetchFoodQueue() {
        // 1. Bắt đầu Loading UI
        statusIndicator.html('<i class="fa-solid fa-rotate text-primary fa-spin-slow me-2"></i> Đang tải dữ liệu...');

        axios.get("{{ route('nhanVien.phuc-vu.dashboard_api') }}")
            .then(response => {
                const newItems = response.data.monChoPhucVu;
                let newHtml = '';
                
                if (newItems && newItems.length > 0) {
                    newItems.forEach(mon => {
                        newHtml += renderFoodItem(mon);
                    });
                } else {
                    newHtml = `
                        <div class="text-center text-secondary py-4" id="empty-queue-message">
                            ✅ Không có món nào chờ phục vụ lúc này.
                        </div>
                    `;
                }

                // Cập nhật DOM
                queueContainer.html(newHtml);
                
                // 2. Cập nhật UI sau khi tải thành công
                const now = new Date().toLocaleTimeString('vi-VN');
                statusIndicator.html(`<i class="fa-solid fa-check text-success me-2"></i> Cập nhật lúc ${now}`);
            })
            .catch(error => {
                console.error("Lỗi khi tải hàng chờ:", error);
                // 2. Cập nhật UI khi gặp lỗi
                statusIndicator.html('<i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Lỗi tải');
            })
            .finally(() => {
                loadingIndicator.hide();
                queueContainer.css('opacity', 1);
            });
    }

    // --- 2. HÀM XỬ LÝ CHECK-IN MÓN (Đã sửa để gọi refresh) ---
    function xacNhanDaBung(chiTietOrderId) {
        const url = "{{ route('nhanVien.phuc-vu.confirm_served', ['id' => 'TEMP_ID']) }}".replace('TEMP_ID', chiTietOrderId);
        
        const itemEl = document.getElementById('item-' + chiTietOrderId);
        const originalHtml = itemEl ? itemEl.innerHTML : '';
        if(itemEl) {
            itemEl.innerHTML = '<div class="w-100 text-center text-primary"><i class="fas fa-spinner fa-spin"></i> Đang xác nhận...</div>';
        }

        axios.post(url) 
            .then(response => {
                // Sau khi xác nhận thành công, tải lại danh sách để cập nhật trạng thái Order cha
                fetchFoodQueue(); 
            })
            .catch(error => {
                let errorMessage = 'Lỗi không xác định.';
                if (error.response && error.response.data && error.response.data.message) {
                    errorMessage = `Lỗi Server (${error.response.status}): ${error.response.data.message}`;
                } else {
                    errorMessage = 'Lỗi kết nối mạng hoặc hệ thống.';
                }

                console.error('Lỗi khi xác nhận bưng:', error);
                alert(`Không thể gửi yêu cầu!\nChi tiết: ${errorMessage}`);
                
                if(itemEl) {
                   itemEl.innerHTML = originalHtml; // Khôi phục HTML
                }
            });
    }
    
    // --- 3. TỰ ĐỘNG TẢI LẠI DỮ LIỆU MỖI 3 GIÂY ---
    document.addEventListener("DOMContentLoaded", function () {
        // Kích hoạt lần đầu để hiển thị trạng thái Loading
        fetchFoodQueue(); 
        // Tự động tải lại mỗi 5 giây (5000ms)
        setInterval(fetchFoodQueue, 5000); 
    });
</script>
@endpush
@endsection