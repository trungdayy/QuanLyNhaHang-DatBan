@extends('layouts.admins.layout-admin')

@section('title', 'Sửa Đơn Đặt Bàn')

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
                                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
                            </div>
                        @endif
                         @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form class="row" method="POST" action="{{ route('admin.dat-ban.update', $datBan->id) }}">
                            @csrf
                            {{-- @method('PUT') --}} 
                            {{-- Lưu ý: Route update của bạn đang dùng POST hay PUT? Nếu resource thì cần @method('PUT') --}}
                            
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
                                <label class="control-label">Số Lượng Khách (*)</label>
                                <input class="form-control" type="number" name="so_khach" min="1" value="{{ old('so_khach', $datBan->so_khach) }}" required>
                            </div>
                             <div class="form-group col-md-3">
                                <label class="control-label">Tiền Cọc (Nếu có)</label>
                                <input class="form-control" type="number" name="tien_coc" value="{{ old('tien_coc', $datBan->tien_coc) }}" min="0">
                            </div>

                            {{-- THÔNG TIN ĐẶT BÀN --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Giờ Khách Đến (*)</label>
                                @php
                                    $gioDenVal = old('gio_den', $datBan->gio_den ? \Carbon\Carbon::parse($datBan->gio_den)->format('Y-m-d\TH:i') : '');
                                @endphp
                                <input class="form-control" type="datetime-local" name="gio_den" id="gio_den_input" value="{{ $gioDenVal }}" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="control-label">Chọn Bàn (*)</label>
                                <select class="form-control" name="ban_id" id="ban_id_select" required>
                                    <option value="">-- Vui lòng chọn Giờ đến trước --</option>
                                    
                                    {{-- Hiển thị danh sách ban đầu (Server render) --}}
                                    @foreach ($banAns as $ban)
                                        @php
                                            $isCurrent = ($ban->id == $datBan->ban_id);
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
                                <small class="text-muted"><i>* Bàn đang chọn: Bàn {{ $datBan->banAn->so_ban ?? 'N/A' }}</i></small>
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

                            <div class="form-group col-md-12">
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
    </main>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timeInput = document.getElementById('gio_den_input');
        const tableSelect = document.getElementById('ban_id_select');
        
        // ID của đơn hàng hiện tại (để loại trừ khi check trùng)
        const currentBookingId = "{{ $datBan->id }}";
        // ID bàn đang được chọn trong DB
        const currentBanId = "{{ $datBan->ban_id }}";

        function updateAvailableTables() {
            const selectedTime = timeInput.value;
            if (!selectedTime) return;

            tableSelect.innerHTML = '<option value="">Đang kiểm tra bàn trống...</option>';
            tableSelect.disabled = true;

            // Gọi API, truyền thêm exclude_booking_id để server biết mà bỏ qua đơn này
            const url = `{{ route('admin.ajax.get-available-tables') }}?time=${selectedTime}&exclude_booking_id=${currentBookingId}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    tableSelect.innerHTML = ''; 
                    tableSelect.disabled = false;
                    
                    let isCurrentBanAvailable = false;

                    if (data.length === 0) {
                        tableSelect.innerHTML = '<option value="">❌ Không có bàn trống</option>';
                    } else {
                        tableSelect.innerHTML = '<option value="">-- Chọn bàn --</option>';
                        
                        data.forEach(ban => {
                            const option = document.createElement('option');
                            option.value = ban.id;
                            
                            let label = `Bàn ${ban.so_ban} - ${ban.so_ghe} ghế`;
                            if(ban.khu_vuc) label += ` (${ban.khu_vuc})`;
                            
                            option.textContent = label;
                            
                            // Nếu là bàn cũ thì select lại
                            if (ban.id == currentBanId) {
                                option.selected = true;
                                isCurrentBanAvailable = true;
                            }
                            tableSelect.appendChild(option);
                        });
                    }
                    
                    // Nếu bàn cũ không còn khả dụng (bị người khác chiếm trong giờ mới chọn)
                    if (!isCurrentBanAvailable) {
                        const warningOption = document.createElement('option');
                        warningOption.textContent = `⚠️ Bàn hiện tại (${currentBanId}) bị trùng lịch`;
                        warningOption.value = currentBanId;
                        warningOption.selected = true;
                        warningOption.disabled = true; // Chặn không cho chọn tiếp
                        warningOption.style.color = 'red';
                        tableSelect.prepend(warningOption);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải bàn:', error);
                    tableSelect.innerHTML = '<option value="">Lỗi kết nối server</option>';
                    tableSelect.disabled = false;
                });
        }

        timeInput.addEventListener('change', updateAvailableTables);
        
        // Chạy 1 lần khi load trang
        if (timeInput.value) {
            // Không gọi ajax ngay lập tức để tránh reset lựa chọn ban đầu của server render
            // Chỉ gọi khi user THAY ĐỔI giờ. 
            // Tuy nhiên nếu muốn chắc chắn bàn hiện tại còn trống hay không thì có thể gọi.
            // Ở đây ta để server render lần đầu cho nhanh.
        }
    });
</script>
@endsection