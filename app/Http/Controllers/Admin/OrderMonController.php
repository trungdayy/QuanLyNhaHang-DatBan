<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMon;
use App\Models\MonAn;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ChiTietOrder;
use Illuminate\Http\Request;

class OrderMonController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderMon::with(['datBan', 'banAn'])->latest();

        // Lọc theo mã đặt bàn
        if ($request->filled('ma_dat_ban')) {
            $query->whereHas('datBan', function ($q) use ($request) {
                $q->where('ma_dat_ban', 'like', '%' . $request->ma_dat_ban . '%');
            });
        }

        // Lọc theo tên khách
        if ($request->filled('ten_khach')) {
            $query->whereHas('datBan', function ($q) use ($request) {
                $q->where('ten_khach', 'like', '%' . $request->ten_khach . '%');
            });
        }

        // Lọc theo số bàn
        if ($request->filled('so_ban')) {
            $query->whereHas('banAn', function ($q) use ($request) {
                $q->where('so_ban', 'like', '%' . $request->so_ban . '%');
            });
        }

        // Lọc theo trạng thái order
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admins.order-mon.index', compact('orders'));
    }
    public function create()
    {
        $datBans = DatBan::with(['banAn', 'comboBuffet.monTrongCombo.monAn'])
            ->where('trang_thai', 'khach_da_den') // chỉ lấy những đơn đã xác nhận
            ->orderByDesc('id')
            ->get();
        $banAns = BanAn::all();
        return view('admins.order-mon.create', compact('datBans', 'banAns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dat_ban_id' => 'required|exists:dat_ban,id',
        ]);

        $datBan = DatBan::with('comboBuffet.monTrongCombo.monAn')->findOrFail($request->dat_ban_id);
        $giaCombo = $datBan->comboBuffet?->gia_co_ban ?? 0;
        $soKhach  = $datBan->so_khach ?? 0;
        $giamGia  = $datBan->giam_gia ?? 0;

        $order = OrderMon::create([
            'dat_ban_id' => $datBan->id,
            'ban_id'     => $datBan->ban_id,
            'tong_mon'   => 0,
            'tong_tien'  => 0,
            'trang_thai' => 'dang_xu_li',
        ]);

        $tongMon = 0;
        $tongTienGoiThem = 0;
        $tongPhuPhiVuot = 0;

        // Thêm món trong combo
        if ($datBan->comboBuffet && $datBan->comboBuffet->monTrongCombo->isNotEmpty()) {
            foreach ($datBan->comboBuffet->monTrongCombo as $monCombo) {
                $monAnModel = $monCombo->monAn;
                if (!$monAnModel) continue;

                $soLuongCombo = $monCombo->gioi_han_so_luong ?? 1;
                $tongMon += $soLuongCombo;

                ChiTietOrder::create([
                    'order_id'   => $order->id,
                    'mon_an_id'  => $monAnModel->id,
                    'so_luong'   => $soLuongCombo,
                    'loai_mon'   => 'combo',
                    'trang_thai' => 'cho_bep',
                ]);
            }
        }

        // Thêm món gọi thêm
        if ($request->filled('mon') && is_array($request->mon)) {
            foreach ($request->mon as $mon) {
                $monAn = MonAn::find($mon['mon_an_id']);
                if (!$monAn) continue;

                $soLuong = (int) $mon['so_luong'];
                $loaiMon = $mon['loai_mon'] ?? 'goi_them';
                $tongMon += $soLuong;

                ChiTietOrder::create([
                    'order_id'   => $order->id,
                    'mon_an_id'  => $monAn->id,
                    'so_luong'   => $soLuong,
                    'loai_mon'   => $loaiMon,
                    'trang_thai' => 'cho_bep',
                ]);

                if ($loaiMon === 'goi_them') $tongTienGoiThem += $monAn->gia * $soLuong;
                if ($loaiMon === 'combo') {
                    $gioiHan = $monAn->gioi_han ?? 0;
                    $phuPhi  = $monAn->phu_phi ?? 0;
                    if ($soLuong > $gioiHan) $tongPhuPhiVuot += ($soLuong - $gioiHan) * $phuPhi;
                }
            }
        }
        $tongTien = ($giaCombo * $soKhach) + $tongTienGoiThem + $tongPhuPhiVuot - $giamGia;

        $order->update([
            'tong_mon'  => $tongMon,
            'tong_tien' => $tongTien,
        ]);

        return redirect()->route('admin.order-mon.index')->with('success', 'Tạo order món thành công!');
    }

    public function edit(OrderMon $orderMon)
    {
        $orderMon->load('chiTietOrders');
        $datBans = DatBan::with('banAn')->get();
        $banAns = BanAn::all();

        // Luôn cho phép chọn "Hoàn thành" nhưng sẽ kiểm tra khi update
        $allowedStatus = [
            'dang_xu_li' => 'Đang xử lý',
            'hoan_thanh' => 'Hoàn thành',
        ];

        return view('admins.order-mon.edit', compact('orderMon', 'datBans', 'banAns', 'allowedStatus'));
    }

    public function update(Request $request, OrderMon $orderMon)
    {
        $request->validate([
            'dat_ban_id' => 'required|exists:dat_ban,id',
            'trang_thai' => 'required|in:dang_xu_li,hoan_thanh',
        ]);

        $orderMon->load('chiTietOrders');
        $newStatus = $request->trang_thai;

        // Nếu chọn Hoàn thành, kiểm tra chi tiết món
        if ($newStatus === 'hoan_thanh') {
            $tatCaMonDaLenMon = $orderMon->chiTietOrders->every(fn($ct) => $ct->trang_thai === 'da_len_mon');

            if (!$tatCaMonDaLenMon) {
                return back()
                    ->withInput()
                    ->with('error', 'Không thể hoàn thành order vì còn món chưa chế biến xong.');
            }
        }

        $datBan = DatBan::findOrFail($request->dat_ban_id);

        $orderMon->update([
            'dat_ban_id' => $datBan->id,
            'ban_id' => $datBan->ban_id,
            'trang_thai' => $newStatus,
        ]);

        return redirect()->route('admin.order-mon.index')->with('success', 'Cập nhật Order món thành công!');
    }
    public function destroy(OrderMon $orderMon)
    {
        $orderMon->delete();
        return redirect()->route('admin.order-mon.index')->with('success', 'Xóa Order món thành công!');
    }
}