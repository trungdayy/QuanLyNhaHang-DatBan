@extends('layouts.admins.layout-admin')

@section('title', 'Tạo Đơn Đặt Bàn Mới')

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

                        <form class="row" method="POST" action="{{ route('admin.dat-ban.store') }}">
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
                                <input class="form-control" type="number" name="nguoi_lon" min="1" value="{{ old('nguoi_lon', 1) }}" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="control-label">Trẻ em (<1m3)</label>
                                <input class="form-control" type="number" name="tre_em" min="0" value="{{ old('tre_em', 0) }}">
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
                            
                            {{-- 🔥 SỬA: Bỏ bắt buộc chọn bàn --}}
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

                            <div class="form-group col-md-4">
                                <label class="control-label">Chọn Combo (Nếu có)</label>
                                <select class="form-control" name="combo_id">
                                    <option value="">-- Không chọn combo --</option>
                                     @foreach ($combos as $combo)
                                        <option value="{{ $combo->id }}" {{ old('combo_id') == $combo->id ? 'selected' : '' }}>
                                            {{ $combo->ten_combo }} ({{ number_format($combo->gia_co_ban) }} đ)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="control-label">Ghi Chú (Nếu có)</label>
                                <textarea class="form-control" name="ghi_chu" rows="3" placeholder="Ghi chú thêm về đơn đặt bàn...">{{ old('ghi_chu') }}</textarea>
                            </div>
                            
                            <div class="form-group col-md-12">
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
        const oldBanId = "{{ old('ban_id') }}";

        function updateAvailableTables() {
            const selectedTime = timeInput.value;
            if (!selectedTime) {
                // Nếu chưa chọn giờ, giữ nguyên option mặc định
                return;
            }
            // Hiển thị trạng thái đang tải nhưng vẫn giữ option đầu tiên
            const originalText = tableSelect.options[0].text;
            tableSelect.options[0].text = "Đang tải danh sách bàn...";
            tableSelect.disabled = true;
            
            const url = `{{ route('admin.ajax.get-available-tables') }}?time=${selectedTime}`;
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    tableSelect.innerHTML = ''; 
                    tableSelect.disabled = false;
                    
                    // 🔥 SỬA: Option mặc định rõ ràng hơn
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
                    
                    if (oldBanId && !tableSelect.querySelector(`option[value="${oldBanId}"]`)) {
                        const oldOption = document.createElement('option');
                        oldOption.value = oldBanId;
                        oldOption.textContent = `(Đã chọn Bàn cũ - NHƯNG BỊ TRÙNG)`;
                        oldOption.selected = true;
                        oldOption.style.color = 'red';
                        tableSelect.appendChild(oldOption);
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi tải bàn:', error);
                    tableSelect.innerHTML = '<option value="">Lỗi tải dữ liệu - Vui lòng thử lại</option>';
                    tableSelect.disabled = false;
                });
        }
        timeInput.addEventListener('change', updateAvailableTables);
        if (timeInput.value) {
            updateAvailableTables();
        }
    });
</script>
@endsection