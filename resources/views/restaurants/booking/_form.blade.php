{{-- ============================================================= --}}
{{-- 1. CSS TÙY CHỈNH (FULL) --}}
{{-- ============================================================= --}}
<style>
    /* Form Styles */
    .form-floating .form-control,
    .form-floating .form-select {
        border-radius: 12px;
        border: 1px solid #e0e0e0;
        height: 60px !important;
        /* Tăng chiều cao để chữ thoáng hơn */
        padding-top: 1.8rem;
        /* Đẩy nội dung xuống dưới nhãn */
        padding-bottom: 0.5rem;
        font-size: 1rem;
        font-weight: 600;
        color: #0F172B;
        box-shadow: none !important;
        /* Bỏ shadow mặc định */
        transition: border-color 0.2s;
    }

    /* Hiệu ứng khi focus (bấm vào) */
    .form-floating .form-control:focus,
    .form-floating .form-select:focus {
        border: 2px solid #FEA116;
        /* Viền cam đậm hơn */
        padding-top: 1.8rem;
        /* Giữ nguyên vị trí chữ */
    }

    /* Căn chỉnh Nhãn (Label) */
    .form-floating label {
        padding-top: 0.7rem;
        padding-left: 1rem;
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 500;
    }

    /* Căn chỉnh Icon trong nhãn để thẳng hàng dọc */
    .form-floating label i {
        width: 24px;
        /* Cố định chiều rộng icon */
        text-align: center;
        margin-right: 8px;
        color: #FEA116;
    }

    /* Cart Styles (Giữ nguyên) */
    .cart-item-card {
        transition: all 0.2s;
        border: 1px solid #f0f0f0;
    }

    .cart-item-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border-color: #ffe0b2;
    }

    /* Buttons (Giữ nguyên) */
    .btn-gradient-submit {
        background: linear-gradient(45deg, #FEA116, #FF8E53);
        border: none;
        color:z white;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 4px 15px rgba(254, 161, 22, 0.4);
        transition: all 0.3s;
    }

    .btn-gradient-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(254, 161, 22, 0.6);
        color: white;
    }
</style>

