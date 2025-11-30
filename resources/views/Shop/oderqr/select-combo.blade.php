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
            margin-top: 20px; 
        }

        /* Header Form */
        .form-header {
            background: var(--dark);
            padding: 40px 30px;
            text-align: center;
            color: var(--white);
            border-bottom: 4px solid var(--primary);
            position: relative;
        }
        .form-header::before {
            content: '\f2e7'; font-family: "Font Awesome 6 Free"; font-weight: 900;
            position: absolute; top: 50%; left: 20px; transform: translateY(-50%) rotate(-20deg);
            font-size: 5rem; color: var(--white); opacity: 0.03;
        }

        .form-header h1 {
            font-family: 'Nunito', sans-serif;
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

        /* --- COMBO LIST --- */
        .combo-list { display: flex; flex-direction: column; gap: 20px; }

        .combo-option {
            border: 1px solid var(--border-color);
            border-radius: 12px; padding: 0;
            background: #fff; 
            position: relative;
            overflow: hidden;
            transition: var(--anim-smooth);
        }
        .combo-content-wrapper { padding: 15px; display: flex; align-items: center; justify-content: space-between; gap: 15px; }
        
        /* Khi số lượng > 0 */
        .combo-option.active {
            border: 2px solid var(--primary); background: #fffbf2; box-shadow: 0 10px 25px rgba(254, 161, 22, 0.15);
        }

        .combo-info-group { display: flex; gap: 15px; align-items: center; flex: 1; }
        
        .combo-img-box {
            width: 70px; height: 70px; border-radius: 10px; overflow: hidden; flex-shrink: 0;
            background: #eee; display: flex; align-items: center; justify-content: center;
        }
        .combo-img { width: 100%; height: 100%; object-fit: cover; }
        
        .combo-text h4 { font-family: 'Heebo', sans-serif; font-weight: 700; font-size: 1rem; color: var(--dark); margin: 0 0 4px 0; }
        .combo-meta { font-size: 0.85rem; color: var(--text-sub); display: flex; gap: 10px; }
        .combo-price { color: var(--primary-dark); font-weight: 800; font-size: 1rem; }

        /* Quantity Control (NEW) */
        .qty-control {
            display: flex; align-items: center; background: #f1f5f9;
            border-radius: 8px; padding: 4px; border: 1px solid #e2e8f0;
        }
        .qty-btn {
            width: 32px; height: 32px; border: none; border-radius: 6px;
            background: #fff; color: var(--dark); font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .qty-btn:hover { background: var(--primary); color: #fff; }
        .qty-input {
            width: 40px; text-align: center; border: none; background: transparent;
            font-weight: 700; color: var(--dark); font-size: 1.1rem;
        }
        .qty-input:focus { outline: none; }

        /* Combo Details (Ẩn hiện) */
        .combo-details-toggle {
            text-align: center; font-size: 0.8rem; padding: 6px; 
            background: rgba(0,0,0,0.02); color: var(--text-sub); cursor: pointer;
            border-top: 1px solid #f1f5f9; transition: 0.2s;
        }
        .combo-details-toggle:hover { color: var(--primary); background: rgba(254, 161, 22, 0.05); }
        
        .combo-details-wrapper {
            max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out;
            background: #fafbfc;
        }
        .combo-details-wrapper.open { max-height: 500px; transition: max-height 0.5s ease-in; }
        
        .dish-list { padding: 15px; display: flex; flex-wrap: wrap; gap: 8px; }
        .dish-tag {
            font-size: 0.8rem; background: #fff; border: 1px solid #e2e8f0;
            padding: 4px 10px; border-radius: 4px; color: #555;
        }

        /* Footer Bar */
        .bottom-bar {
            margin-top: 30px; padding-top: 20px; border-top: 2px dashed #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .total-price {
            font-size: 1.2rem; font-weight: 800; color: var(--dark);
        }
        .total-price span { color: var(--primary); font-size: 1.5rem; }

        .btn-submit {
            padding: 14px 30px;
            background-color: var(--primary); color: var(--dark);
            border: none; border-radius: var(--radius);
            font-family: 'Heebo', sans-serif; font-weight: 800; font-size: 1.1rem;
            text-transform: uppercase; letter-spacing: 1px;
            cursor: pointer; transition: 0.2s;
            box-shadow: 0 4px 15px rgba(254, 161, 22, 0.3);
            opacity: 0.5; pointer-events: none; /* Disable khi chưa chọn */
        }
        .btn-submit.active { opacity: 1; pointer-events: all; }
        .btn-submit:hover {
            background-color: var(--primary-dark); color: #fff;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .app-content { padding: 20px 10px; }
            .form-header { padding: 30px 20px; }
            .input-grid { grid-template-columns: 1fr; gap: 15px; }
            .combo-content-wrapper { flex-direction: column; align-items: stretch; }
            .qty-control { justify-content: space-between; margin-top: 10px; }
            .qty-input { width: 100%; }
            .bottom-bar { flex-direction: column; gap: 20px; text-align: center; }
            .btn-submit { width: 100%; }
        }
    </style>

    <main class="app-content">
        <div class="form-container">
            
            <div class="form-header">
                <h1>Bắt Đầu Phục Vụ</h1>
                <p>Chọn số lượng suất Buffet cho từng loại</p>
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

                <form action="{{ route('oderqr.start_order') }}" method="POST" id="orderForm">
                    @csrf
                    <input type="hidden" name="ma_qr" value="{{ $qrKey }}">
                    
                    @if($datBan)
                        <input type="hidden" name="dat_ban_id" value="{{ $datBan->id }}">
                    @endif

                    <div class="section-label"><i class="fa-solid fa-user-group"></i> Thông Tin Khách Hàng</div>

                    @if($datBan && $datBan->ten_khach && $datBan->ten_khach !== 'Khách Vãng Lai')
                        <div class="datban-info">
                            <div class="info-row"><strong>Khách hàng</strong> <span>{{ $datBan->ten_khach }}</span></div>
                            <div class="info-row"><strong>SĐT</strong> <span>{{ $datBan->sdt_khach }}</span></div>
                            <div class="info-row" style="grid-column: span 2;">
                                <strong>Tổng khách hiện tại</strong> 
                                <span>{{ $datBan->nguoi_lon }} khách</span>
                            </div>
                        </div>
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
                        @endif

                    <div class="section-label" style="margin-top: 40px;"><i class="fa-solid fa-book-open"></i> Chọn Gói Buffet</div>
                    
                    <div class="combo-list">
                        @foreach ($combos as $index => $combo)
                            <div class="combo-option" id="combo-card-{{ $combo->id }}">
                                <div class="combo-content-wrapper">
                                    <div class="combo-info-group">
                                        <div class="combo-img-box">
                                            @if ($combo->anh)
                                                <img src="{{ url('uploads/' . $combo->anh) }}" class="combo-img" onerror="this.src='https://via.placeholder.com/80?text=Buffet'">
                                            @else
                                                <i class="fa-solid fa-utensils" style="color:#ccc; font-size:1.5rem"></i>
                                            @endif
                                        </div>
                                        <div class="combo-text">
                                            <h4 class="combo-name">{{ $combo->ten_combo }}</h4>
                                            <div class="combo-meta">
                                                <span class="combo-price" data-price="{{ $combo->gia_co_ban }}">{{ number_format($combo->gia_co_ban) }}đ</span>
                                                <span><i class="fa-regular fa-clock"></i> {{ $combo->thoi_luong_phut }}p</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="qty-control">
                                        <button type="button" class="qty-btn minus" data-id="{{ $combo->id }}"><i class="fa-solid fa-minus"></i></button>
                                        
                                        <input type="number" 
                                               name="combos[{{ $index }}][so_luong]" 
                                               class="qty-input" 
                                               id="qty-{{ $combo->id }}" 
                                               value="0" 
                                               min="0" 
                                               readonly>
                                        
                                        <input type="hidden" name="combos[{{ $index }}][id]" value="{{ $combo->id }}">
                                        
                                        <button type="button" class="qty-btn plus" data-id="{{ $combo->id }}"><i class="fa-solid fa-plus"></i></button>
                                    </div>
                                </div>

                                @if ($combo->monTrongCombo->isNotEmpty())
                                    <div class="combo-details-toggle" onclick="toggleDetails({{ $combo->id }})">
                                        Xem thực đơn <i class="fa-solid fa-chevron-down"></i>
                                    </div>
                                    <div class="combo-details-wrapper" id="details-{{ $combo->id }}">
                                        <div class="dish-list">
                                            @foreach ($combo->monTrongCombo as $item)
                                                @if ($item->monAn)
                                                    <div class="dish-tag"><i class="fa-solid fa-check" style="color:#20d489; margin-right:4px;"></i> {{ $item->monAn->ten_mon }}</div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="bottom-bar">
                        <div class="total-price">
                            Tổng tạm tính: <span id="total-display">0đ</span>
                        </div>
                        <button type="submit" class="btn-submit" id="btn-submit">
                            Xác Nhận & Gọi Món <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnSubmit = document.getElementById('btn-submit');
            const totalDisplay = document.getElementById('total-display');
            const orderForm = document.getElementById('orderForm');
            
            // Format tiền tệ
            const formatter = new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
            });

            // Hàm tính tổng và XỬ LÝ DISABLE INPUT
            function calculateTotal() {
                let total = 0;
                let totalQty = 0;

                document.querySelectorAll('.combo-option').forEach(card => {
                    const priceText = card.querySelector('.combo-price').dataset.price;
                    const price = parseFloat(priceText);
                    
                    // Lấy các input trong card này
                    const qtyInput = card.querySelector('.qty-input');
                    // Tìm input hidden chứa ID combo nằm cùng cấp hoặc bên trong card
                    // Cách selector: input có name chứa [id]
                    const idInput = card.querySelector('input[name*="[id]"]');

                    const qty = parseInt(qtyInput.value) || 0;

                    if (qty > 0) {
                        // Nếu có chọn số lượng: Active card và Enable input để gửi đi
                        card.classList.add('active');
                        total += price * qty;
                        totalQty += qty;

                        qtyInput.disabled = false;
                        if(idInput) idInput.disabled = false;
                    } else {
                        // Nếu số lượng = 0: Bỏ active và Disable input để KHÔNG gửi đi
                        card.classList.remove('active');
                        
                        qtyInput.disabled = true;
                        if(idInput) idInput.disabled = true;
                    }
                });

                totalDisplay.innerText = formatter.format(total);

                // Disable nút submit nếu chưa chọn gì
                if (totalQty > 0) {
                    btnSubmit.classList.add('active');
                } else {
                    btnSubmit.classList.remove('active');
                }
            }

            // Xử lý nút cộng trừ
            document.querySelectorAll('.qty-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    // Tìm input dựa trên ID duy nhất đã gán trong vòng lặp blade
                    const input = document.getElementById(`qty-${id}`);
                    
                    if (!input) return; // Safety check

                    let val = parseInt(input.value) || 0;

                    if (this.classList.contains('plus')) {
                        val++;
                    } else if (this.classList.contains('minus')) {
                        if (val > 0) val--;
                    }

                    input.value = val;
                    calculateTotal(); // Tính toán lại và cập nhật trạng thái disable
                });
            });
            
            // Hàm toggle xem chi tiết món
            window.toggleDetails = function(id) {
                const details = document.getElementById(`details-${id}`);
                if(details) {
                    details.classList.toggle('open');
                }
            }

            // Chạy lần đầu để disable hết các ô số lượng 0 ngay khi tải trang
            calculateTotal();
        });
    </script>
@endsection