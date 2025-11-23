<?php

namespace App\Http\Controllers\Shop\NhanVien;

use App\Http\Controllers\Controller;
use App\Models\ChiTietOrder;
use App\Models\OrderMon;
use App\Models\MonTrongCombo;
use App\Models\DatBan;
use App\Models\MonAn;
use App\Models\BanAn;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;

class NhanVienOrderMonController extends Controller
{
    // Trang danh sách bàn
    public function index()
    {
        $bans = BanAn::with('khuVuc')->get();
        $orders = OrderMon::where('trang_thai', 'dang_xu_li')->get()->keyBy('ban_id');

        return view('Shop.nhanVien.order.index', compact('bans', 'orders'));
    }

    // Hiển thị chi tiết 1 order
    public function show($orderId)
    {
        $order = OrderMon::with(['chiTietOrders.monAn', 'banAn', 'datBan.comboBuffet'])
            ->findOrFail($orderId);

        $monAns = MonAn::where('trang_thai', 'dang_ban')->get();

        // Lấy số lượng món combo (nếu có)
        $comboId = $order->datBan->combo_id ?? null;
        $soLuongMonTrongCombo = [];
        if ($comboId) {
            $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();
            $soLuongMonTrongCombo = $monTrongCombo->pluck('gioi_han_so_luong', 'mon_an_id')->toArray();
        }

        // Gắn số lượng hiển thị cho từng chi tiết order
        $order->chiTietOrders->transform(function ($ct) use ($soLuongMonTrongCombo) {
            $ct->so_luong_hien_thi = $ct->loai_mon === 'combo'
                ? ($soLuongMonTrongCombo[$ct->mon_an_id] ?? $ct->so_luong)
                : $ct->so_luong;
            return $ct;
        });

        return view('Shop.nhanVien.chi-tiet-order.show', compact('order', 'monAns'));
    }

    // Mở order cho bàn đang hoạt động
    public function moOrder(Request $request)
    {
        $banId = $request->input('ban_id');

        // Kiểm tra xem bàn này đã có order dang_xu_li chưa
        $existingOrder = OrderMon::where('ban_id', $banId)
            ->where('trang_thai', 'dang_xu_li')
            ->first();

        if ($existingOrder) {
            return redirect()->back()->with('warning', 'Bàn này đã có order đang xử lý!');
        }

        // Tìm đặt bàn hợp lệ: CHỈ 2 trạng thái này được mở order
        $datBan = DatBan::where('ban_id', $banId)
            ->whereIn('trang_thai', ['da_xac_nhan', 'khach_da_den'])
            ->latest()
            ->first();

        if (!$datBan) {
            return redirect()->back()->with('warning', 'Bàn này chưa có đặt bàn hợp lệ!');
        }

        // Cập nhật trạng thái đặt bàn thành khách đã đến
        if ($datBan->trang_thai != 'khach_da_den') {
            $datBan->update(['trang_thai' => 'khach_da_den']);
        }

        // Tạo order
        $order = OrderMon::create([
            'ban_id'     => $banId,
            'dat_ban_id' => $datBan->id,
            'tong_mon'   => 0,
            'tong_tien'  => 0,
            'trang_thai' => 'dang_xu_li',
        ]);

        return redirect()->route('nhanVien.order.index', ['order_id' => $order->id])
            ->with('success', 'Mở order thành công!');
    }

    // public function moOrder(Request $request)
    // {
    //     $banId = $request->ban_id;

    //     // Kiểm tra bàn tồn tại
    //     $ban = BanAn::find($banId);
    //     if (!$ban) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Bàn không tồn tại!'
    //         ]);
    //     }

    //     // Kiểm tra xem bàn đã có order đang xử lý chưa
    //     $order = OrderMon::where('ban_id', $banId)
    //         ->where('trang_thai', 'dang_xu_li')
    //         ->first();

    //     // 👉 Nếu đã có order đang xử lý → tiếp tục order đó
    //     if ($order) {
    //         return response()->json([
    //             'success' => true,
    //             'order' => $order,
    //             'mode' => 'tiep_tuc_order'
    //         ]);
    //     }

    //     // 👉 Nếu chưa có → tạo order mới
    //     $order = OrderMon::create([
    //         'ban_id'     => $banId,
    //         'tong_mon'   => 0,
    //         'tong_tien'  => 0,
    //         'trang_thai' => 'dang_xu_li',
    //     ]);

    //     // 👉 Cập nhật trạng thái bàn thành "có khách"
    //     $ban->update(['trang_thai' => 'co_khach']);

    //     return response()->json([
    //         'success' => true,
    //         'order' => $order,
    //         'mode' => 'tao_moi'
    //     ]);
    // }


    public function edit($orderId, $ctId)
    {
        $order = OrderMon::with('chiTietOrders.monAn')
            ->findOrFail($orderId);

        $ct = ChiTietOrder::findOrFail($ctId);

        return view('Shop.nhanVien.chi-tiet-order.edit', compact('order', 'ct'));
    }

    // Hiển thị form tạo chi tiết order mới
    public function create(Request $request)
    {
        $orderId = $request->query('order_id');

        // Lấy order cùng bàn và combo
        $order = OrderMon::with(['banAn', 'datBan.comboBuffet'])->findOrFail($orderId);

        // Lấy danh sách món đang bán
        $monAns = MonAn::where('trang_thai', 'con')->get();

        // Nếu cần gợi ý món combo
        $comboId = $order->datBan->combo_id ?? null;
        $soLuongMonTrongCombo = [];
        if ($comboId) {
            $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();
            $soLuongMonTrongCombo = $monTrongCombo->pluck('gioi_han_so_luong', 'mon_an_id')->toArray();
        }

        return view('Shop.nhanVien.chi-tiet-order.create', compact('order', 'monAns', 'soLuongMonTrongCombo'));
    }


