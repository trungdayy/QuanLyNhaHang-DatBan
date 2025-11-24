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
        // 1) Load map khu bếp từ file (không được sửa DB nên dùng file php)
        $mapKhuBep = include __DIR__ . '/bep_map.php';

        // 2) Lấy toàn bộ món CẦN CHẾ BIẾN
        $monCanCheBien = ChiTietOrder::with([
            'orderMon.banAn:id,so_ban',
            'orderMon.chiTietOrders',     // để xét uu_tien
            'monAn:id,ten_mon,hinh_anh'
        ])
            ->whereIn('trang_thai', ['cho_bep', 'dang_che_bien'])
            ->get();

        // 3) Lọc theo khu bếp (dựa trên map tên món → khu bếp)
        if ($request->filled('khu_bep')) {
            $desired = $request->khu_bep;

            $monCanCheBien = $monCanCheBien->filter(function ($mon) use ($mapKhuBep, $desired) {
                $ten = $mon->monAn->ten_mon ?? null;
                if (!$ten) return false;
                return ($mapKhuBep[$ten] ?? null) === $desired;
            })->values();
        }

        // 4) Đánh dấu ưu tiên (món được gọi nhiều lần)
        $monCanCheBien = $monCanCheBien->map(function ($mon) {
            $order = $mon->orderMon;

            // đếm số lần gọi món giống nhau trong cùng order
            $count = $order->chiTietOrders
                ->where('mon_an_id', $mon->mon_an_id)
                ->count();

            $mon->uu_tien = $count > 1;
            return $mon;
        });

        // 5) Nhóm món theo bàn (theo số bàn)
        $theoBan = $monCanCheBien->groupBy(function ($m) {
            return optional($m->orderMon->banAn)->so_ban
                ?? $m->orderMon->ban_id
                ?? 'Không rõ bàn';
        });

        // 6) Sắp xếp món trong mỗi bàn:
        //    - ưu tiên (true trước)
        //    - món cũ hơn lên trước
        $theoBan = $theoBan->map(function ($dsMon) {
            return $dsMon->sortBy([
                ['uu_tien', 'desc'],      // ưu tiên trước
                ['created_at', 'asc'],    // món cũ lên đầu
            ])->values();
        });

        // 7) Trả về view với $theoBan (không trả monCanCheBien)
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

    public function updateMonStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:chi_tiet_order,id',
            'trang_thai' => 'required|string|in:cho_bep,dang_che_bien,da_len_mon,huy_mon',
        ]);

        $chiTiet = ChiTietOrder::findOrFail($request->id);
        $from = $chiTiet->trang_thai;
        $to   = $request->trang_thai;

        // Định nghĩa các chuyển trạng thái hợp lệ
        $allowed = [
            'cho_bep'        => ['dang_che_bien', 'huy_mon'],
            'dang_che_bien'  => ['da_len_mon', 'huy_mon'],
            'da_len_mon'     => [],  // đã xong, không cho phép đổi
            'huy_mon'        => [],  // đã hủy, không cho phép đổi
        ];

        // Nếu yêu cầu không nằm trong allowed transitions → báo lỗi
        // if (!isset($allowed[$from]) || !in_array($to, $allowed[$from], true)) {
        //     return redirect()
        //         ->route('bep.dashboard')
        //         ->with('error', 'Chuyển trạng thái không hợp lệ từ "' . $from . '" sang "' . $to . '".');
        // }

        // Cập nhật chi tiết món
        $chiTiet->trang_thai = $to;
        $chiTiet->save();

        // Đồng bộ trạng thái tổng order_mon
        $order = $chiTiet->orderMon;
        if ($order) {
            // Còn món đang chờ/đang chế biến?
            $dangXuLy = $order->chiTietOrders()
                ->whereIn('trang_thai', ['cho_bep', 'dang_che_bien'])
                ->exists();

            // order_mon dùng enum riêng: đang xử lý / hoàn thành
            $order->update([
                'trang_thai' => $dangXuLy ? 'dang_xu_li' : 'hoan_thanh',
            ]);
        }

        return redirect()->route('bep.dashboard')
            ->with('success', 'Cập nhật trạng thái món thành công');
    }
}
