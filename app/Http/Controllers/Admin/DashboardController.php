<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BanAn;
use App\Models\HoaDon;
use App\Models\OrderMon;
use App\Models\MonAn;
use Carbon\Carbon;
use App\Models\ChiTietOrder;
use Illuminate\Support\Facades\DB;
use App\Models\NhanVien;
use App\Models\DatBan;

class DashboardController extends Controller
{
    public function index()
    {
        $thangHienTai = now()->month;
        $today = Carbon::today();
        $thisYear = now()->year; // Biến phụ trợ

        // ------------------ 1. THỐNG KÊ KPIs (Cơ sở từ Controller 1) ------------------

        // Nhân viên
        $nhanVien = NhanVien::orderBy('id')->take(10)->get(); // Danh sách 10 nhân viên
        $tongNhanVien = NhanVien::count();
        $nhanVienNghiHomNay = NhanVien::where('trang_thai', 0)->count();
        $nhanVienMoi = NhanVien::latest('created_at')->take(5)->get(); // Lấy từ Controller 2 (dùng cho widget khác)

        // Tổng doanh thu hôm nay & Tổng doanh thu toàn hệ thống (Tổng DT toàn hệ thống lấy từ C2)
        $doanhThuHomNay = HoaDon::whereDate('created_at', $today)->sum('tong_tien');
        $tongDoanhThu = HoaDon::sum('tong_tien');

        // Lượt đặt bàn hôm nay (Đã sửa từ BanAn sang DatBan)
        $luotDatBanHomNay = DatBan::whereDate('created_at', $today)->count();

        // Tổng số món ăn & Sản phẩm hết hàng
        $tongMonAn = MonAn::count();
        $monHetHang = MonAn::where('trang_thai', 'het')->count(); // Dùng 'het' theo enum DB

        // Tổng số đơn hàng
        $tongDonHang = HoaDon::count();

        // Hóa đơn chưa thanh toán (mới nhất)
        $donHangMoi = HoaDon::with('datBan')
            ->where('trang_thai', 'chua_thanh_toan')
            ->latest('created_at')
            ->take(5)
            ->get();

        // Món bán chạy nhất hôm nay (Đã sửa with('monAn') thành with('mon') theo gợi ý trước)
        $monBanChay = ChiTietOrder::selectRaw('mon_an_id, SUM(so_luong) as tong')
            ->whereDate('created_at', $today)
            ->groupBy('mon_an_id')
            ->orderByDesc('tong')
            ->take(5)
            ->with('monAn') 
            ->get();

        // ------------------ 2. THỐNG KÊ BIỂU ĐỒ THEO THÁNG (Cơ sở từ Controller 1) ------------------

        // Thống kê doanh thu theo tháng
        $rawDoanhThu = HoaDon::selectRaw('MONTH(created_at) as thang, SUM(tong_tien) as tong')
            ->whereYear('created_at', $thisYear)
            ->whereMonth('created_at', '<=', $thangHienTai)
            ->groupBy('thang')
            ->pluck('tong', 'thang')
            ->toArray();
        $doanhThuTheoThang = [];
        $labels = [];
        for ($i = 1; $i <= $thangHienTai; $i++) {
            $labels[] = "Tháng $i";
            $doanhThuTheoThang[] = isset($rawDoanhThu[$i]) ? (int)$rawDoanhThu[$i] : 0;
        }

        // Thống kê lượt đặt bàn theo tháng
        $rawDatBan = DatBan::selectRaw('MONTH(created_at) as thang, COUNT(*) as tong')
            ->whereYear('created_at', $thisYear)
            ->groupBy('thang')
            ->pluck('tong', 'thang')
            ->toArray();
        $luotDatBanTheoThang = [];
        for ($i = 1; $i <= $thangHienTai; $i++) {
            $luotDatBanTheoThang[] = isset($rawDatBan[$i]) ? (int)$rawDatBan[$i] : 0;
        }

        // ------------------ 3. BỔ SUNG: COMBO & KHÁCH HÀNG (Từ Controller 2) ------------------

// Combo bán chạy (ĐÃ SỬA: Dùng bảng dat_ban_combo)
        $totalDatBan = DatBan::count();
        
        $comboBanChay = DB::table('dat_ban_combo') // Bắt đầu từ bảng chi tiết
            ->join('dat_ban', 'dat_ban.id', '=', 'dat_ban_combo.dat_ban_id')
            ->join('combo_buffet', 'combo_buffet.id', '=', 'dat_ban_combo.combo_id')
            ->select(
                'combo_buffet.id',
                'combo_buffet.ten_combo',
                // Đếm tổng số lượng suất bán ra
                DB::raw('SUM(dat_ban_combo.so_luong) as so_luot_ban'),
                // Tính doanh thu dựa trên số lượng * giá vé (chính xác hơn lấy tổng hóa đơn)
                DB::raw('SUM(dat_ban_combo.so_luong * combo_buffet.gia_co_ban) as tong_doanh_thu'),
                // Đếm số đơn đặt có chứa combo này
                DB::raw('COUNT(DISTINCT dat_ban.id) as tong_luot_dat'),
                // Đếm số đơn bị hủy
                DB::raw('COUNT(DISTINCT CASE WHEN dat_ban.trang_thai = "huy" THEN dat_ban.id ELSE NULL END) as so_luot_huy')
            )
            ->groupBy('combo_buffet.id', 'combo_buffet.ten_combo')
            ->orderByDesc('so_luot_ban')
            ->take(4)
            ->get()
            ->map(function ($combo) use ($totalDatBan) {
                $combo->ti_le_dat = $totalDatBan > 0 ? round(($combo->tong_luot_dat / $totalDatBan) * 100, 1) : 0;
                $combo->ti_le_huy = $combo->tong_luot_dat > 0 ? round(($combo->so_luot_huy / $combo->tong_luot_dat) * 100, 1) : 0;
                return $combo;
            });

        // Thống kê khách hàng tiềm năng + tỉ lệ quay lại
        $topKhachHang = DB::table('dat_ban')
            ->join('hoa_don', 'hoa_don.dat_ban_id', '=', 'dat_ban.id')
            ->select(
                'ten_khach',
                'sdt_khach',
                DB::raw('COUNT(dat_ban.id) as so_lan_dat'),
                DB::raw('SUM(hoa_don.tong_tien) as tong_chi_tieu')
            )
            ->groupBy('ten_khach', 'sdt_khach')
            ->orderByDesc('tong_chi_tieu')
            ->take(5)
            ->get();

        $tongKhach = DB::table('dat_ban')->distinct('sdt_khach')->count('sdt_khach');
        $khachQuayLai = DB::table('dat_ban')
            ->select('sdt_khach', DB::raw('COUNT(*) as so_lan'))
            ->groupBy('sdt_khach')
            ->having('so_lan', '>', 1)
            ->count();
        $tiLeQuayLai = $tongKhach > 0 ? round(($khachQuayLai / $tongKhach) * 100, 2) : 0;

        // ------------------ 4. TOP MÓN ĂN ĐƯỢC GỌI NHIỀU NHẤT ------------------
        $topMonAn = DB::table('chi_tiet_order as cto')
            ->join('mon_an as ma', 'cto.mon_an_id', '=', 'ma.id')
            ->select(
                'ma.id',
                'ma.ten_mon',
                'ma.gia',
                DB::raw('SUM(CASE WHEN cto.trang_thai != "huy_mon" THEN cto.so_luong ELSE 0 END) as so_luot_goi'),
                DB::raw('SUM(CASE WHEN cto.trang_thai = "huy_mon" THEN cto.so_luong ELSE 0 END) as so_luot_huy'),
                DB::raw('SUM(CASE WHEN cto.trang_thai != "huy_mon" THEN cto.so_luong * ma.gia ELSE 0 END) as tong_gia_tri')
            )
            ->groupBy('ma.id', 'ma.ten_mon', 'ma.gia')
            ->havingRaw('SUM(CASE WHEN cto.trang_thai != "huy_mon" THEN cto.so_luong ELSE 0 END) > 0')
            ->orderByDesc(DB::raw('SUM(CASE WHEN cto.trang_thai != "huy_mon" THEN cto.so_luong ELSE 0 END)'))
            ->take(10)
            ->get()
            ->map(function ($mon) {
                $tongLuot = $mon->so_luot_goi + $mon->so_luot_huy;
                $mon->ti_le_huy = $tongLuot > 0 ? round(($mon->so_luot_huy / $tongLuot) * 100, 1) : 0;
                return $mon;
            });

        // ------------------ 5. TRẢ VỀ VIEW ------------------

        return view('admins.dashboard', compact(
            'doanhThuHomNay',
            'luotDatBanHomNay', // Thay thế 'luotDatBan' bằng biến mới
            'monBanChay',
            'tongMonAn',
            'tongDonHang',
            'monHetHang',
            'tongNhanVien',
            'donHangMoi',
            'doanhThuTheoThang',
            'luotDatBanTheoThang',
            'nhanVienNghiHomNay',
            'labels',
            'nhanVien', // Danh sách 10 nhân viên
            'nhanVienMoi', // Danh sách 5 nhân viên mới
            'comboBanChay', // Mới
            'totalDatBan', // Mới
            'topKhachHang', // Mới
            'tiLeQuayLai', // Mới
            'tongKhach', // Mới
            'tongDoanhThu', // Mới
            'topMonAn', // Top món ăn được gọi nhiều nhất
        ));
    }

// ------------------------------------------------------------------------------------------------------
// BỔ SUNG HÀM getChartData TỪ CONTROLLER 2
// ------------------------------------------------------------------------------------------------------

