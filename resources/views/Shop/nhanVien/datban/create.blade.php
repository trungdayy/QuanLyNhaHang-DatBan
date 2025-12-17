@extends('layouts.shop.layout-nhanvien')
@section('title', 'Tạo đặt bàn mới')

@section('content')
{{-- 1. IMPORT FONTS & ICONS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">

{{-- 2. CSS STYLING --}}
<style>
    :root { --primary: #fea116; --primary-dark: #d98a12; --dark: #0f172b; --white: #ffffff; --text-main: #1e293b; --text-sub: #64748b; --bg-light: #f8f9fa; --radius: 8px; --shadow-card: 0 10px 30px -5px rgba(0, 0, 0, 0.05); --anim-fast: 0.2s ease; --danger: #dc2626; --warning: #d97706; --success: #16a34a; }
    
    body { font-family: 'Nunito', sans-serif; background-color: var(--bg-light); color: var(--text-main); }
    .section-title { color: var(--primary); font-family: 'Heebo'; font-weight: 700; font-size: 1rem; text-transform: uppercase; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
    
    .main-card { background: var(--white); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-card); border: 1px solid #f1f5f9; max-width: 1100px; margin: 0 auto; }
    .card-header-custom { background: var(--dark); color: var(--white); padding: 18px 25px; display: flex; justify-content: space-between; align-items: center; }
    .header-title { margin: 0; font-family: 'Heebo'; font-weight: 800; text-transform: uppercase; font-size: 1.1rem; letter-spacing: 0.5px; }
    
    .section-box { background: #fff; padding: 25px; height: 100%; border-right: 1px solid #f1f5f9; }
    
    /* Form Elements */
    .form-label-custom { font-size: 0.85rem; font-weight: 700; color: var(--text-sub); margin-bottom: 6px; display: block; }
    .required-star { color: var(--danger); margin-left: 3px; }
    .form-control-custom, .form-select-custom { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.95rem; color: var(--dark); font-weight: 600; transition: var(--anim-fast); }
    .form-control-custom:focus, .form-select-custom:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(254, 161, 22, 0.15); }
    .form-control-custom[readonly] { background-color: #f8fafc; color: #94a3b8; cursor: not-allowed; }

    /* COMBO PICKER STYLES */
    .combo-picker-wrapper { border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; max-height: 350px; overflow-y: auto; background: #fff; }
    .combo-item { display: flex; align-items: center; padding: 12px 15px; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
    .combo-item:last-child { border-bottom: none; }
    .combo-item:hover { background-color: #fffbeb; }
    
    .combo-info { flex-grow: 1; padding-right: 15px; }
    .combo-name { font-size: 0.95rem; font-weight: 700; color: var(--dark); margin-bottom: 2px; display: block;}
    .combo-meta { font-size: 0.8rem; color: var(--text-sub); display: flex; gap: 10px; align-items: center; }
    .combo-price-tag { color: var(--primary-dark); font-weight: 700; }
    
    .combo-actions { display: flex; align-items: center; gap: 5px; }
    .btn-qty { width: 30px; height: 30px; border-radius: 50%; border: 1px solid #e2e8f0; background: #fff; color: var(--text-main); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; font-size: 0.8rem; }
    .btn-qty:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
    .input-qty { width: 40px; text-align: center; border: none; font-weight: 700; font-size: 1rem; color: var(--dark); background: transparent; }
    .input-qty:focus { outline: none; }

    /* Buttons */
    .btn-submit { background: var(--primary); color: var(--white); border: none; padding: 12px 30px; border-radius: 6px; font-weight: 800; font-family: 'Heebo'; text-transform: uppercase; font-size: 0.9rem; box-shadow: 0 4px 15px rgba(254, 161, 22, 0.3); transition: var(--anim-fast); cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
    .btn-submit:hover { background: var(--primary-dark); transform: translateY(-2px); }
    .btn-cancel { background: #f1f5f9; color: var(--text-sub); border: 1px solid #e2e8f0; padding: 12px 25px; border-radius: 6px; font-weight: 700; font-family: 'Heebo'; text-transform: uppercase; font-size: 0.9rem; text-decoration: none; display: inline-block; }
    .btn-cancel:hover { background: #e2e8f0; color: var(--dark); }

    /* Select Option Colors */
    option.opt-free { color: var(--success); font-weight: bold; }
    option.opt-limited { color: var(--warning); font-weight: bold; }
    
    /* Utility */
    .d-none { display: none !important; }
</style>

<div class="container py-5">
    <div class="main-card">
        {{-- HEADER --}}
        <div class="card-header-custom">
            <div class="d-flex align-items-center">
                <h5 class="header-title"><i class="fa-solid fa-plus-circle me-2"></i> Tạo đơn Walk-in</h5>
            </div>
            <span class="badge bg-white text-dark px-3 py-2 rounded-pill fw-bold"><i class="fa-solid fa-clock"></i> {{ now()->format('H:i d/m/Y') }}</span>
        </div>

        <div class="card-body p-0">
            {{-- ERROR ALERTS --}}
            @if(session('error') || $errors->any())
            <div class="p-3" style="background-color: #dc2626; border-bottom: 2px solid #b91c1c;">
                @if(session('error'))
                <div class="text-white fw-bold" style="color: #ffffff !important;"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}</div>
                @endif
                @if($errors->any())
                <ul class="mb-0 small ps-4 mt-1" style="color: #ffffff !important;">
                    @foreach($errors->all() as $e) <li style="color: #ffffff !important;">{{ $e }}</li> @endforeach
                </ul>
                @endif
            </div>
            @endif

            {{-- FORM START --}}
            <form id="formDatBan" action="{{ route('nhanVien.datban.store') }}" method="post">
                @csrf
                <input type="hidden" name="cart_data" id="cart_data">

                <div class="row g-0">
                    {{-- CỘT TRÁI: THÔNG TIN KHÁCH --}}
                    <div class="col-md-5">
                        <div class="section-box">
                            <div class="section-title"><i class="fa-solid fa-user-tag"></i> Thông tin khách</div>

                            <div class="mb-3">
                                <label class="form-label-custom">Tên khách hàng <span class="required-star">*</span></label>
                                <input name="ten_khach" class="form-control-custom" value="{{ old('ten_khach') }}" required placeholder="Nhập tên khách...">
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label-custom">Số điện thoại <span class="required-star">*</span></label>
                                    <input name="sdt_khach" class="form-control-custom" value="{{ old('sdt_khach') }}" required placeholder="09xxxx...">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label-custom">Email (Tùy chọn)</label>
                                    <input name="email_khach" class="form-control-custom" type="email" value="{{ old('email_khach') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label-custom">Người lớn <span class="required-star">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="nguoi_lon" id="inpNguoiLon" class="form-control-custom text-center" value="{{ old('nguoi_lon', 1) }}" min="1" required>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label-custom">Trẻ em</label>
                                    <input type="number" name="tre_em" id="inpTreEm" class="form-control-custom text-center" value="{{ old('tre_em', 0) }}" min="0">
                                </div>
                            </div>
                            <input type="hidden" name="tong_khach" id="inpTongKhach" value="{{ old('tong_khach', 1) }}">

                            <div class="mb-3">
                                <label class="form-label-custom">Giờ Check-in</label>
                                <input type="datetime-local" name="gio_den" id="inpGioDen" class="form-control-custom" readonly required>
                            </div>
                            
<div class="mb-0">
    <label class="form-label-custom">Nhân viên phụ trách</label>
    <select name="nhan_vien_id" class="form-select-custom">
        @foreach ($nhanViens->where('vai_tro', 'phuc_vu') as $nv)
        <option value="{{ $nv->id }}" {{ (old('nhan_vien_id') == $nv->id || auth()->id() == $nv->id) ? 'selected' : '' }}>
            {{ $nv->ho_ten }} (Phục vụ)
        </option>
        @endforeach
    </select>
</div>
                        </div>
                    </div>

                    {{-- CỘT PHẢI: CHỌN BÀN & COMBO --}}
                    <div class="col-md-7">
                        <div class="section-box" style="border-right: none;">
                            <div class="section-title"><i class="fa-solid fa-chair"></i> Chọn Bàn & Thực Đơn</div>

                            {{-- SELECT BÀN --}}
                            <div class="mb-4">
                                <label class="form-label-custom">Chọn bàn <span class="required-star">*</span></label>
                                <select name="ban_id" id="selBanAn" class="form-select-custom" required size="5" style="height: auto; max-height: 150px;">
                                    <option value="" disabled selected>-- Vui lòng nhập số lượng khách trước --</option>
                                </select>
                                <div id="msgBanLimited" class="alert alert-warning mt-2 d-none py-2 px-3 small">
                                    <i class="fa-solid fa-clock me-1"></i> <strong>Lưu ý:</strong> Bàn này chỉ trống trong khoảng thời gian giới hạn. Hãy thông báo cho khách.
                                </div>
                            </div>

                            {{-- COMBO PICKER (LOGIC MỚI: ẨN KHI CHƯA CHỌN GIÁ) --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label-custom mb-0">Chọn Combo Buffet</label>
                                    <select id="selGiaCombo" class="form-select-custom py-1 px-2" style="width: auto; font-size: 0.85rem;">
                                        <option value="">-- Chọn mức giá --</option>
                                        @php $giaList = $combos->pluck('gia_co_ban')->unique()->sort(); @endphp
                                        @foreach($giaList as $gia)
                                            <option value="{{ $gia }}">{{ number_format($gia) }}đ</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Container chứa danh sách combo, mặc định ẩn --}}
                                <div id="combo-wrapper" class="d-none">
                                    <div class="combo-picker-wrapper" id="combo-picker-container">
                                        @forelse ($combos as $c)
                                        <div class="combo-item" data-combo-price="{{ $c->gia_co_ban }}">
                                            <div class="me-3 text-center" style="width: 40px;">
                                                <i class="fa-solid fa-utensils text-muted fa-lg"></i>
                                            </div>
                                            
                                            <div class="combo-info">
                                                <span class="combo-name">{{ $c->ten_combo }}</span>
                                                <div class="combo-meta">
                                                    <span class="combo-price-tag">{{ number_format($c->gia_co_ban) }}đ</span>
                                                    <span><i class="fa-regular fa-clock me-1"></i>{{ $c->thoi_luong_phut }}p</span>
                                                </div>
                                            </div>

                                            <div class="combo-actions">
                                                <button type="button" class="btn-qty btn-minus"><i class="fa-solid fa-minus"></i></button>
                                                <input type="number" 
                                                       class="input-qty combo-input"
                                                       data-combo-id="{{ $c->id }}"
                                                       data-combo-key="combo_{{ $c->id }}"
                                                       value="0" min="0" max="100" readonly>
                                                <button type="button" class="btn-qty btn-plus"><i class="fa-solid fa-plus"></i></button>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center py-4 text-muted">
                                            <p class="small">Chưa có dữ liệu Combo.</p>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Thông báo khi chưa chọn giá --}}
                                <div id="msgChonGia" class="text-center py-4 border rounded bg-white text-muted">
                                    <i class="fa-solid fa-arrow-up mb-2"></i><br>
                                    Vui lòng chọn mức giá để xem thực đơn.
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label-custom">Ghi chú đơn</label>
                                <textarea name="ghi_chu" class="form-control-custom" rows="1" placeholder="VD: Khách dị ứng tôm...">{{ old('ghi_chu') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FOOTER ACTIONS --}}
                <div class="p-4 bg-light border-top text-center d-flex justify-content-center align-items-center gap-3">
                    <a href="{{ route('nhanVien.datban.index') }}" class="btn-cancel">Hủy bỏ</a>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-check-circle"></i> XÁC NHẬN TẠO ĐƠN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT XỬ LÝ LOGIC --}}
<script>
    const CHECK_URL = "{{ url('/nhanVien/dat-ban/check-ban-trong') }}";
    const OLD_BAN_ID = "{{ old('ban_id') }}";

    // Hàm tính tổng số khách (Định nghĩa trước để dùng ở các nơi khác)
    function getTongKhach() {
        let nguoiLon = parseInt(document.getElementById('inpNguoiLon').value) || 0;
        let treEm = parseInt(document.getElementById('inpTreEm').value) || 0;
        return nguoiLon + treEm;
    }

    // Hàm tự động cập nhật số lượng combo tối thiểu
    function autoUpdateComboQuantity() {
        let tongKhach = getTongKhach();
        if (tongKhach < 1) return; // Không có khách thì không làm gì

        // Lấy tất cả combo items và kiểm tra xem có đang hiển thị không
        let allCombos = document.querySelectorAll('#combo-picker-container .combo-item');
        
        allCombos.forEach(item => {
            // Kiểm tra xem combo có đang hiển thị không (không bị ẩn)
            let style = window.getComputedStyle(item);
            if (style.display !== 'none') {
                let input = item.querySelector('.input-qty');
                if (input) {
                    let currentVal = parseInt(input.value) || 0;
                    // Nếu số lượng hiện tại < tổng khách, tự động tăng lên
                    if (currentVal < tongKhach) {
                        input.value = tongKhach;
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
            let list = document.querySelectorAll('#combo-picker-container .combo-item');

            // Bước 1: Reset toàn bộ số lượng về 0 trước khi lọc
            // Để đảm bảo không bị sót combo đã chọn ở mức giá cũ khi submit
            list.forEach(item => {
                let input = item.querySelector('.combo-input');
                if (input) input.value = 0; 
                item.style.display = 'none'; // Ẩn hết trước
            });

            // Bước 2: Xử lý hiển thị
            if (!selectedPrice) {
                // Nếu không chọn giá -> Ẩn list, Hiện thông báo
                comboWrapper.classList.add('d-none');
                msgChonGia.classList.remove('d-none');
            } else {
                // Nếu đã chọn giá -> Hiện list, Ẩn thông báo
                comboWrapper.classList.remove('d-none');
                msgChonGia.classList.add('d-none');

                // Bước 3: Chỉ hiện combo đúng giá
                let tongKhach = getTongKhach();
                let firstVisibleCombo = null;
                
                list.forEach(item => {
                    let price = item.getAttribute('data-combo-price');
                    // So sánh lỏng (==) vì value là string, data là string/number
                    if (Number(selectedPrice) == Number(price)) {
                        item.style.display = "flex";
                        // Lưu combo đầu tiên được hiển thị
                        if (!firstVisibleCombo) {
                            firstVisibleCombo = item;
                        }
                    }
                });

                // Bước 4: Tự động đặt số lượng tối thiểu cho combo đầu tiên (nếu có khách)
                if (firstVisibleCombo && tongKhach > 0) {
                    let input = firstVisibleCombo.querySelector('.input-qty');
                    if (input) {
                        input.value = tongKhach;
                    }
                }
            }
        });
    }

    // Logic Tăng/Giảm số lượng (Dùng Delegation để code gọn)
    document.addEventListener('click', function(e) {
        let tongKhach = getTongKhach();
        
        if (e.target.closest('.btn-plus')) {
            let btn = e.target.closest('.btn-plus');
            let input = btn.parentElement.querySelector('.input-qty');
            let currentVal = parseInt(input.value) || 0;
            if (currentVal < 100) input.value = currentVal + 1;
        }
        
        if (e.target.closest('.btn-minus')) {
            let btn = e.target.closest('.btn-minus');
            let input = btn.parentElement.querySelector('.input-qty');
            let currentVal = parseInt(input.value) || 0;
            // Chỉ cho phép giảm nếu vẫn >= tổng số khách
            if (currentVal > tongKhach) {
                input.value = currentVal - 1;
            } else if (currentVal === tongKhach && tongKhach > 0) {
                // Nếu đang ở mức tối thiểu, không cho giảm
                // Có thể hiển thị thông báo hoặc không làm gì
                return;
            }
        }
    });

    // Ngăn người dùng nhập số lượng < tổng khách
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('input-qty')) {
            let tongKhach = getTongKhach();
            let inputVal = parseInt(e.target.value) || 0;
            
            // Nếu nhập số < tổng khách, tự động đặt về tổng khách
            if (inputVal < tongKhach && tongKhach > 0) {
                e.target.value = tongKhach;
            }
        }
    });

    // --- 2. LOGIC TÌM BÀN (THÔNG MINH - GAP FILLING) ---
    function updateTongKhach() {
        let nguoiLon = parseInt(document.getElementById('inpNguoiLon').value) || 0;
        let treEm = parseInt(document.getElementById('inpTreEm').value) || 0;
        let tong = nguoiLon + treEm;
        document.getElementById('inpTongKhach').value = tong;
        return tong;
    }

    function timBanTrong() {
        let inpTime = document.getElementById('inpGioDen');
        let selBan = document.getElementById('selBanAn');
        let soKhachVal = updateTongKhach(); 

        if (!inpTime.value || soKhachVal < 1) {
            selBan.innerHTML = '<option value="" disabled selected>-- Vui lòng nhập số lượng khách trước --</option>';
            selBan.disabled = true;
            return;
        }

        selBan.disabled = true;
        selBan.innerHTML = '<option value="">⏳ Đang quét lịch trống...</option>';
        document.getElementById('msgBanLimited').classList.add('d-none'); // Ẩn cảnh báo cũ

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

                // Placeholder
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
                    
                    // Xử lý hiển thị thông minh dựa trên response từ Controller
                    if (ban.trang_thai === 'free') {
                        // Bàn trống hoàn toàn
                        opt.text = `✅  ${ban.so_ban} (${ban.so_ghe} ghế) - ${ban.message}`;
                        opt.className = 'opt-free';
                    } else {
                        // Bàn chèn giờ (Gap)
                        opt.text = `⚠️ Bàn ${ban.so_ban} (${ban.so_ghe} ghế) - ${ban.message}`;
                        opt.className = 'opt-limited';
                        // Lưu data vào option để JS check khi change
                        opt.setAttribute('data-limited', 'true'); 
                    }

                    // Lưu thông tin bàn vào option để dùng cho auto-select
                    opt.setAttribute('data-so-ghe', ban.so_ghe);
                    opt.setAttribute('data-trang-thai', ban.trang_thai);

                    if (OLD_BAN_ID == ban.id) {
                        opt.selected = true;
                        // Trigger event change thủ công nếu cần để hiện warning
                        if (ban.trang_thai !== 'free') document.getElementById('msgBanLimited').classList.remove('d-none');
                    }
                    selBan.appendChild(opt);

                    // Logic tự động chọn bàn phù hợp nhất
                    // Chỉ xét bàn có số ghế >= số khách
                    if (ban.so_ghe >= soKhach) {
                        let isFree = (ban.trang_thai === 'free');
                        let soGhe = ban.so_ghe;
                        
                        // Ưu tiên: 1) Bàn free hơn limited, 2) Số ghế gần nhất với số khách
                        if (!bestBanId || 
                            (isFree && !bestBanIsFree) || // Ưu tiên free
                            (isFree === bestBanIsFree && soGhe < bestBanGhe)) { // Cùng loại thì chọn số ghế nhỏ hơn
                            bestBanId = ban.id;
                            bestBanGhe = soGhe;
                            bestBanIsFree = isFree;
                        }
                    }
                });

                // Tự động chọn bàn phù hợp nhất (nếu không có OLD_BAN_ID)
                if (!OLD_BAN_ID && bestBanId) {
                    selBan.value = bestBanId;
                    // Trigger change event để hiển thị warning nếu là bàn limited
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

    // Sự kiện khi chọn bàn -> Hiển thị cảnh báo nếu là bàn Limited
    document.getElementById('selBanAn').addEventListener('change', function() {
        let selectedOpt = this.options[this.selectedIndex];
        let msgBox = document.getElementById('msgBanLimited');
        
        if (selectedOpt.getAttribute('data-limited') === 'true') {
            msgBox.classList.remove('d-none');
        } else {
            msgBox.classList.add('d-none');
        }
    });

    // --- 3. INIT & LISTENERS ---
    document.addEventListener('DOMContentLoaded', function() {
        // Auto Set Time Now
        let inpTime = document.getElementById('inpGioDen');
        if (inpTime && !inpTime.value) {
            let now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            inpTime.value = now.toISOString().slice(0, 16);
        }

        // Listeners thay đổi số khách
        ['inpNguoiLon', 'inpTreEm'].forEach(id => {
            let el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', function() {
                    timBanTrong();
                    autoUpdateComboQuantity(); // Tự động cập nhật combo khi số khách thay đổi
                });
                el.addEventListener('keyup', function() {
                    timBanTrong();
                    autoUpdateComboQuantity(); // Tự động cập nhật combo khi số khách thay đổi
                });
            }
        });

        // Run check ngay khi load (nếu có default value)
        setTimeout(timBanTrong, 300);

        // Tự động cập nhật combo quantity khi trang load (nếu đã chọn giá)
        setTimeout(function() {
            if (selGia && selGia.value) {
                autoUpdateComboQuantity();
            }
        }, 500);

        // Handle Submit Form -> Gom data Combo vào input ẩn
        let form = document.getElementById('formDatBan');
        if (form) {
            form.addEventListener('submit', function(e) {
                let cartItems = [];
                document.querySelectorAll('.combo-input').forEach((input) => {
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