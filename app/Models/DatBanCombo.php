<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatBanCombo extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong cơ sở dữ liệu
     */
    protected $table = 'dat_ban_combo';

    /**
     * Các trường được phép thêm/sửa (Mass Assignment)
     */
    protected $fillable = [
        'dat_ban_id',
        'combo_id',
        'so_luong',
        // Nếu bạn có thêm cột 'don_gia', 'thanh_tien' trong bảng này thì thêm vào đây
    ];

    /**
     * =================================================================
     * CÁC MỐI QUAN HỆ (RELATIONSHIPS)
     * =================================================================
     */

    /**
     * Quan hệ n-1: Dòng chi tiết này thuộc về 1 Đơn Đặt Bàn
     */
    public function datBan()
    {
        return $this->belongsTo(DatBan::class, 'dat_ban_id', 'id');
    }

    /**
     * Quan hệ n-1: Dòng chi tiết này thuộc về 1 Combo Buffet
     * 👇 QUAN TRỌNG: Tên hàm phải là 'combo' để khớp với Controller
     */
    public function combo()
    {
        // Liên kết với Model ComboBuffet thông qua khóa ngoại combo_id
        return $this->belongsTo(ComboBuffet::class, 'combo_id', 'id');
    }

    /**
     * (Tùy chọn) Alias: Giữ lại hàm này nếu code cũ của bạn có chỗ nào lỡ gọi ->comboBuffet
     * Nó sẽ trỏ về hàm combo ở trên.
     */
    public function comboBuffet()
    {
        return $this->combo();
    }
public function monAn()
{
    // Giả sử Model món ăn là MonAn và khóa ngoại là mon_an_id
    return $this->belongsTo(MonAn::class, 'mon_an_id');
}
}
