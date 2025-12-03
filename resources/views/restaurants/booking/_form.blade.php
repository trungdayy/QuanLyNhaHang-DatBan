{{-- ============================================================= --}}
{{-- 1. CSS TÙY CHỈNH CHO FORM (Đẹp, hiện đại, Style Restoran) --}}
{{-- ============================================================= --}}
<style>
    /* Input đẹp hơn */
    .form-floating .form-control {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        background: #fdfdfd;
        height: 55px;
    }

    .form-floating .form-control:focus {
        border-color: #FEA116;
        /* Màu cam chủ đạo */
        box-shadow: 0 0 0 0.25rem rgba(254, 161, 22, 0.15);
        background: #fff;
    }

    /* Card món ăn từng dòng */
    .cart-item-card {
        transition: all 0.2s;
        border: 1px solid #f0f0f0;
    }

    .cart-item-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border-color: #ffe0b2;
    }

    /* Checkbox to hơn chút cho dễ bấm */
    .cart-checkbox {
        width: 22px;
        height: 22px;
        cursor: pointer;
        border-color: #FEA116;
    }

    .cart-checkbox:checked {
        background-color: #FEA116;
        border-color: #FEA116;
    }

    /* Nút bấm gradient */
    .btn-gradient-submit {
        background: linear-gradient(45deg, #FEA116, #FF8E53);
        border: none;
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
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
{{-- 2. HTML FORM (Cấu trúc giữ nguyên, Class đẹp hơn) --}}
{{-- ============================================================= --}}
<form action="{{ $action }}" method="POST" id="bookingForm" class="booking-form-container p-2">
    @csrf
    @if (isset($method) && $method === 'PUT')
        @method('PUT')
    @endif

    {{-- Input ẩn chứa dữ liệu giỏ hàng --}}
    <input type="hidden" name="cart_data" id="cartDataInput">

    <div class="row g-4">
        {{-- Header Form --}}
        <div class="col-12 text-center mb-2">
            <h4 class="text-uppercase fw-bold" style="color: #0F172B;">Thông Tin Đặt Bàn</h4>
            <div style="width: 50px; height: 3px; background: #FEA116; margin: 0 auto;"></div>
        </div>

        {{-- 1. THÔNG TIN KHÁCH HÀNG --}}
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

        <div class="col-md-12">
            <div class="form-floating">
                <input type="datetime-local" name="gio_den" class="form-control fw-bold text-dark" id="floatTime"
                    value="{{ old('gio_den', isset($datBan->gio_den) ? \Carbon\Carbon::parse($datBan->gio_den)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                    required>
                <label for="floatTime" class="text-muted"><i class="fa fa-clock text-primary me-2"></i>Thời gian
                    đến</label>
            </div>
        </div>

        {{-- 2. SỐ LƯỢNG NGƯỜI --}}
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

        {{-- 3. DANH SÁCH MÓN ĂN (Giao diện đẹp hơn) --}}
        <div class="col-12 mt-4">
            <div class="d-flex justify-content-between align-items-end mb-3 px-1">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="fa fa-utensils text-primary me-2"></i>Thực đơn đã chọn
                    </h6>
                    <small class="text-muted" style="font-size: 0.8rem;">Tích chọn vào ô vuông để xóa món</small>
                </div>

                {{-- Nút xóa --}}
                <button type="button" id="btnBulkDelete"
                    class="btn btn-sm btn-danger shadow-sm rounded-pill px-3 d-none animate__animated animate__fadeIn">
                    <i class="fa fa-trash-alt me-1"></i> Xóa (<span id="countDelete">0</span>)
                </button>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                {{-- Header chọn tất cả --}}
                <div class="card-header bg-primary bg-opacity-10 border-bottom-0 py-3 d-flex align-items-center"
                    id="cartHeader" style="display: none !important;">
                    <div class="form-check m-0 d-flex align-items-center">
                        <input class="form-check-input cart-checkbox" type="checkbox" id="selectAllCart"
                            style="border-color: #FEA116;">
                        <label class="form-check-label fw-bold text-dark ms-2" for="selectAllCart">Chọn tất cả</label>
                    </div>
                    <a href="/" class="ms-auto text-primary text-decoration-none fw-bold small">
                        <i class="fa fa-plus-circle me-1"></i>Thêm món khác
                    </a>
                </div>

                <div class="card-body p-0 bg-white">
                    <div id="bookingCartContainer">
                        {{-- Loading --}}
                        <div class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary mb-2" role="status"></div>
                            <p class="mb-0 small">Đang tải danh sách...</p>
                        </div>
                    </div>

                    {{-- Tổng tiền --}}
                    <div id="bookingCartTotalSection" class="d-none border-top p-4 bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-secondary text-uppercase">Tổng tạm tính</span>
                            <span class="fw-bold text-danger fs-3" id="bookingDisplayTotal">0 đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. GHI CHÚ --}}
        <div class="col-12">
            <div class="form-floating">
                <textarea class="form-control" name="ghi_chu" id="floatNote" placeholder="Ghi chú" style="height: 100px;">{{ old('ghi_chu', $datBan->ghi_chu ?? '') }}</textarea>
                <label for="floatNote" class="text-muted"><i class="fa fa-sticky-note text-primary me-2"></i>Ghi chú
                    thêm (dị ứng, ghế trẻ em...)</label>
            </div>
        </div>

        {{-- 5. NÚT SUBMIT --}}
        <div class="col-12 mt-4 mb-3">
            <button type="submit" class="btn btn-primary-custom w-100 py-3 rounded-pill btn-gradient-submit"
                id="btnSubmitBooking">
                @if (isset($method) && $method === 'PUT')
                    <i class="fa fa-save me-2"></i> LƯU CẬP NHẬT
                @else
                    <i class="fa fa-paper-plane me-2"></i> XÁC NHẬN ĐẶT BÀN
                @endif
            </button>
        </div>
    </div>
