@extends('layouts.shop.layout-nhanvien')

@section('title', 'Xác nhận xếp bàn')

@section('content')
    {{-- Import Fonts & Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #fea116;
            --primary-hover: #d98a12;
            --dark-blue: #0f172b;
            --light-bg: #f8f9fa;
            --success-green: #10b981;
            --warning-red: #ef4444;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Nunito', sans-serif;
        }

        /* --- CARD CONTAINER --- */
        .main-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background: #fff;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--dark-blue) 0%, #1e293b 100%);
            padding: 25px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circle in header */
        .card-header-custom::after {
            content: '';
            position: absolute;
            top: -20px; right: -20px;
            width: 100px; height: 100px;
            background: rgba(254, 161, 22, 0.2);
            border-radius: 50%;
            filter: blur(20px);
        }

        /* --- CUSTOMER INFO TICKET --- */
        .ticket-info {
            background: #fff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 15px;
            position: relative;
            background-image: radial-gradient(#f1f5f9 2px, transparent 2px);
            background-size: 10px 10px;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 700;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 800;
            color: var(--dark-blue);
        }

        /* --- CUSTOM TABLE SELECTOR (THE MAGIC) --- */
        .table-list-container {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 5px;
        }
        
        /* Custom scrollbar */
        .table-list-container::-webkit-scrollbar { width: 6px; }
        .table-list-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .table-list-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* Ẩn Radio button mặc định */
        .table-radio-input { display: none; }

        /* Label đóng vai trò là Card bàn */
        .table-option-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            margin-bottom: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fff;
            position: relative;
        }

        /* Hover effect */
        .table-option-card:hover {
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* --- TRẠNG THÁI CHECKED --- */
        .table-radio-input:checked + .table-option-card {
            border-color: var(--primary-color);
            background-color: #fffbf2; /* Màu cam rất nhạt */
            box-shadow: 0 0 0 4px rgba(254, 161, 22, 0.15);
        }

        /* Dấu tích khi chọn */
        .check-mark {
            width: 24px; height: 24px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
            color: transparent;
        }

        .table-radio-input:checked + .table-option-card .check-mark {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
            transform: scale(1.1);
        }

        /* --- TRẠNG THÁI PHÙ HỢP / KHÔNG PHÙ HỢP --- */
        .table-option-card.is-suitable .status-badge {
            background: #dcfce7; color: var(--success-green);
        }
        
        .table-option-card.not-suitable {
            opacity: 0.7;
            background: #f8fafc;
        }
        .table-option-card.not-suitable .status-badge {
            background: #fee2e2; color: var(--warning-red);
        }

        /* Typography trong card */
        .table-name { font-size: 1.1rem; font-weight: 800; color: var(--dark-blue); }
        .table-detail { font-size: 0.85rem; color: #64748b; font-weight: 600; }
        .status-badge {
            font-size: 0.7rem; font-weight: 800; padding: 4px 8px; border-radius: 6px; text-transform: uppercase;
        }

        /* --- BUTTONS --- */
        .btn-confirm {
            background: var(--primary-color);
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-weight: 800;
            letter-spacing: 0.5px;
            box-shadow: 0 10px 20px -5px rgba(254, 161, 22, 0.4);
            transition: 0.3s;
        }
        .btn-confirm:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(254, 161, 22, 0.5);
        }
    </style>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">
                <form action="{{ route('nhanVien.ban-an.process-checkin') }}" method="POST">
                    @csrf
                    <input type="hidden" name="dat_ban_id" value="{{ $datBan->id }}">

                    <div class="main-card">
                        {{-- Header --}}
                        <div class="card-header-custom text-white">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-chair fa-lg"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold text-uppercase" style="letter-spacing: 1px;">Xếp Bàn Cho Khách</h5>
                                    <small style="opacity: 0.8;">Vui lòng chọn bàn phù hợp bên dưới</small>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            {{-- Info Ticket --}}
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="ticket-info d-flex align-items-center justify-content-between flex-wrap gap-3">
                                        <div>
                                            <div class="info-label"><i class="fa-solid fa-user"></i> Khách hàng</div>
                                            <div class="info-value">{{ $datBan->ten_khach }}</div>
                                        </div>
                                        <div>
                                            <div class="info-label"><i class="fa-solid fa-phone"></i> SĐT</div>
                                            <div class="info-value">{{ $datBan->sdt_khach }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="info-label"><i class="fa-solid fa-users"></i> Số lượng</div>
                                            <div class="info-value text-danger" style="font-size: 1.2rem;">
                                                {{ $tongKhach }} <span style="font-size: 0.8rem; font-weight: 600; color:#64748b">khách</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-light mb-4">

                            {{-- Table Selection --}}
                            <div class="mb-3">
                                <label class="fw-bold mb-3 d-block text-dark">
                                    <i class="fa-solid fa-list-ul text-primary me-1"></i> DANH SÁCH BÀN TRỐNG:
                                </label>

                                <div class="table-list-container">
                                    @if($banTrongsSorted->isEmpty())
                                        <div class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="80" class="mb-3 opacity-50" alt="Full">
                                            <h6 class="fw-bold text-muted">Hiện tại không còn bàn trống!</h6>
                                            <p class="small text-muted">Vui lòng kiểm tra lại sơ đồ bàn hoặc yêu cầu khách chờ.</p>
                                        </div>
                                    @else
                                        @foreach($banTrongsSorted as $index => $ban)
                                            @php
                                                $isPhuHop = $ban->so_ghe >= $tongKhach;
                                                $recommended = $index === 0 && $isPhuHop; // Bàn đầu tiên của list sorted là best fit
                                                
                                                // Kiểm tra khu vực để gắn nhãn
                                                $isBackup = in_array($ban->khu_vuc_id, [5, 9]); // 5: Kho, 9: SOS
                                                $khuVucLabel = $isBackup ? '<span class="text-danger fw-bold me-1">[DỰ PHÒNG]</span>' : '';
                                            @endphp

                                            {{-- Radio Input ẩn --}}
                                            <input type="radio" 
                                                   name="ban_id" 
                                                   id="ban_{{ $ban->id }}" 
                                                   value="{{ $ban->id }}" 
                                                   class="table-radio-input" 
                                                   {{ $recommended ? 'checked' : '' }} required>

                                            {{-- Label đóng vai trò Card --}}
                                            <label for="ban_{{ $ban->id }}" class="table-option-card {{ $isPhuHop ? 'is-suitable' : 'not-suitable' }}">
                                                <div class="d-flex align-items-center gap-3">
                                                    {{-- Icon Bàn --}}
                                                    <div style="background: {{ $isPhuHop ? '#f0fdf4' : '#fef2f2' }}; width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: {{ $isPhuHop ? '#15803d' : '#ef4444' }}; font-weight: bold; font-size: 1.2rem;">
                                                        {{ $ban->so_ban }}
                                                    </div>
                                                    
                                                    {{-- Thông tin --}}
                                                    <div>
                                                        <div class="table-name">
                                                            {!! $khuVucLabel !!} Bàn số {{ $ban->so_ban }}
                                                            @if($recommended) 
                                                                <span class="badge bg-primary ms-1" style="font-size: 0.6rem; vertical-align: middle;">GỢI Ý</span> 
                                                            @endif
                                                        </div>
                                                        <div class="table-detail">
                                                            <i class="fa-solid fa-map-pin me-1"></i> {{ $ban->khuVuc->ten_khu_vuc ?? 'N/A' }} 
                                                            <span class="mx-2">•</span> 
                                                            <i class="fa-solid fa-chair me-1"></i> {{ $ban->so_ghe }} ghế
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Badge trạng thái & Checkmark --}}
                                                <div class="text-end">
                                                    @if($isPhuHop)
                                                        <div class="status-badge mb-1">Phù hợp</div>
                                                    @else
                                                        <div class="status-badge mb-1">Hơi nhỏ</div>
                                                    @endif
                                                    <div class="d-flex justify-content-end">
                                                        <div class="check-mark"><i class="fa-solid fa-check"></i></div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="d-grid gap-2 mt-4 pt-2">
                                <button type="submit" class="btn btn-primary btn-confirm text-white text-uppercase">
                                    <i class="fa-solid fa-circle-check me-2"></i> Xác Nhận Check-in
                                </button>
                                <a href="{{ route('nhanVien.ban-an.index') }}" class="btn btn-light fw-bold text-muted py-3" style="border: 1px solid #e2e8f0; border-radius: 10px;">
                                    <i class="fa-solid fa-xmark me-2"></i> Hủy bỏ
                                </a>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection