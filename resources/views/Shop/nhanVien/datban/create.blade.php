@extends('layouts.Shop.layout-nhanvien')
@section('title', 'Tạo đặt bàn mới')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Tạo đặt bàn mới (Khách đến ngay)</h5>
    </div>
    <div class="card-body">

        {{-- Hiển thị lỗi nếu có --}}
        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if($errors->any()) 
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div>
        @endif

        <form action="{{ route('nhanVien.datban.store') }}" method="post">
            @csrf
            <div class="row g-3">
                
                {{-- CỘT TRÁI: THÔNG TIN & THỜI GIAN --}}
                <div class="col-md-6">
                    <div class="card h-100 bg-light border-0">
                        <div class="card-body">
                            <h6 class="text-primary fw-bold">1. Thông tin khách & Thời gian</h6>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Tên khách hàng (*)</label>
                                <input name="ten_khach" class="form-control" value="{{ old('ten_khach') }}" required placeholder="Ví dụ: Anh Nam">
                            </div>
                            
                            {{-- THAY ĐỔI VỊ TRÍ VÀ THÊM EMAIL --}}
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">SĐT (*)</label>
                                    <input name="sdt_khach" class="form-control" value="{{ old('sdt_khach') }}" required>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input name="email_khach" class="form-control" type="email" value="{{ old('email_khach') }}" placeholder="Không bắt buộc">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Số khách (*)</label>
                                <input type="number" name="so_khach" class="form-control" value="{{ old('so_khach', 2) }}" min="1" required>
                            </div>
                            
                            {{-- Ô NHẬP GIỜ: BỎ onchange VÀ THAY BẰNG readOnly/style trong JS --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian đến (Tự động lấy giờ hiện tại)</label>
                                <input type="datetime-local" 
                                        name="gio_den" 
                                        id="inpGioDen" 
                                        class="form-control border-primary fw-bold" 
                                        value="{{ old('gio_den') }}" 
                                        required>
                                <small class="text-muted"><i>* Giờ check-in được cố định là thời điểm tạo phiếu.</i></small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: CHỌN BÀN --}}
                <div class="col-md-6">
                    <div class="card h-100 bg-light border-0">
                        <div class="card-body">
                            <h6 class="text-primary fw-bold">2. Chọn Bàn & Dịch vụ</h6>
                            <hr>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Chọn bàn trống (*)</label>
                                <select name="ban_id" id="selBanAn" class="form-control form-select" required>
                                    <option value="">-- Đang tải dữ liệu... --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Combo Buffet</label>
                                <select name="combo_id" class="form-control form-select">
                                    <option value="">-- Không chọn / Gọi món lẻ --</option>
                                    @foreach ($combos as $c)
                                        <option value="{{ $c->id }}" {{ old('combo_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->ten_combo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nhân viên phục vụ</label>
                                <select name="nhan_vien_id" class="form-control form-select">
                                    @foreach ($nhanViens as $nv)
                                        <option value="{{ $nv->id }}" {{ auth()->id() == $nv->id ? 'selected' : '' }}>
                                            {{ $nv->ho_ten }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="ghi_chu" class="form-control" rows="2" placeholder="Ghi chú thêm...">{{ old('ghi_chu') }}</textarea>
                </div>
            </div>

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary px-5 fw-bold">LƯU & BẮT ĐẦU PHỤC VỤ</button>
                <a href="{{ route('nhanVien.datban.index') }}" class="btn btn-secondary px-4">Hủy</a>
            </div>
        </form>

        {{-- SCRIPT XỬ LÝ: Đặt ở đây để đảm bảo chạy 100% --}}
        <script>
            // URL API (Đã sửa để dùng đường dẫn tuyệt đối)
            var CHECK_URL = "{{ url('/nhanVien/dat-ban/check-ban-trong') }}"; 
            var OLD_BAN_ID = "{{ old('ban_id') }}";

            // Hàm tìm bàn (Chạy mỗi khi đổi giờ hoặc mới vào trang)
            function timBanTrong() {
                var inpTime = document.getElementById('inpGioDen');
                var inpSoKhach = document.querySelector('input[name="so_khach"]'); // Lấy input số khách
                var selBan = document.getElementById('selBanAn');
                var timeVal = inpTime.value;
                var soKhachVal = inpSoKhach.value;

                if (!timeVal) {
                    selBan.innerHTML = '<option value="">-- Vui lòng chọn giờ trước --</option>';
                    return;
                }
                
                if (!soKhachVal || soKhachVal < 1) {
                    selBan.innerHTML = '<option value="">-- Số khách không hợp lệ --</option>';
                    return;
                }

                // Hiện trạng thái đang tải
                selBan.disabled = true;
                selBan.innerHTML = '<option>⏳ Đang tìm bàn trống...</option>';

                // Tạo URL với cả hai tham số: time và so_khach
                var url = CHECK_URL + "?time=" + timeVal + "&so_khach=" + soKhachVal;

                // Gọi Server
                fetch(url)
                    .then(function(res) {
                        // Kiểm tra HTTP Status
                        if (!res.ok) {
                            throw new Error('Server returned error: ' + res.status);
                        }
                        return res.json();
                    })
                    .then(function(data) {
                        selBan.innerHTML = '';
                        selBan.disabled = false;

                        // Nếu không có bàn nào
                        if (!data || data.length === 0) {
                            selBan.innerHTML = '<option value="">❌ Hết bàn trống / Không đủ ghế giờ này</option>';
                            return;
                        }

                        // Tạo option mặc định
                        var defOpt = document.createElement('option');
                        defOpt.value = "";
                        defOpt.text = "-- Chọn bàn ăn --";
                        selBan.appendChild(defOpt);

                        // Duyệt danh sách bàn
                        data.forEach(function(ban) {
                            var opt = document.createElement('option');
                            opt.value = ban.id;
                            
                            // Hiển thị gọn gàng
                            opt.text = "Bàn " + ban.so_ban + " (" + ban.so_ghe + " ghế)";
                            
                            // Chọn lại bàn cũ nếu form reload
                            if(OLD_BAN_ID == ban.id) opt.selected = true;
                            
                            selBan.appendChild(opt);
                        });
                    })
                    .catch(function(err) {
                        console.error(err);
                        selBan.innerHTML = '<option>⚠️ Lỗi kết nối Server</option>';
                        selBan.disabled = false;
                    });
            }

            // Tự động chạy khi trang vừa load xong và gắn sự kiện onchange cho so_khach
            document.addEventListener('DOMContentLoaded', function() {
                var inpTime = document.getElementById('inpGioDen');
                var inpSoKhach = document.querySelector('input[name="so_khach"]');
                
                // 1. LUÔN LUÔN ghi đè giờ hiện tại và VÔ HIỆU HÓA nó (đảm bảo giờ đến là giờ tạo MỚI NHẤT)
                if(inpTime) {
                    var now = new Date();
                    // Chỉnh múi giờ để input datetime-local hiển thị đúng giờ địa phương
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                    
                    // GHI ĐÈ GIÁ TRỊ CŨ (old()) bằng giờ hiện tại
                    inpTime.value = now.toISOString().slice(0, 16);
                    
                    // VÔ HIỆU HÓA input để nhân viên không thay đổi được giờ check-in
                    inpTime.readOnly = true;
                    inpTime.style.backgroundColor = '#e9ecef'; // Màu xám nhạt
                }

                // 2. Gắn sự kiện onchange cho Số khách
                if(inpSoKhach) {
                    inpSoKhach.addEventListener('change', timBanTrong);
                }

                // 3. Gọi hàm tìm bàn ngay lập tức (dựa vào giờ mặc định là hiện tại)
                if(inpTime && inpTime.value) {
                    // Chạy sau 100ms để đảm bảo các yếu tố khác đã được render xong
                    setTimeout(timBanTrong, 100); 
                }
            });
        </script>
    </div>
</div>
@endsection