{{-- ============================================================= --}}
{{-- 2. HTML FORM (ĐÃ SỬA: CA SÁNG TỪ 7H) --}}
{{-- ============================================================= --}}
<form action="{{ $action }}" method="POST" id="bookingForm" class="booking-form-container p-2">
    @csrf
    {{-- Xử lý Method PUT cho trang Sửa --}}
    @if (isset($method) && ($method === 'PUT' || $method === 'PATCH'))
    @method($method)
    @endif

    {{-- Input ẩn chứa dữ liệu giỏ hàng --}}
    <input type="hidden" name="cart_data" id="cartDataInput">

    <div class="row g-4">
        <div class="col-12 text-center mb-2">
            <h4 class="text-uppercase fw-bold" style="color: #0F172B;">
                {{ isset($datBan) ? 'Cập Nhật Đặt Bàn' : 'Thông Tin Đặt Bàn' }}</h4>
            <div style="width: 50px; height: 3px; background: #FEA116; margin: 0 auto;"></div>
        </div>

        {{-- 1. THÔNG TIN KHÁCH --}}
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" name="ten_khach" class="form-control fw-bold text-dark" id="floatTen"
                    placeholder="Họ tên" value="{{ old('ten_khach', $datBan->ten_khach ?? '') }}" required>
                <label for="floatTen" class="text-muted"><i class="fa fa-user text-primary me-2"></i>Họ và tên</label>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" name="sdt_khach" class="form-control fw-bold text-dark" id="floatSdt"
                    placeholder="Số điện thoại" value="{{ old('sdt_khach', $datBan->sdt_khach ?? '') }}" required>
                <label for="floatSdt" class="text-muted"><i class="fa fa-phone text-primary me-2"></i>Số điện
                    thoại</label>
            </div>
        </div>

        {{-- 2. THỜI GIAN --}}
        <div class="col-md-4">
            <div class="form-floating">
                <input type="date" name="booking_date" class="form-control" id="bookingDate"
                    value="{{ old('booking_date', isset($datBan->gio_den) ? \Carbon\Carbon::parse($datBan->gio_den)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                    min="{{ date('Y-m-d') }}"
                    required>
                <label for="bookingDate"><i class="fa fa-calendar-alt"></i>Ngày đến</label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-floating">
                <select name="ca_dat" class="form-select" id="bookingShiftSelect" required>
                    {{-- ĐÃ SỬA LABEL HIỂN THỊ --}}
                    <option value="trua"
                        {{ (old('ca_dat') == 'trua' || (isset($datBan) && \Carbon\Carbon::parse($datBan->gio_den)->hour < 15)) ? 'selected' : '' }}>
                        Sáng/Trưa (07:00 - 14:00)
                    </option>
                    <option value="toi"
                        {{ (old('ca_dat') == 'toi' || (isset($datBan) && \Carbon\Carbon::parse($datBan->gio_den)->hour >= 15)) ? 'selected' : '' }}>
                        Tối (17:00 - 22:00)
                    </option>
                </select>
                <label for="bookingShiftSelect"><i class="fa fa-sun"></i>Chọn Ca</label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-floating">
                <select name="booking_time" class="form-select" id="bookingTimeSelect" required
                    data-selected="{{ old('booking_time', isset($datBan->gio_den) ? \Carbon\Carbon::parse($datBan->gio_den)->format('H:i') : '') }}">
                    <option value="">-- Chọn giờ --</option>
                </select>
                <label for="bookingTimeSelect"><i class="fa fa-clock"></i>Khung giờ</label>
            </div>
        </div>

        {{-- 3. SỐ NGƯỜI --}}
        <div class="col-md-6">
            <div class="bg-light rounded-3 p-2 border">
                <label class="small fw-bold text-muted ps-2 mb-1">Người lớn</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i
                            class="fa fa-users text-primary fs-5"></i></span>
                    <input type="number" name="nguoi_lon"
                        class="form-control border-0 bg-transparent fw-bold fs-5 text-dark"
                        value="{{ old('nguoi_lon', $datBan->nguoi_lon ?? 1) }}" min="1" required>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-light rounded-3 p-2 border">
                <label class="small fw-bold text-muted ps-2 mb-1">Trẻ em</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i
                            class="fa fa-child text-primary fs-5"></i></span>
                    <input type="number" name="tre_em"
                        class="form-control border-0 bg-transparent fw-bold fs-5 text-dark"
                        value="{{ old('tre_em', $datBan->tre_em ?? 0) }}" min="0">
                </div>
            </div>
        </div>

        {{-- 4. GIỎ HÀNG --}}
        <div class="col-12 mt-4">
                        <div class="d-flex justify-content-between align-items-end mb-3 px-1">
                            <h6 class="fw-bold text-dark mb-0"><i class="fa fa-utensils text-primary me-2"></i>Thực đơn đã chọn</h6>
                        </div>
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            {{-- Phần danh sách món --}}
                            <div class="card-body p-0 bg-white" style="max-height: 400px; overflow-y: auto;">
                                <div id="bookingCartContainer">
                                    <div class="text-center py-5 text-muted">
                                        <p class="mb-0 small">Đang tải giỏ hàng...</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Phần Footer --}}
                            <div id="bookingCartTotalSection" class="card-footer bg-light border-top p-3 d-none">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-end w-100">
                                        <span class="text-muted small">Tạm tính:</span>
                                        <span id="bookingDisplayTotal" class="fw-bold text-danger fs-5 ms-2">0 đ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
        </div>

        {{-- 5. GHI CHÚ --}}
        <div class="col-12">
            <div class="form-floating">
                <textarea class="form-control" name="ghi_chu" id="floatNote" placeholder="Ghi chú"
                    style="height: 100px;">{{ old('ghi_chu', $datBan->ghi_chu ?? '') }}</textarea>
                <label for="floatNote" class="text-muted"><i class="fa fa-sticky-note text-primary me-2"></i>Ghi chú
                    thêm</label>
            </div>
        </div>

        {{-- 6. NÚT SUBMIT --}}
        <div class="col-12 mt-4 mb-3">
            <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-pill btn-gradient-submit"
                id="btnSubmitBooking">
                <i class="fa fa-paper-plane me-2"></i> {{ isset($datBan) ? 'LƯU CẬP NHẬT' : 'XÁC NHẬN ĐẶT BÀN' }}
            </button>
        </div>
    </div>
</form>

