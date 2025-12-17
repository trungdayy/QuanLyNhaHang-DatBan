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
        'mon_an_id', // Thêm trường này vào để tránh lỗi nếu bạn lưu món lẻ
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
     * Alias: Hỗ trợ code cũ nếu gọi $detail->comboBuffet
     */
    public function comboBuffet()
    {
        return $this->combo();
    }

    /**
     * [MỚI] Quan hệ: Chi tiết này thuộc về 1 Món ăn (Món lẻ)
     * Hàm này bắt buộc phải có để sửa lỗi RelationNotFoundException
     */
    public function monAn()
    {
        // Lưu ý: Nếu bảng dat_ban_combo trong database chưa có cột 'mon_an_id'
        // thì code vẫn chạy được nhưng sẽ trả về null (không lỗi đỏ nữa).
        return $this->belongsTo(MonAn::class, 'mon_an_id', 'id');
    }

    /**
     * Quan hệ: Chi tiết này thuộc về 1 Đơn đặt bàn
     */
    public function datBan()
    {
        return $this->belongsTo(DatBan::class, 'dat_ban_id', 'id');
    }
}