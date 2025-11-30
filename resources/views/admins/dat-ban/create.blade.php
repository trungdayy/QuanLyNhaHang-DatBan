@extends('layouts.admins.layout-admin')

@section('title', 'Tạo Đơn Đặt Bàn Mới')

@section('style')
<style>
    /* --- CSS Cũ giữ nguyên --- */
    
    /* [MỚI] CSS cho Combo Selection */
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
        box-shadow: 0 0 5px rgba(0, 150, 136, 0.3);
        background-color: #f0faf9;
    }
    .combo-name-price {
        margin-bottom: 10px;
    }
    .combo-name-price label {
        font-weight: 700;
        color: #333;
    }
    .combo-name-price small {
        font-size: 0.85em;
        color: #777;
    }
    
    /* Quantity Control */
    .combo-qty-control {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #ccc;
        border-radius: 4px;
        overflow: hidden;
    }
    .qty-btn {
        padding: 6px 12px;
        border: none;
        background: #e9ecef;
        cursor: pointer;
        transition: 0.2s;
        font-weight: bold;
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

    /* Summary Header */
    .combo-summary-header {
        cursor: pointer;
        padding: 10px 15px;
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
    }
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
                        
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
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

                            {{-- THÔNG TIN ĐẶT BÀN --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Giờ Khách Đến (*)</label>
                                <input class="form-control" type="datetime-local" name="gio_den" id="gio_den_input" value="{{ old('gio_den') }}" required>
                            </div>
                            
                            {{-- CHỌN BÀN --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Chọn Bàn (Nếu có)</label>
                                <select class="form-control" name="ban_id" id="ban_id_select">
                                    <option value="">-- Chưa xếp bàn / Mang về --</option>
                                    
                                    @foreach($banAns as $ban)
                                        @php
                                            $isBusy = in_array($ban->trang_thai, ['dang_phuc_vu', 'da_dat']);
                                            $statusText = match($ban->trang_thai) {
                                                'dang_phuc_vu' => '(Đang phục vụ)',
                                                'da_dat' => '(Đã đặt)',
                                                default => ''
                                            };
                                            $style = $isBusy ? 'background-color: #ffeeee; color: #d9534f;' : '';
                                        @endphp

                                        <option value="{{ $ban->id }}" 
                                            {{ old('ban_id') == $ban->id ? 'selected' : '' }}
                                            {{ $isBusy ? 'disabled' : '' }} 
                                            style="{{ $style }}"
                                        >
                                            Bàn {{ $ban->so_ban }} - {{ $ban->so_ghe }} ghế 
                                            @if($ban->khuVuc) ({{ $ban->khuVuc->ten_khu_vuc }}) @endif
                                            {{ $statusText }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted"><i>* Có thể để trống nếu chưa xếp bàn hoặc mua mang về.</i></small>
                            </div>

                            {{-- 🔥 [SỬA] KHU VỰC CHỌN COMBO MỚI - GOM LẠI --}}
                            <div class="form-group col-md-12 mt-3">
                                <label class="control-label fw-bold mb-2 d-block">Chọn Combo Buffet</label>
                                
                                {{-- HEADER TÓM TẮT --}}
                                <div class="combo-summary-header" data-bs-toggle="collapse" data-bs-target="#combo-selection-collapse" aria-expanded="false" aria-controls="combo-selection-collapse" id="combo-summary-header">
                                    <div class="summary-text">
                                        <i class="fas fa-list-check me-2"></i> 
                                        <span id="combo-summary-display" class="text-muted">Chưa chọn combo nào.</span>
                                    </div>
                                    <i class="fas fa-chevron-down small summary-icon"></i>
                                </div>

                                {{-- DANH SÁCH CHỌN THU GỌN (MẶC ĐỊNH ĐÓNG) --}}
                                <div class="collapse" id="combo-selection-collapse">
                                    <div id="combo-selection-container" class="row g-3 mt-1">
                                        @foreach ($combos as $index => $combo)
                                            <div class="col-md-3">
                                                <div class="combo-admin-card" id="card-{{ $combo->id }}">
                                                    <div class="combo-name-price">
                                                        <label class="mb-0 d-block text-primary combo-name-label">
                                                            {{ $combo->ten_combo }}
                                                        </label>
                                                        <small>{{ number_format($combo->gia_co_ban) }} đ / suất</small>
                                                    </div>
                                                    
                                                    <div class="combo-qty-control">
                                                        <button type="button" class="qty-btn minus-btn" data-id="{{ $combo->id }}"><i class="fas fa-minus"></i></button>
                                                        
                                                        <div class="qty-input-display" id="display-{{ $combo->id }}">
                                                            {{ old('combos.'.$index.'.so_luong', 0) }}
                                                        </div>
                                                        
                                                        <input type="hidden" 
                                                               class="combo-qty-input"
                                                               id="qty-{{ $combo->id }}"
                                                               name="combos[{{ $index }}][so_luong]"
                                                               value="{{ old('combos.'.$index.'.so_luong', 0) }}">
                                                               
                                                        <input type="hidden" name="combos[{{ $index }}][id]" value="{{ $combo->id }}" class="combo-id-input">

                                                        <button type="button" class="qty-btn plus-btn" data-id="{{ $combo->id }}"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            {{-- HẾT SỬA ĐOẠN CHỌN COMBO --}}
                            
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
    </main>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timeInput = document.getElementById('gio_den_input');
        const tableSelect = document.getElementById('ban_id_select');
        const comboContainer = document.getElementById('combo-selection-container');
        const summaryDisplay = document.getElementById('combo-summary-display');
        const summaryIcon = document.querySelector('.summary-icon');
        const comboCollapse = document.getElementById('combo-selection-collapse');
        const summaryHeader = document.getElementById('combo-summary-header');
        const oldBanId = "{{ old('ban_id') }}";
        
        // [FIX LỖI] Bật/tắt thủ công Collapse khi Bootstrap JS bị thiếu
        summaryHeader.addEventListener('click', function() {
            const isOpen = comboCollapse.classList.contains('show');
            
            // Toggle lớp 'show'
            comboCollapse.classList.toggle('show');
            
            // Xử lý icon xoay và thuộc tính aria-expanded
            if (isOpen) {
                summaryIcon.classList.remove('rotated');
                this.setAttribute('aria-expanded', 'false');
            } else {
                summaryIcon.classList.add('rotated');
                this.setAttribute('aria-expanded', 'true');
            }
        });
        
        // Gán sự kiện xoay icon (giữ lại logic cũ)
        comboCollapse.addEventListener('show.bs.collapse', () => {
            summaryIcon.classList.add('rotated');
        });
        comboCollapse.addEventListener('hide.bs.collapse', () => {
            summaryIcon.classList.remove('rotated');
        });

        // Hàm cập nhật tóm tắt combo và trạng thái disable/enable input
        function updateSummary() {
            let summaryText = [];
            
            document.querySelectorAll('.combo-admin-card').forEach(card => {
                const qtyInput = card.querySelector('.combo-qty-input'); 
                const idInput = card.querySelector('input[type="hidden"][name*="[id]"]'); // Sửa selector an toàn hơn
                const nameLabel = card.querySelector('.combo-name-label');
                
                if (!qtyInput || !idInput || !nameLabel) return; 

                let qty = parseInt(qtyInput.value) || 0;

                if (qty > 0) {
                    // ENABLE: Bật cả 2 input để gửi lên server
                    card.classList.add('active');
                    qtyInput.disabled = false;
                    idInput.disabled = false;
                    
                    const name = nameLabel.textContent.trim();
                    summaryText.push(`${name} (x${qty})`);

                } else {
                    // DISABLE: Tắt cả 2 input nếu số lượng là 0
                    card.classList.remove('active');
                    qtyInput.disabled = true;
                    idInput.disabled = true;
                }
            });
            
            // Cập nhật text tóm tắt
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

        // LOGIC XỬ LÝ COMBO QUANTITY VÀ HIỂN THỊ
        comboContainer.addEventListener('click', function(e) {
            if (e.target.closest('.plus-btn') || e.target.closest('.minus-btn')) {
                const btn = e.target.closest('.qty-btn');
                const comboId = btn.dataset.id;
                const input = document.getElementById(`qty-${comboId}`);
                const display = document.getElementById(`display-${comboId}`);
                let qty = parseInt(input.value) || 0;

                if (btn.classList.contains('plus-btn')) {
                    qty++;
                } else if (btn.classList.contains('minus-btn') && qty > 0) {
                    qty--;
                }

                input.value = qty;
                display.textContent = qty;
                updateSummary();
            }
        });

        // Cập nhật trạng thái bàn trống theo giờ
        function updateAvailableTables() {
            const selectedTime = timeInput.value;
            const excludeBookingId = 0; 

            if (!selectedTime) {
                return;
            }
            
            const originalText = tableSelect.options[0].text;
            tableSelect.options[0].text = "Đang tải danh sách bàn...";
            tableSelect.disabled = true;
            
            const url = `{{ route('admin.ajax.get-available-tables') }}?time=${selectedTime}&exclude_booking_id=${excludeBookingId}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    tableSelect.innerHTML = ''; 
                    tableSelect.disabled = false;
                    
                    tableSelect.innerHTML = '<option value="">-- Chưa xếp bàn / Mang về --</option>';
                    
                    if (data.length > 0) {
                        data.forEach(ban => {
                            const option = document.createElement('option');
                            option.value = ban.id;
                            option.textContent = `Bàn ${ban.so_ban} (${ban.so_ghe} ghế)`;
                            if (oldBanId && ban.id == oldBanId) {
                                option.selected = true;
                            }
                            tableSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải bàn:', error);
                    tableSelect.innerHTML = '<option value="">Lỗi tải dữ liệu - Vui lòng thử lại</option>';
                    tableSelect.disabled = false;
                });
        }
        
        // Gán sự kiện
        timeInput.addEventListener('change', updateAvailableTables);
        if (timeInput.value) {
            updateAvailableTables();
        }

        // Chạy lần đầu cho combo
        updateSummary();
    });
</script>
@endsection