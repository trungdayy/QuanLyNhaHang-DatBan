{{-- ============================================================= --}}
{{-- 1. CSS TÙY CHỈNH --}}
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
        color: white;
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
{{-- 2. HTML FORM (ĐÃ SỬA: SELECT GIỜ) --}}
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
                    required>
                <label for="bookingDate"><i class="fa fa-calendar-alt"></i>Ngày đến</label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-floating">
                <select name="ca_dat" class="form-select" id="bookingShiftSelect" required>
                    <option value="trua"
                        {{ (old('ca_dat') == 'trua' || (isset($datBan) && \Carbon\Carbon::parse($datBan->gio_den)->hour < 15)) ? 'selected' : '' }}>
                        Trưa (10:30 - 14:00)
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
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="selectAllCart"
                                    style="cursor: pointer;">
                                <label class="form-check-label small fw-bold text-muted cursor-pointer"
                                    for="selectAllCart">
                                    Tất cả
                                </label>
                            </div>
                            <button type="button" id="btnBulkDelete"
                                class="btn btn-sm btn-danger rounded-pill px-3 fw-bold d-none animate__animated animate__fadeIn">
                                <i class="fa fa-trash me-1"></i> Xóa (<span id="countDelete">0</span>)
                            </button>
                        </div>
                        <div class="text-end">
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
{{-- 3. JAVASCRIPT LOGIC (ĐÃ CẬP NHẬT LOGIC GIỜ) --}}
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
        // 1. LOGIC XỬ LÝ CA & GIỜ (ĐÃ SỬA LẠI CHẶT CHẼ)
        // ---------------------------------------------------------
        const dateInput = document.getElementById('bookingDate');
        const shiftSelect = document.getElementById('bookingShiftSelect'); // Select Ca
        const timeSelect = document.getElementById('bookingTimeSelect');   // Select Giờ

        // Cấu hình khung giờ cho từng ca (Đơn vị: Giờ)
        // start: Giờ bắt đầu, end: Giờ kết thúc, minStart: Phút bắt đầu của giờ đầu tiên
        const SHIFT_CONFIG = {
            'trua': { start: 10, end: 14, minStart: 30 }, // 10:30 -> 14:00
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
                    
                    // -- LOGIC LỌC GIỜ THEO CA --
                    
                    // Ca Trưa: Bắt đầu từ 10:30 (Bỏ qua 10:00)
                    if (selectedShift === 'trua' && h === 10 && m < 30) continue;
                    
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

            // Khi đổi Ca -> Tính lại giờ (QUAN TRỌNG: Sửa lỗi hình ảnh bạn gửi)
            shiftSelect.addEventListener('change', function() {
                timeSelect.setAttribute('data-selected', ''); // Reset chọn
                generateTimeSlots();
            });

            // Chạy ngay lần đầu khi load trang
            generateTimeSlots();
        }


        // ---------------------------------------------------------
        // 2. CÁC PHẦN CÒN LẠI (GIỎ HÀNG, FORM SUBMIT...) - GIỮ NGUYÊN
        // ---------------------------------------------------------
        
        // ... (Giữ nguyên phần logic giỏ hàng ở các câu trả lời trước) ...
        // Để code gọn, tôi chỉ paste lại phần render giỏ hàng cơ bản để code chạy được
        
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
            cart.forEach((item, index) => {
                grandTotal += item.price * item.quantity;
                html += `
                    <div class="d-flex align-items-center p-2 border-bottom bg-white mx-2 my-1 rounded">
                        <div class="flex-grow-1 ms-2">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">${item.name}</div>
                            <div class="text-danger small fw-bold">${formatMoney(item.price)}</div>
                        </div>
                        <div class="d-flex align-items-center bg-light rounded px-2">
                            <span class="fw-bold text-dark mx-2">x${item.quantity}</span>
                        </div>
                    </div>`;
            });
            container.innerHTML = html;
            if(totalDisplay) totalDisplay.innerText = formatMoney(grandTotal);
        };

        // Submit form handler
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(e) {
                let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
                if (formInput) formInput.value = JSON.stringify(cart);
            });
        }

        // Init
        renderBookingCart();
    });
</script>