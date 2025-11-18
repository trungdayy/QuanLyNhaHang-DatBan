@extends('layouts.Shop.layout-oderqr')

@section('title', 'Chọn Combo Bắt Đầu Order')

@section('content')

<style>
/* ===========================
   Trang Chọn Combo Bắt Đầu Order
=========================== */

/* Body & Main */
.app-content {
    min-height: 100vh;
    background: linear-gradient(to right, #f5f7fa, #c3cfe2);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    justify-content: center;
    padding: 30px 15px;
}

/* Form chính */
.form-container {
    background: #ffffff;
    border-radius: 20px;
    padding: 30px;
    max-width: 100%;
    width: 100%;
    box-shadow: 0 12px 25px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}

.form-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 20px 35px rgba(0,0,0,0.15);
}

h1 {
    color: #1c7ed6;
    margin-bottom: 15px;
}

p {
    color: #333;
    font-size: 1rem;
    margin-bottom: 10px;
}

/* Thông tin khách */
.datban-info {
    background: #f8f9fa;
    border-left: 4px solid #1c7ed6;
    padding: 12px 15px;
    border-radius: 10px;
    margin-bottom: 20px;
}

/* Lỗi form */
.error {
    background: #ffe3e3;
    color: #c92a2a;
    padding: 10px 15px;
    border-radius: 10px;
    margin-bottom: 20px;
}

/* Combo option */
.combo-option {
    display: flex;
    flex-direction: column;
    background: #f9f9f9;
    border-radius: 15px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s, border 0.2s;
    border: 2px solid transparent;
}

.combo-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

.combo-option.selected {
    border: 2px solid #1c7ed6;
    background: #e7f5ff;
}

/* Combo info */
.combo-info-content {
    display: flex;
    gap: 15px;
    align-items: center;
    padding: 10px 15px;
}

.combo-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid #ddd;
}

.placeholder-img {
    width: 80px;
    height: 80px;
    background: #ced4da;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
}

/* Combo text */
.combo-details-text {
    flex: 1;
}

.combo-details-text strong {
    font-size: 1.1rem;
    display: block;
    margin-bottom: 5px;
}

.combo-details-text p {
    font-size: 0.9rem;
    color: #555;
    margin: 0;
}

/* Danh sách món ăn trong combo */
.combo-dish-details {
    padding: 10px 20px;
    font-size: 0.9rem;
    color: #333;
    border-top: 1px dashed #dee2e6;
}

.combo-dish-details ul {
    padding-left: 20px;
    margin: 5px 0 0 0;
}

.combo-dish-details li {
    margin-bottom: 4px;
}

/* Nút submit */
button[type="submit"] {
    width: 100%;
    padding: 12px;
    background: #fd7e14;
    color: white;
    font-weight: 600;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.2s;
    margin-top: 15px;
}

button[type="submit"]:hover {
    background: #e8590c;
}

/* Responsive */
@media (max-width: 576px) {
    .combo-info-content {
        flex-direction: column;
        align-items: flex-start;
    }

    .combo-image, .placeholder-img {
        width: 100%;
        height: auto;
    }
}
</style>

<main class="app-content">
    <div class="form-container">
        <h1>Chọn Combo Bắt Đầu Order</h1>
        <p>Bàn hiện tại: <strong>{{ $tenBan }}</strong></p>

        {{-- Hiển thị thông tin khách nếu đã có --}}
        @if($datBan)
            <div class="datban-info">
                <p><strong>Họ tên khách:</strong> {{ $datBan->ten_khach }}</p>
                <p><strong>SĐT:</strong> {{ $datBan->sdt_khach }}</p>
                <p><strong>Số khách:</strong> {{ $datBan->so_khach }}</p>
                <p><strong>Giờ đến:</strong> {{ \Carbon\Carbon::parse($datBan->gio_den)->format('H:i d/m/Y') }}</p>
                @if($datBan->ghi_chu)
                    <p><strong>Ghi chú:</strong> {{ $datBan->ghi_chu }}</p>
                @endif
            </div>
        @endif

        @if ($errors->any())
            <div class="error">
                Vui lòng chọn ít nhất một combo hoặc món lẻ.
            </div>
        @endif

        <form action="{{ route('oderqr.start_order') }}" method="POST">
            @csrf
            <input type="hidden" name="ma_qr" value="{{ $qrKey }}">
            <input type="hidden" name="combo_id" id="selected-combo-id">
            <input type="hidden" name="so_khach" value="{{ $datBan->so_khach ?? 1 }}">
            
            <div class="form-group">
                <label>Chọn Combo:</label> <br>
                @foreach ($combos as $combo)
                    <div class="combo-option" data-combo-id="{{ $combo->id }}">
                        <div class="combo-info-content">
                            @if ($combo->anh)
                                {{-- SỬA ĐƯỜNG DẪN ẢNH Ở ĐÂY --}}
                                {{-- Sử dụng url('uploads/') để trỏ thẳng vào thư mục public/uploads --}}
                                {{-- Nếu $combo->anh đã có chữ 'uploads/' trong DB thì dùng url($combo->anh) --}}
                                {{-- Dựa vào file SQL, $combo->anh là "combo_buffet/ten_anh.jpg" --}}
                                <img src="{{ url('uploads/' . $combo->anh) }}" 
                                     alt="{{ $combo->ten_combo }}" 
                                     class="combo-image"
                                     onerror="this.src='https://placehold.co/80x80/eee/ccc?text=No+Img'">
                            @else
                                <div class="placeholder-img">C</div>
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
                    // Ẩn tất cả danh sách món ăn trước
                    document.querySelectorAll('.combo-dish-details').forEach(d => d.style.display = 'none');
                    // Xóa trạng thái selected cũ
                    comboOptions.forEach(opt => opt.classList.remove('selected'));
                    // Thêm trạng thái selected mới
                    this.classList.add('selected');
                    // Hiển thị danh sách món ăn của combo được chọn
                    const dishContainer = this.querySelector('.combo-dish-details');
                    if (dishContainer) dishContainer.style.display = 'block';
                    // Cập nhật giá trị combo_id ẩn
                    const comboId = this.getAttribute('data-combo-id');
                    comboIdInput.value = comboId === '0' ? '' : comboId;
                });
            });

            // LOGIC CHỌN MẶC ĐỊNH
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