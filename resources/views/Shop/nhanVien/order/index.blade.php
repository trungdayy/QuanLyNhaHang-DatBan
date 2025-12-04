@extends('layouts.shop.layout-nhanvien')

@section('title', 'Danh sách bàn - Buffet Ocean')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #fea116;
            --dark: #0f172b;
            --light: #f0f2f5; /* Nền xám nhẹ để nổi Card */
            --white: #ffffff;
            --gray: #64748b;
            --success: #10b981;
            --danger: #ef4444;
        }

        body { background-color: var(--light); font-family: 'Nunito', sans-serif; color: #333; }
        h1, h2, h3, h4, h5 { font-family: 'Heebo', sans-serif; }

        /* --- 1. DASHBOARD STATS (LÀM NỔI BẬT) --- */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            border: none;
            transition: transform 0.3s;
            height: 100%;
        }
        .stat-card:hover { transform: translateY(-5px); }
        
        .stat-content { position: relative; z-index: 2; }
        .stat-num { font-size: 2.2rem; font-weight: 800; font-family: 'Heebo', sans-serif; line-height: 1; margin-bottom: 5px; }
        .stat-desc { text-transform: uppercase; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.8; }
        
        /* Icon mờ trang trí góc phải */
        .stat-icon-bg {
            position: absolute; right: -10px; bottom: -10px;
            font-size: 4rem; opacity: 0.1; z-index: 1; transform: rotate(-15deg);
        }

        /* Màu sắc riêng cho từng ô Dashboard */
        .st-total { border-bottom: 4px solid var(--dark); color: var(--dark); }
        .st-empty { border-bottom: 4px solid var(--gray); color: var(--gray); }
        .st-serving { border-bottom: 4px solid var(--primary); color: var(--primary); }
        .st-waiting { border-bottom: 4px solid var(--success); color: var(--success); }

        /* --- 2. TABLE CARD (THAY ĐỔI LỚN) --- */
        .ocean-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); /* Bóng đổ sâu hơn */
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: none;
            height: 100%;
            display: flex; flex-direction: column;
        }
        .ocean-card:hover { transform: translateY(-7px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); }

        /* Header Card: Giờ sẽ có MÀU NỀN FULL */
        .oc-header {
            padding: 12px 15px;
            display: flex; justify-content: space-between; align-items: center;
            color: #fff; /* Chữ trắng mặc định */
            font-family: 'Heebo', sans-serif;
        }
        .ban-title { font-weight: 800; font-size: 1.2rem; text-transform: uppercase; }
        .ban-badge { background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; backdrop-filter: blur(5px); }

        .oc-body {
            padding: 20px;
            flex-grow: 1;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-align: center;
            background: #fff;
        }

        /* --- COLOR THEMES CHO CARD --- */
        /* TRỐNG: Màu xám/trắng thanh lịch */
        .theme-trong .oc-header { background: #e2e8f0; color: #64748b; } /* Header xám */
        .theme-trong .ban-badge { background: #cbd5e1; color: #475569; }
        .theme-trong .icon-main { color: #e2e8f0; }

        /* ĐANG PHỤC VỤ: Màu Cam Brand rực rỡ */
        .theme-serving .oc-header { background: linear-gradient(135deg, #fea116, #d97706); color: #fff; }
        .theme-serving .icon-main { color: #fea116; }

        /* ĐÃ ĐẶT: Màu Xanh đen sang trọng */
        .theme-booked .oc-header { background: linear-gradient(135deg, #0f172b, #334155); color: #fff; }
        .theme-booked .icon-main { color: #0f172b; }

        /* KHÁCH MỚI: Màu Xanh lá + Animation */
        .theme-new { border: 2px solid #10b981; }
        .theme-new .oc-header { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
        .theme-new .icon-main { color: #10b981; }
        /* Hiệu ứng nhấp nháy cho khách mới */
        .pulse-border { animation: pulse 2s infinite; }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* --- CONTENT ELEMENTS --- */
        .customer-name { font-weight: 700; color: var(--dark); font-size: 1rem; margin-bottom: 2px; }
        .order-info { color: var(--gray); font-size: 0.85rem; margin-bottom: 10px; }
        .price-display { font-size: 1.1rem; font-weight: 800; color: var(--primary); }

        /* --- BUTTONS ACTION --- */
        .btn-fab {
            width: 50px; height: 50px; border-radius: 50%; border: none;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; color: #fff; cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: 0.2s;
            margin-top: 10px;
        }
        .btn-fab:hover { transform: scale(1.1) rotate(90deg); }
        
        .fab-add { background: linear-gradient(135deg, #10b981, #059669); }
        .fab-view { background: linear-gradient(135deg, #fea116, #d97706); }

        /* Zone Title */
        .zone-label {
            display: inline-block;
            background: var(--dark); color: #fff;
            padding: 8px 16px; border-radius: 6px;
            font-weight: 700; text-transform: uppercase; margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>

    <div class="container-fluid px-4 py-4">
        
        {{-- Flash Messages --}}
        @if(session('success')) <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4"><i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4"><i class="fa-solid fa-bug me-2"></i> {{ session('error') }}</div> @endif

        {{-- 1. DASHBOARD NỔI BẬT --}}
        <div class="row g-4 mb-5">
            <div class="col-6 col-md-3">
                <div class="stat-card st-total">
                    <div class="stat-content">
                        <div class="stat-num">{{ $bans->count() }}</div>
                        <div class="stat-desc">Tổng số bàn</div>
                    </div>
                    <i class="fa-solid fa-layer-group stat-icon-bg"></i>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card st-empty">
                    <div class="stat-content">
                        <div class="stat-num">{{ $bans->where('trang_thai', 'trong')->count() }}</div>
                        <div class="stat-desc">Bàn trống</div>
                    </div>
                    <i class="fa-solid fa-chair stat-icon-bg"></i>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card st-serving">
                    <div class="stat-content">
                        <div class="stat-num">{{ $bans->where('trang_thai', 'dang_phuc_vu')->count() }}</div>
                        <div class="stat-desc">Đang phục vụ</div>
                    </div>
                    <i class="fa-solid fa-utensils stat-icon-bg"></i>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card st-waiting">
                    <div class="stat-content">
                        <div class="stat-num">
                             {{ $bans->filter(function($ban) use ($orders) {
                                 $db = \App\Models\DatBan::where('ban_id', $ban->id)->latest()->first();
                                 return $db && $db->trang_thai == 'khach_da_den' && !$orders->has($ban->id);
                            })->count() }}
                        </div>
                        <div class="stat-desc">Khách mới chờ</div>
                    </div>
                    <i class="fa-solid fa-bell stat-icon-bg"></i>
                </div>
            </div>
        </div>

        {{-- 2. LIST BÀN --}}
        @foreach($khuVucs as $khu)
        <div class="mb-5">
            <div class="zone-label">
                <i class="fa-solid fa-map-pin me-2 text-warning"></i> {{ $khu->ten_khu_vuc }} <span class="opacity-50 mx-2">|</span> Tầng {{ $khu->tang }}
            </div>

            <div class="row g-3">
                @foreach($bans->where('khu_vuc_id', $khu->id) as $ban)
                    @php
                        $order = $orders->has($ban->id) ? $orders[$ban->id] : null;
                        $datBan = \App\Models\DatBan::where('ban_id', $ban->id)->latest()->first();
                        
                        // Xác định Theme và Text
                        $themeClass = 'theme-trong';
                        $statusText = 'TRỐNG';
                        $iconClass = 'fa-chair';

                        if($ban->trang_thai == 'dang_phuc_vu') {
                            $themeClass = 'theme-serving';
                            $statusText = 'ĐANG ĂN';
                            $iconClass = 'fa-utensils';
                        } elseif ($ban->trang_thai == 'khong_su_dung') {
                            $themeClass = 'theme-trong'; // Hoặc làm theme bảo trì riêng
                            $statusText = 'BẢO TRÌ';
                            $iconClass = 'fa-ban';
                        } elseif ($datBan) {
                            if($datBan->trang_thai == 'da_xac_nhan') {
                                $themeClass = 'theme-booked';
                                $statusText = 'ĐÃ ĐẶT';
                                $iconClass = 'fa-calendar-check';
                            } elseif ($datBan->trang_thai == 'khach_da_den' && !$order) {
                                $themeClass = 'theme-new pulse-border';
                                $statusText = 'KHÁCH MỚI';
                                $iconClass = 'fa-user-clock';
                            }
                        }
                    @endphp

                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                        <div class="ocean-card {{ $themeClass }}">
                            
                            {{-- HEADER FULL MÀU --}}
                            <div class="oc-header">
                                <span class="ban-title">Bàn {{ $ban->so_ban }}</span>
                                <span class="ban-badge">{{ $statusText }}</span>
                            </div>

                            <div class="oc-body">
                                {{-- NỘI DUNG --}}
                                @if($order)
                                    <div class="customer-name text-truncate w-100" title="{{ $order->datBan->ten_khach ?? 'Khách lẻ' }}">
                                        {{ $order->datBan->ten_khach ?? 'Khách lẻ' }}
                                    </div>
                                    <div class="order-info">Order #{{ $order->id }} • {{ $order->tong_mon }} món</div>
                                    <div class="price-display">{{ number_format($order->tong_tien) }}đ</div>

                                    <a href="{{ route('nhanVien.order.page', $order->id) }}" class="btn-fab fab-view" title="Xem chi tiết">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                @else
                                    {{-- Chưa có order --}}
                                    <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 80px;">
                                        @if($datBan && $datBan->trang_thai == 'khach_da_den')
                                            <div class="fw-bold text-dark">{{ $datBan->ten_khach }}</div>
                                            <small class="text-danger fw-bold">Chưa gọi món</small>
                                        @elseif($datBan && $datBan->trang_thai == 'da_xac_nhan')
                                            <div class="fw-bold text-dark">{{ $datBan->ten_khach }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($datBan->gio_den)->format('H:i') }}</small>
                                        @else
                                            <i class="fa-solid {{ $iconClass }} fa-3x icon-main opacity-25"></i>
                                        @endif
                                    </div>

                                    @if($ban->trang_thai != 'khong_su_dung')
                                        <form action="{{ route('nhanVien.order.mo-order') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="ban_id" value="{{ $ban->id }}">
                                            <button type="submit" class="btn-fab fab-add" title="Mở bàn">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach

    </div>
@endsection