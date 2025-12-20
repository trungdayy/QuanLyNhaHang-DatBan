@extends('layouts.admins.layout-admin')

@section('title', 'Dashboard')

@section('style')
<style>
    /* ====== TỔNG THỂ ====== */
    .app-content {
        padding: 20px;
    }

    .app-title {
        background: #ffffff;
        padding: 15px 20px;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    }

    .app-breadcrumb .breadcrumb-item a {
        font-size: 16px;
        color: #0d6efd;
    }

    /* ====== KPI WIDGET ====== */
    .widget-small {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        transition: 0.2s;
    }

    .widget-small:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    }

    .widget-small .icon {
        width: 60px;
        height: 60px;
        background: #f1f5fb;
        color: #4c6ef5;
        border-radius: 12px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-right: 15px;
    }

    .widget-small.primary .icon { background: #e7f1ff; color: #0d6efd; }
    .widget-small.info .icon { background: #e8f7ff; color: #0dcaf0; }
    .widget-small.warning .icon { background: #fff3cd; color: #ffc107; }
    .widget-small.danger .icon { background: #fdecea; color: #dc3545; }

    .widget-small .info h4 {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .widget-small .info p {
        margin: 0;
        font-size: 14px;
    }

    .info-tong {
        font-size: 13px;
        color: #666;
    }

    /* ====== TILE ====== */
    .tile {
        background: #ffffff;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.06);
    }

    .tile-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }

    /* ====== TABLE ====== */
    .table {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
    }

    .table thead {
        font-size: 14px;
        background: #002b5b;
    }

    .table tbody tr:hover {
        background: #f8f9fa;
    }

    .progress {
        height: 8px;
        border-radius: 10px;
    }

    .badge {
        padding: 6px 10px;
        font-size: 12px;
    }

    canvas {
        max-height: 320px;
    }

    .form-select {
        border-radius: 10px;
        padding: 6px 10px;
    }

    .text-center p {
        color: #666;
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
<main class="app-content">

    <div class="row">
        <div class="col-md-12">
            <div class="app-title">
                <ul class="app-breadcrumb breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><b>Bảng điều khiển</b></a></li>
                </ul>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label for="date-range" style="margin: 0; font-weight: 600; color: #333;">Chọn khoảng thời gian:</label>
                        <input type="text" id="date-range" class="form-control" style="width: 300px; cursor: pointer;" readonly placeholder="Chọn khoảng thời gian">
                        <button type="button" id="btn-apply-date" class="btn btn-primary btn-sm">
                            <i class="fa fa-filter"></i> Áp dụng
                        </button>
                        <button type="button" id="btn-reset-date" class="btn btn-secondary btn-sm">
                            <i class="fa fa-undo"></i> Reset
                        </button>
                    </div>
                    <div id="clock"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        {{-- CỘT 1 --}}
        <div class="col-md-12 col-lg-6">
            <div class="row">

                {{-- KPI 1 --}}
                <div class="col-md-6">
                    <div class="widget-small primary coloured-icon">
                        <i class='icon bx bxs-user-account fa-3x'></i>
                        <div class="info">
                            <h4>Tổng khách hàng</h4>
                            <p><b>{{ $tongKhach }} khách hàng</b></p>
                            <p class="info-tong">
                                Tỉ lệ khách hàng quay lại:
                                <b class="{{ $tiLeQuayLai >= 50 ? 'text-success' : 'text-danger' }}">
                                    {{ $tiLeQuayLai }}%
                                </b>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- KPI 2 --}}
                <div class="col-md-6">
                    <div class="widget-small info coloured-icon">
                        <i class='icon bx bxs-data fa-3x'></i>
                        <div class="info">
                            <h4>Tổng món ăn</h4>
                            <p><b>{{ $tongMonAn }} món</b></p>
                            <p class="info-tong">Tổng số món ăn hiện có trong combo.</p>
                        </div>
                    </div>
                </div>

                {{-- KPI 3 --}}
                <div class="col-md-6">
                    <div class="widget-small warning coloured-icon">
                        <i class='icon bx bxs-shopping-bags fa-3x'></i>
                        <div class="info">
                            <h4>Tổng đơn hàng</h4>
                            <p><b>{{ $tongDonHang }} đơn hàng</b></p>
                            <p class="info-tong">Tổng số hóa đơn bán hàng đã tạo.</p>
                        </div>
                    </div>
                </div>

                {{-- KPI 4 --}}
                <div class="col-md-6">
                    <div class="widget-small danger coloured-icon">
                        <i class='icon bx bxs-dollar-circle fa-3x'></i>
                        <div class="info">
                            <h4>Tổng doanh thu</h4>
                            <p><b>{{ number_format($tongDoanhThu, 0, ',', '.') }} đ</b></p>
                            <p class="info-tong">Tổng doanh thu của nhà hàng.</p>
                        </div>
                    </div>
                </div>

                {{-- BẢNG 1 --}}
                <div class="col-md-12">
                    <div class="tile">
                        <h3 class="tile-title">Combo buffet bán chạy</h3>
                        <div style="overflow-x:auto;">
                            <table class="table table-bordered text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Top</th>
                                        <th>Tên combo</th>
                                        <th>Số lượt bán</th>
                                        <th>Tổng doanh thu</th>
                                        <th>Tỷ lệ đặt</th>
                                        <th>Tỷ lệ hủy</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($comboBanChay as $index => $combo)
                                    <tr>
                                        <td>#{{ $index + 1 }}</td>
                                        <td><strong>{{ $combo->ten_combo }}</strong></td>
                                        <td>
                                            {{ $combo->so_luot_ban }} lượt
                                            <div class="progress mt-1">
                                                <div class="progress-bar bg-success" style="width: {{ ($combo->so_luot_ban / $comboBanChay->max('so_luot_ban')) * 100 }}%"></div>
                                            </div>
                                        </td>
                                        <td class="text-success fw-bold">{{ number_format($combo->tong_doanh_thu, 0, ',', '.') }} đ</td>
                                        <td class="fw-semibold text-primary">{{ $combo->ti_le_dat }}%</td>
                                        <td class="fw-semibold text-danger">{{ $combo->ti_le_huy }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- BẢNG 2 --}}
                <div class="col-md-12">
                    <div class="tile">
                        <h3 class="tile-title">Khách hàng tiềm năng</h3>
                        <div style="overflow-x:auto;">
                            <table class="table table-bordered text-center align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Top</th>
                                        <th>Tên khách hàng</th>
                                        <th>Số điện thoại</th>
                                        <th>Số lần đặt</th>
                                        <th>Tổng chi tiêu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topKhachHang as $index => $kh)
                                    <tr>
                                        <td>#{{ $index + 1 }}</td>
                                        <td><strong>{{ $kh->ten_khach }}</strong></td>
                                        <td>{{ $kh->sdt_khach }}</td>
                                        <td>
                                            {{ $kh->so_lan_dat }} lần
                                            <div class="progress mt-1">
                                                <div class="progress-bar bg-info" style="width: {{ ($kh->so_lan_dat / $topKhachHang->max('so_lan_dat')) * 100 }}%"></div>
                                            </div>
                                        </td>
                                        <td class="text-success fw-bold">{{ number_format($kh->tong_chi_tieu, 0, ',', '.') }} đ</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- BẢNG 3 --}}
                <div class="col-md-12">
                    <div class="title">
                        <h3 class="tile-title">Hóa đơn chưa thanh toán</h3>
                        <div style="overflow-x:auto;">
                            <table class="table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Mã hóa đơn</th>
                                        <th>Tên khách</th>
                                        <th>Tổng tiền</th>
                                        <th>Thực thu</th>
                                        <th>Ngày</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($donHangMoi as $don)
                                    @php
                                        $chiTiet = $don->chiTietHoaDon;
                                        $tongTienComboMon = $chiTiet->tong_tien_combo_mon ?? $don->tong_tien ?? 0;
                                        $tienGiamVoucher = $chiTiet->tien_giam_voucher ?? $don->tien_giam ?? 0;
                                        $tienCoc = $chiTiet->tien_coc ?? $don->datBan->tien_coc ?? 0;
                                        $tongPhuThu = $chiTiet->tong_phu_thu ?? $don->phu_thu ?? 0;
                                        $phaiThanhToan = $chiTiet->phai_thanh_toan ?? null;
                                        if($phaiThanhToan === null) {
                                            $phaiThanhToan = $tongTienComboMon - $tienGiamVoucher - $tienCoc + $tongPhuThu;
                                            if($phaiThanhToan < 0) $phaiThanhToan = 0;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $don->id }}</td>
                                        <td><span class="badge bg-info">{{ $don->ma_hoa_don }}</span></td>
                                        <td>{{ $don->datBan->ten_khach ?? 'Ẩn' }}</td>
                                        <td>{{ number_format($tongTienComboMon) }} đ</td>
                                        <td class="fw-bold text-primary">{{ number_format($phaiThanhToan) }} đ</td>
                                        <td>{{ \Carbon\Carbon::parse($don->created_at)->format('d/m/Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Không có hóa đơn chưa thanh toán</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- BẢNG 4 --}}
                <div class="col-md-12">
                    <div class="tile">
                        <h3 class="tile-title">Nhân viên</h3>
                        <div style="overflow-x:auto;">
                            <table class="table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên nhân viên</th>
                                        <th>SĐT</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nhanVien as $nv)
                                    <tr>
                                        <td>#{{ $nv->id }}</td>
                                        <td>{{ $nv->ho_ten ?? 'Ẩn' }}</td>
                                        <td>{{ $nv->sdt ?? '---' }}</td>
                                        <td>{{ $nv->email ?? '---' }}</td>
                                        <td>
                                            @if($nv->vai_tro == 'quan_ly')
                                                <span class="badge bg-primary">Quản lý</span>
                                            @elseif($nv->vai_tro == 'bep')
                                                <span class="badge bg-warning">Bếp</span>
                                            @elseif($nv->vai_tro == 'phuc_vu')
                                                <span class="badge bg-info">Phục vụ</span>
                                            @elseif($nv->vai_tro == 'le_tan')
                                                <span class="badge bg-success">Lễ tân</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($nv->vai_tro) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $nv->trang_thai == 1 ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $nv->trang_thai == 1 ? 'Đi làm' : 'Nghỉ' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- BẢNG 5: TOP MÓN ĂN --}}
                <div class="col-md-12 mt-3">
                    <div class="tile">
                        <h3 class="tile-title">Top món ăn được gọi nhiều nhất</h3>
                        <div style="overflow-x:auto;">
                            <table class="table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên món</th>
                                        <th>Số lượt gọi</th>
                                        <th>Số lượt hủy</th>
                                        <th>Tỉ lệ hủy</th>
                                        <th>Đơn giá</th>
                                        <th>Tổng giá trị</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($topMonAn as $index => $mon)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $mon->ten_mon }}</strong></td>
                                        <td>
                                            <span class="badge bg-info">{{ number_format($mon->so_luot_goi) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ number_format($mon->so_luot_huy) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $mon->ti_le_huy > 20 ? 'bg-danger' : ($mon->ti_le_huy > 10 ? 'bg-warning' : 'bg-success') }}">
                                                {{ $mon->ti_le_huy }}%
                                            </span>
                                        </td>
                                        <td>{{ number_format($mon->gia) }} đ</td>
                                        <td>
                                            <strong class="text-success">{{ number_format($mon->tong_gia_tri) }} đ</strong>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Chưa có dữ liệu thống kê.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- CỘT 2 --}}
        <div class="col-md-12 col-lg-6">
            <div class="row">

                <div class="col-md-12">
                    <div class="tile">
                        <h3 class="tile-title">Doanh thu tổng</h3>
                        <select id="filterTotal" class="form-select form-select-sm mb-2">
                            <option value="day">7 ngày</option>
                            <option value="month" selected>12 tháng</option>
                            <option value="year">5 năm</option>
                        </select>
                        <canvas id="lineChart"></canvas>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="tile">
                        <h3 class="tile-title">Doanh thu combo</h3>
                        <select id="filterCombo" class="form-select form-select-sm mb-2">
                            <option value="day">7 ngày</option>
                            <option value="month" selected>12 tháng</option>
                            <option value="year">5 năm</option>
                        </select>
                        <canvas id="barChart"></canvas>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="tile">
                        <h3 class="tile-title">Khung giờ đặt bàn</h3>
                        <canvas id="hourChart"></canvas>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="tile">
                        <h3 class="tile-title">Ngày trong tuần đặt bàn</h3>
                        <canvas id="weekdayChart"></canvas>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="text-center" style="font-size: 13px">
        <p><b>
            © <script>document.write(new Date().getFullYear())</script>
            Phần mềm quản lý bán hàng | Dev By PH55158 / Trường
        </b></p>
    </div>

