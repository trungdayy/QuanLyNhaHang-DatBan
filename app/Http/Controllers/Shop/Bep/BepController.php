<?php

namespace App\Http\Controllers\Shop\Bep;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderMon;
use App\Models\ChiTietOrder;

class BepController extends Controller
{
public function dashboard(Request $request)
{
    // 1. Cấu hình Map Danh mục ID vào Khu bếp (như đã thống nhất)
    // Bạn có thể để mảng này vào file config riêng nếu muốn gọn
    $mapKhuBep = [
        'nong'  => [1, 3], // Bếp nóng: Hải sản (1), Món chay (3)
        'nuong' => [2],    // Bếp nướng: Thịt nướng (2)
        'lanh'  => [4],    // Bếp lạnh: Tráng miệng (4)
        'nuoc'  => [5],    // Bar: Đồ uống (5)
    ];

    // 2. Khởi tạo query
    $query = ChiTietOrder::with([
        'orderMon.banAn:id,so_ban',
        'orderMon.chiTietOrders', // Eager load để tính ưu tiên
        'monAn:id,ten_mon,hinh_anh,danh_muc_id' // Lấy thêm danh_muc_id để debug nếu cần
    ])
    ->whereIn('trang_thai', ['cho_bep', 'dang_che_bien']);

    // 3. Lọc theo khu bếp (Sử dụng whereHas để lọc SQL trực tiếp)
    if ($request->filled('khu_bep')) {
        $khuVuc = $request->khu_bep;
        
        // Lấy danh sách ID danh mục tương ứng với khu vực
        $danhMucIds = $mapKhuBep[$khuVuc] ?? [];

        if (!empty($danhMucIds)) {
            // Chỉ lấy các order có món ăn thuộc danh mục ID này
            $query->whereHas('monAn', function ($q) use ($danhMucIds) {
                $q->whereIn('danh_muc_id', $danhMucIds);
            });
        }
    }

    // Thực thi câu lệnh SQL lấy dữ liệu
    $monCanCheBien = $query->get();

    // 4. Đánh dấu ưu tiên (Logic cũ của bạn giữ nguyên)
    // Logic: Nếu trong 1 order gọi món này > 1 lần (số lượng nhiều) -> ưu tiên
    $monCanCheBien = $monCanCheBien->map(function ($mon) {
        $order = $mon->orderMon;
        
        if ($order && $order->chiTietOrders) {
            $count = $order->chiTietOrders
                ->where('mon_an_id', $mon->mon_an_id)
                ->count();
            $mon->uu_tien = $count > 1;
        } else {
            $mon->uu_tien = false;
        }

        return $mon;
    });

    // 5. Nhóm món theo bàn (Logic cũ giữ nguyên)
    $theoBan = $monCanCheBien->groupBy(function ($m) {
        return optional($m->orderMon->banAn)->so_ban 
            ?? $m->orderMon->ban_id 
            ?? 'Mang về';
    });

    // 6. Sắp xếp món trong mỗi bàn (Logic cũ giữ nguyên)
    $theoBan = $theoBan->map(function ($dsMon) {
        return $dsMon->sortBy([
            ['uu_tien', 'desc'],     // Ưu tiên (true) lên đầu
            ['created_at', 'asc'],   // Món gọi trước làm trước
        ])->values();
    });

    // 7. Trả về view
    return view('bep.dashboard', compact('theoBan'));
}

    public function getOrderStatus($id)
    {
        $orders = OrderMon::with([
            'banAn:id,so_ban',
            'chiTietOrders' => function ($q) {
                $q->with('monAn:id,ten_mon');
            }
        ])
            ->where('dat_ban_id', $id)
            ->get();

        return response()->json($orders);
    }

// Tìm đến hàm xử lý cập nhật trạng thái trong BepController.php
    // Tên hàm phải khớp với Route của bạn (VD: updateMonStatus hoặc updateStatus)
    public function updateMonStatus(Request $request)
    {
        try {
            // 1. Validate dữ liệu đầu vào
            // (Không bắt buộc validate quá chặt ở đây nếu muốn nhanh, nhưng nên có)
            
            // 2. Tìm món ăn
            // Sử dụng Model ChiTietOrder (hoặc OrderMon tùy logic DB của bạn)
            // Dựa theo code cũ bạn gửi thì bạn dùng ChiTietOrder
            $chiTiet = ChiTietOrder::find($request->id);

            if (!$chiTiet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy món ăn (ID sai).'
                ], 404);
            }

            // 3. Cập nhật trạng thái
            $chiTiet->trang_thai = $request->trang_thai;
            $chiTiet->save();

            // 4. Logic phụ: Đồng bộ trạng thái với Order cha (nếu có)
            $order = $chiTiet->orderMon;
            if ($order) {
                // Kiểm tra xem còn món nào đang làm không
                $conMonDangLam = $order->chiTietOrders()
                    ->whereIn('trang_thai', ['cho_bep', 'dang_che_bien'])
                    ->exists();

                // Cập nhật trạng thái order cha
                $order->update([
                    'trang_thai' => $conMonDangLam ? 'dang_xu_li' : 'hoan_thanh',
                ]);
            }

            // --- ĐÂY LÀ PHẦN QUAN TRỌNG NHẤT CẦN SỬA ---
            // Code cũ: return redirect()->route(...);  <-- SAI (gây lỗi cú pháp JSON)
            // Code mới:
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công!'
            ]);
            // -------------------------------------------

        } catch (\Exception $e) {
            // Bắt lỗi server để không bị màn hình xám
            return response()->json([
                'success' => false,
                'message' => 'Lỗi Server: ' . $e->getMessage()
            ], 500);
        }
    }
}
