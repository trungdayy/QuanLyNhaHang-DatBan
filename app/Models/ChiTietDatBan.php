<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDatBan extends Model
{
    use HasFactory;

    // QUAN TRỌNG: Map đúng tên bảng trong database
    protected $table = 'dat_ban_combo';

    protected $fillable = [
        'dat_ban_id',
        'combo_id',
        'so_luong',
    ];

    /**
     * Quan hệ: Chi tiết này thuộc về 1 Combo
     */
    public function combo()
    {
        return $this->belongsTo(ComboBuffet::class, 'combo_id', 'id');
    }

    /**
     * Quan hệ: Chi tiết này thuộc về 1 Đơn đặt bàn
     */
    public function datBan()
    {
        return $this->belongsTo(DatBan::class, 'dat_ban_id', 'id');
    }

    /**
     * Alias: Hỗ trợ code cũ nếu lỡ gọi $detail->comboBuffet
     */
    public function comboBuffet()
    {
        return $this->combo();
    }
}