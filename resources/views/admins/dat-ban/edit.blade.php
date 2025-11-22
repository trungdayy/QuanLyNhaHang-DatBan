@extends('layouts.admins.layout-admin')

@section('title', 'Sửa Đơn Đặt Bàn')

@section('content')
    <main class="app-content">
        
        {{-- Tiêu đề trang --}}
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
                        
                        {{-- Hiển thị lỗi Validation nếu có --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                         @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Form Sửa --}}
                        <form class="row" method="POST" action="{{ route('admin.dat-ban.update', $datBan->id) }}">
                            @csrf
                            
                            {{-- THÔNG TIN KHÁCH HÀNG --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Tên Khách Hàng (*)</label>
                                <input class="form-control" type="text" name="ten_khach" value="{{ old('ten_khach', $datBan->ten_khach) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Email Khách Hàng</label>
                                <input class="form-control" type="email" name="email_khach" value="{{ old('email_khach', $datBan->email_khach) }}">
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Số Điện Thoại (*)</label>
                                <input class="form-control" type="text" name="sdt_khach" value="{{ old('sdt_khach', $datBan->sdt_khach) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Số Lượng Khách (*)</label>
                                <input class="form-control" type="number" name="so_khach" min="1" value="{{ old('so_khach', $datBan->so_khach) }}" required>
                            </div>

                            {{-- THÔNG TIN ĐẶT BÀN --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Giờ Khách Đến (*)</label>
                                {{-- 💡 Định dạng lại giờ đến cho input datetime-local --}}
                                @php
                                    $gioDenFormatted = $datBan->gio_den ? \Carbon\Carbon::parse($datBan->gio_den)->format('Y-m-d\TH:i') : '';
                                @endphp
                                <input class="form-control" type="datetime-local" name="gio_den" id="gio_den_input" value="{{ old('gio_den', $gioDenFormatted) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Chọn Bàn (*)</label>
                                {{-- 💡 Thêm ID để AJAX hoạt động --}}
                                <select class="form-control" name="ban_id" id="ban_id_select" required>
                                    <option value="">-- Vui lòng chọn Giờ đến trước --</option>
                                    
                                    {{-- Tải tất cả bàn (JS sẽ lọc), nhưng ưu tiên chọn bàn đã lưu --}}
                                    @foreach ($banAns as $ban)
                                        <option value="{{ $ban->id }}" {{ old('ban_id', $datBan->ban_id) == $ban->id ? 'selected' : '' }}>
                                            {{ $ban->so_ban }} ({{ $ban->so_ghe }} ghế)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Chọn Combo (Nếu có)</label>
                                <select class="form-control" name="combo_id">
                                    <option value="">-- Không chọn combo --</option>
                                     @foreach ($combos as $combo)
                                        <option value="{{ $combo->id }}" {{ old('combo_id', $datBan->combo_id) == $combo->id ? 'selected' : '' }}>
                                            {{ $combo->ten_combo }} ({{ number_format($combo->gia_co_ban) }} đ)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- THÔNG TIN KHÁC --}}
                            <div class="form-group col-md-12">
                                <label class="control-label">Ghi Chú (Nếu có)</label>
                                <textarea class="form-control" name="ghi_chu" rows="3">{{ old('ghi_chu', $datBan->ghi_chu) }}</textarea>
                            </div>
                            
                            {{-- Nút bấm --}}
                            <div class="form-group col-md-12">
                                <button class="btn btn-save" type="submit">Cập Nhật Đặt Bàn</button>
                                <a class="btn btn-cancel" href="{{ route('admin.dat-ban.index') }}">Hủy bỏ</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('script')
{{-- 💡 THÊM JAVASCRIPT AJAX ĐỂ LỌC BÀN (Tương tự trang Create) --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timeInput = document.getElementById('gio_den_input');
        const tableSelect = document.getElementById('ban_id_select');
        
        // Lấy ID bàn hiện tại đang được chọn
        const currentSelectedBanId = "{{ $datBan->ban_id }}";

        function updateAvailableTables() {
            const selectedTime = timeInput.value;
            
            if (!selectedTime) {
                tableSelect.innerHTML = '<option value="">-- Vui lòng chọn Giờ đến trước --</option>';
                return;
            }

            tableSelect.innerHTML = '<option value="">Đang tải danh sách bàn...</option>';
            tableSelect.disabled = true;

            const url = `{{ route('admin.ajax.get-available-tables') }}?time=${selectedTime}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    tableSelect.innerHTML = ''; 
                    tableSelect.disabled = false;
                    
                    let isCurrentBanAvailable = false;

                    if (data.length > 0) {
                         tableSelect.innerHTML = '<option value="">-- Chọn bàn --</option>';
                        data.forEach(ban => {
                            const option = document.createElement('option');
                            option.value = ban.id;
                            option.textContent = `${ban.so_ban} (${ban.so_ghe} ghế)`;
                            
                            if (ban.id == currentSelectedBanId) {
                                option.selected = true;
                                isCurrentBanAvailable = true;
                            }
                            tableSelect.appendChild(option);
                        });
                    }
                    
                    // Nếu bàn hiện tại không còn trống (bị trùng)
                    if (!isCurrentBanAvailable) {
                        // Vẫn thêm bàn hiện tại vào list nhưng báo lỗi
                        const oldOption = document.createElement('option');
                        oldOption.value = currentSelectedBanId;
                        oldOption.textContent = `(Bàn hiện tại - BỊ TRÙNG)`;
                        oldOption.selected = true;
                        oldOption.style.color = 'red';
                        tableSelect.appendChild(oldOption);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải bàn:', error);
                    tableSelect.innerHTML = '<option value="">Lỗi khi tải danh sách bàn</option>';
                    tableSelect.disabled = false;
                });
        }

        // Gắn sự kiện 'change' vào ô nhập giờ
        timeInput.addEventListener('change', updateAvailableTables);

        // Tự động chạy khi tải trang (vì đã có giá trị giờ)
        if (timeInput.value) {
            updateAvailableTables();
        }
    });
</script>
@endsection