    /**
     * Lấy dữ liệu biểu đồ chi tiết (doanh thu theo ngày/tháng/năm, combo, khung giờ, ngày trong tuần)
     * Hàm này thường được gọi bằng AJAX.
     */
    public function getChartData(Request $request)
    {
        $filter = $request->filter ?? 'month';
        $thisYear = now()->year;

        // ------------------ 1. BIỂU ĐỒ TỔNG DOANH THU ------------------
        if ($filter == 'day') {
            $labels = [];
            $dataTotal = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $labels[] = Carbon::parse($date)->format('d/m');
                $dataTotal[] = HoaDon::whereDate('created_at', $date)->sum('tong_tien');
            }
        } elseif ($filter == 'month') {
            $labels = [];
            $dataTotal = [];
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = "Tháng $i";
                $dataTotal[] = HoaDon::whereYear('created_at', $thisYear)
                    ->whereMonth('created_at', $i)
                    ->sum('tong_tien');
            }
        } else { // filter == 'year'
            $labels = [];
            $dataTotal = [];
            $startYear = $thisYear - 5;
            for ($y = $startYear; $y <= $thisYear; $y++) {
                $labels[] = $y;
                $dataTotal[] = HoaDon::whereYear('created_at', $y)->sum('tong_tien');
            }
        }

