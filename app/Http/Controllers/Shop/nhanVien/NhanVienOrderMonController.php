<?php

namespace App\Http\Controllers\Shop\NhanVien;

use App\Http\Controllers\Controller;
use App\Models\ChiTietOrder;
use App\Models\OrderMon;
use App\Models\MonTrongCombo;
use App\Models\DatBan;
use App\Models\MonAn;
use App\Models\BanAn;
use App\Models\KhuVuc;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;

class NhanVienOrderMonController extends Controller
{
    // Trang danh sách bàn
    public function index()
    {
        $bans = BanAn::with('khuVuc')->get();

        $orders = OrderMon::with('datBan')
            ->whereIn('trang_thai', ['dang_xu_li', 'dang_phuc_vu'])
            ->get()
            ->filter(function ($order) {
                return in_array($order->datBan->trang_thai, ['da_xac_nhan', 'khach_da_den']);
            })
            ->keyBy('ban_id');

        foreach ($bans as $ban) {
            $datBanMoiNhat = DatBan::where('ban_id', $ban->id)->latest()->first();

            if (!$datBanMoiNhat) {
                $ban->trang_thai = 'trong';
                continue;
            }

            if ($orders->has($ban->id)) {
                $ban->trang_thai = 'dang_phuc_vu';
                continue;
            }

            if (in_array($datBanMoiNhat->trang_thai, ['da_xac_nhan', 'khach_da_den'])) {
                $ban->trang_thai = 'san_sang';
            } else {
                $ban->trang_thai = 'trong';
            }
        }

        // Lấy danh sách khu vực từ các bàn (nếu không có bàn cũng trả về collection rỗng)
        $khuVucs = KhuVuc::whereIn('id', $bans->pluck('khu_vuc_id')->unique())->get();

        return view('Shop.nhanVien.order.index', compact('bans', 'orders', 'khuVucs'));
    }

    // Mở order mới cho bàn
    public function moOrder(Request $request)
    {
        $ban_id = $request->ban_id;

        // Tìm đặt bàn theo ban_id
        $datBan = DatBan::where('ban_id', $ban_id)
            ->whereIn('trang_thai', ['da_xac_nhan', 'khach_da_den'])
            ->latest()
            ->first();

        if (!$datBan) {
            return back()->with('error', 'Bàn này chưa được đặt hoặc chưa xác nhận!');
        }

        // ❗ Chỉ mở order nếu khách đã đến
        if ($datBan->trang_thai !== 'khach_da_den') {
            return back()->with('error', 'Khách chưa đến — không thể mở Order!');
        }

        // Tạo order mới KHÔNG kiểm tra order cũ
        $order = OrderMon::create([
            'dat_ban_id' => $datBan->id,
            'ban_id'     => $ban_id,
            'trang_thai' => 'dang_xu_li',
            'tong_mon'   => 0,
            'tong_tien'  => 0,
        ]);

        // Cập nhật trạng thái bàn → đang phục vụ
        $ban = BanAn::find($ban_id);
        $ban->trang_thai = 'dang_phuc_vu';
        $ban->save();

        return redirect()->route('nhanVien.order.page', $order->id)
            ->with('success', 'Mở order thành công!');
    }


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
                    'so_luong' =>  1,
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

        return redirect()->route('nhanVien.order.page', $ct->order_id)
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
        $order = OrderMon::with(['banAn', 'datBan.comboBuffet', 'chiTietOrders.monAn'])
            ->findOrFail($orderId);

        // Nếu chưa chọn combo thì chuyển sang trang chọn combo
        if (!$order->datBan->combo_id) {
            return redirect()
                ->route('nhanVien.order.chon-combo', $orderId)
                ->with('warning', 'Vui lòng chọn combo trước khi gọi món!');
        }

        // Lấy số lượng món combo (nếu có)
        $comboId = $order->datBan->combo_id ?? null;
        $soLuongMonTrongCombo = [];
        $soKhach = $order->datBan->so_khach ?? 1;

        if ($comboId) {
            $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();
            $soLuongMonTrongCombo = $monTrongCombo->pluck('mon_an_id')->toArray();

            foreach ($monTrongCombo as $item) {
                $exists = $order->chiTietOrders
                    ->where('mon_an_id', $item->mon_an_id)
                    ->where('loai_mon', 'combo')
                    ->first();

                if (!$exists) {
                    ChiTietOrder::create([
                        'order_id'   => $orderId,
                        'mon_an_id'  => $item->mon_an_id,
                        'so_luong'   => $item->so_luong,
                        'loai_mon'   => 'combo',
                        'trang_thai' => 'cho_bep',
                    ]);
                }
            }

            $order->load('chiTietOrders.monAn');
        }


        $order->chiTietOrders->transform(function ($ct) use ($soLuongMonTrongCombo, $soKhach) {
            if ($ct->loai_mon === 'combo') {
                $gioiHan = $soLuongMonTrongCombo[$ct->mon_an_id] ?? $ct->so_luong ?? 1;
                $ct->so_luong_hien_thi = min($soKhach, $gioiHan);
            } else {
                $ct->so_luong_hien_thi = $ct->so_luong;
            }
            return $ct;
        });

        return view('Shop.nhanVien.order.page', compact('order'));
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