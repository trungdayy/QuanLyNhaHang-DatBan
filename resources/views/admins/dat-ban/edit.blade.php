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
                            {{-- 🔥 ĐÃ XÓA @method('PUT') ĐỂ KHỚP VỚI ROUTE POST --}}
                            
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
                                <input class="form-control" type="number" name="nguoi_lon" min="1" value="{{ old('nguoi_lon', $datBan->nguoi_lon) }}" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label">Trẻ em (<1m3)</label>
                                <input class="form-control" type="number" name="tre_em" min="0" value="{{ old('tre_em', $datBan->tre_em) }}">
                            </div>

                             <div class="form-group col-md-3">
                                <label class="control-label">Tiền Cọc (VNĐ)</label>
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

                            {{-- CHỌN BÀN --}}
                            <div class="form-group col-md-4">
                                <label class="control-label">Chọn Bàn (Nếu có)</label>
                                <select class="form-control" name="ban_id" id="ban_id_select">
                                    <option value="">-- Chưa xếp bàn / Mang về --</option>
                                    
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
                                <small class="text-muted"><i>* Bàn hiện tại: {{ $datBan->banAn->so_ban ?? 'Chưa xếp bàn' }}</i></small>
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
        
        const currentBookingId = "{{ $datBan->id }}";
        const currentBanId = "{{ $datBan->ban_id }}";

        function updateAvailableTables() {
            const selectedTime = timeInput.value;
            if (!selectedTime) return;

            const originalText = tableSelect.options[0] ? tableSelect.options[0].text : '-- Chưa xếp bàn / Mang về --';
            if(tableSelect.options[0]) tableSelect.options[0].text = "Đang kiểm tra bàn trống...";
            
            tableSelect.disabled = true;

            const url = `{{ route('admin.ajax.get-available-tables') }}?time=${selectedTime}&exclude_booking_id=${currentBookingId}`;
            
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
                            
                            let label = `Bàn ${ban.so_ban} - ${ban.so_ghe} ghế`;
                            if(ban.khu_vuc) label += ` (${ban.khu_vuc.ten_khu_vuc})`;
                            
                            option.textContent = label;
                            
                            if (currentBanId && ban.id == currentBanId) {
                                option.selected = true;
                                isCurrentBanAvailable = true;
                            }
                            tableSelect.appendChild(option);
                        });
                    }
                    
                    if (currentBanId && !isCurrentBanAvailable) {
                        const warningOption = document.createElement('option');
                        warningOption.textContent = `⚠️ Bàn hiện tại (${currentBanId}) bị trùng lịch`;
                        warningOption.value = currentBanId;
                        warningOption.selected = true;
                        warningOption.style.color = 'red';
                        tableSelect.insertBefore(warningOption, tableSelect.children[1]);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải bàn:', error);
                    if(tableSelect.options[0]) tableSelect.options[0].text = "Lỗi tải dữ liệu";
                    tableSelect.disabled = false;
                });
        }

        timeInput.addEventListener('change', updateAvailableTables);
    });
</script>
@endsection