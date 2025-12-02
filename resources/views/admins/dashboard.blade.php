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
                <div id="clock"></div>
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
                        <h3 class="tile-title">Trạng thái thanh toán (Đơn hàng mới nhất)</h3>
                        <div style="overflow-x:auto;">
                            <table class="table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên khách</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Phương thức TT</th>
                                        <th>Ngày</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($donHangMoi as $don)
                                    <tr>
                                        <td>{{ $don->id }}</td>
                                        <td>{{ $don->datBan->ten_khach ?? 'Ẩn' }}</td>
                                        <td>{{ number_format($don->tong_tien) }} đ</td>
                                        <td>
                                            <span class="badge {{ ($don->da_thanh_toan ?? 0) > 0 ? 'bg-success' : 'bg-warning' }}">
                                                {{ ($don->da_thanh_toan ?? 0) > 0 ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($don->phuong_thuc_tt == 'tien_mat')
                                                <span class="badge bg-primary">Tiền mặt</span>
                                            @elseif($don->phuong_thuc_tt == 'chuyen_khoan')
                                                <span class="badge bg-secondary">Chuyển khoản</span>
                                            @elseif($don->phuong_thuc_tt == 'the_ATM')
                                                <span class="badge bg-info">Thẻ ATM</span>
                                            @elseif($don->phuong_thuc_tt == 'vnpay')
                                                <span class="badge bg-warning">VNPay</span>
                                            @else
                                                <span class="badge bg-dark">{{ $don->phuong_thuc_tt ?? '---' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($don->created_at)->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
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
<script>
    let lineChart, barChart, hourChart, weekChart;

    function fetchData(filterTotal, filterCombo) {
        fetch(`/admin/dashboard/data?filter=${filterTotal}`)
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
                hourChart = new Chart(document.getElementById('hourChart'), {
                    type: 'bar',
                    data: {
                        labels: Array.from({length:13},(_,i)=>`${i+10}h`),
                        datasets: [{
                            label: 'Số lượt đặt',
                            data: Object.values(res.hourlyData),
                            backgroundColor: '#36A2EB'
                        }]
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

    fetchData('month','month');

    document.getElementById('filterTotal').addEventListener('change', e => {
        fetchData(e.target.value, document.getElementById('filterCombo').value);
    });
    document.getElementById('filterCombo').addEventListener('change', e => {
        fetchData(document.getElementById('filterTotal').value, e.target.value);
    });
</script>
@endsection