</main>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script>
    let lineChart, barChart, hourChart, weekChart;
    let selectedDateRange = null;

    // Khởi tạo date range picker
    const dateRangePicker = flatpickr("#date-range", {
        mode: "range",
        dateFormat: "d/m/Y",
        locale: {
            firstDayOfWeek: 1
        },
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                selectedDateRange = {
                    from: selectedDates[0].toISOString().split('T')[0],
                    to: selectedDates[1].toISOString().split('T')[0]
                };
            }
        }
    });

    // Hàm reload toàn bộ dashboard
    function reloadDashboard() {
        const params = new URLSearchParams();
        if (selectedDateRange) {
            params.append('date_from', selectedDateRange.from);
            params.append('date_to', selectedDateRange.to);
        }
        
        // Reload trang với params
        window.location.href = '/admin/dashboard' + (params.toString() ? '?' + params.toString() : '');
    }

    // Nút áp dụng
    document.getElementById('btn-apply-date').addEventListener('click', function() {
        if (selectedDateRange) {
            reloadDashboard();
        } else {
            alert('Vui lòng chọn khoảng thời gian!');
        }
    });

    // Nút reset
    document.getElementById('btn-reset-date').addEventListener('click', function() {
        dateRangePicker.clear();
        selectedDateRange = null;
        window.location.href = '/admin/dashboard';
    });

    function fetchData(filterTotal, filterCombo) {
        let url = `/admin/dashboard/data?filter=${filterTotal}`;
        if (selectedDateRange) {
            url += `&date_from=${selectedDateRange.from}&date_to=${selectedDateRange.to}`;
        }
        
        fetch(url)
            .then(res => res.json())
            .then(res => {

                if (lineChart) lineChart.destroy();
                lineChart = new Chart(document.getElementById('lineChart'), {
                    type: 'line',
                    data: {
                        labels: res.totalLabels,
                        datasets: [{
                            label: 'Doanh thu tổng',
                            data: res.totalData,
                            borderColor: 'rgb(54,162,235)',
                            backgroundColor: 'rgba(54,162,235,0.2)'
                        }]
                    }
                });

                if (barChart) barChart.destroy();
                barChart = new Chart(document.getElementById('barChart'), {
                    type: 'bar',
                    data: {
                        labels: res.comboLabels,
                        datasets: [{
                            label: 'Doanh thu theo combo',
                            data: res.comboData,
                            backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0']
                        }]
                    }
                });

                if (hourChart) hourChart.destroy();
                // Tạo labels từ keys của hourlyData (đã được sắp xếp từ server)
                const hourlyLabels = Object.keys(res.hourlyData);
                const hourlyValues = Object.values(res.hourlyData);
                
                hourChart = new Chart(document.getElementById('hourChart'), {
                    type: 'bar',
                    data: {
                        labels: hourlyLabels,
                        datasets: [{
                            label: 'Số lượt đặt',
                            data: hourlyValues,
                            backgroundColor: '#36A2EB'
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });

                if (weekChart) weekChart.destroy();
                weekChart = new Chart(document.getElementById('weekdayChart'), {
                    type: 'bar',
                    data: {
                        labels: Object.keys(res.weekdayData),
                        datasets: [{
                            label: 'Số lượt đặt',
                            data: Object.values(res.weekdayData),
                            backgroundColor: '#FFCE56'
                        }]
                    }
                });

            });
    }

    // Load date range từ URL nếu có
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('date_from') && urlParams.get('date_to')) {
        selectedDateRange = {
            from: urlParams.get('date_from'),
            to: urlParams.get('date_to')
        };
        const fromDate = new Date(selectedDateRange.from);
        const toDate = new Date(selectedDateRange.to);
        dateRangePicker.setDate([fromDate, toDate]);
    }

    fetchData('month','month');

    document.getElementById('filterTotal').addEventListener('change', e => {
        fetchData(e.target.value, document.getElementById('filterCombo').value);
    });
    document.getElementById('filterCombo').addEventListener('change', e => {
        fetchData(document.getElementById('filterTotal').value, e.target.value);
    });
</script>
@endsection
