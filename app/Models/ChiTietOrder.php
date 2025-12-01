<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietOrder extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_order';

    protected $fillable = [
        'order_id',
        'mon_an_id',
        'so_luong',
        'ghi_chu',
        'trang_thai',
        'loai_mon', // 'combo' hoặc 'goi_them'
    ];

    public function orderMon()
    {
        return $this->belongsTo(OrderMon::class, 'order_id');
    }

    public function monAn()
    {
        return $this->belongsTo(MonAn::class, 'mon_an_id');
    }

    // --- TẠM THỜI TẮT OBSERVER ĐỂ TRÁNH LỖI LOGIC TÍNH TIỀN ---
    /*
    protected static function booted()
    {
        static::saved(function ($chiTiet) {
            // self::capNhatTongOrder($chiTiet->order_id);
        });

        static::deleted(function ($chiTiet) {
            // self::capNhatTongOrder($chiTiet->order_id);
        });
    }
    */

    /**
     * Tính thành tiền đơn giản
     * - Nếu là món trong gói (combo) -> 0đ
     * - Nếu là món gọi thêm -> Lấy giá món * số lượng
     */
    public function getThanhTienAttribute()
    {
        // Nếu là món trong gói combo thì giá = 0
        if ($this->loai_mon === 'combo') {
            return 0;
        }

        // Nếu là món gọi thêm thì tính tiền
        $gia = $this->monAn->gia ?? 0;
        return $gia * ($this->so_luong ?? 0);
    }
}