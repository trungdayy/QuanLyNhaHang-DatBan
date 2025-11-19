@extends('layouts.Shop.layout-oderqr')

@section('title', 'Chọn Combo Bắt Đầu Order')

@section('content')

<style>
/* ===== Form khách hàng ===== */
.customer-input-group {
    background: #f1f3f5;
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 25px;
    border: 1px solid #e9ecef;
}
.customer-input-group label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
    display: block;
}
.customer-input-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 1rem;
}
.customer-input-group input:focus {
    outline: none;
    border-color: #1c7ed6;
    box-shadow: 0 0 0 3px rgba(28, 126, 214, 0.1);
}

/* ===== Form container ===== */
.form-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

/* ===== Combo list ===== */
.combo-option {
    display: flex;
    align-items: flex-start;
    padding: 12px 15px;
    margin-bottom: 12px;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
}
.combo-option.selected {
    border-color: #1c7ed6;
    box-shadow: 0 2px 6px rgba(28,126,214,0.2);
    background: #e7f5ff;
}
.combo-option:hover {
    background: #f8f9fa;
}

/* ===== Combo image / placeholder ===== */
.combo-info-content {
    display: flex;
    align-items: center;
    width: 100%;
}
.combo-image, .placeholder-img {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
    margin-right: 15px;
    background: #adb5bd;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

/* ===== Combo text ===== */
.combo-details-text {
    flex: 1;
}
.combo-details-text strong {
    display: block;
    font-size: 1.1rem;
    margin-bottom: 5px;
}
.combo-details-text p {
    margin: 0;
    font-size: 0.95rem;
    color: #495057;
}

/* ===== Danh sách món ăn trong combo ===== */
.combo-dish-details {
    margin-top: 8px;
    padding-left: 95px; /* căn theo ảnh */
}
.combo-dish-details ul {
    padding-left: 18px;
    margin: 0;
}

/* ===== Submit button ===== */
button[type="submit"] {
    margin-top: 20px;
    padding: 12px 25px;
    font-size: 1rem;
    background-color: #1c7ed6;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.2s;
}
button[type="submit"]:hover {
    background-color: #1971c2;
}

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .combo-info-content {
        flex-direction: column;
        align-items: flex-start;
    }
    .combo-image, .placeholder-img {
        margin-bottom: 10px;
    }
    .combo-dish-details {
        padding-left: 0;
    }
}
</style>

<main class="app-content">
    <div class="form-container">
        <h1>Chọn Combo Bắt Đầu Order</h1>
        <p>Bàn hiện tại: <strong>{{ $tenBan }}</strong></p>

        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('oderqr.start_order') }}" method="POST">
            @csrf
            <input type="hidden" name="ma_qr" value="{{ $qrKey }}">
            <input type="hidden" name="combo_id" id="selected-combo-id">
            
            {{-- ===== Thông tin khách hàng ===== --}}
            @if($datBan && $datBan->ten_khach && $datBan->ten_khach !== 'Khách Vãng Lai')
                <div class="datban-info">
                    <p><strong>Họ tên khách:</strong> {{ $datBan->ten_khach }}</p>
                    <p><strong>SĐT:</strong> {{ $datBan->sdt_khach }}</p>
                    <p><strong>Số khách:</strong> {{ $datBan->so_khach }}</p>
                    <p><strong>Giờ đến:</strong> {{ \Carbon\Carbon::parse($datBan->gio_den)->format('H:i d/m/Y') }}</p>
                    
                    <input type="hidden" name="so_khach" value="{{ $datBan->so_khach }}">
                    <input type="hidden" name="ten_khach" value="{{ $datBan->ten_khach }}">
                    <input type="hidden" name="sdt_khach" value="{{ $datBan->sdt_khach }}">
                </div>
            @else
                <div class="customer-input-group">
                    <h4 style="color: #1c7ed6; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                        Thông tin khách hàng
                    </h4>
                    <div class="form-group">
                        <label>Họ và Tên (*):</label>
                        <input type="text" name="ten_khach" placeholder="Ví dụ: Nguyễn Văn A" required value="{{ old('ten_khach') }}">
                    </div>
                    <div class="form-group">
                        <label>Số Điện Thoại (*):</label>
                        <input type="text" name="sdt_khach" placeholder="Nhập số điện thoại" required value="{{ old('sdt_khach') }}">
                    </div>
                    <div class="form-group">
                        <label>Số lượng khách (*):</label>
                        <input type="number" name="so_khach" min="1" placeholder="Nhập số người ăn" required value="{{ old('so_khach', 1) }}">
                    </div>
                </div>
            @endif

            {{-- ===== Chọn combo ===== --}}
            <div class="form-group">
                <label style="font-size: 1.1rem; font-weight: bold; margin-bottom: 10px; display: block;">Chọn Combo:</label>
                


                @foreach ($combos as $combo)
                    <div class="combo-option" data-combo-id="{{ $combo->id }}">
                        <div class="combo-info-content">
                            @if ($combo->anh)
                                <img src="{{ url('uploads/' . $combo->anh) }}" 
                                     alt="{{ $combo->ten_combo }}" 
                                     class="combo-image"
                                     onerror="this.src='https://placehold.co/80x80/eee/ccc?text=No+Img'">
                            @else
                                <div class="placeholder-img">{{ substr($combo->ten_combo, 0, 1) }}</div>
                            @endif
                            <div class="combo-details-text">
                                <strong>{{ $combo->ten_combo }}</strong>
                                <p>Giá: {{ number_format($combo->gia_co_ban) }} VNĐ | Thời gian: {{ $combo->thoi_luong_phut }} phút</p>
                            </div>
                        </div>

                        @if ($combo->monTrongCombo->isNotEmpty())
                            <div class="combo-dish-details" style="display: none;">
                                <p><strong>Món ăn bao gồm:</strong></p>
                                <ul>
                                    @foreach ($combo->monTrongCombo as $item)
                                        @if ($item->monAn)
                                            <li>- {{ $item->monAn->ten_mon }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <button type="submit">Bắt Đầu Phục Vụ</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const comboOptions = document.querySelectorAll('.combo-option');
            const comboIdInput = document.getElementById('selected-combo-id');

            comboOptions.forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.combo-dish-details').forEach(d => d.style.display = 'none');
                    comboOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    const dishContainer = this.querySelector('.combo-dish-details');
                    if (dishContainer) dishContainer.style.display = 'block';
                    const comboId = this.getAttribute('data-combo-id');
                    comboIdInput.value = comboId === '0' ? '' : comboId;
                });
            });

            // Chọn mặc định
            const defaultComboToSelect = comboOptions[0] || document.querySelector('[data-combo-id="0"]');
            const oldComboId = '{{ old("combo_id") }}';
            let initialSelection = null;

            if (oldComboId) {
                initialSelection = document.querySelector(`[data-combo-id="${oldComboId}"]`);
            } else if (defaultComboToSelect) {
                initialSelection = defaultComboToSelect;
            } else {
                initialSelection = document.querySelector('[data-combo-id="0"]');
            }

            if (initialSelection) initialSelection.click();
        });
    </script>
</main>
@endsection
