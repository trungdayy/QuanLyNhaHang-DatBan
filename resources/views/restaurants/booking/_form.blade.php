{{-- ============================================================= --}}
{{-- 1. CSS TÙY CHỈNH --}}
{{-- ============================================================= --}}
<style>
    .form-floating .form-control { border-radius: 10px; border: 1px solid #e0e0e0; height: 55px; }
    .form-floating .form-control:focus { border-color: #FEA116; box-shadow: 0 0 0 0.25rem rgba(254, 161, 22, 0.15); }
    .cart-item-card { transition: all 0.2s; border: 1px solid #f0f0f0; }
    .cart-item-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); border-color: #ffe0b2; }
    .btn-gradient-submit { background: linear-gradient(45deg, #FEA116, #FF8E53); border: none; color: white; font-weight: 700; text-transform: uppercase; box-shadow: 0 4px 15px rgba(254, 161, 22, 0.4); transition: all 0.3s; }
    .btn-gradient-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(254, 161, 22, 0.6); color: white; }
</style>

{{-- ============================================================= --}}
{{-- 2. HTML FORM (ĐÃ SỬA ID VÀ VALUE) --}}
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
            <h4 class="text-uppercase fw-bold" style="color: #0F172B;">{{ isset($datBan) ? 'Cập Nhật Đặt Bàn' : 'Thông Tin Đặt Bàn' }}</h4>
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
                <label for="floatSdt" class="text-muted"><i class="fa fa-phone text-primary me-2"></i>Số điện thoại</label>
            </div>
        </div>

        {{-- 2. THỜI GIAN (Value lấy từ DB nếu có) --}}
        <div class="col-md-6">
            <div class="form-floating">
                <input type="date" name="booking_date" class="form-control fw-bold text-dark" id="bookingDate"
                    value="{{ old('booking_date', isset($datBan->gio_den) ? \Carbon\Carbon::parse($datBan->gio_den)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                    required>
                <label for="bookingDate" class="text-muted"><i class="fa fa-calendar text-primary me-2"></i>Ngày đến</label>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-floating">
                <input type="time" name="booking_time" class="form-control fw-bold text-dark" id="bookingTime"
                    value="{{ old('booking_time', isset($datBan->gio_den) ? \Carbon\Carbon::parse($datBan->gio_den)->format('H:i') : \Carbon\Carbon::now()->addMinutes(30)->format('H:i')) }}"
                    required>
                <label for="bookingTime" class="text-muted"><i class="fa fa-clock text-primary me-2"></i>Giờ đến</label>
            </div>
        </div>

        {{-- 3. SỐ NGƯỜI --}}
        <div class="col-md-6">
            <div class="bg-light rounded-3 p-2 border">
                <label class="small fw-bold text-muted ps-2 mb-1">Người lớn</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i class="fa fa-users text-primary fs-5"></i></span>
                    <input type="number" name="nguoi_lon" class="form-control border-0 bg-transparent fw-bold fs-5 text-dark"
                        value="{{ old('nguoi_lon', $datBan->nguoi_lon ?? 1) }}" min="1" required>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-light rounded-3 p-2 border">
                <label class="small fw-bold text-muted ps-2 mb-1">Trẻ em</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i class="fa fa-child text-primary fs-5"></i></span>
                    <input type="number" name="tre_em" class="form-control border-0 bg-transparent fw-bold fs-5 text-dark"
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
                <div class="card-body p-0 bg-white">
                    <div id="bookingCartContainer">
                        <div class="text-center py-5 text-muted">
                            <p class="mb-0 small">Giỏ hàng trống.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. GHI CHÚ --}}
        <div class="col-12">
            <div class="form-floating">
                <textarea class="form-control" name="ghi_chu" id="floatNote" placeholder="Ghi chú" style="height: 100px;">{{ old('ghi_chu', $datBan->ghi_chu ?? '') }}</textarea>
                <label for="floatNote" class="text-muted"><i class="fa fa-sticky-note text-primary me-2"></i>Ghi chú thêm</label>
            </div>
        </div>

        {{-- 6. NÚT SUBMIT --}}
        <div class="col-12 mt-4 mb-3">
            <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-pill btn-gradient-submit" id="btnSubmitBooking">
                <i class="fa fa-paper-plane me-2"></i> {{ isset($datBan) ? 'LƯU CẬP NHẬT' : 'XÁC NHẬN ĐẶT BÀN' }}
            </button>
        </div>
    </div>
</form>

{{-- ============================================================= --}}
{{-- 3. JAVASCRIPT LOGIC (QUAN TRỌNG) --}}
{{-- ============================================================= --}}
{{-- ============================================================= --}}
{{-- 3. JAVASCRIPT LOGIC (ĐÃ FIX LỖI CRASH JS) --}}
{{-- ============================================================= --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- CẤU HÌNH ---
        const CART_KEY = "oceanCart";
        const container = document.getElementById('bookingCartContainer');
        const totalSection = document.getElementById('bookingCartTotalSection');
        const cartHeader = document.getElementById('cartHeader');
        const totalDisplay = document.getElementById('bookingDisplayTotal');
        const formInput = document.getElementById('cartDataInput');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const countDeleteSpan = document.getElementById('countDelete');
        const selectAllCheckbox = document.getElementById('selectAllCart');
        const bookingForm = document.getElementById('bookingForm'); // Lấy form để handle submit

        const formatMoney = (amount) => parseInt(amount).toLocaleString('vi-VN') + ' đ';

        // ---------------------------------------------------------
        // 1. KHỞI TẠO DỮ LIỆU TỪ DB (AN TOÀN HƠN)
        // ---------------------------------------------------------
        @if (isset($datBan) && $datBan->chiTietDatBan && $datBan->chiTietDatBan->count() > 0)
            try {
                // Sử dụng json_encode của PHP để đảm bảo chuỗi an toàn tuyệt đối với JS
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
                // Lưu vào LocalStorage để đồng bộ
                localStorage.setItem(CART_KEY, JSON.stringify(dbItems));
            } catch (e) {
                console.error("Lỗi khi tải dữ liệu từ DB:", e);
                // Nếu lỗi, xóa key để tránh treo
                localStorage.removeItem(CART_KEY);
            }
        @endif

        // ---------------------------------------------------------
        // 2. HÀM RENDER GIỎ HÀNG
        // ---------------------------------------------------------
        window.renderBookingCart = function() {
            let cart = [];
            try {
                cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
            } catch (e) { cart = []; }

            // Cập nhật ngay giá trị cho input ẩn để khi submit có dữ liệu
            if (formInput) formInput.value = JSON.stringify(cart);

            // Xử lý giao diện trống
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fa fa-shopping-basket fa-3x text-black-50 opacity-25 mb-3"></i>
                        <p class="text-muted mb-3 fw-bold">Giỏ hàng đang trống</p>
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold">
                            <i class="fa fa-arrow-left me-1"></i> Thêm món
                        </a>
                    </div>`;
                if (totalSection) totalSection.classList.add('d-none');
                if (cartHeader) cartHeader.style.setProperty('display', 'none', 'important');
                if (btnBulkDelete) btnBulkDelete.classList.add('d-none');
                if (selectAllCheckbox) selectAllCheckbox.checked = false;
                return;
            }

            // Hiển thị danh sách
            if (totalSection) totalSection.classList.remove('d-none');
            if (cartHeader) cartHeader.style.setProperty('display', 'flex', 'important');

            let html = '';
            let grandTotal = 0;

            cart.forEach((item, index) => {
                grandTotal += item.price * item.quantity;
                html += `
                    <div class="d-flex align-items-center p-3 border-bottom bg-white cart-item-card position-relative mx-3 my-2 rounded-3">
                        <div class="form-check me-3">
                            <input class="form-check-input cart-checkbox" type="checkbox" value="${index}">
                        </div>
                        ${item.img ? `<img src="${item.img}" class="rounded-3 shadow-sm me-3 border" style="width: 70px; height: 70px; object-fit: cover;">` : ''}
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 220px;">${item.name}</div>
                            <div class="text-primary fw-bold small mt-1">${formatMoney(item.price)}</div>
                        </div>
                        <div class="d-flex align-items-center bg-light rounded-pill border px-2 py-1 shadow-sm">
                            <button type="button" class="btn btn-sm border-0 text-secondary px-2 fw-bold" onclick="updateQty(${index}, -1)">
                                <i class="fa fa-minus small"></i>
                            </button>
                            <span class="fw-bold text-dark mx-2" style="min-width: 20px; text-align: center;">${item.quantity}</span>
                            <button type="button" class="btn btn-sm border-0 text-primary px-2 fw-bold" onclick="updateQty(${index}, 1)">
                                <i class="fa fa-plus small"></i>
                            </button>
                        </div>
                    </div>`;
            });

            container.innerHTML = html;
            totalDisplay.innerText = formatMoney(grandTotal);
            attachCheckboxEvents();
            toggleDeleteButton(0);
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        };

        // ---------------------------------------------------------
        // 3. XỬ LÝ SUBMIT FORM (QUAN TRỌNG NHẤT)
        // ---------------------------------------------------------
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(e) {
                // Lấy dữ liệu mới nhất từ localStorage trước khi gửi
                let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
                if (formInput) {
                    formInput.value = JSON.stringify(cart);
                }
                // Form sẽ tiếp tục submit bình thường...
            });
        }

        // ---------------------------------------------------------
        // 4. CÁC HÀM TIỆN ÍCH KHÁC
        // ---------------------------------------------------------
        
        function attachCheckboxEvents() {
            const checkboxes = document.querySelectorAll('.cart-checkbox');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const count = document.querySelectorAll('.cart-checkbox:checked').length;
                    toggleDeleteButton(count);
                    if (selectAllCheckbox) selectAllCheckbox.checked = (count === checkboxes.length && count > 0);
                });
            });
        }

        function toggleDeleteButton(count) {
            if (!btnBulkDelete) return;
            if (count > 0) {
                btnBulkDelete.classList.remove('d-none');
                btnBulkDelete.classList.add('animate__fadeIn');
                if (countDeleteSpan) countDeleteSpan.innerText = count;
            } else {
                btnBulkDelete.classList.add('d-none');
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.cart-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                toggleDeleteButton(this.checked ? checkboxes.length : 0);
            });
        }

        if (btnBulkDelete) {
            btnBulkDelete.onclick = function() {
                const checkedBoxes = document.querySelectorAll('.cart-checkbox:checked');
                if (checkedBoxes.length === 0) return;
                
                if (confirm(`Bạn muốn xóa ${checkedBoxes.length} món này?`)) {
                    let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
                    const indexes = Array.from(checkedBoxes).map(cb => parseInt(cb.value)).sort((a, b) => b - a);
                    indexes.forEach(idx => { if (cart[idx]) cart.splice(idx, 1); });
                    localStorage.setItem(CART_KEY, JSON.stringify(cart));
                    renderBookingCart();
                }
            };
        }

        window.updateQty = function(index, change) {
            let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
            if (cart[index]) {
                const newQty = cart[index].quantity + change;
                if (newQty > 0) {
                    cart[index].quantity = newQty;
                    localStorage.setItem(CART_KEY, JSON.stringify(cart));
                    renderBookingCart();
                } else {
                    alert('Số lượng tối thiểu là 1. Hãy tích chọn để xóa món.');
                }
            }
        };

        // --- FLASH MESSAGES ---
        @if (session('success'))
            localStorage.removeItem(CART_KEY);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: "{!! session('success') !!}",
                    confirmButtonColor: '#FEA116'
                }).then(() => renderBookingCart());
            }
        @endif

        @if (session('error'))
            if (typeof Swal !== 'undefined') {
                let msgContent = `{!! session('error') !!}`;
                let isSuggestion = msgContent.includes("Gợi ý") || msgContent.includes("Hotline");
                Swal.fire({
                    icon: isSuggestion ? 'info' : 'error',
                    title: isSuggestion ? 'Thông báo' : 'Lỗi',
                    html: msgContent,
                    confirmButtonColor: isSuggestion ? '#FEA116' : '#d33'
                });
            }
        @endif

        // --- INIT ---
        // Logic ngày giờ
        const dateInput = document.getElementById('bookingDate');
        const timeInput = document.getElementById('bookingTime');
        if(dateInput && timeInput) {
            const updateTime = () => {
                const now = new Date();
                const selected = new Date(dateInput.value);
                const today = new Date(); today.setHours(0,0,0,0); selected.setHours(0,0,0,0);
                if (selected.getTime() === today.getTime()) {
                    timeInput.min = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
                } else {
                    timeInput.removeAttribute('min');
                }
            };
            dateInput.addEventListener('change', updateTime);
            updateTime();
        }

        // Chạy render lần đầu
        renderBookingCart();
    });
</script>