// ------------------ 2. BIỂU ĐỒ DOANH THU THEO COMBO CỤ THỂ ------------------
        // Lấy top combo bán chạy (theo doanh thu) để hiển thị trong biểu đồ
        $comboQuery = DB::table('dat_ban_combo as dbc')
            ->join('dat_ban as db', 'dbc.dat_ban_id', '=', 'db.id')
            ->join('combo_buffet as cb', 'dbc.combo_id', '=', 'cb.id')
            ->join('hoa_don as hd', 'hd.dat_ban_id', '=', 'db.id')
            ->select(
                'cb.id',
                'cb.ten_combo',
                DB::raw('SUM(dbc.so_luong * cb.gia_co_ban) as tong_doanh_thu')
            );

        // Áp dụng filter theo thời gian
        if ($filter == 'day') {
            $comboQuery->whereBetween('hd.created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()]);
        } elseif ($filter == 'month') {
            $comboQuery->whereYear('hd.created_at', $thisYear);
        } else {
            $comboQuery->whereYear('hd.created_at', '>=', $thisYear - 5);
        }

        // Lấy top 10 combo có doanh thu cao nhất
        $comboStats = $comboQuery
            ->groupBy('cb.id', 'cb.ten_combo')
            ->orderByDesc('tong_doanh_thu')
            ->take(10)
            ->get();

        // Tách labels và data
        $comboLabels = $comboStats->pluck('ten_combo')->toArray();
        $comboData = $comboStats->pluck('tong_doanh_thu')->map(function ($value) {
            return (int) $value;
        })->toArray();

        // ------------------ 3. BIỂU ĐỒ KHUNG GIỜ ĐẶT BÀN ------------------
        $hourlyData = DB::table('dat_ban')
            ->selectRaw('HOUR(gio_den) as hour, COUNT(*) as count')
            ->whereBetween(DB::raw('HOUR(gio_den)'), [10, 22])
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour');

        // ------------------ 4. BIỂU ĐỒ NGÀY TRONG TUẦN ------------------
        $weekdayRawData = DB::table('dat_ban')
            ->selectRaw('DAYOFWEEK(gio_den) as weekday, COUNT(*) as count')
            ->groupBy('weekday')
            ->pluck('count', 'weekday');

        // DAYOFWEEK: 1=Chủ nhật, 2=Thứ 2, ..., 7=Thứ 7
        $weekdayLabels = [
            2 => 'Thứ 2',
            3 => 'Thứ 3',
            4 => 'Thứ 4',
            5 => 'Thứ 5',
            6 => 'Thứ 6',
            7 => 'Thứ 7',
            1 => 'Chủ nhật',
        ];

        $weekdayDataFormatted = [];
        foreach ($weekdayLabels as $key => $label) {
            $weekdayDataFormatted[$label] = $weekdayRawData[$key] ?? 0;
        }

        return response()->json([
            'totalLabels' => $labels,
            'totalData' => $dataTotal,
            'comboLabels' => $comboLabels,
            'comboData' => $comboData,
            'hourlyData' => $hourlyData,
            'weekdayData' => $weekdayDataFormatted,
        ]);
    }
}
