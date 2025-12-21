<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChiTietOrder;
use App\Models\OrderMon;
use App\Models\MonTrongCombo;
use App\Models\MonAn;
use Illuminate\Http\Request;
use App\Helpers\OrderHelper;

class ChiTietOrderController extends Controller
{
    public function index(Request $request)
    {
        $orderId = $request->query('order_id');

        if ($orderId) {
            $order = OrderMon::with(['chiTietOrders.monAn', 'datBan.combos'])->find($orderId);

            if (!$order) {
                return redirect()->route('admin.chi-tiet-order.index')
                    ->with('error', 'Đơn hàng không tồn tại.');
            }

            // ✅ Đồng bộ tất cả món từ tất cả combo của bàn
            $this->capNhatSoLuongCombo($order);

            // Lấy danh sách món đang bán (gọi thêm)
            $monAns = MonAn::where('trang_thai', 'dang_ban')->get();

            foreach ($order->chiTietOrders as $ct) {
                $ct->loai_mon_hien_thi = $ct->loai_mon === 'combo' ? 'Combo' : 'Gọi thêm';

                $ct->so_luong_hien_thi = $ct->so_luong;
            }

            return view('admins.chi-tiet-order.show', compact('order', 'monAns'));
        }

        // Nếu không có order_id → hiển thị tất cả đơn
        $orders = OrderMon::with(['datBan.combos'])->latest()->paginate(10);
        $monAns = MonAn::where('trang_thai', 'dang_ban')->get();

        return view('admins.chi-tiet-order.index', compact('orders', 'monAns'));
    }

    /**
     * Trang thêm món vào order
     */
    public function create(Request $request)
    {
        $orderId = $request->query('order_id');

        $order = OrderMon::with(['datBan.combos'])->find($orderId);
        if (!$order) {
            return redirect()->route('admin.chi-tiet-order.index')
                ->with('error', 'Không tìm thấy đơn hàng.');
        }

        // Cập nhật số lượng combo trước khi hiển thị form
        $this->capNhatSoLuongCombo($order);
        OrderHelper::capNhatTongOrder($order);
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

        // Luôn tạo món gọi thêm mới dù trùng combo
        ChiTietOrder::create([
            'order_id' => $request->order_id,
            'mon_an_id' => $request->mon_an_id,
            'so_luong' => $request->so_luong,
            'ghi_chu' => $request->ghi_chu,
            'loai_mon' => 'goi_them', // luôn là gọi thêm
            'trang_thai' => 'cho_bep',
        ]);

        // Đồng bộ số lượng combo sau khi thêm món
        $this->capNhatSoLuongCombo($order);
        OrderHelper::capNhatTongOrder($order);
        return redirect()->route('admin.chi-tiet-order.index', ['order_id' => $request->order_id])
            ->with('success', 'Đã thêm món gọi thêm vào đơn hàng thành công!');
    }

    /**
     * Trang sửa món gọi thêm (nếu cần)
     */
    public function edit($id)
    {
        $ct = ChiTietOrder::with('monAn', 'orderMon')->findOrFail($id);
        $order = $ct->orderMon;
        OrderHelper::capNhatTongOrder($order);
        return view('admins.chi-tiet-order.edit', compact('ct'));
    }

    /**
     * Cập nhật món gọi thêm
     */
    public function update(Request $request, $id)
    {
        $ct = ChiTietOrder::findOrFail($id);

        $request->validate([
            'ghi_chu' => 'nullable|string',
            'trang_thai' => 'required|in:cho_bep,dang_che_bien,da_len_mon,huy_mon',
        ]);

        $ct->update([
            'so_luong' => $request->so_luong ?? $ct->so_luong,
            'ghi_chu' => $request->ghi_chu,
            'trang_thai' => $request->trang_thai,
        ]);



        // Đồng bộ số lượng combo
        $order = OrderMon::with('datBan')->find($ct->order_id);
        $this->capNhatSoLuongCombo($order);

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

        // Đồng bộ số lượng combo
        $order = OrderMon::with('datBan')->find($ct->order_id);
        $this->capNhatSoLuongCombo($order);
        OrderHelper::capNhatTongOrder($order);
        return redirect()->route('admin.chi-tiet-order.index', ['order_id' => $ct->order_id])
            ->with('success', 'Đã xóa món ăn khỏi đơn hàng!');
    }
    private function capNhatSoLuongCombo(OrderMon $order)
    {
        if (!$order->datBan || !$order->datBan->combos) return;

        foreach ($order->datBan->combos as $datBanCombo) {

            $comboId = $datBanCombo->combo_id;
            $soLuongCombo = $datBanCombo->pivot->so_luong;

            $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();

            foreach ($monTrongCombo as $m) {

                // ✅ MỖI MÓN = SỐ COMBO, KHÔNG NHÂN GIỚI HẠN
                $soLuong = $soLuongCombo;

                ChiTietOrder::where([
                    'order_id' => $order->id,
                    'mon_an_id' => $m->mon_an_id,
                    'loai_mon'  => 'combo',
                ])->delete();

                ChiTietOrder::create([
                    'order_id'  => $order->id,
                    'mon_an_id' => $m->mon_an_id,
                    'loai_mon'  => 'combo',
                    'so_luong'  => $soLuong,
                    'trang_thai' => 'cho_bep',
                ]);
            }
        }
    }
}