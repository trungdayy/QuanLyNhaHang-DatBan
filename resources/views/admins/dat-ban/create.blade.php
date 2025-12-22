@extends('layouts.admins.layout-admin')

@section('title', 'Tạo Đơn Đặt Bàn Mới')

@section('style')
<style>
    /* --- 1. CSS CHO CARD CHỌN COMBO --- */
    .combo-admin-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        background: #fff;
        transition: all 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .combo-admin-card.active {
        border-color: #009688; /* Màu xanh chủ đạo */
        box-shadow: 0 0 8px rgba(0, 150, 136, 0.2);
        background-color: #f0faf9;
    }
    .combo-name-price {
        margin-bottom: 10px;
    }
    .combo-name-price label {
        font-weight: 700;
        color: #333;
        display: block;
        margin-bottom: 4px;
    }
    .combo-name-price small {
        font-size: 0.9em;
        color: #666;
        font-weight: 600;
    }
    
    /* Nút cộng trừ số lượng */
    .combo-qty-control {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #ccc;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
    }
    .qty-btn {
        padding: 6px 12px;
        border: none;
        background: #e9ecef;
        cursor: pointer;
        transition: 0.2s;
        color: #333;
    }
    .qty-btn:hover {
        background: #009688;
        color: white;
    }
    .qty-input-display {
        text-align: center;
        flex-grow: 1;
        font-weight: 700;
        padding: 5px 0;
        color: #000;
    }

    /* Header tóm tắt combo */
    .combo-summary-header {
        cursor: pointer;
        padding: 12px 15px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: 0.2s;
    }
    .combo-summary-header:hover {
        background: #e9ecef;
    }
    .fa-chevron-down.rotated {
        transform: rotate(180deg);
        transition: transform 0.3s;
    }

    /* --- 2. CSS CHO MODAL THÔNG BÁO LỖI (CUSTOM ALERT) --- */
    .custom-alert-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Nền tối */
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        animation: fadeIn 0.2s ease-out;
    }

    .custom-alert-box {
        background: white;
        width: 420px;
        max-width: 90%;
        border-radius: 10px;
        padding: 0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        transform: scale(0.9);
        animation: popUp 0.3s ease-out forwards;
        overflow: hidden;
    }

    .alert-header {
        background: #dc3545; /* Màu đỏ lỗi */
        color: white;
        padding: 15px;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .alert-body {
        padding: 20px 25px;
        color: #333;
        font-size: 15px;
        line-height: 1.6;
    }

    .alert-detail-box {
        background: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
        padding: 10px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-size: 14px;
    }

    .alert-footer {
        padding: 15px;
        text-align: center;
        border-top: 1px solid #eee;
        background: #f8f9fa;
    }

    .btn-close-alert {
        background: #009688;
        color: white;
        border: none;
        padding: 8px 30px;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
    }
    .btn-close-alert:hover {
        background: #00796b;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes popUp { from { transform: scale(0.8); } to { transform: scale(1); } }

    /* Select Option Colors */
    option.opt-free { color: #16a34a; font-weight: bold; }
    option.opt-limited { color: #d97706; font-weight: bold; }
</style>
@endsection

@section('content')
    <main class="app-content">
        
        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dat-ban.index') }}">Quản lý Đặt Bàn</a></li>
                <li class="breadcrumb-item"><a href="#"><b>Tạo Đặt Bàn Mới</b></a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title">Tạo Đơn Đặt Bàn Mới (Tại quầy)</h3>
                    <div class="tile-body">
                        
                        {{-- Hiển thị lỗi từ Laravel Validator --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 pl-3">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form class="row" method="POST" action="{{ route('admin.dat-ban.store') }}" id="datBanForm">
                            @csrf
                            
                            {{-- THÔNG TIN LIÊN HỆ --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Tên Khách Hàng (*)</label>
                                <input class="form-control" type="text" name="ten_khach" value="{{ old('ten_khach') }}" placeholder="Nhập tên khách" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Số Điện Thoại (*)</label>
                                <input class="form-control" type="text" name="sdt_khach" value="{{ old('sdt_khach') }}" placeholder="Nhập SĐT" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Email</label>
                                <input class="form-control" type="email" name="email_khach" value="{{ old('email_khach') }}" placeholder="Nhập email (nếu có)">
                            </div>

                            {{-- SỐ LƯỢNG KHÁCH --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Người lớn (>1m3) (*)</label>
                                <input class="form-control" type="number" name="nguoi_lon" id="nguoi_lon_input" min="1" value="{{ old('nguoi_lon', 1) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Trẻ em (<1m3)</label>
                                <input class="form-control" type="number" name="tre_em" id="tre_em_input" min="0" value="{{ old('tre_em', 0) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Tiền Cọc (VNĐ)</label>
                                <input class="form-control" type="number" name="tien_coc" value="{{ old('tien_coc', 0) }}" min="0" placeholder="Nhập số tiền cọc">
                            </div>
                            <input type="hidden" name="tong_khach" id="tong_khach_input" value="{{ old('tong_khach', 1) }}">

                            {{-- THÔNG TIN ĐẶT BÀN --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Giờ Khách Đến (*)</label>
                                <input class="form-control" type="datetime-local" name="gio_den" id="gio_den_input" value="{{ old('gio_den') }}" readonly required>
                            </div>
                            
                            {{-- CHỌN BÀN (Ajax load - Danh sách bàn sẽ thay đổi tùy thuộc vào giờ khách đến) --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Chọn Bàn (*)</label>
                                <select class="form-control" name="ban_id" id="ban_id_select" required>
                                    <option value="" disabled selected>-- Vui lòng nhập số lượng khách trước --</option>
                                    {{-- Dữ liệu ở đây sẽ được Javascript điền vào --}}
                                </select>
                                <small class="text-muted"><i>* Tự động lọc bàn trống theo giờ và số khách.</i></small>
                                <div id="msgBanLimited" class="alert alert-warning mt-2 d-none py-2 px-3 small">
                                    <i class="fa-solid fa-clock me-1"></i> <strong>Lưu ý:</strong> Bàn này chỉ trống trong khoảng thời gian giới hạn. Hãy thông báo cho khách.
                                </div>
                            </div>

                            {{-- NHÂN VIÊN PHỤ TRÁCH --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Nhân viên phụ trách</label>
                                <select class="form-control" name="nhan_vien_id">
                                    <option value="">-- Chọn nhân viên (Tùy chọn) --</option>
                                    @foreach ($nhanViens->where('vai_tro', 'phuc_vu') as $nv)
                                        <option value="{{ $nv->id }}" {{ old('nhan_vien_id') == $nv->id ? 'selected' : '' }}>
                                            {{ $nv->ho_ten }} (Phục vụ)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- BƯỚC 1: CHỌN MỨC GIÁ COMBO --}}
                            <div class="form-group col-md-12 mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="control-label mb-0">Chọn Combo Buffet</label>
                                    <select class="form-control" id="selGiaCombo" style="width: auto; max-width: 200px;">
                                        <option value="">-- Chọn mức giá --</option>
                                        @php $giaList = $combos->pluck('gia_co_ban')->unique()->sort(); @endphp
                                        @foreach($giaList as $gia)
                                            <option value="{{ $gia }}">{{ number_format($gia) }}đ</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Container chứa danh sách combo, mặc định ẩn --}}
                                <div id="combo-wrapper" class="d-none">
                                    <div id="combo-selection-container" class="row g-3 mt-1">
                                        @forelse ($combos as $c)
                                        <div class="col-md-3">
                                            <div class="combo-admin-card" data-combo-price="{{ $c->gia_co_ban }}" id="card-{{ $c->id }}">
                                                <div class="combo-name-price">
                                                    <label class="mb-0 text-primary combo-name-label">{{ $c->ten_combo }}</label>
                                                    <small>{{ number_format($c->gia_co_ban) }}đ / suất</small>
                                                </div>
                                                <div class="combo-qty-control">
                                                    <button type="button" class="qty-btn minus-btn" data-id="{{ $c->id }}"><i class="fas fa-minus"></i></button>
                                                    <div class="qty-input-display" id="display-{{ $c->id }}">0</div>
                                                    <input type="number" 
                                                           class="combo-qty-input d-none" 
                                                           id="qty-{{ $c->id }}"
                                                           data-combo-id="{{ $c->id }}"
                                                           data-combo-key="combo_{{ $c->id }}"
                                                           value="0" min="0" max="100" readonly>
                                                    <button type="button" class="qty-btn plus-btn" data-id="{{ $c->id }}"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="col-12 text-center py-4 text-muted">
                                            <p class="small">Chưa có dữ liệu Combo.</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Thông báo khi chưa chọn giá --}}
                                <div id="msgChonGia" class="text-center py-4 border rounded bg-white text-muted mt-2">
                                    <i class="fa-solid fa-arrow-up mb-2"></i><br>
                                    Vui lòng chọn mức giá để xem thực đơn.
                                </div>
                            </div>
                            <input type="hidden" name="cart_data" id="cart_data">
                            
                            <div class="form-group col-md-12 mt-3">
                                <label class="control-label">Ghi Chú (Nếu có)</label>
                                <textarea class="form-control" name="ghi_chu" rows="3" placeholder="Ghi chú thêm về đơn đặt bàn...">{{ old('ghi_chu') }}</textarea>
                            </div>
                            
                            <div class="form-group col-md-12 mt-4">
                                <button class="btn btn-save" type="submit"><i class="fa fa-floppy-o"></i> Lưu Đặt Bàn</button>
                                <a class="btn btn-cancel" href="{{ route('admin.dat-ban.index') }}"><i class="fa fa-times"></i> Hủy bỏ</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL THÔNG BÁO LỖI CUSTOM --}}
        <div id="custom-alert-modal" class="custom-alert-overlay" style="display: none;">
            <div class="custom-alert-box">
                <div class="alert-header">
                    <i class="fas fa-exclamation-triangle"></i> LỖI SỐ LƯỢNG
                </div>
                <div class="alert-body">
                    <p>Số lượng Combo đã chọn không hợp lệ so với số khách.</p>
                    <div class="alert-detail-box" id="alert-detail-content">
                        </div>
                    <p class="text-muted mb-0"><small>Vui lòng kiểm tra và chọn thêm combo.</small></p>
                </div>
                <div class="alert-footer">
                    <button type="button" class="btn-close-alert">Đã Hiểu</button>
                </div>
            </div>
        </div>

    </main>
@endsection

@section('script')
<script>
    const CHECK_URL = "{{ route('admin.dat-ban.check-ban-trong') }}";
    const OLD_BAN_ID = "{{ old('ban_id') }}";

    // Hàm tính tổng số khách
    function getTongKhach() {
        let nguoiLon = parseInt(document.getElementById('nguoi_lon_input').value) || 0;
        let treEm = parseInt(document.getElementById('tre_em_input').value) || 0;
        return nguoiLon + treEm;
    }

    // Hàm tự động cập nhật số lượng combo tối thiểu
    function autoUpdateComboQuantity() {
        let tongKhach = getTongKhach();
        if (tongKhach < 1) return;

        let allCombos = document.querySelectorAll('#combo-selection-container .combo-admin-card');
        
        allCombos.forEach(card => {
            let style = window.getComputedStyle(card);
            if (style.display !== 'none') {
                let input = card.querySelector('.combo-qty-input');
                if (input) {
                    let currentVal = parseInt(input.value) || 0;
                    input.setAttribute('min', tongKhach);
                    if (currentVal > 0 && currentVal < tongKhach) {
                        input.value = tongKhach;
                        let display = card.querySelector('.qty-input-display');
                        if (display) display.textContent = tongKhach;
                    }
                }
            }
        });
    }

    // --- 1. LOGIC COMBO (CHỌN GIÁ -> HIỆN COMBO -> RESET SỐ LƯỢNG) ---
    const selGia = document.getElementById('selGiaCombo');
    const comboWrapper = document.getElementById('combo-wrapper');
    const msgChonGia = document.getElementById('msgChonGia');

    if (selGia) {
        selGia.addEventListener('change', function() {
            let selectedPrice = this.value;
            let list = document.querySelectorAll('#combo-selection-container .combo-admin-card');

            // Reset toàn bộ số lượng về 0 trước khi lọc
            list.forEach(card => {
                let input = card.querySelector('.combo-qty-input');
                if (input) {
                    input.value = 0;
                    let display = card.querySelector('.qty-input-display');
                    if (display) display.textContent = 0;
                    card.classList.remove('active');
                }
                card.style.display = 'none';
            });

            // Xử lý hiển thị
            if (!selectedPrice) {
                comboWrapper.classList.add('d-none');
                msgChonGia.classList.remove('d-none');
            } else {
                comboWrapper.classList.remove('d-none');
                msgChonGia.classList.add('d-none');

                let tongKhach = getTongKhach();
                let firstVisibleCombo = null;
                
                list.forEach(card => {
                    let price = card.getAttribute('data-combo-price');
                    if (Number(selectedPrice) == Number(price)) {
                        card.style.display = "block";
                        if (!firstVisibleCombo) {
                            firstVisibleCombo = card;
                        }
                        let input = card.querySelector('.combo-qty-input');
                        if (input && tongKhach > 0) {
                            input.setAttribute('min', tongKhach);
                        }
                    }
                });

                // Tự động đặt số lượng combo đầu tiên = số người
                if (firstVisibleCombo && tongKhach > 0) {
                    let firstInput = firstVisibleCombo.querySelector('.combo-qty-input');
                    if (firstInput) {
                        firstInput.value = tongKhach;
                        let display = firstVisibleCombo.querySelector('.qty-input-display');
                        if (display) display.textContent = tongKhach;
                        firstVisibleCombo.classList.add('active');
                    }
                }
            }
        });
    }

    // Logic Tăng/Giảm số lượng
    document.addEventListener('click', function(e) {
        let tongKhach = getTongKhach();
        
        if (e.target.closest('.btn-plus')) {
            let btn = e.target.closest('.btn-plus');
            let input = btn.parentElement.querySelector('.combo-qty-input');
            let display = btn.parentElement.querySelector('.qty-input-display');
            let currentVal = parseInt(input.value) || 0;
            if (currentVal < 100) {
                input.value = currentVal + 1;
                display.textContent = currentVal + 1;
                btn.closest('.combo-admin-card').classList.add('active');
            }
        }
        
        if (e.target.closest('.btn-minus')) {
            let btn = e.target.closest('.btn-minus');
            let input = btn.parentElement.querySelector('.combo-qty-input');
            let display = btn.parentElement.querySelector('.qty-input-display');
            let currentVal = parseInt(input.value) || 0;
            let minVal = parseInt(input.getAttribute('min')) || 0;
            if (currentVal > minVal) {
                input.value = currentVal - 1;
                display.textContent = currentVal - 1;
                if (currentVal - 1 === 0) {
                    btn.closest('.combo-admin-card').classList.remove('active');
                }
            }
        }
    });

    // Ngăn người dùng nhập số lượng < min
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('combo-qty-input')) {
            let minVal = parseInt(e.target.getAttribute('min')) || 0;
            let inputVal = parseInt(e.target.value) || 0;
            
            if (inputVal < minVal && minVal > 0) {
                e.target.value = minVal;
                let display = e.target.parentElement.querySelector('.qty-input-display');
                if (display) display.textContent = minVal;
            }
        }
    });

    // --- 2. LOGIC TÌM BÀN (THÔNG MINH - GAP FILLING) ---
    function updateTongKhach() {
        let nguoiLon = parseInt(document.getElementById('nguoi_lon_input').value) || 0;
        let treEm = parseInt(document.getElementById('tre_em_input').value) || 0;
        let tong = nguoiLon + treEm;
        document.getElementById('tong_khach_input').value = tong;
        return tong;
    }

    function timBanTrong() {
        let inpTime = document.getElementById('gio_den_input');
        let selBan = document.getElementById('ban_id_select');
        let soKhachVal = updateTongKhach(); 

        if (!inpTime.value || soKhachVal < 1) {
            selBan.innerHTML = '<option value="" disabled selected>-- Vui lòng nhập số lượng khách trước --</option>';
            selBan.disabled = true;
            return;
        }

        selBan.disabled = true;
        selBan.innerHTML = '<option value="">⏳ Đang quét lịch trống...</option>';
        document.getElementById('msgBanLimited').classList.add('d-none');

        let url = `${CHECK_URL}?time=${inpTime.value}&so_khach=${soKhachVal}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                selBan.innerHTML = '';
                selBan.disabled = false;

                if (!data || data.length === 0) {
                    selBan.innerHTML = '<option value="">❌ Hết bàn phù hợp (Kể cả chèn giờ)</option>';
                    return;
                }

                let defOpt = document.createElement('option');
                defOpt.value = "";
                defOpt.text = "-- Chọn bàn (Ưu tiên bàn trống) --";
                defOpt.disabled = true;
                defOpt.selected = true;
                selBan.appendChild(defOpt);

                let soKhach = soKhachVal;
                let bestBanId = null;
                let bestBanGhe = Infinity;
                let bestBanIsFree = false;

                data.forEach(ban => {
                    let opt = document.createElement('option');
                    opt.value = ban.id;
                    
                    if (ban.trang_thai === 'free') {
                        opt.text = `✅  ${ban.so_ban} (${ban.so_ghe} ghế) - ${ban.message}`;
                        opt.className = 'opt-free';
                    } else {
                        opt.text = `⚠️ Bàn ${ban.so_ban} (${ban.so_ghe} ghế) - ${ban.message}`;
                        opt.className = 'opt-limited';
                        opt.setAttribute('data-limited', 'true'); 
                    }

                    opt.setAttribute('data-so-ghe', ban.so_ghe);
                    opt.setAttribute('data-trang-thai', ban.trang_thai);

                    if (OLD_BAN_ID == ban.id) {
                        opt.selected = true;
                        if (ban.trang_thai !== 'free') document.getElementById('msgBanLimited').classList.remove('d-none');
                    }
                    selBan.appendChild(opt);

                    if (ban.so_ghe >= soKhach) {
                        let isFree = (ban.trang_thai === 'free');
                        let soGhe = ban.so_ghe;
                        
                        if (!bestBanId || 
                            (isFree && !bestBanIsFree) || 
                            (isFree === bestBanIsFree && soGhe < bestBanGhe)) {
                            bestBanId = ban.id;
                            bestBanGhe = soGhe;
                            bestBanIsFree = isFree;
                        }
                    }
                });

                if (!OLD_BAN_ID && bestBanId) {
                    selBan.value = bestBanId;
                    let selectedOpt = selBan.options[selBan.selectedIndex];
                    if (selectedOpt && selectedOpt.getAttribute('data-limited') === 'true') {
                        document.getElementById('msgBanLimited').classList.remove('d-none');
                    } else {
                        document.getElementById('msgBanLimited').classList.add('d-none');
                    }
                }
            })
            .catch(err => {
                console.error(err);
                selBan.innerHTML = '<option value="">⚠️ Lỗi hệ thống kết nối</option>';
                selBan.disabled = false;
            });
    }

    // Sự kiện khi chọn bàn
    document.getElementById('ban_id_select').addEventListener('change', function() {
        let selectedOpt = this.options[this.selectedIndex];
        let msgBox = document.getElementById('msgBanLimited');
        
        if (selectedOpt && selectedOpt.getAttribute('data-limited') === 'true') {
            msgBox.classList.remove('d-none');
        } else {
            msgBox.classList.add('d-none');
        }
    });

    // --- 3. INIT & LISTENERS ---
    document.addEventListener('DOMContentLoaded', function() {
        // Auto Set Time Now
        let inpTime = document.getElementById('gio_den_input');
        if (inpTime && !inpTime.value) {
            let now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            inpTime.value = now.toISOString().slice(0, 16);
        }

        // Listeners thay đổi số khách
        ['nguoi_lon_input', 'tre_em_input'].forEach(id => {
            let el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function() {
                    timBanTrong();
                    autoUpdateComboQuantity();
                });
                el.addEventListener('keyup', function() {
                    timBanTrong();
                    autoUpdateComboQuantity();
                });
            }
        });

        // Run check ngay khi load
        setTimeout(timBanTrong, 300);

        // Tự động cập nhật combo quantity khi trang load
        setTimeout(function() {
            if (selGia && selGia.value) {
                autoUpdateComboQuantity();
            }
        }, 500);

        // Handle Submit Form -> Gom data Combo vào input ẩn
        let form = document.getElementById('datBanForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                let cartItems = [];
                document.querySelectorAll('.combo-qty-input').forEach((input) => {
                    let qty = parseInt(input.value) || 0;
                    let key = input.getAttribute('data-combo-key');
                    
                    if (qty > 0) {
                        cartItems.push({ key: key, quantity: qty });
                    }
                });
                document.getElementById('cart_data').value = JSON.stringify(cartItems);
            });
        }
    });
</script>
@endsection