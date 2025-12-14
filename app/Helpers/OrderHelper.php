<?php
namespace App\Helpers;

use App\Models\OrderMon;

class OrderHelper {
    public static function capNhatTongOrder(OrderMon $order) {
        $order->load(['chiTietOrders.monAn', 'datBan.chiTietDatBan.combo']);

        $tongTienCombo = $order->datBan->chiTietDatBan->sum(function($ct){
            return ($ct->so_luong ?? 1) * ($ct->combo->gia_co_ban ?? 0);
        });

        $tongTienMonThem = $order->chiTietOrders->where('loai_mon', 'goi_them')->sum(function($ct){
            return ($ct->so_luong ?? 0) * ($ct->monAn->gia ?? 0);
        });

        $tongMonCombo = $order->datBan->chiTietDatBan->sum(function($ct){
            return ($ct->so_luong ?? 0) * ($ct->combo->monTrongCombo->sum('gioi_han_so_luong') ?? 0);
        });

        $tongMonThem = $order->chiTietOrders->where('loai_mon', 'goi_them')->sum('so_luong');

        $order->update([
            'tong_mon' => $tongMonCombo + $tongMonThem,
            'tong_tien' => $tongTienCombo + $tongTienMonThem,
        ]);
    }
}