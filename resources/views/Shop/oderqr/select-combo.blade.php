@extends('layouts.Shop.layout-oderqr')

@section('title', 'Chọn Combo - ' . ($tenBan ?? 'Ocean Buffet'))

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- CẤU HÌNH MÀU SẮC (RESTORAN THEME) --- */
        :root {
            --primary: #fea116;       /* Cam vàng chủ đạo */
            --primary-dark: #d98a12;  /* Cam đậm */
            --dark: #0f172b;          /* Xanh đen đậm */
            --light: #f1f8ff;
            --white: #ffffff;
            
            --text-main: #1e293b;
            --text-sub: #64748b;
            --border-color: #e2e8f0;
            
            --radius: 12px;
            --shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
            --anim-smooth: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f2f4f8;
            color: var(--text-main);
        }

        .app-content {
            min-height: 100vh;
            padding: 40px 15px;
            display: flex; justify-content: center; align-items: flex-start;
        }

        /* --- FORM CONTAINER --- */
        .form-container {
            width: 100%; max-width: 850px;
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            overflow: hidden;
            /* Margin top để tách khỏi Banner gốc của theme */
            margin-top: 20px; 
        }

        /* Header Form: Xanh đen + Chữ Cam */
        .form-header {
            background: var(--dark);
            padding: 40px 30px;
            text-align: center;
            color: var(--white);
            border-bottom: 4px solid var(--primary);
            position: relative;
        }
        /* Icon trang trí chìm */
        .form-header::before {
            content: '\f2e7'; font-family: "Font Awesome 6 Free"; font-weight: 900;
            position: absolute; top: 50%; left: 20px; transform: translateY(-50%) rotate(-20deg);
            font-size: 5rem; color: var(--white); opacity: 0.03;
        }

        .form-header h1 {
            font-family: 'Nunito', sans-serif; /* Đồng bộ font tròn trịa */
            font-weight: 800; font-size: 2rem;
            margin: 0 0 10px 0;
            color: var(--primary);
            text-transform: uppercase; letter-spacing: 1px;
        }
        .form-header p { margin: 0; opacity: 0.9; font-size: 1rem; color: #e2e8f0; }

        .table-badge {
            display: inline-block; margin-top: 20px;
            padding: 8px 24px; border-radius: 50px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--white); font-weight: 700;
            border: 1px solid rgba(254, 161, 22, 0.3);
        }
        .table-badge i { color: var(--primary); margin-right: 8px; }

        /* --- FORM BODY --- */
        .form-body { padding: 30px; }

        .section-label {
            font-family: 'Heebo', sans-serif; font-weight: 700; font-size: 1.1rem;
            color: var(--dark); margin-bottom: 25px;
            display: flex; align-items: center; gap: 10px;
            padding-bottom: 10px; border-bottom: 1px solid var(--border-color);
        }
        .section-label i { color: var(--primary); font-size: 1.2rem; }

        /* Input Groups */
        .input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        
        .form-group label {
            display: block; font-weight: 700; font-size: 0.95rem; margin-bottom: 8px; color: var(--text-main);
        }
        .form-control {
            width: 100%; padding: 14px;
            border: 1px solid var(--border-color); border-radius: var(--radius);
            font-size: 1rem; transition: 0.2s; background: #fcfcfc;
            font-family: 'Nunito', sans-serif;
        }
        .form-control:focus {
            outline: none; border-color: var(--primary); background: #fff;
            box-shadow: 0 0 0 4px rgba(254, 161, 22, 0.15);
        }

        /* Thông tin đặt bàn có sẵn */
        .datban-info {
            background: #fffbf0; border: 1px solid #ffeeba;
            border-radius: var(--radius); padding: 20px;
            display: grid; grid-template-columns: 1fr 1fr; gap: 15px;
        }
        .info-row strong { display: block; font-size: 0.8rem; color: #b45309; text-transform: uppercase; }
        .info-row span { font-weight: 700; color: var(--dark); font-size: 1rem; }

        /* --- COMBO LIST (ANIMATION MƯỢT) --- */
        .combo-list { display: flex; flex-direction: column; gap: 20px; }

        .combo-option {
            border: 1px solid var(--border-color);
            border-radius: 12px; padding: 0; /* Padding chuyển vào trong wrapper */
            background: #fff; 
            cursor: pointer; 
            position: relative;
            overflow: hidden; /* Quan trọng để bo góc */
            transition: var(--anim-smooth);
        }

        /* Wrapper nội dung chính để tách biệt với phần slide */
        .combo-content-wrapper { padding: 20px; }

        /* Hiệu ứng Hover */
        .combo-option:hover {
            border-color: #cbd5e1; 
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }

        /* Trạng thái SELECTED */
        .combo-option.selected {
            border: 2px solid var(--primary);
            background: #fffbf2; /* Cam siêu nhạt */
            box-shadow: 0 10px 25px rgba(254, 161, 22, 0.15);
        }

        /* Icon Checkmark trượt vào */
        .combo-option::after {
            content: '\f00c'; font-family: "Font Awesome 6 Free"; font-weight: 900;
            position: absolute; top: 0; right: 0;
            background: var(--primary); color: var(--dark);
            padding: 6px 12px; border-bottom-left-radius: 12px; font-size: 0.9rem;
            /* Ẩn đi */
            opacity: 0; transform: translate(100%, -100%);
            transition: all 0.3s ease;
        }
        .combo-option.selected::after {
            opacity: 1; transform: translate(0, 0);
        }

        .combo-header { display: flex; gap: 20px; align-items: center; }
        
        .combo-img-box {
            width: 80px; height: 80px; border-radius: 10px; overflow: hidden; flex-shrink: 0;
            background: #eee; display: flex; align-items: center; justify-content: center;
            transition: transform 0.3s ease;
        }
        .combo-img { width: 100%; height: 100%; object-fit: cover; }
        .combo-option.selected .combo-img-box { transform: scale(1.05); }

        .combo-info { flex: 1; }
        .combo-name {
            font-family: 'Heebo', sans-serif; font-weight: 700; font-size: 1.1rem;
            color: var(--dark); margin-bottom: 5px; transition: color 0.2s;
        }
        .combo-option.selected .combo-name { color: #b45309; }

        .combo-meta { display: flex; gap: 15px; align-items: center; font-size: 0.95rem; color: var(--text-sub); }
        .combo-price { color: var(--primary-dark); font-weight: 800; font-size: 1.1rem; }
        
        /* --- ANIMATION SLIDE DOWN --- */
        .combo-details-wrapper {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-in-out, opacity 0.4s ease-in-out;
            background: rgba(255, 255, 255, 0.5);
        }
        
        .combo-option.selected .combo-details-wrapper {
            max-height: 500px; /* Mở rộng ra */
            opacity: 1;
        }

        .combo-details-inner {
            padding: 0 20px 20px 20px;
            border-top: 1px dashed rgba(0,0,0,0.1);
            margin-top: 5px; padding-top: 15px;
        }

        .dish-list { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .dish-tag {
            font-size: 0.9rem; background: #fff; border: 1px solid #e2e8f0;
            padding: 6px 12px; border-radius: 6px; color: #555;
            display: flex; align-items: center;
        }
        .dish-tag i { color: #20d489; margin-right: 6px; }

        /* --- NÚT SUBMIT --- */
        .btn-submit {
            width: 100%; margin-top: 40px; padding: 16px;
            background-color: var(--primary);
            color: var(--dark);
            border: none; border-radius: var(--radius);
            font-family: 'Heebo', sans-serif; font-weight: 800; font-size: 1.2rem;
            text-transform: uppercase; letter-spacing: 1px;
            cursor: pointer; transition: 0.2s;
            box-shadow: 0 4px 15px rgba(254, 161, 22, 0.3);
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-submit:hover {
            background-color: var(--primary-dark); color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(254, 161, 22, 0.4);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .app-content { padding: 20px 10px; }
            .form-header { padding: 30px 20px; }
            .input-grid { grid-template-columns: 1fr; gap: 15px; }
            .combo-img-box { width: 60px; height: 60px; }
        }
    </style>

    <main class="app-content">
        <div class="form-container">
            
            <div class="form-header">
                <h1>Bắt Đầu Phục Vụ</h1>
                <p>Vui lòng xác nhận thông tin & chọn gói Combo</p>
                <div class="table-badge"><i class="fa-solid fa-utensils"></i> {{ $tenBan }}</div>
            </div>

            <div class="form-body">
                @if ($errors->any())
                    <div style="background: #fff5f5; border: 1px solid #feb2b2; color: #c53030; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                        </ul>
                    </div>
                @endif

<form action="{{ route('oderqr.start_order') }}" method="POST">
    @csrf
    <input type="hidden" name="ma_qr" value="{{ $qrKey }}">
    <input type="hidden" name="combo_id" id="selected-combo-id">
    
    {{-- 🔥 THÊM DÒNG NÀY: Truyền dat_ban_id nếu đã có đơn --}}
    @if($datBan)
        <input type="hidden" name="dat_ban_id" value="{{ $datBan->id }}">
    @endif

    <div class="section-label"><i class="fa-solid fa-user-group"></i> Thông Tin Khách Hàng</div>

    @if($datBan && $datBan->ten_khach && $datBan->ten_khach !== 'Khách Vãng Lai')
        <div class="datban-info">
            <div class="info-row"><strong>Khách hàng</strong> <span>{{ $datBan->ten_khach }}</span></div>
            <div class="info-row"><strong>SĐT</strong> <span>{{ $datBan->sdt_khach }}</span></div>
            <div class="info-row"><strong>Số lượng</strong> <span>{{ $datBan->so_khach }} người</span></div>
            <div class="info-row"><strong>Giờ đến</strong> <span>{{ \Carbon\Carbon::parse($datBan->gio_den)->format('H:i d/m') }}</span></div>
        </div>
        <input type="hidden" name="so_khach" value="{{ $datBan->so_khach }}">
        <input type="hidden" name="ten_khach" value="{{ $datBan->ten_khach }}">
        <input type="hidden" name="sdt_khach" value="{{ $datBan->sdt_khach }}">
    @else
        <div class="input-grid">
            <div class="form-group">
                <label>Họ và Tên (*)</label>
                <input type="text" class="form-control" name="ten_khach" placeholder="Ví dụ: Anh Nam" required value="{{ old('ten_khach') }}">
            </div>
            <div class="form-group">
                <label>Số Điện Thoại (*)</label>
                <input type="text" class="form-control" name="sdt_khach" placeholder="Ví dụ: 098..." required value="{{ old('sdt_khach') }}">
            </div>
        </div>
        <div class="form-group">
            <label>Số lượng khách ăn (*)</label>
            <input type="number" class="form-control" name="so_khach" min="1" placeholder="Nhập số người" required value="{{ old('so_khach', 1) }}">
        </div>
    @endif

    <div class="section-label" style="margin-top: 40px;"><i class="fa-solid fa-book-open"></i> Chọn Gói Buffet</div>
    
    <div class="combo-list">
        @foreach ($combos as $combo)
            <div class="combo-option" data-combo-id="{{ $combo->id }}">
                <div class="combo-content-wrapper">
                    <div class="combo-header">
                        <div class="combo-img-box">
                            @if ($combo->anh)
                                <img src="{{ url('uploads/' . $combo->anh) }}" class="combo-img" onerror="this.style.display='none';this.parentNode.innerHTML='<i class=\'fa-solid fa-utensils\' style=\'color:#ccc; font-size:1.5rem\'></i>'">
                            @else
                                <i class="fa-solid fa-utensils" style="color:#ccc; font-size:1.5rem"></i>
                            @endif
                        </div>
                        <div class="combo-info">
                            <div class="combo-name">{{ $combo->ten_combo }}</div>
                            <div class="combo-meta">
                                <span class="combo-price">{{ number_format($combo->gia_co_ban) }}đ</span>
                                <span><i class="fa-regular fa-clock"></i> {{ $combo->thoi_luong_phut }} phút</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($combo->monTrongCombo->isNotEmpty())
                    <div class="combo-details-wrapper">
                        <div class="combo-details-inner">
                            <div style="font-size:0.85rem; font-weight:700; color:#888; margin-bottom:8px;">
                                <i class="fa-solid fa-list-check"></i> THỰC ĐƠN BAO GỒM:
                            </div>
                            <div class="dish-list">
                                @foreach ($combo->monTrongCombo as $item)
                                    @if ($item->monAn)
                                        <div class="dish-tag"><i class="fa-solid fa-check"></i> {{ $item->monAn->ten_mon }}</div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <button type="submit" class="btn-submit">
        Xác Nhận & Gọi Món <i class="fa-solid fa-arrow-right"></i>
    </button>
</form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const comboOptions = document.querySelectorAll('.combo-option');
            const comboIdInput = document.getElementById('selected-combo-id');

            comboOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // 1. Bỏ chọn tất cả các item khác
                    comboOptions.forEach(opt => opt.classList.remove('selected'));
                    
                    // 2. Chọn item hiện tại -> CSS sẽ tự kích hoạt transition max-height
                    this.classList.add('selected');
                    
                    // 3. Cập nhật giá trị input hidden
                    const comboId = this.getAttribute('data-combo-id');
                    comboIdInput.value = comboId === '0' ? '' : comboId;

                    // 4. (Optional) Cuộn nhẹ để item không bị khuất (nếu danh sách quá dài)
                    // this.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            });

            // Chọn mặc định khi vào trang
            const oldComboId = '{{ old("combo_id") }}';
            let target = oldComboId 
                ? document.querySelector(`.combo-option[data-combo-id="${oldComboId}"]`) 
                : comboOptions[0];
                
            if (target) {
                // Trigger thủ công không cần click (để tránh scroll jump)
                target.classList.add('selected');
                comboIdInput.value = target.getAttribute('data-combo-id');
            }
        });
    </script>
@endsection