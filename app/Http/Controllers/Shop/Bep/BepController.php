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
        // 1. Cấu hình Map Danh mục ID vào Khu bếp
        // Dựa trên DB: 6-Khai Vị, 7-Hải Sản, 8-Thịt, 9-Món Nóng, 13-Tráng Miệng, 14-Đồ Uống
        $mapKhuBep = [
            'nong' => [7, 8, 9], 
            'lanh' => [6, 10, 11, 12, 13, 15], // Bao gồm Khai vị, Rau/Nấm, Viên, Sushi, Tráng miệng, Sốt
            'bar'  => [14], // Đồ uống
        ];

        // 2. Khởi tạo query và Eager Load data
        $query = ChiTietOrder::with([
            'orderMon.banAn:id,so_ban',
            'orderMon.chiTietOrders',
            // BỔ SUNG: danh_muc_id, thoi_gian_che_bien để phục vụ sắp xếp
            'monAn:id,ten_mon,hinh_anh,danh_muc_id,thoi_gian_che_bien' 
        ])
            ->whereIn('trang_thai', ['cho_bep', 'dang_che_bien'])
            // Chỉ lấy order chưa hoàn thành và đặt bàn chưa hoàn tất
            ->whereHas('orderMon', function ($q) {
                $q->where('trang_thai', '!=', 'hoan_thanh');
            })
            ->whereHas('orderMon.datBan', function ($q) {
                $q->where('trang_thai', '!=', 'hoan_tat');
            });

        // 3. Lọc theo khu bếp
        if ($request->filled('khu_bep')) {
            $khuVuc = $request->khu_bep;
            
            $danhMucIds = $mapKhuBep[$khuVuc] ?? [];

            if (!empty($danhMucIds)) {
                $query->whereHas('monAn', function ($q) use ($danhMucIds) {
                    $q->whereIn('danh_muc_id', $danhMucIds);
                });
            }
        }

        // Thực thi câu lệnh SQL lấy dữ liệu
        $monCanCheBien = $query->get();

        // 4. Đánh dấu ưu tiên (Món được gọi lặp lại > 1 lần)
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

        // 5. Nhóm món theo bàn
        $theoBan = $monCanCheBien->groupBy(function ($m) {
            return optional($m->orderMon->banAn)->so_ban 
                ?? $m->orderMon->ban_id 
                ?? 'Mang về';
        });

        // 6. Sắp xếp món trong mỗi bàn theo logic ưu tiên: Nước -> Món lâu -> Món nhanh
        $theoBan = $theoBan->map(function ($dsMon) {
            return $dsMon->sortBy(function ($mon) {
                $monAn = $mon->monAn;
                $danhMucId = optional($monAn)->danh_muc_id;
                // Lấy thời gian chế biến, mặc định 0 nếu null
                $thoiGianCheBien = optional($monAn)->thoi_gian_che_bien ?? 0;
                $createdAt = optional($mon)->created_at; 
                
                // Món nước (danh_muc_id = 14) luôn ưu tiên cao nhất (số âm lớn nhất)
                $priorityNuoc = ($danhMucId == 14) ? -10000 : 0;
                
                // Trạng thái: 'cho_bep' ưu tiên hơn 'dang_che_bien' (số âm nhỏ hơn)
                $statusMap = ['cho_bep' => -1000, 'dang_che_bien' => -500];
                $priorityStatus = $statusMap[$mon->trang_thai] ?? 1000; // Món xong/hủy xếp cuối
                
                // Thời gian chế biến giảm dần (Món nấu lâu hơn phải lên đầu, dùng giá trị âm)
                $priorityTime = -$thoiGianCheBien;
                
                // Thời gian gọi (Món gọi trước lên trước, dùng timestamp)
                $priorityCreatedAt = $createdAt ? $createdAt->timestamp : 0;

                if ($danhMucId == 14) {
                    // Món nước: ưu tiên tuyệt đối (-10000) + Sắp xếp theo thời gian gọi
                    return $priorityNuoc + $priorityCreatedAt; 
                } else {
                    // Các món khác: Ưu tiên theo Trạng thái -> Thời gian chế biến -> Thời gian gọi
                    // Tổng ưu tiên: giá trị càng nhỏ càng ưu tiên
                    // (Ví dụ: -1000 (cho_bep) - 25 (25 phút) + timestamp)
                    return $priorityStatus + $priorityTime + $priorityCreatedAt;
                }
            })->values();
        });

        // 7. Trả về view
        return view('bep.dashboard', compact('theoBan'));
    }

    // Hàm cập nhật trạng thái (giữ nguyên logic API)
    public function updateMonStatus(Request $request)
    {
        try {
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

            // 4. Logic phụ: Đồng bộ trạng thái với Order cha
            $order = $chiTiet->orderMon;
            if ($order) {
                $conMonDangLam = $order->chiTietOrders()
                    ->whereIn('trang_thai', ['cho_bep', 'dang_che_bien'])
                    ->exists();

                $order->update([
                    'trang_thai' => $conMonDangLam ? 'dang_xu_li' : 'hoan_thanh',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi Server: ' . $e->getMessage()
            ], 500);
        }
    }

    // Giữ nguyên hàm getOrderStatus nếu bạn có dùng
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
}