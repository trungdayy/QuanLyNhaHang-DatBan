@extends('layouts.page')

@section('title', 'Đặt Bàn & Tra Cứu')

@section('content')

{{-- =========================================================== --}}
{{-- 0. THƯ VIỆN CẦN THIẾT --}}
{{-- =========================================================== --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

{{-- =========================================================== --}}
{{-- 1. MODERN CSS --}}
{{-- =========================================================== --}}
<style>
    :root {
        --primary-color: #FEA116;
        --secondary-color: #0F172B;
        --bg-soft: #f8f9fa;
        --card-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
        --hover-shadow: 0 20px 60px -15px rgba(0, 0, 0, 0.12);
    }

    body { background-color: var(--bg-soft); }

    /* Hero Section */
    .mini-hero {
        background: linear-gradient(rgba(15, 23, 43, 0.9), rgba(15, 23, 43, 0.9)), url('https://technext.github.io/restoran/img/bg-hero.jpg');
        background-position: center center;
        background-size: cover;
        padding: 100px 0 80px 0; 
        margin-bottom: -60px;
        color: white;
        position: relative;
        z-index: 0;
    }
    .mini-hero::after {
        content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 50px;
        background: var(--bg-soft); clip-path: polygon(0 100%, 100% 100%, 100% 0);
    }

    /* Modern Card */
    .glass-card {
        background: white; border: none; border-radius: 20px;
        box-shadow: var(--card-shadow); overflow: hidden; position: relative; z-index: 1;
    }

    /* Search Box */
    .search-wrapper { position: relative; }
    .search-input {
        padding-left: 50px; height: 60px; border-radius: 50px; border: none;
        background: white; box-shadow: var(--card-shadow); font-size: 1.1rem;
    }
    .search-icon {
        position: absolute; left: 20px; top: 50%; transform: translateY(-50%);
        color: #bbb; font-size: 1.2rem;
    }
    .search-btn {
        position: absolute; right: 5px; top: 5px; bottom: 5px;
        border-radius: 40px; padding: 0 25px;
    }

    /* Loader */
    .loader-overlay {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(255, 255, 255, 0.8); display: flex;
        justify-content: center; align-items: center; z-index: 10;
        border-radius: 20px; opacity: 0; visibility: hidden; transition: all 0.3s;
    }
    .loader-overlay.active { opacity: 1; visibility: visible; }

    /* CSS CHO VÉ ĐIỆN TỬ (TICKET) */
    .ticket-header { position: relative; overflow: hidden; }
    .ticket-serrated-bottom {
        position: absolute; bottom: -10px; left: 0; width: 100%; height: 20px; 
        background: radial-gradient(circle, transparent 70%, white 72%) 0 -10px; 
        background-size: 20px 20px;
    }
</style>

{{-- =========================================================== --}}
{{-- 2. HTML STRUCTURE --}}
{{-- =========================================================== --}}

{{-- Mini Hero --}}
<div class="mini-hero text-center"></div>

<div class="container pb-5" style="margin-top: 20px;">
    
    {{-- ALERT THÔNG BÁO TOÀN CỤC --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Thông báo lỗi VALIDATE (SĐT sai, Thiếu Combo...)
            @if ($errors->any())
                let errorHtml = "<ul style='text-align: left; font-size: 0.95rem; list-style: none; padding-left: 0;'>";
                @foreach ($errors->all() as $error)
                    errorHtml += "<li class='mb-2 text-danger'><i class='fa fa-exclamation-circle me-2'></i>{{ $error }}</li>";
                @endforeach
                errorHtml += "</ul>";

                Swal.fire({
                    icon: 'warning',
                    title: 'Vui lòng kiểm tra lại!',
                    html: errorHtml,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Đã hiểu'
                });
            @endif

            // 2. [ĐÃ SỬA] Thông báo Lỗi Logic & Gợi ý (Hết bàn...)
            @if(session('error'))
                let msgContent = `{!! session('error') !!}`;
                // Kiểm tra xem đây là lỗi thường hay thông báo gợi ý (có chứa từ khóa)
                let isSuggestion = msgContent.includes("Gợi ý") || msgContent.includes("Hotline");

                Swal.fire({
                    // Nếu là gợi ý thì dùng icon 'info' (xanh/lam) thay vì 'error' (đỏ)
                    icon: isSuggestion ? 'info' : 'error', 
                    title: isSuggestion ? 'Thông báo từ nhà hàng' : 'Rất tiếc...',
                    // Dùng html để render thẻ <br> xuống dòng
                    html: msgContent, 
                    confirmButtonColor: isSuggestion ? '#FEA116' : '#d33', // Đổi màu nút nếu là gợi ý
                    confirmButtonText: isSuggestion ? 'Đã hiểu' : 'Thử lại',
                    width: isSuggestion ? '600px' : '32em' // Popup rộng hơn để hiển thị gợi ý rõ ràng
                });
            @endif

            // 3. Thông báo Thành công (Chỉ hiện khi Update/Delete)
            @if(session('success'))
                @if(!isset($newBooking)) // Nếu KHÔNG có Modal vé thì mới hiện
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: "{!! session('success') !!}",
                        confirmButtonColor: '#FEA116',
                        timer: 3000
                    });
                @endif
            @endif
        });
    </script>

    <div class="row g-5">
        {{-- CỘT TRÁI: FORM ĐẶT BÀN --}}
        <div class="col-lg-7">
            <div class="glass-card h-100 p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fa fa-utensils text-primary fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Thông tin đặt bàn</h4>
                        <small class="text-muted">Vui lòng điền đầy đủ thông tin bên dưới</small>
                    </div>
                </div>

                {{-- Include Form --}}
                @include('restaurants.booking._form', [
                    'action' => route('booking.store'),
                    'method' => 'POST',
                    'datBan' => null
                ])
            </div>
        </div>

        {{-- CỘT PHẢI: TRA CỨU --}}
        <div class="col-lg-5">
            <div class="sticky-top" style="top: 20px; z-index: 9;">
                <div class="mb-4">
                    <form id="searchForm" onsubmit="return false;">
                        <div class="search-wrapper">
                            <i class="fa fa-search search-icon"></i>
                            <input type="tel" id="searchInput" class="form-control search-input"
                                placeholder="Nhập SĐT để tra cứu..." value="{{ $sdt ?? '' }}" autocomplete="off">
                            <button type="button" id="btnSearch" class="btn btn-primary search-btn fw-bold">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="position-relative">
                    <div id="searchLoader" class="loader-overlay">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <div id="historyResults">
                        @include('restaurants.booking._history_list', ['datBans' => $datBans ?? collect([]), 'sdt' => $sdt ?? null])
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <p class="small text-muted mb-1">Cần hỗ trợ gấp?</p>
                    <a href="tel:0999999999" class="fw-bold text-dark text-decoration-none fs-5 hover-primary">
                        <i class="fa fa-phone-alt text-primary me-2"></i> 0999.999.999
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================== --}}
{{-- 3. MODAL VÉ ĐIỆN TỬ (CHỈ HIỆN KHI CÓ ĐƠN MỚI) --}}
{{-- =========================================================== --}}
@if(isset($newBooking))
<div class="modal fade" id="ticketModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 bg-transparent">
            {{-- Nút đóng --}}
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal"></button>
            
            <div class="modal-body p-0">
                {{-- VÙNG CHỤP ẢNH --}}
                <div id="captureArea" class="bg-white rounded-4 overflow-hidden shadow-lg position-relative">
                    
                    {{-- Header Vé --}}
                    <div class="ticket-header p-4 text-center text-white position-relative" style="background: linear-gradient(135deg, #FEA116, #FF8E53);">
                        <i class="fa fa-check-circle fa-3x mb-2"></i>
                        <h4 class="fw-bold mb-0 text-uppercase">Đặt Bàn Thành Công</h4>
                        <div class="mt-2 badge bg-white text-primary rounded-pill px-3 py-1">Mã: {{ $newBooking->ma_dat_ban }}</div>
                        <div class="ticket-serrated-bottom"></div>
                    </div>

                    {{-- Nội Dung Vé --}}
                    <div class="p-4 pt-5">
                        <div class="text-center mb-3">
                            <h5 class="fw-bold text-uppercase text-primary mb-0">Buffet Ocean</h5>
                            <small class="text-muted">Hân hạnh phục vụ quý khách</small>
                        </div>
                        
                        <div class="row g-2 small">
                            <div class="col-6 text-muted">Khách hàng:</div>
                            <div class="col-6 text-end fw-bold text-dark">{{ $newBooking->ten_khach }}</div>
                            <div class="col-6 text-muted">SĐT:</div>
                            <div class="col-6 text-end fw-bold text-dark">{{ $newBooking->sdt_khach }}</div>
                            <div class="col-12 border-bottom border-dashed my-2"></div>
                            <div class="col-6 text-muted">Thời gian:</div>
                            <div class="col-6 text-end fw-bold text-primary fs-6">{{ \Carbon\Carbon::parse($newBooking->gio_den)->format('H:i - d/m/Y') }}</div>
                            <div class="col-6 text-muted">Số lượng:</div>
                            <div class="col-6 text-end fw-bold text-dark">{{ $newBooking->nguoi_lon }} Lớn @if($newBooking->tre_em > 0), {{ $newBooking->tre_em }} Trẻ @endif</div>
                            
                            {{-- Chi tiết món --}}
                            @if($newBooking->chiTietDatBan && $newBooking->chiTietDatBan->count() > 0)
                                <div class="col-12 mt-3">
                                    <div class="bg-light p-2 rounded">
                                        <div class="fw-bold text-muted mb-1">Thực đơn:</div>
                                        @foreach($newBooking->chiTietDatBan as $ct)
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>{{ $ct->comboBuffet->ten_combo ?? ($ct->monAn->ten_mon ?? 'Món khác') }}</span>
                                                <span class="fw-bold">x{{ $ct->so_luong }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="text-center mt-4 pt-3 border-top">
                            <p class="small text-muted mb-0">Vui lòng lưu vé này để check-in nhanh hơn.</p>
                        </div>
                    </div>
                </div>

                {{-- Nút Hành Động --}}
                <div class="text-center mt-3 pb-3">
                    <button id="btnSaveTicket" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fa fa-download me-2"></i> Lưu ảnh vé
                    </button>
                    <button type="button" class="btn btn-outline-light rounded-pill px-4 fw-bold ms-2" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- =========================================================== --}}
{{-- 4. JAVASCRIPT LOGIC --}}
{{-- =========================================================== --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- CẤU HÌNH ---
        const CART_KEY = "oceanCart"; 
        
        // --- DOM ELEMENTS ---
        const container = document.getElementById('bookingCartContainer');
        const totalSection = document.getElementById('bookingCartTotalSection');
        const cartHeader = document.getElementById('cartHeader');
        const totalDisplay = document.getElementById('bookingDisplayTotal');
        const formInput = document.getElementById('cartDataInput');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const countDeleteSpan = document.getElementById('countDelete');
        const selectAllCheckbox = document.getElementById('selectAllCart');
        const formatMoney = (amount) => parseInt(amount).toLocaleString('vi-VN') + ' đ';

        // --- 1. RENDER GIỎ HÀNG ---
        window.renderBookingCart = function() {
            let cart = [];
            try {
                cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
            } catch (e) { cart = []; }

            // Đẩy dữ liệu vào input hidden
            if (formInput) formInput.value = JSON.stringify(cart);

            // Nếu giỏ trống
            if (cart.length === 0) {
                if(container) {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fa fa-shopping-basket fa-3x text-black-50 opacity-25 mb-3"></i>
                            <p class="text-muted mb-3 fw-bold">Giỏ hàng đang trống</p>
                            <a href="{{ route('menu') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold">
                                <i class="fa fa-arrow-left me-1"></i> Quay lại chọn món
                            </a>
                        </div>`;
                }
                if (totalSection) totalSection.classList.add('d-none');
                if (cartHeader) cartHeader.style.setProperty('display', 'none', 'important');
                if (btnBulkDelete) btnBulkDelete.classList.add('d-none');
                if (selectAllCheckbox) selectAllCheckbox.checked = false;
                return;
            }

            // Nếu có món
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
                            <button type="button" class="btn btn-sm border-0 text-secondary px-2 fw-bold" onclick="updateQty(${index}, -1)"><i class="fa fa-minus small"></i></button>
                            <span class="fw-bold text-dark mx-2" style="min-width: 20px; text-align: center;">${item.quantity}</span>
                            <button type="button" class="btn btn-sm border-0 text-primary px-2 fw-bold" onclick="updateQty(${index}, 1)"><i class="fa fa-plus small"></i></button>
                        </div>
                    </div>`;
            });

            if(container) container.innerHTML = html;
            if(totalDisplay) totalDisplay.innerText = formatMoney(grandTotal);
            attachCheckboxEvents();
            toggleDeleteButton(0);
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
        };

        // --- Các hàm phụ trợ ---
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
                
                const performDelete = () => {
                    let cart = JSON.parse(localStorage.getItem(CART_KEY)) || [];
                    const indexes = Array.from(checkedBoxes).map(cb => parseInt(cb.value)).sort((a, b) => b - a);
                    indexes.forEach(idx => { if (cart[idx]) cart.splice(idx, 1); });
                    localStorage.setItem(CART_KEY, JSON.stringify(cart));
                    renderBookingCart();
                    if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Đã xóa!', timer: 1000, showConfirmButton: false });
                };

                if (typeof Swal !== 'undefined') {
                    Swal.fire({ title: 'Xóa món?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Xóa ngay' }).then((r) => { if (r.isConfirmed) performDelete(); });
                } else { if (confirm('Xóa món?')) performDelete(); }
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
                    if (typeof Swal !== 'undefined') Swal.fire('Thông báo', 'Dùng ô tích chọn để xóa món.', 'info');
                    else alert('Dùng ô tích chọn để xóa món.');
                }
            }
        };

        // --- 3. LOGIC TÌM KIẾM ---
        const searchInput = document.getElementById('searchInput');
        const btnSearch = document.getElementById('btnSearch');
        const historyResults = document.getElementById('historyResults');
        const loader = document.getElementById('searchLoader');
        let timeout = null;

        function performSearch() {
            const phone = searchInput.value.trim();
            loader.classList.add('active'); historyResults.style.opacity = '0.5';
            fetch(`{{ route('booking.index') }}?sdt=${phone}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text()).then(html => { historyResults.innerHTML = html; })
            .catch(err => { historyResults.innerHTML = '<div class="text-center text-danger">Lỗi.</div>'; })
            .finally(() => { loader.classList.remove('active'); historyResults.style.opacity = '1'; });
        }
        if(btnSearch) btnSearch.addEventListener('click', performSearch);
        if(searchInput) searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') { performSearch(); return; }
            clearTimeout(timeout); timeout = setTimeout(() => { if(this.value.length >= 3) performSearch(); }, 800);
        });

        // --- 4. LOGIC MODAL VÉ (QUAN TRỌNG: CÓ XÓA GIỎ HÀNG) ---
        @if(isset($newBooking))
            // 1. Xóa sạch giỏ hàng trong LocalStorage ngay lập tức
            localStorage.removeItem(CART_KEY);
            
            // 2. Vẽ lại giỏ hàng (sẽ thành trống) để ẩn các thành phần UI bên dưới Modal
            renderBookingCart(); 

            // 3. Hiển thị Modal Vé
            var ticketModal = new bootstrap.Modal(document.getElementById('ticketModal'));
            ticketModal.show();

            // 4. Logic nút Lưu ảnh
            const btnSave = document.getElementById('btnSaveTicket');
            if(btnSave) {
                btnSave.addEventListener('click', function() {
                    const btn = this; const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> ...'; btn.disabled = true;
                    html2canvas(document.querySelector("#captureArea"), { scale: 2, useCORS: true, backgroundColor: null }).then(canvas => {
                        const link = document.createElement('a'); link.download = 'Ve_BuffetOcean_{{ $newBooking->ma_dat_ban }}.png';
                        link.href = canvas.toDataURL("image/png"); link.click();
                        btn.innerHTML = originalText; btn.disabled = false;
                        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                        Toast.fire({ icon: 'success', title: 'Đã tải vé!' });
                    }).catch(err => { console.error(err); btn.innerHTML = originalText; btn.disabled = false; alert("Lỗi lưu ảnh."); });
                });
            }
        @else
            // Nếu KHÔNG có Modal Vé thì mới chạy logic khôi phục giỏ hàng từ DB (nếu đang Edit)
            @if(isset($datBan) && $datBan->chiTietDatBan && $datBan->chiTietDatBan->count() > 0)
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
                    localStorage.setItem(CART_KEY, JSON.stringify(dbItems));
                } catch (e) { console.error(e); }
            @endif
            
            // Vẽ lại giỏ hàng
            renderBookingCart();
        @endif
    });
</script>

@endsection