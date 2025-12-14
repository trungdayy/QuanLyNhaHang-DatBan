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
use App\Models\ComboBuffet;
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

            // bàn bảo trì
            if ($ban->trang_thai === 'khong_su_dung') {
                continue;
            }
            $datBanMoiNhat = DatBan::where('ban_id', $ban->id)->latest()->first();

            if (!$datBanMoiNhat) {
                $ban->trang_thai = 'trong';
                continue;
            }

            if ($orders->has($ban->id)) {
                $ban->trang_thai = 'dang_phuc_vu';
                $order = $orders[$ban->id];
                $soKhach = $order->datBan->so_khach ?? 1;

                $soLuongMonTrongCombo = [];
                if ($order->datBan->combo_id) {
                    $monTrongCombo = MonTrongCombo::where('combo_id', $order->datBan->combo_id)->get();
                    $soLuongMonTrongCombo = $monTrongCombo->pluck('gioi_han_so_luong', 'mon_an_id')->toArray();
                }

                $tongMon = 0;
                $tongTien = 0;
                foreach ($order->chiTietOrders as $ct) {
                    if ($ct->loai_mon === 'combo') {
                        $gioiHan = $soLuongMonTrongCombo[$ct->mon_an_id] ?? $ct->so_luong;
                        $soLuongHienThi = min($soKhach, $gioiHan);
                    } else {
                        $soLuongHienThi = $ct->so_luong;
                    }
                    $tongMon += $soLuongHienThi;
                    $tongTien += $soLuongHienThi * ($ct->monAn->gia ?? 0);
                }

                $order->tong_mon = $tongMon;
                $order->tong_tien = $tongTien;

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

        $datBan = DatBan::where('ban_id', $ban_id)
            ->whereIn('trang_thai', ['da_xac_nhan', 'khach_da_den'])
            ->latest()
            ->first();

        if (!$datBan || $datBan->trang_thai !== 'khach_da_den') {
            return back()->with('error', 'Khách chưa đến — không thể mở Order!');
        }

        $order = OrderMon::create([
            'dat_ban_id' => $datBan->id,
            'ban_id'     => $ban_id,
            'trang_thai' => 'dang_xu_li',
            'tong_mon'   => 0,
            'tong_tien'  => 0,
        ]);

        BanAn::find($ban_id)->update(['trang_thai' => 'dang_phuc_vu']);

        return redirect()->route('nhanVien.order.chon-combo', $order->id)
            ->with('success', 'Mở order thành công! Vui lòng chọn combo.');
    }

    public function edit($orderId, $ctId)
    {
        $order = OrderMon::with(['chiTietOrders.monAn', 'datBan'])->findOrFail($orderId);
        $ct = ChiTietOrder::findOrFail($ctId);

        $soKhach = $order->datBan->so_khach ?? 1;
        $comboId = $order->datBan->combo_id ?? null;

        // Lấy giới hạn số lượng từng món trong combo
        $soLuongMonTrongCombo = [];
        if ($comboId) {
            $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();
            $soLuongMonTrongCombo = $monTrongCombo->pluck('gioi_han_so_luong', 'mon_an_id')->toArray();
        }

        // Tính số lượng hiển thị dựa trên toàn bộ logic combo
        if ($ct->loai_mon === 'combo') {
            $gioiHan = $soLuongMonTrongCombo[$ct->mon_an_id] ?? $ct->so_luong;
            $ct->so_luong_hien_thi = min($soKhach, $gioiHan);
        } else {
            // Kiểm tra xem có món gọi thêm vượt combo không
            $tongMonCombo = array_sum($soLuongMonTrongCombo) * $soKhach;
            $tongMonHienTai = $order->chiTietOrders->sum('so_luong');
            $soMonVuot = max(0, $ct->so_luong + $tongMonHienTai - $tongMonCombo);

            if ($soMonVuot > 0) {
                $ct->so_luong_hien_thi = $ct->so_luong - $soMonVuot;
            } else {
                $ct->so_luong_hien_thi = $ct->so_luong;
            }
        }

        return view('Shop.nhanVien.chi-tiet-order.edit', compact('order', 'ct'));
    }

    // Hiển thị form tạo chi tiết order mới
    public function create(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = OrderMon::with(['banAn', 'datBan.combos'])->findOrFail($orderId);

        $monAns = MonAn::where('trang_thai', 'con')->get();

        $comboId = $order->datBan?->combo_id;
        $soLuongMonTrongCombo = [];
        if ($comboId) {
            $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();
            $soLuongMonTrongCombo = $monTrongCombo->pluck('gioi_han_so_luong', 'mon_an_id')->toArray();
        }

        $danhMucs = $monAns->pluck('danh_muc')->unique()->values();
        $loaiMons = $monAns->pluck('loai_mon')->unique()->values();

        return view('Shop.nhanVien.chi-tiet-order.create', compact(
            'order',
            'monAns',
            'soLuongMonTrongCombo',
            'danhMucs',
            'loaiMons'
        ));
    }

    // Xử lý lưu món mới vào database
    public function store(Request $request)
    {
        $data = $request->all();

        // Nếu gửi theo AJAX
        if (isset($data['items'])) {
            foreach ($data['items'] as $item) {
                ChiTietOrder::create([
                    'order_id'  => $data['order_id'],
                    'mon_an_id' => $item['mon_an_id'],
                    'so_luong'  => $item['so_luong'] ?? 1,
                    'loai_mon'  => ($item['is_combo'] == 1) ? 'combo' : 'goi_them',
                    'ghi_chu'   => $item['ghi_chu'] ?? null,
                    'trang_thai' => 'cho_bep',
                ]);
            }
        } else {
            // Nếu gửi form thường
            $request->validate([
                'order_id' => 'required',
                'mon_an_id' => 'required',
                'so_luong' => 'required|integer|min:1'
            ]);

            ChiTietOrder::create([
                'order_id'  => $request->order_id,
                'mon_an_id' => $request->mon_an_id,
                'so_luong'  => $request->so_luong,
                'loai_mon'  => $request->is_combo == 1 ? 'combo' : 'goi_them',
                'ghi_chu'   => $request->ghi_chu,
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

        $data = [
            'ghi_chu' => $request->ghi_chu
        ];

        // Chỉ cho sửa số lượng nếu không phải combo
        if ($ct->loai_mon !== 'combo') {
            $data['so_luong'] = $request->so_luong;
        }

        $ct->update($data);

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
        $order = OrderMon::with([
            'banAn',
            'datBan.combos.monTrongCombo.monAn',
            'chiTietOrders.monAn'
        ])->findOrFail($orderId);

        $datBan = $order->datBan;

        // Nếu chưa có combo nào
        if ($datBan->combos->isEmpty()) {
            return redirect()->route('nhanVien.order.chon-combo', $orderId)
                ->with('warning', 'Vui lòng chọn combo trước khi vào chi tiết order!');
        }

        // Đồng bộ các món combo vào chiTietOrders nếu chưa có
        foreach ($datBan->combos as $combo) {
            foreach ($combo->monTrongCombo as $item) {
                $exists = $order->chiTietOrders
                    ->where('mon_an_id', $item->mon_an_id)
                    ->where('loai_mon', 'combo')
                    ->first();

                if (!$exists) {
                    ChiTietOrder::create([
                        'order_id' => $orderId,
                        'mon_an_id' => $item->mon_an_id,
                        'so_luong' => $combo->pivot->so_luong,
                        'loai_mon' => 'combo',
                        'trang_thai' => 'cho_bep',
                    ]);
                }
            }
        }

        $order->load('chiTietOrders.monAn');
        $cts = $order->chiTietOrders;

        $order->chiTietOrders->each(function ($ct) {
            $ct->so_luong_hien_thi = $ct->so_luong;
        });

        return view('Shop.nhanVien.order.page', compact('order', 'cts'));
    }

    public function chonCombo($orderId)
    {
        $order = OrderMon::with('datBan')->findOrFail($orderId);
        $combos = ComboBuffet::with('monTrongCombo.monAn')->get();

        foreach ($combos as $combo) {
            $comboFolder = public_path('uploads/combo_buffet');
            $images = [];

            // Nếu combo có ảnh mặc định trong DB
            if ($combo->anh && file_exists(public_path('uploads/' . $combo->anh))) {
                $images[] = asset('uploads/' . $combo->anh);
            }

            // Lấy ảnh từ folder uploads/combo_buffet theo pattern combo_{id}_*.jpg/png
            if (file_exists($comboFolder)) {
                $pattern = $comboFolder . '/combo_' . $combo->id . '_*.{jpg,jpeg,png,gif}';
                $files = glob($pattern, GLOB_BRACE);
                foreach ($files as $file) {
                    $images[] = asset('uploads/combo_buffet/' . basename($file));
                }
            }

            // Nếu không có ảnh nào, dùng placeholder
            if (empty($images)) {
                $images[] = 'https://placehold.co/600x400?text=No+Image';
            }

            $combo->images = $images; // gán property tạm thời cho view
        }

        return view('Shop.nhanVien.order.chon-combo', compact('order', 'combos'));
    }

    public function luuCombo(Request $request, $orderId)
    {
        $order = OrderMon::with('datBan')->findOrFail($orderId);

        $combosInput = [];

        // Lặp qua combos gửi về
        if ($request->has('combos')) {
            foreach ($request->combos as $comboId => $qty) {
                $qty = (int)$qty;
                if ($qty > 0) {
                    $combosInput[$comboId] = ['so_luong' => $qty];
                }
            }
        }

        if (empty($combosInput)) {
            return redirect()->back()->with('warning', 'Vui lòng chọn ít nhất 1 combo!');
        }

        // Sync pivot table
        $order->datBan->combos()->sync($combosInput);

        // Xóa món combo cũ
        ChiTietOrder::where('order_id', $orderId)
            ->where('loai_mon', 'combo')
            ->delete();

        // Thêm món combo mới vào chi_tiet_order
        $combos = $order->datBan->combos()->with('monTrongCombo')->get();
        $chiTietToInsert = [];
        foreach ($combos as $combo) {
            $soLuongCombo = $combo->pivot->so_luong;

            foreach ($combo->monTrongCombo as $item) {
                $chiTietToInsert[] = [
                    'order_id' => $orderId,
                    'mon_an_id' => $item->mon_an_id,
                    'so_luong' => $soLuongCombo,
                    'loai_mon' => 'combo',
                    'trang_thai' => 'cho_bep',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($chiTietToInsert)) {
            ChiTietOrder::insert($chiTietToInsert);
        }

        return redirect()->route('nhanVien.order.page', $orderId)
            ->with('success', 'Đã chọn combo cho khách!');
    }

    protected function tinhTongTienOrder(OrderMon $order)
    {
        $soKhach = $order->datBan->so_khach ?? 1;
        $comboId = $order->datBan->combo_id ?? null;
        $tongTien = 0;
        $tongMon = 0;

        $soLuongMonTrongCombo = [];
        if ($comboId) {
            $monTrongCombo = MonTrongCombo::where('combo_id', $comboId)->get();
            $soLuongMonTrongCombo = $monTrongCombo->pluck('gioi_han_so_luong', 'mon_an_id')->toArray();
        }

        foreach ($order->chiTietOrders as $ct) {
            if ($ct->loai_mon === 'combo') {
                // số lượng hiển thị combo = số khách hoặc giới hạn món
                $gioiHan = $soLuongMonTrongCombo[$ct->mon_an_id] ?? $ct->so_luong;
                $soLuongHienThi = min($soKhach, $gioiHan);
                $tongMon += $soLuongHienThi;
                $tongTien += $soLuongHienThi * ($ct->monAn->gia ?? 0);
            } else {
                // món gọi thêm
                $soLuong = $ct->so_luong;
                $gia = $ct->monAn->gia ?? 0;
                $phuPhiVuot = $ct->phu_phi_vuot ?? 0; // thêm cột phu_phi_vuot trong chi_tiet_orders nếu cần

                // tính tổng số món combo giới hạn
                $tongMonCombo = array_sum($soLuongMonTrongCombo) * $soKhach;
                $soMonVuot = max(0, $soLuong + $tongMon - $tongMonCombo);

                if ($soMonVuot > 0) {
                    // số lượng vượt tính thêm phí
                    $tongTien += $soMonVuot * ($gia + $phuPhiVuot);
                    $soMonHienThi = $soLuong - $soMonVuot;
                    if ($soMonHienThi > 0) {
                        $tongTien += $soMonHienThi * $gia;
                    }
                } else {
                    $tongTien += $soLuong * $gia;
                }

                $tongMon += $soLuong;
            }
        }

        $order->tong_mon = $tongMon;
        $order->tong_tien = $tongTien;
        return $order;
    }
}