</form>

{{-- ============================================================= --}}
{{-- 3. JAVASCRIPT LOGIC (Giữ nguyên logic cũ như bạn yêu cầu) --}}
{{-- ============================================================= --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- CẤU HÌNH ---
        const CART_KEY = "oceanCart";

        // --- ELEMENTS ---
        const container = document.getElementById('bookingCartContainer');
        const totalSection = document.getElementById('bookingCartTotalSection');
        const cartHeader = document.getElementById('cartHeader');
        const totalDisplay = document.getElementById('bookingDisplayTotal');
        const formInput = document.getElementById('cartDataInput');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const countDeleteSpan = document.getElementById('countDelete');
        const selectAllCheckbox = document.getElementById('selectAllCart');

        // Định dạng tiền tệ
        const formatMoney = (amount) => parseInt(amount).toLocaleString('vi-VN') + ' đ';

        // --- 0. KHỞI TẠO DỮ LIỆU TỪ DB (KHI SỬA ĐƠN) ---
        @if (isset($datBan) && $datBan->chiTietDatBan)
            try {
                const dbItems = [
                    @foreach ($datBan->chiTietDatBan as $ct)
                        {
                            key: "{{ $ct->combo_buffet_id ? 'combo_' . $ct->combo_buffet_id : 'mon_' . $ct->mon_an_id }}",
                            name: "{{ $ct->comboBuffet->ten_combo ?? ($ct->monAn->ten_mon ?? 'Món không xác định') }}",
                            price: {{ $ct->comboBuffet->gia_co_ban ?? ($ct->monAn->gia ?? 0) }},
                            quantity: {{ $ct->so_luong }},
                            img: "{{ $ct->comboBuffet ? asset('uploads/' . $ct->comboBuffet->anh) : ($ct->monAn ? asset($ct->monAn->hinh_anh) : '') }}"
                        },
                    @endforeach
                ];
                // Chỉ ghi đè nếu muốn ưu tiên dữ liệu DB khi vào trang sửa
                localStorage.setItem(CART_KEY, JSON.stringify(dbItems));
            } catch (e) {
                console.error("Lỗi parse DB cart", e);
            }
        @endif

        // --- 1. RENDER GIỎ HÀNG ---
        window.renderBookingCart = function() {
            let cart = [];
            try {
                cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
            } catch (e) {
                cart = [];
            }

            // Cập nhật input hidden cho form submit
            if (formInput) formInput.value = JSON.stringify(cart);

            // Xử lý giỏ hàng trống
            if (cart.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fa fa-shopping-basket fa-3x text-black-50 opacity-25 mb-3"></i>
                        <p class="text-muted mb-3 fw-bold">Giỏ hàng đang trống</p>
                        <a href="/" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại chọn món
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
                        {{-- Checkbox chọn món --}}
                        <div class="form-check me-3">
                            <input class="form-check-input cart-checkbox" type="checkbox" value="${index}">
                        </div>

                        {{-- Ảnh --}}
                        ${item.img ? `<img src="${item.img}" class="rounded-3 shadow-sm me-3 border" style="width: 70px; height: 70px; object-fit: cover;">` : ''}
                        
                        {{-- Thông tin --}}
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 220px;">${item.name}</div>
                            <div class="text-primary fw-bold small mt-1">${formatMoney(item.price)}</div>
                        </div>

                        {{-- Tăng giảm --}}
                        <div class="d-flex align-items-center bg-light rounded-pill border px-2 py-1 shadow-sm">
                            <button type="button" class="btn btn-sm border-0 text-secondary px-2 fw-bold" onclick="updateQty(${index}, -1)">
                                <i class="fa fa-minus small"></i>
                            </button>
                            <span class="fw-bold text-dark mx-2" style="min-width: 20px; text-align: center;">${item.quantity}</span>
                            <button type="button" class="btn btn-sm border-0 text-primary px-2 fw-bold" onclick="updateQty(${index}, 1)">
                                <i class="fa fa-plus small"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
            totalDisplay.innerText = formatMoney(grandTotal);

            // QUAN TRỌNG: Gắn lại sự kiện cho các checkbox mới sinh ra
            attachCheckboxEvents();

            // Reset trạng thái nút xóa về ẩn mỗi khi vẽ lại
            toggleDeleteButton(0);
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        };

        // --- 2. XỬ LÝ CHECKBOX & ĐỒNG BỘ "CHỌN TẤT CẢ" ---
        function attachCheckboxEvents() {
            const checkboxes = document.querySelectorAll('.cart-checkbox');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    // 1. Đếm số lượng đang check
                    const checkedCount = document.querySelectorAll('.cart-checkbox:checked')
                        .length;

                    // 2. Cập nhật nút Xóa
                    toggleDeleteButton(checkedCount);

                    // 3. Đồng bộ với nút "Chọn tất cả"
                    // Nếu số lượng check == tổng số checkbox thì tick vào "Chọn tất cả", ngược lại thì bỏ tick
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = (checkedCount === checkboxes.length &&
                            checkedCount > 0);
                    }
                });
            });
        }

        // --- 3. HÀM ẨN/HIỆN NÚT XÓA ---
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

        // --- 4. SỰ KIỆN NÚT "CHỌN TẤT CẢ" ---
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.cart-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);

                // Cập nhật nút xóa ngay lập tức
                const count = this.checked ? checkboxes.length : 0;
                toggleDeleteButton(count);
            });
        }

        // --- 5. SỰ KIỆN NÚT "XÓA" (CORE LOGIC) ---
        if (btnBulkDelete) {
            // Dùng onclick để tránh bị gắn sự kiện nhiều lần nếu script chạy lại
            btnBulkDelete.onclick = function() {
                const checkedBoxes = document.querySelectorAll('.cart-checkbox:checked');

                if (checkedBoxes.length === 0) return;

                // Hàm thực thi xóa
                const performDelete = () => {
                    let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];

                    // Lấy danh sách index cần xóa
                    // QUAN TRỌNG: Sắp xếp GIẢM DẦN (b - a) để khi xóa index lớn không làm sai lệch index nhỏ
                    const indexesToDelete = Array.from(checkedBoxes)
                        .map(cb => parseInt(cb.value))
                        .sort((a, b) => b - a);

                    indexesToDelete.forEach(idx => {
                        if (cart[idx]) cart.splice(idx, 1);
                    });

                    // Lưu và vẽ lại
                    localStorage.setItem(CART_KEY, JSON.stringify(cart));
                    renderBookingCart();

                    // Đồng bộ Mini Cart (nếu có)
                    if (typeof window.renderCartUI === "function") window.renderCartUI();

                    // Thông báo xóa thành công
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Đã xóa!',
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                };

                // Hiển thị hộp thoại xác nhận
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Xóa món đã chọn?',
                        text: `Bạn muốn xóa ${checkedBoxes.length} món này khỏi danh sách?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Xóa ngay',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) performDelete();
                    });
                } else {
                    // Fallback nếu không có SweetAlert
                    if (confirm(`Bạn muốn xóa ${checkedBoxes.length} món này?`)) performDelete();
                }
            };
        }

        // --- 6. HÀM TĂNG GIẢM SỐ LƯỢNG (Giữ nguyên) ---
        window.updateQty = function(index, change) {
            let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
            if (cart[index]) {
                const newQty = cart[index].quantity + change;
                if (newQty > 0) {
                    cart[index].quantity = newQty;
                    localStorage.setItem(CART_KEY, JSON.stringify(cart));
                    renderBookingCart();
                } else {
                    // Nếu giảm về 0 -> Bắt buộc dùng checkbox để xóa (theo yêu cầu của bạn)
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Số lượng tối thiểu là 1',
                            'Để xóa món, vui lòng tích vào ô chọn và bấm nút Xóa.', 'info');
                    } else {
                        alert(
                            'Số lượng tối thiểu là 1. Để xóa món, vui lòng tích vào ô chọn và bấm nút Xóa.');
                    }
                }
            }
        };

        // --- 7. THÔNG BÁO SAU KHI ĐẶT THÀNH CÔNG ---
        @if (session('success'))
            localStorage.removeItem(CART_KEY);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: "{!! session('success') !!}",
                    confirmButtonColor: '#FEA116',
                    confirmButtonText: 'OK'
                }).then(() => {
                    renderBookingCart();
                });
            }
        @endif

        @if (session('error'))
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Có lỗi xảy ra',
                    text: "{!! session('error') !!}",
                    confirmButtonColor: '#d33'
                });
            }
        @endif

        // Render lần đầu khi trang tải xong
        renderBookingCart();
    });
</script>
