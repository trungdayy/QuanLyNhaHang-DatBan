<?php

namespace App\Http\Controllers\Shop\NhanVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChiTietOrder;
use App\Models\OrderMon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ThongBao; // Import Model ThongBao

class WaiterController extends Controller
{
    /**
     * Hiển thị màn hình Hàng chờ phục vụ của nhân viên đó.
     */
    public function dashboard(Request $request)
    {
        $nhanVienId = Auth::id();

        if (!$nhanVienId) {
            Log::error("WAITER DASHBOARD: NhanVienId is null after passing Middleware.");
            return redirect()->route('login');
        }

        // Lấy danh sách món ban đầu (để render view lần đầu)
        $monChoPhucVu = ChiTietOrder::where('trang_thai', 'cho_cung_ung')
            ->whereHas('orderMon.datBan', function($q) use ($nhanVienId) {
                // $q->where('nhan_vien_id', $nhanVienId); 
            })
            ->whereDoesntHave('orderMon.datBan.hoaDon', function($q) {
                // Loại bỏ các món từ bàn đã thanh toán
                $q->where('trang_thai', 'da_thanh_toan');
            })
            ->with(['monAn:id,ten_mon,hinh_anh', 'orderMon.banAn:id,so_ban'])
            ->latest() 
            ->get();
        
        return view('Shop.nhanVien.tiep_thi_mon', compact('monChoPhucVu', 'nhanVienId'));
    }

    /**
     * API trả về dữ liệu (Món ăn + Thông báo) cho JS polling
     */
    public function getFoodQueueJson()
    {
        $nhanVienId = Auth::id();

        // 1. Lấy danh sách món chờ phục vụ
        $monChoPhucVu = ChiTietOrder::where('trang_thai', 'cho_cung_ung')
            ->whereHas('orderMon.datBan', function($q) use ($nhanVienId) {
                // $q->where('nhan_vien_id', $nhanVienId);
            })
            ->whereDoesntHave('orderMon.datBan.hoaDon', function($q) {
                // Loại bỏ các món từ bàn đã thanh toán
                $q->where('trang_thai', 'da_thanh_toan');
            })
            ->with(['monAn:id,ten_mon,hinh_anh', 'orderMon.banAn:id,so_ban'])
            ->latest() 
            ->get();

        // 2. [CẬP NHẬT] Lấy TẤT CẢ thông báo chưa xem
        $thongBaoMoi = ThongBao::where('da_xem', false)
                        // Bỏ điều kiện ->where('loai', 'goi_phuc_vu') cũ
                        ->with('datBan.banAn') // Load mối quan hệ để JS có thể lấy số bàn
                        ->latest()
                        ->get(); // Lấy tất cả thông báo chưa xem

        // 3. Trả về cả hai
        return response()->json([
            'monChoPhucVu' => $monChoPhucVu,
            'thongBao' => $thongBaoMoi 
        ]);
    }

    /**
     * Nhân viên xác nhận đã bưng món (Đổi trạng thái thành da_len_mon).
     */
    public function xacNhanDaBung($chiTietOrderId)
    {
        Log::info("WAITER: Request received to serve item ID: " . $chiTietOrderId);

        try {
            $chiTiet = ChiTietOrder::with('orderMon')->find($chiTietOrderId);

            if (!$chiTiet) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy chi tiết món.'], 404);
            }

            // Cập nhật trạng thái
            $chiTiet->trang_thai = 'da_len_mon';
            $chiTiet->save();
            
            // Logic phụ: Đồng bộ trạng thái Order cha
            $order = $chiTiet->orderMon;
            if ($order) {
                $conMonDangXuLy = ChiTietOrder::where('order_id', $order->id)
                    ->whereIn('trang_thai', ['cho_bep', 'dang_che_bien', 'cho_cung_ung'])
                    ->exists();

                if (!$conMonDangXuLy) {
                    $order->update(['trang_thai' => 'hoan_thanh']);
                }
            }

            return response()->json(['success' => true, 'message' => 'Đã xác nhận phục vụ món.'], 200);

        } catch (\Exception $e) {
            Log::error("WAITER: Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi Server: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * [MỚI] Đánh dấu thông báo là đã xem/đã xử lý.
     */
    public function markNotifRead(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        
        $count = ThongBao::whereIn('id', $request->input('ids'))
                ->update(['da_xem' => true]);
                
        Log::info("WAITER: Marked $count notifications as read.");
        
        return response()->json(['success' => true]);
    }
}