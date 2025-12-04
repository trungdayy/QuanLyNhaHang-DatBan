@extends('layouts.admins.layout-admin')

@section('title', 'Sửa Đơn Đặt Bàn')

@section('style')
<style>
    /* --- 1. CSS CHO CARD CHỌN COMBO (GIỐNG CREATE) --- */
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
        border-color: #009688; /* Màu xanh lá chủ đạo */
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
        background: rgba(0, 0, 0, 0.5);
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
        background: #dc3545;
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
</style>
@endsection

@section('content')
    <main class="app-content">
        
        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dat-ban.index') }}">Quản lý Đặt Bàn</a></li>
                <li class="breadcrumb-item"><a href="#"><b>Sửa Đặt Bàn: {{ $datBan->ma_dat_ban }}</b></a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title">Sửa Đơn Đặt Bàn</h3>
                    <div class="tile-body">
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 pl-3">@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form class="row" method="POST" action="{{ route('admin.dat-ban.update', $datBan->id) }}" id="datBanForm">
                            @csrf
                            @method('PUT')
                            
                            {{-- THÔNG TIN KHÁCH HÀNG --}}
                            <div class="form-group col-md-3">
                                <label class="control-label">Tên Khách Hàng (*)</label>
                                <input class="form-control" type="text" name="ten_khach" value="{{ old('ten_khach', $datBan->ten_khach) }}" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label">Số Điện Thoại (*)</label>
                                <input class="form-control" type="text" name="sdt_khach" value="{{ old('sdt_khach', $datBan->sdt_khach) }}" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label">Email</label>
                                <input class="form-control" type="text" name="email_khach" value="{{ old('email_khach', $datBan->email_khach) }}">
                            </div>
                            
                            {{-- SỐ LƯỢNG KHÁCH --}}
                            <div class="form-group col-md-3">
                                <label class="control-label">Người lớn (>1m3) (*)</label>
                                <input class="form-control" type="number" name="nguoi_lon" id="nguoi_lon_input" min="1" value="{{ old('nguoi_lon', $datBan->nguoi_lon) }}" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label">Trẻ em (<1m3)</label>
                                <input class="form-control" type="number" name="tre_em" id="tre_em_input" min="0" value="{{ old('tre_em', $datBan->tre_em) }}">
                            </div>

                            <div class="form-group col-md-3">
                                <label class="control-label">Tiền Cọc (VNĐ)</label>
                                <input class="form-control" type="number" name="tien_coc" value="{{ old('tien_coc', $datBan->tien_coc) }}" min="0">
                            </div>

                            {{-- THÔNG TIN ĐẶT BÀN --}}
                            <div class="form-group col-md-3">
                                <label class="control-label">Giờ Khách Đến (*)</label>
                                @php
                                    $gioDenVal = old('gio_den', $datBan->gio_den ? \Carbon\Carbon::parse($datBan->gio_den)->format('Y-m-d\TH:i') : '');
                                @endphp
                                <input class="form-control" type="datetime-local" name="gio_den" id="gio_den_input" value="{{ $gioDenVal }}" required>
                            </div>

                            {{-- CHỌN BÀN (Ajax Logic) --}}
                            <div class="form-group col-md-3">
                                <label class="control-label">Chọn Bàn (Nếu có)</label>
                                <select class="form-control" name="ban_id" id="ban_id_select">
                                    <option value="">-- Chưa xếp bàn / Mang về --</option>
                                    @foreach ($banAns as $ban)
                                        @php
                                            $isCurrent = ($ban->id == $datBan->ban_id);
                                            // Chỉ disable nếu bàn đó bận VÀ không phải là bàn của đơn hiện tại
                                            $isBusy = !$isCurrent && in_array($ban->trang_thai, ['dang_phuc_vu', 'da_dat']);
                                            $style = $isBusy ? 'background-color: #ffeeee; color: #d9534f;' : '';
                                        @endphp
                                        <option value="{{ $ban->id }}" 
                                            {{ old('ban_id', $datBan->ban_id) == $ban->id ? 'selected' : '' }}
                                            {{ $isBusy ? 'disabled' : '' }}
                                            style="{{ $style }}"
                                        >
                                            Bàn {{ $ban->so_ban }} - {{ $ban->so_ghe }} ghế 
                                            @if($ban->khuVuc) ({{ $ban->khuVuc->ten_khu_vuc }}) @endif
                                            {{ $isBusy ? '(Đang bận)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 🔥 [LOGIC MỚI] BƯỚC 1: CHỌN LOẠI COMBO --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Chọn Mức Giá Combo (*)</label>
                                <select class="form-control" id="loai_combo_select">
                                    <option value="">-- Chọn mức giá --</option>
                                    @foreach($loaiCombos as $loai)
                                        <option value="{{ $loai->loai_combo }}"
                                            {{ ($currentLoaiCombo == $loai->loai_combo) ? 'selected' : '' }}>
                                            Gói {{ $loai->loai_combo }} ({{ number_format($loai->gia_co_ban) }}đ)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 🔥 [LOGIC MỚI] BƯỚC 2: CHỌN COMBO CỤ THỂ --}}
                            <div class="form-group col-md-12 mt-3">
                                <label class="control-label fw-bold mb-2 d-block">Danh Sách Combo Tương Ứng</label>
                                
                                {{-- HEADER TÓM TẮT --}}
                                <div class="combo-summary-header" data-bs-toggle="collapse" data-bs-target="#combo-selection-collapse" aria-expanded="true" id="combo-summary-header">
                                    <div class="summary-text">
                                        <i class="fas fa-list-check me-2"></i> 
                                        <span id="combo-summary-display" class="text-muted">Đang tải combo đã chọn...</span>
                                    </div>
                                    <i class="fas fa-chevron-down small summary-icon"></i>
                                </div>

                                {{-- DANH SÁCH CHỌN (AJAX hoặc PRE-FILL) --}}
                                <div class="collapse show" id="combo-selection-collapse">
                                    <div id="combo-selection-container" class="row g-3 mt-1">
                                        @if(count($combosOfCurrentType) > 0)
                                            {{-- Render sẵn danh sách combo nếu đã có loại combo được chọn --}}
                                            @php
                                                // Map số lượng đã chọn
                                                $selectedCombosMap = $datBan->chiTietDatBan->pluck('so_luong', 'combo_id')->toArray();
                                            @endphp

                                            @foreach ($combosOfCurrentType as $index => $combo)
                                                @php
                                                    $defaultQty = old('combos.'.$index.'.so_luong', $selectedCombosMap[$combo->id] ?? 0);
                                                    $isInitiallyActive = $defaultQty > 0;
                                                    $price = number_format($combo->gia_co_ban);
                                                @endphp
                                                <div class="col-md-3">
                                                    <div class="combo-admin-card @if($isInitiallyActive) active @endif" id="card-{{ $combo->id }}">
                                                        <div class="combo-name-price">
                                                            <label class="mb-0 text-primary combo-name-label">{{ $combo->ten_combo }}</label>
                                                            <small>{{ $price }} đ / suất</small>
                                                        </div>
                                                        <div class="combo-qty-control">
                                                            <button type="button" class="qty-btn minus-btn" data-id="{{ $combo->id }}"><i class="fas fa-minus"></i></button>
                                                            <div class="qty-input-display" id="display-{{ $combo->id }}">{{ $defaultQty }}</div>
                                                            
                                                            <input type="hidden" class="combo-qty-input" id="qty-{{ $combo->id }}" name="combos[{{ $index }}][so_luong]" value="{{ $defaultQty }}" @if(!$isInitiallyActive) disabled @endif>
                                                            <input type="hidden" name="combos[{{ $index }}][id]" value="{{ $combo->id }}" class="combo-id-input" @if(!$isInitiallyActive) disabled @endif>

                                                            <button type="button" class="qty-btn plus-btn" data-id="{{ $combo->id }}"><i class="fas fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="col-12 text-center py-3 text-muted"><i>Hãy chọn mức giá combo ở trên</i></div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-12 mt-3">
                                <label class="control-label">Ghi Chú</label>
                                <textarea class="form-control" name="ghi_chu" rows="2">{{ old('ghi_chu', $datBan->ghi_chu) }}</textarea>
                            </div>
                            
                            <div class="form-group col-md-12 mt-3">
                                <button class="btn btn-save" type="submit"><i class="fas fa-save"></i> Cập Nhật Đặt Bàn</button>
                                <a class="btn btn-cancel" href="{{ route('admin.dat-ban.index') }}"><i class="fas fa-times"></i> Hủy bỏ</a>
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
                    <div class="alert-detail-box" id="alert-detail-content"></div>
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
    document.addEventListener('DOMContentLoaded', function () {
        const timeInput = document.getElementById('gio_den_input');
        const tableSelect = document.getElementById('ban_id_select');
        
        // Combo & Modal Elements
        const loaiComboSelect = document.getElementById('loai_combo_select');
        const comboContainer = document.getElementById('combo-selection-container');
        const summaryDisplay = document.getElementById('combo-summary-display');
        const summaryHeader = document.getElementById('combo-summary-header');
        const comboCollapse = document.getElementById('combo-selection-collapse');
        const summaryIcon = document.querySelector('.summary-icon');
        
        const modal = document.getElementById('custom-alert-modal');
        const modalDetail = document.getElementById('alert-detail-content');
        const closeModalBtn = document.querySelector('.btn-close-alert');

        // Biến lưu trạng thái ban đầu
        const currentBookingId = "{{ $datBan->id }}";
        const currentBanId = "{{ $datBan->ban_id }}";
        let originalGioDen = timeInput.value;

        // --- 1. MODAL LOGIC ---
        function showModal(totalPeople, totalCombos) {
            modalDetail.innerHTML = `
                <div class="d-flex justify-content-between mb-1">
                    <span>Tổng số khách:</span> <strong>${totalPeople} người</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Đã chọn:</span> <strong class="text-danger">${totalCombos} suất combo</strong>
                </div>
                <hr style="margin: 5px 0;">
                <div class="text-center text-danger font-weight-bold">
                    Thiếu: ${totalPeople - totalCombos} suất
                </div>
            `;
            modal.style.display = 'flex';
        }
        function closeModal() { modal.style.display = 'none'; }
        closeModalBtn.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => { if(e.target === modal) closeModal(); });

        // --- 2. COLLAPSE COMBO LIST ---
        summaryHeader.addEventListener('click', function() {
            const isOpen = comboCollapse.classList.contains('show');
            comboCollapse.classList.toggle('show');
            if (isOpen) {
                summaryIcon.classList.remove('rotated');
                this.setAttribute('aria-expanded', 'false');
            } else {
                summaryIcon.classList.add('rotated');
                this.setAttribute('aria-expanded', 'true');
            }
        });

        // --- 3. AJAX LOAD COMBO (KHI THAY ĐỔI LOẠI) ---
        loaiComboSelect.addEventListener('change', function() {
            const loai = this.value;
            if (!loai) {
                comboContainer.innerHTML = '<div class="col-12 text-center py-3 text-muted"><i>Hãy chọn mức giá combo</i></div>';
                summaryDisplay.textContent = 'Chưa chọn mức giá.';
                return;
            }

            comboContainer.innerHTML = '<div class="col-12 text-center py-3"><i class="fas fa-spinner fa-spin"></i> Đang tải gói combo...</div>';

            const url = "{{ route('admin.dat-ban.ajax-get-combos-by-loai') }}";
            fetch(url + '?loai_combo=' + loai)
                .then(response => response.json())
                .then(data => {
                    renderCombos(data.combos);
                })
                .catch(error => {
                    console.error(error);
                    comboContainer.innerHTML = '<div class="col-12 text-center text-danger">Lỗi tải dữ liệu.</div>';
                });
        });

        function renderCombos(combos) {
            if (!combos || combos.length === 0) {
                comboContainer.innerHTML = '<div class="col-12 text-center text-danger">Không có combo nào trong gói này.</div>';
                return;
            }
            let html = '';
            combos.forEach((combo, index) => {
                const price = new Intl.NumberFormat('vi-VN').format(combo.gia_co_ban);
                html += `
                <div class="col-md-3">
                    <div class="combo-admin-card" id="card-${combo.id}">
                        <div class="combo-name-price">
                            <label class="mb-0 text-primary combo-name-label">${combo.ten_combo}</label>
                            <small>${price} đ / suất</small>
                        </div>
                        <div class="combo-qty-control">
                            <button type="button" class="qty-btn minus-btn" data-id="${combo.id}"><i class="fas fa-minus"></i></button>
                            <div class="qty-input-display" id="display-${combo.id}">0</div>
                            <input type="hidden" class="combo-qty-input" id="qty-${combo.id}" name="combos[${index}][so_luong]" value="0" disabled>
                            <input type="hidden" name="combos[${index}][id]" value="${combo.id}" class="combo-id-input" disabled>
                            <button type="button" class="qty-btn plus-btn" data-id="${combo.id}"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>`;
            });
            comboContainer.innerHTML = html;
            updateSummary();
        }

        // --- 4. XỬ LÝ SỐ LƯỢNG ---
        comboContainer.addEventListener('click', function(e) {
            const btn = e.target.closest('.qty-btn');
            if (!btn) return;

            const comboId = btn.dataset.id;
            const input = document.getElementById(`qty-${comboId}`);
            const display = document.getElementById(`display-${comboId}`);
            const card = document.getElementById(`card-${comboId}`);
            const idInput = card.querySelector('.combo-id-input');

            let qty = parseInt(input.value) || 0;
            if (btn.classList.contains('plus-btn')) qty++;
            else if (btn.classList.contains('minus-btn') && qty > 0) qty--;

            input.value = qty;
            display.textContent = qty;

            if (qty > 0) {
                card.classList.add('active');
                input.disabled = false;
                idInput.disabled = false;
            } else {
                card.classList.remove('active');
                input.disabled = true;
                idInput.disabled = true;
            }
            updateSummary();
        });

        function updateSummary() {
            let summaryText = [];
            document.querySelectorAll('.combo-admin-card.active').forEach(card => {
                const qtyInput = card.querySelector('.combo-qty-input');
                const nameLabel = card.querySelector('.combo-name-label');
                const qty = parseInt(qtyInput.value) || 0;
                if (qty > 0) summaryText.push(`${nameLabel.textContent.trim()} (x${qty})`);
            });
            
            if (summaryText.length > 0) {
                summaryDisplay.innerHTML = summaryText.join('; ');
                summaryDisplay.classList.remove('text-muted');
                summaryDisplay.classList.add('fw-bold', 'text-dark');
            } else {
                summaryDisplay.textContent = 'Chưa chọn combo nào.';
                summaryDisplay.classList.add('text-muted');
                summaryDisplay.classList.remove('fw-bold', 'text-dark');
            }
        }

        // --- 5. LOGIC CHECK BÀN TRỐNG ---
        function updateAvailableTables() {
            const selectedTime = timeInput.value;
            if (!selectedTime) return;
            
            const originalText = tableSelect.options[0] ? tableSelect.options[0].text : '';
            if(tableSelect.options[0]) tableSelect.options[0].text = "Đang kiểm tra...";
            tableSelect.disabled = true;
            
            const url = `{{ route('admin.dat-ban.ajax-get-available-tables') }}?time=${selectedTime}&exclude_booking_id=${currentBookingId}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    tableSelect.innerHTML = ''; 
                    tableSelect.disabled = false;
                    const defaultOption = document.createElement('option');
                    defaultOption.value = "";
                    defaultOption.textContent = "-- Chưa xếp bàn / Mang về --";
                    tableSelect.appendChild(defaultOption);

                    let isCurrentBanAvailable = false;
                    if (data.length > 0) {
                        data.forEach(ban => {
                            const option = document.createElement('option');
                            option.value = ban.id;
                            option.textContent = `Bàn ${ban.so_ban} (${ban.so_ghe} ghế)`;
                            if (ban.id == currentBanId) {
                                option.selected = true;
                                isCurrentBanAvailable = true;
                            }
                            tableSelect.appendChild(option);
                        });
                    }

                    // Xử lý logic nếu bàn hiện tại bị trùng khi đổi giờ
                    if (currentBanId && !isCurrentBanAvailable && originalGioDen != selectedTime) {
                         // Lấy thông tin bàn hiện tại từ Controller truyền xuống
                         const currentBanData = @json($datBan->banAn);
                         if (currentBanData) {
                            const warningOption = document.createElement('option');
                            warningOption.textContent = `⚠️ Bàn ${currentBanData.so_ban} (Hiện tại) - Đã có lịch khác giờ này`;
                            warningOption.value = currentBanId;
                            warningOption.selected = true;
                            warningOption.style.color = 'red';
                            tableSelect.insertBefore(warningOption, tableSelect.children[1]);
                         }
                    } else if (currentBanId && originalGioDen == selectedTime) {
                        // Nếu không đổi giờ, đảm bảo chọn bàn cũ
                        if (tableSelect.querySelector(`option[value="${currentBanId}"]`)) {
                            tableSelect.value = currentBanId;
                        }
                    }
                })
                .catch(() => {
                    if(tableSelect.options[0]) tableSelect.options[0].text = "Lỗi tải dữ liệu";
                    tableSelect.disabled = false;
                });
        }
        
        timeInput.addEventListener('change', updateAvailableTables);

        // --- 6. VALIDATE FORM ---
        const form = document.getElementById('datBanForm');
        form.addEventListener('submit', function(e) {
            const nguoiLon = parseInt(document.getElementById('nguoi_lon_input').value) || 0;
            const treEm = parseInt(document.getElementById('tre_em_input').value) || 0;
            const tongNguoi = nguoiLon + treEm;

            let tongCombo = 0;
            document.querySelectorAll('.combo-qty-input').forEach(input => {
                if (!input.disabled) tongCombo += parseInt(input.value) || 0;
            });

            if (tongCombo < tongNguoi) {
                e.preventDefault();
                showModal(tongNguoi, tongCombo);
                const comboArea = document.getElementById('combo-selection-container');
                comboArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // --- 7. CHẠY LẦN ĐẦU ---
        updateSummary(); // Cập nhật text tóm tắt dựa trên dữ liệu pre-fill
    });
</script>
@endsection