{{-- ============================================================= --}}
{{-- 3. JAVASCRIPT LOGIC (ĐÃ SỬA: CA SÁNG START TỪ 7H) --}}
{{-- ============================================================= --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- CẤU HÌNH ---
        const CART_KEY = "oceanCart";
        const container = document.getElementById('bookingCartContainer');
        const totalSection = document.getElementById('bookingCartTotalSection');
        const totalDisplay = document.getElementById('bookingDisplayTotal');
        const formInput = document.getElementById('cartDataInput');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const countDeleteSpan = document.getElementById('countDelete');
        const selectAllCheckbox = document.getElementById('selectAllCart');
        const bookingForm = document.getElementById('bookingForm'); 

        const formatMoney = (amount) => parseInt(amount).toLocaleString('vi-VN') + ' đ';

        // ---------------------------------------------------------
        // 1. LOGIC XỬ LÝ CA & GIỜ (ĐÃ SỬA START TỪ 7H)
        // ---------------------------------------------------------
        const dateInput = document.getElementById('bookingDate');
        const shiftSelect = document.getElementById('bookingShiftSelect'); // Select Ca
        const timeSelect = document.getElementById('bookingTimeSelect');   // Select Giờ

        // Cấu hình khung giờ cho từng ca (Đơn vị: Giờ)
        // start: Giờ bắt đầu, end: Giờ kết thúc, minStart: Phút bắt đầu của giờ đầu tiên
        const SHIFT_CONFIG = {
            'trua': { start: 7, end: 14, minStart: 0 }, // SỬA: 07:00 -> 14:00
            'toi':  { start: 17, end: 22, minStart: 0 }   // 17:00 -> 22:00
        };

        function generateTimeSlots() {
            if (!dateInput || !shiftSelect || !timeSelect) return;

            const selectedDateVal = dateInput.value;
            const selectedShift = shiftSelect.value; // 'trua' hoặc 'toi'
            const selectedTimeVal = timeSelect.getAttribute('data-selected'); // Giờ cũ (nếu có)

            // 1. Xóa hết option cũ (Trừ cái đầu tiên "-- Chọn giờ --")
            while (timeSelect.options.length > 1) {
                timeSelect.remove(1);
            }

            if (!selectedDateVal || !selectedShift) return;

            // 2. Lấy thời gian hiện tại
            const now = new Date();
            const selectedDate = new Date(selectedDateVal);
            // Kiểm tra xem ngày chọn có phải là "hôm nay" không
            const isToday = selectedDate.setHours(0,0,0,0) === new Date().setHours(0,0,0,0);
            const currentHour = now.getHours();
            const currentMin = now.getMinutes();

            // 3. Lấy cấu hình của Ca đang chọn
            const config = SHIFT_CONFIG[selectedShift];
            if (!config) return; // Nếu chưa chọn ca thì thôi

            let hasSlot = false;

            // 4. Vòng lặp tạo giờ
            for (let h = config.start; h <= config.end; h++) {
                // Trong mỗi giờ, chạy 2 mốc: 00 và 30
                for (let m = 0; m < 60; m += 30) {
                    
                    // -- LOGIC LỌC GIỜ THEO CA (ĐÃ CẬP NHẬT) --
                    
                    // Ca Sáng/Trưa: Nếu giờ là giờ bắt đầu (7h) thì kiểm tra minStart
                    if (selectedShift === 'trua' && h === config.start && m < config.minStart) continue;
                    
                    // Ca Trưa: Kết thúc lúc 14:00 (Bỏ qua 14:30)
                    if (selectedShift === 'trua' && h === 14 && m > 0) continue;

                    // Ca Tối: Kết thúc lúc 22:00 (Bỏ qua 22:30)
                    if (selectedShift === 'toi' && h === 22 && m > 0) continue;


                    // -- LOGIC LỌC GIỜ THEO THỜI GIAN THỰC (NẾU LÀ HÔM NAY) --
                    if (isToday) {
                        // Nếu giờ < giờ hiện tại -> Bỏ qua
                        if (h < currentHour) continue;
                        // Nếu giờ == giờ hiện tại NHƯNG phút < (phút hiện tại + 30 phút buffer) -> Bỏ qua
                        // Ví dụ: Bây giờ là 10:15, khách phải đặt từ 10:45 trở đi
                        if (h === currentHour && m < (currentMin + 30)) continue;
                    }

                    // -- TẠO OPTION --
                    const hourStr = h.toString().padStart(2, '0');
                    const minStr = m.toString().padStart(2, '0');
                    const timeString = `${hourStr}:${minStr}`;

                    const option = document.createElement('option');
                    option.value = timeString;
                    option.text = timeString;

                    // Tự động chọn lại giờ cũ nếu khớp (khi edit hoặc validate)
                    if (selectedTimeVal && selectedTimeVal.startsWith(timeString)) {
                        option.selected = true;
                    }

                    timeSelect.appendChild(option);
                    hasSlot = true;
                }
            }

            // Nếu không còn giờ nào (do quá giờ), báo hết chỗ
            if (!hasSlot) {
                const opt = document.createElement('option');
                opt.text = "Đã hết giờ nhận khách";
                opt.disabled = true;
                timeSelect.appendChild(opt);
            }
        }

        // Kích hoạt sự kiện
        if (dateInput && shiftSelect) {
            // Khi đổi Ngày -> Tính lại giờ
            dateInput.addEventListener('change', function() {
                timeSelect.setAttribute('data-selected', ''); // Reset chọn
                generateTimeSlots();
            });

            // Khi đổi Ca -> Tính lại giờ
            shiftSelect.addEventListener('change', function() {
                timeSelect.setAttribute('data-selected', ''); // Reset chọn
                generateTimeSlots();
            });

            // Chạy ngay lần đầu khi load trang
            generateTimeSlots();
        }


        // ---------------------------------------------------------
        // 2. CÁC PHẦN CÒN LẠI (GIỎ HÀNG, FORM SUBMIT...)
        // ---------------------------------------------------------
        
        @if (isset($datBan) && $datBan->chiTietDatBan && $datBan->chiTietDatBan->count() > 0)
            try {
                const dbItems = [
                    @foreach ($datBan->chiTietDatBan as $ct)
                        {
                            key: "{{ $ct->combo_buffet_id ? 'combo_' . $ct->combo_buffet_id : 'mon_' . $ct->mon_an_id }}",
                            name: {!! json_encode($ct->comboBuffet->ten_combo ?? ($ct->monAn->ten_mon ?? 'Món không xác định')) !!},
                            price: {{ $ct->comboBuffet->gia_co_ban ?? ($ct->monAn->gia ?? 0) }},
                            quantity: {{ $ct->so_luong }},
                            img: {!! json_encode($ct->comboBuffet ? asset('uploads/' . $ct->comboBuffet->anh) : ($ct->monAn ? asset($ct->monAn->hinh_anh) : '')) !!}
                        },
                    @endforeach
                ];
                localStorage.setItem(CART_KEY, JSON.stringify(dbItems));
            } catch (e) {}
        @endif

        // Hàm tính tổng số khách
        function getTongKhach() {
            const nguoiLonInput = document.querySelector('input[name="nguoi_lon"]');
            const treEmInput = document.querySelector('input[name="tre_em"]');
            const nguoiLon = parseInt(nguoiLonInput?.value) || 0;
            const treEm = parseInt(treEmInput?.value) || 0;
            return nguoiLon + treEm;
        }

        window.renderBookingCart = function() {
            let cart = [];
            try { cart = JSON.parse(localStorage.getItem(CART_KEY)) || []; } catch (e) { cart = []; }
            if (formInput) formInput.value = JSON.stringify(cart);

            if (cart.length === 0) {
                container.innerHTML = `<div class="text-center py-5"><p class="text-muted small">Giỏ hàng đang trống</p></div>`;
                if(totalSection) totalSection.classList.add('d-none');
                return;
            }
            if(totalSection) totalSection.classList.remove('d-none');
            
            let html = '';
            let grandTotal = 0;
            const nguoiLon = parseInt(document.querySelector('input[name="nguoi_lon"]')?.value) || 0;
            const treEm = parseInt(document.querySelector('input[name="tre_em"]')?.value) || 0;

            cart.forEach((item, index) => {
                const isCombo = item.key && item.key.startsWith('combo_');
                
                // Tính giá: combo giảm 50% cho trẻ em
                let itemTotal = 0;
                if (isCombo) {
                    // Tính số combo cho người lớn và trẻ em
                    let soComboNguoiLon = Math.min(item.quantity, nguoiLon);
                    let soComboTreEm = Math.max(0, item.quantity - nguoiLon);
                    // Giảm giá 50% cho trẻ em
                    itemTotal = (item.price * soComboNguoiLon) + (item.price * 0.5 * soComboTreEm);
                } else {
                    itemTotal = item.price * item.quantity;
                }
                
                grandTotal += itemTotal;
                
                // Tính chi tiết giá để hiển thị
                let priceDetail = '';
                if (isCombo && treEm > 0) {
                    const soComboNguoiLon = Math.min(item.quantity, nguoiLon);
                    const soComboTreEm = Math.max(0, item.quantity - nguoiLon);
                    if (soComboNguoiLon > 0 && soComboTreEm > 0) {
                        priceDetail = `<div class="text-muted small">(${soComboNguoiLon} người lớn × ${formatMoney(item.price)} + ${soComboTreEm} trẻ em × ${formatMoney(item.price * 0.5)})</div>`;
                    } else if (soComboTreEm > 0) {
                        priceDetail = `<div class="text-muted small">(${soComboTreEm} trẻ em × ${formatMoney(item.price * 0.5)})</div>`;
                    }
                }
                
                const tongKhach = getTongKhach();
                const minQty = isCombo ? tongKhach : 0;
                
                html += `
                    <div class="d-flex align-items-center p-2 border-bottom bg-white mx-2 my-1 rounded">
                        <div class="flex-grow-1 ms-2">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">${item.name}</div>
                            <div class="text-danger small fw-bold">${formatMoney(item.price)}${isCombo ? ' <span class="text-muted">(Trẻ em: 50%)</span>' : ''}</div>
                            ${priceDetail}
                        </div>
                        <div class="d-flex align-items-center bg-light rounded-pill border px-2 py-1 shadow-sm">
                            <button type="button" class="btn btn-sm border-0 text-secondary px-2 fw-bold" 
                                onclick="updateComboQty(${index}, -1)" 
                                ${isCombo && item.quantity <= minQty ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}
                                title="${isCombo && item.quantity <= minQty ? 'Không thể giảm dưới số khách' : 'Giảm'}">
                                <i class="fa fa-minus small"></i>
                            </button>
                            <span class="fw-bold text-dark mx-2" style="min-width: 20px; text-align: center;">${item.quantity}</span>
                            <button type="button" class="btn btn-sm border-0 text-primary px-2 fw-bold" 
                                onclick="updateComboQty(${index}, 1)"
                                title="Tăng">
                                <i class="fa fa-plus small"></i>
                            </button>
                        </div>
                    </div>`;
            });
            container.innerHTML = html;
            if(totalDisplay) totalDisplay.innerText = formatMoney(grandTotal);
        };

        // Hàm tự động cập nhật combo khi số khách thay đổi
        function autoUpdateComboQuantity() {
            const tongKhach = getTongKhach();
            if (tongKhach < 1) return;

            let cart = [];
            try {
                cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
            } catch (e) {
                cart = [];
            }
            
            let comboIndex = -1;

            // Tìm combo trong giỏ hàng
            cart.forEach((item, idx) => {
                if (item.key && item.key.startsWith('combo_')) {
                    comboIndex = idx;
                }
            });

            // Nếu có combo, tự động điều chỉnh số lượng về bằng số khách
            if (comboIndex >= 0) {
                const currentQty = parseInt(cart[comboIndex].quantity) || 0;
                const tongKhachInt = parseInt(tongKhach) || 0;
                
                console.log('autoUpdateComboQuantity - Combo hiện tại:', currentQty, 'Số khách:', tongKhachInt);
                
                if (currentQty !== tongKhachInt) {
                    console.log('Cập nhật combo từ', currentQty, 'lên', tongKhachInt);
                    cart[comboIndex].quantity = tongKhachInt;
                    localStorage.setItem(CART_KEY, JSON.stringify(cart));
                    return true; // Trả về true nếu có thay đổi
                }
            }
            return false; // Không có thay đổi
        }

        // Hàm cập nhật số lượng combo (global để có thể gọi từ onclick)
        window.updateComboQty = function(index, change) {
            let cart = [];
            try {
                cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
            } catch (e) {
                cart = [];
            }
            
            if (!cart[index]) return;
            
            const item = cart[index];
            const tongKhach = getTongKhach();
            const isCombo = item.key && item.key.startsWith('combo_');
            
            let currentQty = parseInt(item.quantity) || 0;
            let newQty = currentQty + change;
            
            // Nếu là combo, không cho giảm dưới số khách
            if (isCombo) {
                const tongKhachInt = parseInt(tongKhach) || 0;
                if (change < 0 && newQty < tongKhachInt) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Không thể giảm!',
                            text: `Số lượng combo phải >= số khách (${tongKhachInt} người).`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(`Số lượng combo phải >= số khách (${tongKhachInt} người)`);
                    }
                    return;
                }
                // Nếu tăng nhưng vẫn thấp hơn số khách, tự động đặt về số khách
                if (newQty < tongKhachInt) {
                    newQty = tongKhachInt;
                }
            }
            
            if (newQty > 0) {
                cart[index].quantity = newQty;
                localStorage.setItem(CART_KEY, JSON.stringify(cart));
                renderBookingCart();
            } else {
                // Xóa item nếu quantity = 0
                cart.splice(index, 1);
                localStorage.setItem(CART_KEY, JSON.stringify(cart));
                renderBookingCart();
            }
        };
        
        // Hàm xử lý thay đổi số khách
        function handleGuestChange() {
            console.log('handleGuestChange được gọi');
            // Cập nhật combo trước
            const hasChanged = autoUpdateComboQuantity();
            console.log('Combo đã thay đổi:', hasChanged);
            // Sau đó tính lại giá (luôn render lại để đảm bảo giá được tính đúng)
            renderBookingCart();
        }
        
        const nguoiLonInput = document.querySelector('input[name="nguoi_lon"]');
        const treEmInput = document.querySelector('input[name="tre_em"]');
        
        // Lắng nghe mọi sự kiện có thể
        if (nguoiLonInput) {
            nguoiLonInput.addEventListener('change', handleGuestChange);
            nguoiLonInput.addEventListener('input', handleGuestChange);
            nguoiLonInput.addEventListener('keyup', handleGuestChange);
            nguoiLonInput.addEventListener('blur', handleGuestChange);
            // Quan trọng: Lắng nghe sự kiện khi click vào spinner
            nguoiLonInput.addEventListener('mouseup', handleGuestChange);
            nguoiLonInput.addEventListener('touchend', handleGuestChange);
            // Lắng nghe khi giá trị thay đổi qua DOM
            nguoiLonInput.addEventListener('propertychange', handleGuestChange);
        }
        
        if (treEmInput) {
            treEmInput.addEventListener('change', handleGuestChange);
            treEmInput.addEventListener('input', handleGuestChange);
            treEmInput.addEventListener('keyup', handleGuestChange);
            treEmInput.addEventListener('blur', handleGuestChange);
            treEmInput.addEventListener('mouseup', handleGuestChange);
            treEmInput.addEventListener('touchend', handleGuestChange);
            treEmInput.addEventListener('propertychange', handleGuestChange);
        }
        
        // Polling để đảm bảo bắt được mọi thay đổi (fallback method)
        let lastNguoiLon = parseInt(nguoiLonInput?.value) || 0;
        let lastTreEm = parseInt(treEmInput?.value) || 0;
        
        // Polling với interval ngắn hơn để phản ứng nhanh hơn
        setInterval(function() {
            if (nguoiLonInput && treEmInput) {
                const currentNguoiLon = parseInt(nguoiLonInput.value) || 0;
                const currentTreEm = parseInt(treEmInput.value) || 0;
                
                // Nếu có thay đổi
                if (currentNguoiLon !== lastNguoiLon || currentTreEm !== lastTreEm) {
                    console.log('Số khách thay đổi:', currentNguoiLon, '+', currentTreEm, '=', currentNguoiLon + currentTreEm);
                    lastNguoiLon = currentNguoiLon;
                    lastTreEm = currentTreEm;
                    handleGuestChange();
                }
            }
        }, 100); // Kiểm tra mỗi 100ms để phản ứng nhanh hơn

        // Submit form handler
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(e) {
                let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
                if (formInput) formInput.value = JSON.stringify(cart);
            });
        }

        // Init - Đảm bảo render lại sau khi tất cả đã load
        function initializeCart() {
            // Đảm bảo lấy giá trị mới nhất từ input
            const nguoiLon = parseInt(document.querySelector('input[name="nguoi_lon"]')?.value) || 0;
            const treEm = parseInt(document.querySelector('input[name="tre_em"]')?.value) || 0;
            console.log('Khởi tạo giỏ hàng - Số khách:', nguoiLon, '+', treEm, '=', nguoiLon + treEm);
            
            // Cập nhật combo về đúng số khách
            const hasChanged = autoUpdateComboQuantity();
            // Render lại giỏ hàng
            renderBookingCart();
        }
        
        // Gọi ngay lập tức
        initializeCart();
        
        // Gọi lại sau khi DOM đã sẵn sàng
        setTimeout(initializeCart, 100);
        
        // Gọi lại sau khi window load xong (đảm bảo tất cả input đã có giá trị)
        if (document.readyState === 'loading') {
            window.addEventListener('load', function() {
                setTimeout(initializeCart, 200);
            });
        } else {
            setTimeout(initializeCart, 200);
        }
    });
</script>