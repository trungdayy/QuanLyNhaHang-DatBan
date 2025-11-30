<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChiTietOrder;
use App\Models\OrderMon;
use App\Models\MonTrongCombo;
use App\Models\MonAn;
use Illuminate\Http\Request;

class ChiTietOrderController extends Controller
{
    /**
     * Hiển thị danh sách món trong 1 đơn hàng hoặc tất cả đơn
     */
public function index(Request $request)
    {
        $orderId = $request->query('order_id');

        if ($orderId) {
            $order = OrderMon::with(['chiTietOrders.monAn', 'datBan.comboBuffet'])->find($orderId);

            if (!$order) {
                return redirect()->route('admin.chi-tiet-order.index')
                    ->with('error', 'Đơn hàng không tồn tại.');
            }

            // Lấy danh sách món đang bán
            $monAns = MonAn::where('trang_thai', 'dang_ban')->get();

            // Lấy combo_id của bàn để xác định món trong combo
            $comboId = $order->datBan->combo_id ?? null;
            
            // ✅ MỚI: Lấy số người từ đặt bàn (mặc định 1 nếu không có)
            $soNguoi = $order->datBan->so_nguoi ?? 1;

            $soLuongMonTrongCombo = [];

            if ($comboId) {
                $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();
                $soLuongMonTrongCombo = $monTrongCombo->pluck('gioi_han_so_luong', 'mon_an_id')->toArray();
            }

            // Gắn loại món hiển thị + số lượng hiển thị
            foreach ($order->chiTietOrders as $ct) {
                if ($ct->loai_mon === 'combo') {
                    $ct->loai_mon_hien_thi = 'Combo';
                    
                    // ✅ SỬA LOGIC: Định lượng 1 người * Số người
                    if (isset($soLuongMonTrongCombo[$ct->mon_an_id])) {
                        $ct->so_luong_hien_thi = $soLuongMonTrongCombo[$ct->mon_an_id] * $soNguoi;
                    } else {
                        $ct->so_luong_hien_thi = $ct->so_luong;
                    }
                    
                } else {
                    $ct->loai_mon_hien_thi = 'Gọi thêm';
                    $ct->so_luong_hien_thi = $ct->so_luong;
                }
            }

            return view('admins.chi-tiet-order.show', compact('order', 'monAns', 'soLuongMonTrongCombo'));
        }

        // Nếu không có order_id → hiển thị tất cả đơn
        $orders = OrderMon::with(['datBan.comboBuffet'])->latest()->paginate(10);
        $monAns = MonAn::where('trang_thai', 'dang_ban')->get();

        return view('admins.chi-tiet-order.index', compact('orders', 'monAns'));
    }

    /**
     * Trang thêm món vào order
     */
    public function create(Request $request)
    {
        $orderId = $request->query('order_id');

        $order = OrderMon::with(['datBan.comboBuffet'])->find($orderId);
        if (!$order) {
            return redirect()->route('admin.chi-tiet-order.index')
                ->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Lấy danh sách món đang bán
        $monAns = MonAn::where('trang_thai', 'con')->get();

        // Gợi ý món trong combo (nếu có)
        $comboId = $order->datBan->combo_id ?? null;
        $soLuongMonTrongCombo = [];

        if ($comboId) {
            $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();
            $soLuongMonTrongCombo = $monTrongCombo->pluck('gioi_han_so_luong', 'mon_an_id')->toArray();
        }

        return view('admins.chi-tiet-order.create', compact('order', 'monAns', 'soLuongMonTrongCombo'));
    }

    /**
     * Lưu món mới vào order (gọi thêm)
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:order_mon,id',
            'mon_an_id' => 'required|exists:mon_an,id',
            'so_luong' => 'required|integer|min:1',
        ]);

        $order = OrderMon::with('datBan')->findOrFail($request->order_id);

        // ✅ Lấy combo của bàn để xác định món trong combo
        $comboId = $order->datBan->combo_id ?? null;
        $soLuongMonTrongCombo = [];

        if ($comboId) {
            $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();
            $soLuongMonTrongCombo = $monTrongCombo->pluck('gioi_han_so_luong', 'mon_an_id')->toArray();
        }

        // Luôn tạo món gọi thêm mới dù trùng combo
        ChiTietOrder::create([
            'order_id' => $request->order_id,
            'mon_an_id' => $request->mon_an_id,
            'so_luong' => $request->so_luong,
            'ghi_chu' => $request->ghi_chu,
            'loai_mon' => 'goi_them', // luôn là gọi thêm
            'trang_thai' => 'cho_bep',
        ]);

        return redirect()->route('admin.chi-tiet-order.index', ['order_id' => $request->order_id])
            ->with('success', 'Đã thêm món gọi thêm vào đơn hàng thành công!');
    }

    /**
     * Trang sửa món gọi thêm (nếu cần)
     */
    public function edit($id)
    {
        $ct = ChiTietOrder::with('monAn', 'orderMon')->findOrFail($id);
        return view('admins.chi-tiet-order.edit', compact('ct'));
    }

    /**
     * Cập nhật món gọi thêm
     */
    public function update(Request $request, $id)

    {
        $ct = ChiTietOrder::findOrFail($id);

        $request->validate([
            // 'so_luong' => 'required|integer|min:1',
            'ghi_chu' => 'nullable|string',
            'trang_thai' => 'required|in:cho_bep,dang_che_bien,da_len_mon,huy_mon',
        ]);

        // Update trực tiếp các trường
        $ct->update([
            'so_luong' => $request->so_luong ?? $ct->so_luong,
            'ghi_chu' => $request->ghi_chu,
            'trang_thai' => $request->trang_thai,
        ]);
        //  dd($ct->fresh());
        // Cập nhật tổng tiền/tổng món
        ChiTietOrder::capNhatTongOrder($ct->order_id);

        return redirect()->route('admin.chi-tiet-order.index', ['order_id' => $ct->order_id])
            ->with('success', 'Cập nhật món ăn thành công!');
    }

    /**
     * Xóa món gọi thêm
     */
    public function destroy($id)
    {
        $ct = ChiTietOrder::findOrFail($id);
        $ct->delete();

        return redirect()->route('admin.chi-tiet-order.index', ['order_id' => $ct->order_id])
            ->with('success', 'Đã xóa món ăn khỏi đơn hàng!');
    }
}
