<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderMon;
use App\Models\MonAn;
use App\Models\DatBan;
use App\Models\BanAn;
use App\Models\ChiTietOrder;
use Illuminate\Http\Request;
use App\Helpers\OrderHelper;

class OrderMonController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderMon::with(['datBan', 'banAn'])->latest();

        if ($request->filled('ma_dat_ban')) {
            $query->whereHas('datBan', function ($q) use ($request) {
                $q->where('ma_dat_ban', 'like', '%' . $request->ma_dat_ban . '%');
            });
        }

        if ($request->filled('ten_khach')) {
            $query->whereHas('datBan', function ($q) use ($request) {
                $q->where('ten_khach', 'like', '%' . $request->ten_khach . '%');
            });
        }

        if ($request->filled('so_ban')) {
            $query->whereHas('banAn', function ($q) use ($request) {
                $q->where('so_ban', 'like', '%' . $request->so_ban . '%');
            });
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $orders = $query->paginate(10)->withQueryString();
        return view('admins.order-mon.index', compact('orders'));
    }


    public function create()
    {
        $datBans = DatBan::with(['banAn', 'chiTietDatBan.combo'])
            ->where('trang_thai', 'khach_da_den')
            ->orderByDesc('id')
            ->get();

        $banAns = BanAn::all();

        return view('admins.order-mon.create', compact('datBans', 'banAns'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'dat_ban_id' => 'required|exists:dat_ban,id',
            'ban_id' => 'required|exists:ban_an,id',
        ]);

        // Lấy thông tin đặt bàn + combo đã chọn
        $datBan = DatBan::with(['chiTietDatBan.combo'])->findOrFail($request->dat_ban_id);

        // Tạo order
        $order = OrderMon::create([
            'dat_ban_id' => $datBan->id,
            'ban_id'     => $datBan->ban_id,
            'tong_mon'   => 0,
            'tong_tien'  => 0,
            'trang_thai' => 'dang_xu_li',
        ]);

        $tongMon = 0;
        $tongTienCombo = 0;
        $tongTienMonThem = 0;

        /*
        |--------------------------------------------------------------------------
        | 1) TÍNH TIỀN COMBO
        |--------------------------------------------------------------------------
        | Công thức:
        | tiền combo = SUM( combo.don_gia * so_luong )
        */
        foreach ($datBan->chiTietDatBan as $ct) {
            $combo = $ct->combo;

            if (!$combo) continue;

            $donGiaCombo = $combo->gia_co_ban ?? 0;
            $soLuongCombo = $ct->so_luong ?? 1;

            $tongTienCombo += $donGiaCombo * $soLuongCombo;
            $tongMon += $combo->monTrongCombo->sum('gioi_han_so_luong') * $soLuongCombo;// mỗi combo tính như 1 phần
        }

        /*
        |--------------------------------------------------------------------------
        | 2) TÍNH TIỀN MÓN GỌI THÊM
        |--------------------------------------------------------------------------
        */
        if ($request->filled('mon')) {
            foreach ($request->mon as $item) {
                $monAn = MonAn::find($item['mon_an_id']);
                if (!$monAn) continue;

                $soLuong = (int)$item['so_luong'];
                $donGia = $monAn->gia;

                $tongMon += $soLuong;
                $tongTienMonThem += $donGia * $soLuong;

                ChiTietOrder::create([
                    'order_id'   => $order->id,
                    'mon_an_id'  => $monAn->id,
                    'so_luong'   => $soLuong,
                    'don_gia'    => $donGia,
                    'loai_mon'   => 'goi_them',
                    'trang_thai' => 'cho_bep',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3) TÍNH TỔNG TIỀN
        |--------------------------------------------------------------------------
        */
        $tongTien = $tongTienCombo + $tongTienMonThem;

        $order->update([
            'tong_mon'  => $tongMon,
            'tong_tien' => $tongTien,
        ]);

        return redirect()->route('admin.order-mon.index')
            ->with('success', 'Tạo order món thành công!');
    }


    public function edit(OrderMon $orderMon)
    {
        $orderMon->load(['chiTietOrders.monAn', 'datBan.chiTietDatBan.combo']);
        $datBans = DatBan::with('banAn')->get();
        $banAns = BanAn::all();

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

        if ($newStatus === 'hoan_thanh') {
            $daLenMon = $orderMon->chiTietOrders->every(fn($ct) => $ct->trang_thai === 'da_len_mon');

            if (!$daLenMon) {
                return back()->withInput()->with('error', 'Còn món chưa chế biến xong.');
            }
        }

        $datBan = DatBan::findOrFail($request->dat_ban_id);

        $orderMon->update([
            'dat_ban_id' => $datBan->id,
            'ban_id'     => $datBan->ban_id,
            'trang_thai' => $newStatus,
        ]);

        return redirect()->route('admin.order-mon.index')
            ->with('success', 'Cập nhật Order món thành công!');
    }


    public function destroy(OrderMon $orderMon)
    {
        $orderMon->delete();
        return redirect()->route('admin.order-mon.index')
            ->with('success', 'Xóa Order món thành công!');
    }

}