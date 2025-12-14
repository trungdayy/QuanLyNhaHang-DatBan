<?php

namespace App\Http\Controllers\Shop\NhanVien;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChiTietOrder;
use App\Models\OrderMon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // Thêm Facade Auth

class WaiterController extends Controller
{
    /**
     * Hiển thị màn hình Hàng chờ phục vụ của nhân viên đó.
     */
    public function dashboard(Request $request)
    {
        // ---------------------------------------------------------------------
        // SỬA: Lấy ID nhân viên bằng Auth::id() hoặc auth('guard')->id()
        // Vì Middleware CheckRole đã kiểm tra, ta có thể dùng Auth::user()
        // Nếu Auth::id() vẫn lỗi, thử dùng Guard Tường minh:
        $nhanVienId = Auth::id(); // Thử dùng cách lấy ID trực tiếp.
        
        // HOẶC dùng Guard nếu bạn có nhiều loại user:
        // $nhanVienId = auth('ten_guard_nhan_vien')->id(); 
        // ---------------------------------------------------------------------

        // Bổ sung kiểm tra an toàn (Mặc dù Middleware đã làm, nhưng đây là lý do lỗi)
        if (!$nhanVienId) {
            // Log lỗi và chuyển hướng an toàn (tránh lỗi nếu Guard bị nhầm)
            Log::error("WAITER DASHBOARD: NhanVienId is null after passing Middleware.");
            return redirect()->route('login');
        }

        // Lấy danh sách món đang ở trạng thái 'cho_cung_ung' (Bếp đã xong, chờ bưng)
        $monChoPhucVu = ChiTietOrder::where('trang_thai', 'cho_cung_ung')
            // Chỉ lấy các món thuộc bàn mà nhân viên này phụ trách
            ->whereHas('orderMon.datBan', function($q) use ($nhanVienId) {
                // Đảm bảo datBan có tồn tại và nhan_vien_id khớp
                $q->where('nhan_vien_id', $nhanVienId);
            })
            // Eager load các mối quan hệ cần thiết
            ->with(['monAn:id,ten_mon,hinh_anh', 'orderMon.banAn:id,so_ban'])
            // Sắp xếp theo thời gian tạo (Món xong trước bưng trước)
            ->latest() 
            ->get();
        
        // Truyền dữ liệu sang view tại đường dẫn mới: Shop.nhanVien.tiep_thi_mon
        return view('Shop.nhanVien.tiep_thi_mon', compact('monChoPhucVu', 'nhanVienId'));
    }

    /**
     * Nhân viên xác nhận đã bưng món ăn lên cho khách (Đổi trạng thái thành da_len_mon).
     * @param int $chiTietOrderId
     */
    public function xacNhanDaBung($chiTietOrderId)
    {
        // ... (Phần code xacNhanDaBung giữ nguyên vì không liên quan đến lỗi auth()->id())
        // ... (Nếu Auth::id() bị lỗi ở đây, bạn cũng phải sửa tương tự)

        // Ghi log để xác nhận yêu cầu đã được nhận
        Log::info("WAITER: Request received to serve item ID: " . $chiTietOrderId);

        try {
            // Tải ChiTietOrder và OrderMon liên quan
            $chiTiet = ChiTietOrder::with('orderMon')->find($chiTietOrderId);

            if (!$chiTiet) {
                Log::error("WAITER: ChiTietOrder ID not found: " . $chiTietOrderId);
                return response()->json(['success' => false, 'message' => 'Không tìm thấy chi tiết món.'], 404);
            }

            // 1. Cập nhật trạng thái
            $chiTiet->trang_thai = 'da_len_mon';
            $chiTiet->save();
            
            // Log xác nhận lưu thành công
            Log::info("WAITER: Item ID " . $chiTietOrderId . " status updated to da_len_mon.");


            // 2. Logic phụ: Đồng bộ trạng thái Order cha
            $order = $chiTiet->orderMon;
            if ($order) {
                // Kiểm tra xem còn món nào đang ở trạng thái xử lý (chờ bếp, đang làm, chờ bưng) không
                
                $conMonDangXuLy = ChiTietOrder::where('order_id', $order->id)
                    ->whereIn('trang_thai', ['cho_bep', 'dang_che_bien', 'cho_cung_ung'])
                    ->exists();

                if (!$conMonDangXuLy) {
                    // Nếu không còn món nào đang xử lý, chuyển trạng thái order cha thành hoan_thanh
                    $order->update(['trang_thai' => 'hoan_thanh']);
                    // Log hoàn thành Order
                    Log::info("WAITER: Order ID " . $order->id . " updated to hoan_thanh.");
                }
            }

            return response()->json(['success' => true, 'message' => 'Đã xác nhận phục vụ món.'], 200);

        } catch (\Exception $e) {
            // Ghi log lỗi server chi tiết hơn (bao gồm file và dòng code)
            Log::error("WAITER: Critical Error on serving item: " . $e->getMessage(), [
                'id' => $chiTietOrderId, 
                'file' => $e->getFile(), 
                'line' => $e->getLine()
            ]);
            
            // Trả về lỗi 500
            return response()->json(['success' => false, 'message' => 'Lỗi Server: ' . $e->getMessage()], 500);
        }
    }

    // Ví dụ trong WaiterController.php
public function getFoodQueueJson()
{
    $nhanVienId = Auth::id(); // hoặc auth('guard')->id();
    $monChoPhucVu = ChiTietOrder::where('trang_thai', 'cho_cung_ung')
        ->whereHas('orderMon.datBan', function($q) use ($nhanVienId) {
            $q->where('nhan_vien_id', $nhanVienId);
        })
        ->with(['monAn:id,ten_mon,hinh_anh', 'orderMon.banAn:id,so_ban'])
        ->latest() 
        ->get();

    return response()->json(['monChoPhucVu' => $monChoPhucVu]);
}
}