    // Xử lý lưu món mới vào database
    public function store(Request $request)
    {
        $data = $request->all();

        // Nếu gửi theo AJAX, items là mảng
        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                ChiTietOrder::create([
                    'order_id' => $data['order_id'],
                    'mon_an_id' => $item['mon_an_id'],
                    'so_luong' => $item['so_luong'] ?? 1,
                    'loai_mon' => 'goi_them',
                    'ghi_chu' => $item['ghi_chu'] ?? null,
                    'trang_thai' => 'cho_bep',
                ]);
            }
        } else {
            // Nếu gửi form bình thường
            $request->validate([
                'order_id' => 'required',
                'mon_an_id' => 'required',
                'so_luong' => 'required|integer|min:1'
            ]);
            ChiTietOrder::create([
                'order_id' => $request->order_id,
                'mon_an_id' => $request->mon_an_id,
                'so_luong' => $request->so_luong,
                'loai_mon' => 'goi_them',
                'ghi_chu' => $request->ghi_chu,
                'trang_thai' => 'cho_bep',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm món vào order!'
        ]);
    }

    // Sửa món
    public function update(Request $request, $id)
    {
        $ct = ChiTietOrder::findOrFail($id);
        $ct->update([
            'so_luong' => $request->so_luong,
            'ghi_chu' => $request->ghi_chu
        ]);

        return redirect()->route('nhanVien.chi-tiet-order.show', $ct->order_id)
            ->with('success', 'Cập nhật thành công!');
    }

    // Xóa món
    public function destroy($id)
    {
        $ct = ChiTietOrder::findOrFail($id);
        if ($ct->loai_mon === 'combo') {
            return redirect()->back()->with('error', 'Món trong combo không thể xóa!');
        }
        $ct->delete();

        return redirect()->back()->with('success', 'Đã xóa món!');
    }

    public function orderPage($orderId)
    {
        $order = OrderMon::with(['banAn', 'chiTietOrders.monAn'])
            ->findOrFail($orderId);

        // Nếu chưa chọn combo thì chuyển sang trang chọn combo
        if (!$order->datBan->combo_id) {
            return redirect()
                ->route('Shop.nhanVien.order.chon-combo', $orderId)
                ->with('warning', 'Vui lòng chọn combo trước khi gọi món!');
        }

        // Lấy số lượng món combo (nếu có)
        $comboId = $order->datBan->combo_id ?? null;
        $soLuongMonTrongCombo = [];
        if ($comboId) {
            $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();
            $soLuongMonTrongCombo = $monTrongCombo->pluck('gioi_han_so_luong', 'mon_an_id')->toArray();
        }

        // Gắn số lượng hiển thị cho từng chi tiết order
        $order->chiTietOrders->transform(function ($ct) use ($soLuongMonTrongCombo) {
            $ct->so_luong_hien_thi = $ct->loai_mon === 'combo'
                ? ($soLuongMonTrongCombo[$ct->mon_an_id] ?? $ct->so_luong)
                : $ct->so_luong;
            return $ct;
        });

        return view('Shop.nhanVien.order.page', compact('order'));
    }

    // Gửi order sang bếp

    public function guiBep($orderId)
    {
        $order = OrderMon::findOrFail($orderId);
        $order->load(['banAn', 'chiTietOrders.monAn']);

        // Lấy danh sách order gửi bếp hiện tại trong cache
        $ordersGuiBep = Cache::get('orders_gui_bep', []);

        $ordersGuiBep[$order->id] = $order; // thêm order vào cache

        Cache::put('orders_gui_bep', $ordersGuiBep, 3600); // lưu 1 giờ

        return redirect()->back()->with('success', 'Đã gửi bếp');
    }

    public function chonCombo($orderId)
    {
        $order = OrderMon::with('datBan')->findOrFail($orderId);

        // Lấy combo kèm hình ảnh, giá và danh sách món trong combo
        $combos = \App\Models\ComboBuffet::with([
            'monTrongCombo.monAn'
        ])
            ->where('trang_thai', 'dang_ban')
            ->get();

        return view('Shop.nhanVien.order.chon-combo', compact('order', 'combos'));
    }


    public function luuCombo(Request $request, $orderId)
    {
        $order = OrderMon::with('datBan')->findOrFail($orderId);

        $request->validate([
            'combo_id' => 'required|exists:combo_buffet,id'
        ]);

        // Lưu combo vào đặt bàn
        $order->datBan->update([
            'combo_id' => $request->combo_id
        ]);

        // Lấy danh sách món trong combo
        $monTrongCombo = MonTrongCombo::where('combo_id', $request->combo_id)->get();

        // Thêm món combo vào order
        foreach ($monTrongCombo as $item) {
            ChiTietOrder::create([
                'order_id' => $orderId,
                'mon_an_id' => $item->mon_an_id,
                'so_luong' => $item->gioi_han_so_luong,
                'loai_mon' => 'combo',
                'trang_thai' => 'cho_bep',
            ]);
        }

        return redirect()->route('nhanVien.order.page', $orderId)
            ->with('success', 'Đã chọn combo cho khách!');
    }
}
