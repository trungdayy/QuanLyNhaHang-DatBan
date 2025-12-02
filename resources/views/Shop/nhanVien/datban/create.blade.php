@extends('layouts.shop.layout-nhanvien')
@section('title', 'Tạo đặt bàn mới')

@section('content')
    {{-- 1. IMPORT FONTS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700;800&family=Nunito:wght@600;700;800&display=swap" rel-stylesheet">

    {{-- 2. CSS STYLING --}}
    <style>
        :root {
            --primary: #fea116; --primary-dark: #d98a12;
            --dark: #0f172b; --white: #ffffff;
            --text-main: #1e293b; --text-sub: #64748b;
            --bg-light: #f8f9fa;
            --radius: 8px;
            --shadow-card: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            --anim-fast: 0.2s ease;
            --danger: #dc3545;
        }

        /* --- Global Reset & Typefaces --- */
        .section-title {
            color: var(--primary); 
            font-family: 'Heebo'; 
            font-weight: 700; 
            font-size: 1rem;
            text-transform: uppercase; 
            margin-bottom: 15px; 
            display: flex; 
            align-items: center; 
            gap: 8px;
            border: none !important;
            text-decoration: none !important;
        }

        .section-title::before, 
        .section-title::after {
            content: none !important;
            display: none !important;
            width: 0 !important;
        }

        body { font-family: 'Nunito', sans-serif; background-color: var(--bg-light); color: var(--text-main); }
        h5, h6, strong, .font-heading { font-family: 'Heebo', sans-serif; }

        /* --- CARD CONTAINER --- */
        .main-card {
            background: var(--white); border-radius: var(--radius); overflow: hidden;
            box-shadow: var(--shadow-card); border: 1px solid #f1f5f9;
            max-width: 1000px; margin: 0 auto; /* Căn giữa màn hình */
        }

        .card-header-custom {
            background: var(--dark); color: var(--white); padding: 20px 25px;
            display: flex; justify-content: space-between; align-items: center;
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.1) 1px, transparent 0);
            background-size: 20px 20px;
        }
        .header-title { margin: 0; font-family: 'Heebo'; font-weight: 800; text-transform: uppercase; font-size: 1.2rem; letter-spacing: 0.5px; }
        .header-badge { background: rgba(254, 161, 22, 0.2); color: var(--primary); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }

        /* --- FORM ELEMENTS --- */
        .section-box {
            background: #fff; padding: 20px; height: 100%;
            border-right: 1px solid #f1f5f9; /* Đường kẻ giữa */
        }
        
        .form-label-custom {
            font-size: 0.8rem; font-weight: 700; color: var(--text-sub); text-transform: uppercase; margin-bottom: 6px; display: block;
        }
        .required-star { color: var(--danger); margin-left: 3px; }

        .form-control-custom, .form-select-custom {
            width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px;
            font-size: 0.95rem; color: var(--dark); font-weight: 600; transition: var(--anim-fast);
            background-color: #fff;
        }
        .form-control-custom:focus, .form-select-custom:focus {
            outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(254, 161, 22, 0.15);
        }
        
        /* Input Readonly (Giờ đến) */
        .form-control-custom[readonly] {
            background-color: #f1f5f9; color: #64748b; cursor: not-allowed; border-color: #e2e8f0;
        }

        /* Combo Picker Styles */
        .combo-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #f1f5f9;
        }
        .combo-item:last-child { border-bottom: none; }
        .combo-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
            flex-grow: 1;
            padding-right: 10px;
        }
        .combo-price {
            font-size: 0.8rem;
            color: var(--text-sub);
            white-space: nowrap;
        }
        .combo-quantity input {
            width: 50px;
            text-align: center;
            padding: 5px 8px;
            font-size: 0.9rem;
            height: auto;
        }

        /* --- BUTTONS --- */
        .btn-submit {
            background: var(--primary); color: var(--white); border: none; padding: 12px 30px;
            border-radius: 6px; font-weight: 800; font-family: 'Heebo'; text-transform: uppercase;
            font-size: 0.9rem; box-shadow: 0 4px 15px rgba(254, 161, 22, 0.3); transition: var(--anim-fast);
            cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-submit:hover { background: var(--primary-dark); transform: translateY(-2px); }

        .btn-cancel {
            background: #f1f5f9; color: var(--text-sub); border: 1px solid #e2e8f0; padding: 12px 25px;
            border-radius: 6px; font-weight: 700; font-family: 'Heebo'; text-transform: uppercase;
            font-size: 0.9rem; text-decoration: none; transition: var(--anim-fast); display: inline-block;
        }
        .btn-cancel:hover { background: #e2e8f0; color: var(--dark); }

    </style>

    <div class="container py-5">
        
        <div class="main-card">
            {{-- HEADER --}}
            <div class="card-header-custom">
                <h5 class="header-title"><i class="fa-solid fa-plus-circle me-2"></i> Tạo đặt bàn tại chỗ</h5>
                <span class="header-badge"><i class="fa-solid fa-bolt"></i> Khách đến ngay</span>
            </div>

            <div class="card-body p-0">
                {{-- ERROR ALERTS --}}
                @if(session('error') || $errors->any())
                    <div class="p-3 bg-danger bg-opacity-10 border-bottom border-danger border-opacity-25">
                        @if(session('error')) 
                            <div class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}</div>
                        @endif
                        @if($errors->any())
                            <ul class="mb-0 text-danger small ps-4 mt-1">
                                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                            </ul>
                        @endif
                    </div>
                @endif

                <form action="{{ route('nhanVien.datban.store') }}" method="post">
                    @csrf
                    <div class="row g-0">
                        
                        {{-- CỘT TRÁI: THÔNG TIN KHÁCH --}}
                        <div class="col-md-6">
                            <div class="section-box">
                                <div class="section-title"><i class="fa-regular fa-id-card"></i> Thông tin khách hàng</div>
                                
                                <div class="mb-3">
                                    <label class="form-label-custom">Tên khách hàng <span class="required-star">*</span></label>
                                    <input name="ten_khach" class="form-control-custom" value="{{ old('ten_khach') }}" required placeholder="VD: Anh Nam, Chị Lan...">
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label-custom">Số điện thoại <span class="required-star">*</span></label>
                                        <input name="sdt_khach" class="form-control-custom" value="{{ old('sdt_khach') }}" required placeholder="09xxxx...">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label-custom">Email</label>
                                        <input name="email_khach" class="form-control-custom" type="email" value="{{ old('email_khach') }}" placeholder="Không bắt buộc">
                                    </div>
                                </div>

                                {{-- CHIA NGƯỜI LỚN & TRẺ EM (Đã sửa từ so_khach) --}}
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label-custom">Số lượng người lớn <span class="required-star">*</span></label>
                                        <input type="number" name="nguoi_lon" id="inpNguoiLon" class="form-control-custom" value="{{ old('nguoi_lon', 1) }}" min="0" required>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label-custom">Số lượng trẻ em</label>
                                        <input type="number" name="tre_em" id="inpTreEm" class="form-control-custom" value="{{ old('tre_em', 0) }}" min="0">
                                    </div>
                                </div>

                                <input type="hidden" name="tong_khach" id="inpTongKhach" value="{{ old('tong_khach', 1) }}">
                                
                                <div class="mb-3">
                                    <label class="form-label-custom">Giờ Check-in</label>
                                    <input type="datetime-local" name="gio_den" id="inpGioDen" class="form-control-custom" readonly required>
                                </div>
                                <div class="alert alert-info d-flex align-items-center p-2 small mb-0" style="background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1; border-radius: 6px;">
                                    <i class="fa-solid fa-clock me-2"></i> Giờ đến được tự động lấy theo thời gian thực (Giờ hiện tại).
                                </div>
                            </div>
                        </div>

                        {{-- CỘT PHẢI: CHỌN BÀN & COMBO --}}
                        <div class="col-md-6">
                            <div class="section-box" style="border-right: none;">
                                <div class="section-title"><i class="fa-solid fa-utensils"></i> Bàn & Combo</div>

                                <div class="mb-3">
                                    <label class="form-label-custom">Chọn bàn trống <span class="required-star">*</span></label>
                                    <select name="ban_id" id="selBanAn" class="form-select-custom" required>
                                        <option value="">-- Đang tải dữ liệu... --</option>
                                    </select>
                                </div>
                                
                                {{-- COMBO DYNAMIC SELECT (Đã sửa từ select đơn) --}}
                                <div class="mb-3">
                                    <label class="form-label-custom">Chọn Combo Buffet & Số suất</label>
                                    <div id="combo-picker-container" style="max-height: 250px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px;">
                                        @forelse ($combos as $c)
                                            <div class="combo-item">
                                                <div class="combo-name">
                                                    {{ $c->ten_combo }} <span class="combo-price">({{ number_format($c->gia_co_ban) }}đ | {{ $c->thoi_luong_phut }}p)</span>
                                                </div>
                                                <div class="combo-quantity">
                                                    <input 
                                                        type="number" 
                                                        class="form-control-custom combo-input"
                                                        data-combo-id="{{ $c->id }}"
                                                        value="{{ old("combos.{$c->id}.so_luong", 0) }}" 
                                                        min="0"
                                                        max="100"
                                                        onchange="validateComboQuantity(this)"
                                                    >
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted small mb-0 text-center py-3">Chưa có Combo nào đang bán.</p>
                                        @endforelse
                                    </div>
                                    {{-- Input hidden này chỉ là placeholder, dữ liệu thật được tạo trong JS --}}
                                    <input type="hidden" name="combos_data_placeholder" value="1"> 
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label-custom">Nhân viên phụ trách</label>
                                    <select name="nhan_vien_id" class="form-select-custom">
                                        @foreach ($nhanViens as $nv)
                                            <option value="{{ $nv->id }}" {{ (old('nhan_vien_id') == $nv->id || auth()->id() == $nv->id) ? 'selected' : '' }}>
                                                {{ $nv->ho_ten }} ({{ $nv->vai_tro }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label-custom">Ghi chú thêm</label>
                                    <textarea name="ghi_chu" class="form-control-custom" rows="2" placeholder="VD: Khách cần ghế trẻ em...">{{ old('ghi_chu') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FOOTER ACTIONS --}}
                    <div class="p-4 bg-light border-top text-center">
                        <button type="submit" class="btn-submit" onclick="prepareFormData(event)">
                            <i class="fa-solid fa-paper-plane"></i> LƯU & NHẬN BÀN
                        </button>
                        <a href="{{ route('nhanVien.datban.index') }}" class="btn-cancel ms-2">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT XỬ LÝ LOGIC --}}
    <script>
        var CHECK_URL = "{{ url('/nhanVien/dat-ban/check-ban-trong') }}"; 
        var OLD_BAN_ID = "{{ old('ban_id') }}";

        // Hàm cập nhật tổng số khách
        function updateTongKhach() {
            var inpNguoiLon = document.getElementById('inpNguoiLon');
            var inpTreEm = document.getElementById('inpTreEm');
            var nguoiLon = parseInt(inpNguoiLon ? inpNguoiLon.value : 0) || 0;
            var treEm = parseInt(inpTreEm ? inpTreEm.value : 0) || 0;
            var tong = nguoiLon + treEm;
            
            var inpTongKhach = document.getElementById('inpTongKhach');
            if (inpTongKhach) {
                inpTongKhach.value = tong;
            }
            return tong;
        }

        // Hàm tìm bàn trống
        function timBanTrong() {
            var inpTime = document.getElementById('inpGioDen');
            var selBan = document.getElementById('selBanAn');
            var timeVal = inpTime.value;
            var soKhachVal = updateTongKhach(); // Lấy tổng số khách (Người lớn + Trẻ em)

            if (!timeVal || soKhachVal < 1) {
                selBan.innerHTML = '<option value="">-- Vui lòng nhập số khách --</option>';
                return;
            }

            // Loading state
            selBan.disabled = true;
            selBan.innerHTML = '<option value="">⏳ Đang tìm bàn phù hợp...</option>';

            // ĐÚNG ĐƯỜNG DẪN
            var url = CHECK_URL + "?time=" + timeVal + "&so_khach=" + soKhachVal;

            fetch(url)
                .then(res => res.ok ? res.json() : Promise.reject(res.status))
                .then(data => {
                    selBan.innerHTML = '';
                    selBan.disabled = false;

                    if (!data || data.length === 0) {
                        selBan.innerHTML = '<option value="">❌ Không có bàn trống phù hợp</option>';
                        return;
                    }

                    var defOpt = document.createElement('option');
                    defOpt.value = "";
                    defOpt.text = "-- Chọn bàn --";
                    selBan.appendChild(defOpt);

                    data.forEach(ban => {
                        var opt = document.createElement('option');
                        opt.value = ban.id;
                        // Cần sửa API ajaxCheckBanTrong để trả về tên khu vực nếu muốn hiển thị
                        opt.text = `Bàn ${ban.so_ban} (${ban.so_ghe} ghế)`; 
                        if(OLD_BAN_ID == ban.id) opt.selected = true;
                        selBan.appendChild(opt);
                    });
                })
                .catch(err => {
                    console.error("Lỗi tìm bàn:", err);
                    selBan.innerHTML = '<option value="">⚠️ Lỗi kết nối tìm bàn</option>';
                    selBan.disabled = false;
                });
        }
        
        // Chuẩn bị dữ liệu combo trước khi submit (đưa về dạng mảng)
        function prepareFormData(event) {
            var comboInputs = document.querySelectorAll('.combo-input');
            var form = document.querySelector('form'); 
            
            // Xóa các input hidden cũ nếu có (trong trường hợp submit lại)
            document.querySelectorAll('input[name^="combos["]').forEach(el => el.remove());

            var hasCombo = false;
            comboInputs.forEach(input => {
                var quantity = parseInt(input.value) || 0;
                var comboId = input.getAttribute('data-combo-id');

                if (quantity > 0) {
                    hasCombo = true;
                    // Tạo input hidden cho ID combo (combos[ID_COMBO][id])
                    var idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = `combos[${comboId}][id]`;
                    idInput.value = comboId;

                    // Tạo input hidden cho số lượng (combos[ID_COMBO][so_luong])
                    var qtyInput = document.createElement('input');
                    qtyInput.type = 'hidden';
                    qtyInput.name = `combos[${comboId}][so_luong]`;
                    qtyInput.value = quantity;
                    
                    // Thêm vào form trước khi submit
                    form.appendChild(idInput);
                    form.appendChild(qtyInput);
                }
            });

            // Nếu không có combo nào được chọn, Controller sẽ nhận mảng combos rỗng (nullable)
            // Khách có thể không chọn combo nếu họ định gọi món lẻ
            
            return true;
        }

        // Validate số lượng combo
        function validateComboQuantity(input) {
             if (parseInt(input.value) < 0) {
                input.value = 0;
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            var inpTime = document.getElementById('inpGioDen');
            var inpNguoiLon = document.getElementById('inpNguoiLon');
            var inpTreEm = document.getElementById('inpTreEm');
            
            // 1. Auto set current time
            if(inpTime) {
                var now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                // Kiểm tra nếu old('gio_den') không có (đơn mới) thì set giờ hiện tại
                if (!inpTime.value) {
                    inpTime.value = now.toISOString().slice(0, 16);
                }
            }

            // 2. Event Listeners cho số khách
            if(inpNguoiLon) {
                inpNguoiLon.addEventListener('change', timBanTrong);
                inpNguoiLon.addEventListener('keyup', timBanTrong); 
            }
            if(inpTreEm) {
                inpTreEm.addEventListener('change', timBanTrong);
                inpTreEm.addEventListener('keyup', timBanTrong); 
            }

            // 3. Init search
            if(inpTime && inpTime.value) setTimeout(timBanTrong, 200);
        });
    </script>
@endsection