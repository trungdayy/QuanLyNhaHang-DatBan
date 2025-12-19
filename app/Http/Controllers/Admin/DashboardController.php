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
    public function index(Request $request)
    {
        // Lấy date range từ request (nếu có)
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : null;
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : null;
        
        $thangHienTai = now()->month;
        $today = Carbon::today();
        $thisYear = now()->year; // Biến phụ trợ
        
        // Hàm helper để filter theo date range
        $applyDateRange = function($query) use ($dateFrom, $dateTo) {
            if ($dateFrom && $dateTo) {
                return $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            }
            return $query;
        };

        // ------------------ 1. THỐNG KÊ KPIs (Cơ sở từ Controller 1) ------------------

        // Nhân viên
        $nhanVien = NhanVien::orderBy('id')->take(10)->get(); // Danh sách 10 nhân viên
        $tongNhanVien = NhanVien::count();
        $nhanVienNghiHomNay = NhanVien::where('trang_thai', 0)->count();
        $nhanVienMoi = NhanVien::latest('created_at')->take(5)->get(); // Lấy từ Controller 2 (dùng cho widget khác)

        // Tổng doanh thu & Tổng doanh thu theo khoảng thời gian
        $doanhThuQuery = HoaDon::query();
        if ($dateFrom && $dateTo) {
            $doanhThuHomNay = $applyDateRange($doanhThuQuery->clone())->sum('tong_tien');
        } else {
            $doanhThuHomNay = HoaDon::whereDate('created_at', $today)->sum('tong_tien');
        }
        $tongDoanhThu = $applyDateRange(HoaDon::query())->sum('tong_tien');

        // Lượt đặt bàn theo khoảng thời gian
        $datBanQuery = DatBan::query();
        if ($dateFrom && $dateTo) {
            $luotDatBanHomNay = $applyDateRange($datBanQuery->clone())->count();
        } else {
            $luotDatBanHomNay = DatBan::whereDate('created_at', $today)->count();
        }

        // Tổng số món ăn & Sản phẩm hết hàng
        $tongMonAn = MonAn::count();
        $monHetHang = MonAn::where('trang_thai', 'het')->count(); // Dùng 'het' theo enum DB

        // Tổng số đơn hàng
        $tongDonHangQuery = HoaDon::query();
        $tongDonHang = $applyDateRange($tongDonHangQuery)->count();

        // Đơn hàng mới nhất
        $donHangMoiQuery = HoaDon::with('datBan');
        $donHangMoi = $applyDateRange($donHangMoiQuery)->latest('created_at')->take(5)->get();

        // Món bán chạy nhất theo khoảng thời gian
        $monBanChayQuery = ChiTietOrder::selectRaw('mon_an_id, SUM(so_luong) as tong');
        if ($dateFrom && $dateTo) {
            $monBanChay = $applyDateRange($monBanChayQuery)
                ->groupBy('mon_an_id')
                ->orderByDesc('tong')
                ->take(5)
                ->with('monAn') 
                ->get();
        } else {
            $monBanChay = ChiTietOrder::selectRaw('mon_an_id, SUM(so_luong) as tong')
                ->whereDate('created_at', $today)
                ->groupBy('mon_an_id')
                ->orderByDesc('tong')
                ->take(5)
                ->with('monAn') 
                ->get();
        }

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
        $totalDatBanQuery = DatBan::query();
        $totalDatBan = $applyDateRange($totalDatBanQuery)->count();
        
        $comboBanChayQuery = DB::table('dat_ban_combo') // Bắt đầu từ bảng chi tiết
            ->join('dat_ban', 'dat_ban.id', '=', 'dat_ban_combo.dat_ban_id')
            ->join('combo_buffet', 'combo_buffet.id', '=', 'dat_ban_combo.combo_id');
            
        if ($dateFrom && $dateTo) {
            $comboBanChayQuery->whereBetween('dat_ban.created_at', [$dateFrom, $dateTo]);
        }
        
        $comboBanChay = $comboBanChayQuery
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
        $topKhachHangQuery = DB::table('dat_ban')
            ->join('hoa_don', 'hoa_don.dat_ban_id', '=', 'dat_ban.id');
            
        if ($dateFrom && $dateTo) {
            $topKhachHangQuery->whereBetween('dat_ban.created_at', [$dateFrom, $dateTo]);
        }
        
        $topKhachHang = $topKhachHangQuery
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

        $tongKhachQuery = DB::table('dat_ban');
        if ($dateFrom && $dateTo) {
            $tongKhachQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        $tongKhach = $tongKhachQuery->distinct('sdt_khach')->count('sdt_khach');
        
        $khachQuayLaiQuery = DB::table('dat_ban');
        if ($dateFrom && $dateTo) {
            $khachQuayLaiQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        $khachQuayLai = $khachQuayLaiQuery
            ->select('sdt_khach', DB::raw('COUNT(*) as so_lan'))
            ->groupBy('sdt_khach')
            ->having('so_lan', '>', 1)
            ->count();
        $tiLeQuayLai = $tongKhach > 0 ? round(($khachQuayLai / $tongKhach) * 100, 2) : 0;

        // ------------------ 3.1. THỐNG KÊ TOP MÓN ĂN ------------------
        $topMonAnQuery = DB::table('chi_tiet_order')
            ->join('mon_an', 'mon_an.id', '=', 'chi_tiet_order.mon_an_id');
            
        if ($dateFrom && $dateTo) {
            $topMonAnQuery->whereBetween('chi_tiet_order.created_at', [$dateFrom, $dateTo]);
        }
        
        $topMonAn = $topMonAnQuery
            ->select(
                'mon_an.id',
                'mon_an.ten_mon',
                'mon_an.gia',
                // Tổng số lượt gọi (tổng số lượng)
                DB::raw('SUM(chi_tiet_order.so_luong) as so_luot_goi'),
                // Số lượt hủy (số lượng các món có trang_thai = huy_mon)
                DB::raw('SUM(CASE WHEN chi_tiet_order.trang_thai = "huy_mon" THEN chi_tiet_order.so_luong ELSE 0 END) as so_luot_huy'),
                // Tổng số lượt không hủy (để tính doanh thu)
                DB::raw('SUM(CASE WHEN chi_tiet_order.trang_thai != "huy_mon" THEN chi_tiet_order.so_luong ELSE 0 END) as so_luot_thanh_cong')
            )
            ->groupBy('mon_an.id', 'mon_an.ten_mon', 'mon_an.gia')
            ->having('so_luot_goi', '>', 0) // Chỉ lấy món đã được gọi ít nhất 1 lần
            ->orderByDesc('so_luot_goi')
            ->take(10)
            ->get()
            ->map(function ($mon) {
                // Tính tổng giá trị (số lượt thành công * giá)
                $mon->tong_gia_tri = $mon->so_luot_thanh_cong * $mon->gia;
                // Tính tỉ lệ hủy
                $mon->ti_le_huy = $mon->so_luot_goi > 0 
                    ? round(($mon->so_luot_huy / $mon->so_luot_goi) * 100, 2) 
                    : 0;
                return $mon;
            });

        // ------------------ 4. TRẢ VỀ VIEW ------------------

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
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : null;
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : null;
        $thisYear = now()->year;

        // ------------------ 1. BIỂU ĐỒ TỔNG DOANH THU ------------------
        if ($filter == 'day') {
            $labels = [];
            $dataTotal = [];
            if ($dateFrom && $dateTo) {
                // Nếu có date range, chia theo từng ngày trong khoảng
                $start = Carbon::parse($dateFrom);
                $end = Carbon::parse($dateTo);
                $current = $start->copy();
                while ($current->lte($end)) {
                    $labels[] = $current->format('d/m');
                    $dataTotal[] = HoaDon::whereDate('created_at', $current->format('Y-m-d'))->sum('tong_tien');
                    $current->addDay();
                }
            } else {
                // Mặc định: 7 ngày gần nhất
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i)->format('Y-m-d');
                    $labels[] = Carbon::parse($date)->format('d/m');
                    $dataTotal[] = HoaDon::whereDate('created_at', $date)->sum('tong_tien');
                }
            }
        } elseif ($filter == 'month') {
            $labels = [];
            $dataTotal = [];
            if ($dateFrom && $dateTo) {
                // Chia theo tháng trong khoảng
                $start = Carbon::parse($dateFrom);
                $end = Carbon::parse($dateTo);
                $current = $start->copy()->startOfMonth();
                while ($current->lte($end)) {
                    $labels[] = "Tháng " . $current->month . "/" . $current->year;
                    $dataTotal[] = HoaDon::whereYear('created_at', $current->year)
                        ->whereMonth('created_at', $current->month)
                        ->sum('tong_tien');
                    $current->addMonth();
                }
            } else {
                // Mặc định: 12 tháng
                for ($i = 1; $i <= 12; $i++) {
                    $labels[] = "Tháng $i";
                    $dataTotal[] = HoaDon::whereYear('created_at', $thisYear)
                        ->whereMonth('created_at', $i)
                        ->sum('tong_tien');
                }
            }
        } else { // filter == 'year'
            $labels = [];
            $dataTotal = [];
            if ($dateFrom && $dateTo) {
                $startYear = Carbon::parse($dateFrom)->year;
                $endYear = Carbon::parse($dateTo)->year;
                for ($y = $startYear; $y <= $endYear; $y++) {
                    $labels[] = $y;
                    $yearQuery = HoaDon::whereYear('created_at', $y);
                    if ($y == $startYear) {
                        $yearQuery->whereDate('created_at', '>=', Carbon::parse($dateFrom)->format('Y-m-d'));
                    }
                    if ($y == $endYear) {
                        $yearQuery->whereDate('created_at', '<=', Carbon::parse($dateTo)->format('Y-m-d'));
                    }
                    $dataTotal[] = $yearQuery->sum('tong_tien');
                }
            } else {
                $startYear = $thisYear - 5;
                for ($y = $startYear; $y <= $thisYear; $y++) {
                    $labels[] = $y;
                    $dataTotal[] = HoaDon::whereYear('created_at', $y)->sum('tong_tien');
                }
            }
        }

// ------------------ 2. BIỂU ĐỒ DOANH THU THEO COMBO CỤ THỂ (TOP COMBO BÁN CHẠY) ------------------
        // Lấy top 4 combo bán chạy nhất trong khoảng thời gian được chọn
        $query = DB::table('dat_ban_combo as dbc')
            ->join('dat_ban as db', 'dbc.dat_ban_id', '=', 'db.id')
            ->join('combo_buffet as cb', 'dbc.combo_id', '=', 'cb.id')
            ->join('hoa_don as hd', 'hd.dat_ban_id', '=', 'db.id')
            ->whereNotNull('hd.id'); // Chỉ lấy các đơn đã có hóa đơn (đã thanh toán)

        // Áp dụng filter thời gian
        if ($dateFrom && $dateTo) {
            $query->whereBetween('hd.created_at', [$dateFrom, $dateTo]);
        } else {
            if ($filter == 'day') {
                $query->whereBetween('hd.created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()]);
            } elseif ($filter == 'month') {
                $query->whereYear('hd.created_at', $thisYear);
            } else {
                $query->whereYear('hd.created_at', '>=', $thisYear - 5);
            }
        }

        // Tính doanh thu theo từng combo và lấy top 4
        $topCombos = $query
            ->select(
                'cb.id',
                'cb.ten_combo',
                DB::raw('SUM(dbc.so_luong * cb.gia_co_ban) as tong_doanh_thu')
            )
            ->groupBy('cb.id', 'cb.ten_combo')
            ->orderByDesc('tong_doanh_thu')
            ->take(4)
            ->get();

        // Chuẩn bị dữ liệu cho biểu đồ
        $comboLabels = [];
        $comboData = [];
        
        foreach ($topCombos as $combo) {
            $comboLabels[] = $combo->ten_combo;
            $comboData[] = (int) $combo->tong_doanh_thu;
        }

        // Nếu không có combo nào, hiển thị thông báo
        if (empty($comboLabels)) {
            $comboLabels = ['Chưa có dữ liệu'];
            $comboData = [0];
        }

        // ------------------ 3. BIỂU ĐỒ KHUNG GIỜ ĐẶT BÀN ------------------
        // Tạo danh sách khung giờ theo ca trưa/tối, cách nhau 30 phút
        $timeSlots = [];
        
        // Ca trưa: 10:30 -> 14:00 (cách nhau 30 phút)
        for ($h = 10; $h <= 14; $h++) {
            $minutes = ($h == 10) ? [30] : (($h == 14) ? [0] : [0, 30]);
            foreach ($minutes as $m) {
                $timeString = sprintf('%02d:%02d', $h, $m);
                $timeSlots[$timeString] = 0;
            }
        }
        
        // Ca tối: 17:00 -> 22:00 (cách nhau 30 phút)
        for ($h = 17; $h <= 22; $h++) {
            $minutes = ($h == 22) ? [0] : [0, 30];
            foreach ($minutes as $m) {
                $timeString = sprintf('%02d:%02d', $h, $m);
                $timeSlots[$timeString] = 0;
            }
        }
        
        // Query dữ liệu đặt bàn và làm tròn về khung giờ 30 phút bằng SQL
        $hourlyQuery = DB::table('dat_ban');
        if ($dateFrom && $dateTo) {
            $hourlyQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        
        // Làm tròn phút về 0 hoặc 30 theo đúng khung giờ
        // Phút < 30: làm tròn về :00
        // Phút >= 30: làm tròn về :30
        $rawData = $hourlyQuery
            ->selectRaw('
                CONCAT(
                    LPAD(HOUR(gio_den), 2, "0"), ":",
                    LPAD(
                        CASE 
                            WHEN MINUTE(gio_den) < 30 THEN 0
                            ELSE 30
                        END, 2, "0"
                    )
                ) as time_slot,
                COUNT(*) as count
            ')
            ->where(function($q) {
                // Ca trưa: 10:30 - 14:00
                $q->where(function($q1) {
                    $q1->where(DB::raw('HOUR(gio_den)'), 10)
                       ->where(DB::raw('MINUTE(gio_den)'), '>=', 30);
                })
                ->orWhere(function($q1) {
                    $q1->whereBetween(DB::raw('HOUR(gio_den)'), [11, 13]);
                })
                ->orWhere(function($q1) {
                    $q1->where(DB::raw('HOUR(gio_den)'), 14)
                       ->where(DB::raw('MINUTE(gio_den)'), '<=', 0);
                })
                // Ca tối: 17:00 - 22:00
                ->orWhere(function($q1) {
                    $q1->whereBetween(DB::raw('HOUR(gio_den)'), [17, 21]);
                })
                ->orWhere(function($q1) {
                    $q1->where(DB::raw('HOUR(gio_den)'), 22)
                       ->where(DB::raw('MINUTE(gio_den)'), '<=', 0);
                });
            })
            ->groupBy('time_slot')
            ->orderBy('time_slot')
            ->pluck('count', 'time_slot');
        
        // Gán dữ liệu vào các khung giờ đã định nghĩa
        foreach ($rawData as $timeSlot => $count) {
            if (isset($timeSlots[$timeSlot])) {
                $timeSlots[$timeSlot] = $count;
            }
        }
        
        $hourlyData = $timeSlots;

        // ------------------ 4. BIỂU ĐỒ NGÀY TRONG TUẦN ------------------
        $weekdayQuery = DB::table('dat_ban');
        if ($dateFrom && $dateTo) {
            $weekdayQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
        }
        $weekdayRawData = $weekdayQuery
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
