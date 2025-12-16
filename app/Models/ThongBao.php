<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// [MỚI] Thêm Import cho Model DatBan
use App\Models\DatBan; 

class ThongBao extends Model
{
    use HasFactory;
    
    // Khai báo tên bảng (để chắc chắn)
    protected $table = 'thong_bao';
    
    // Các cột được phép điền dữ liệu
    protected $fillable = [
        'loai', 
        'noi_dung', 
        'dat_ban_id', 
        'da_xem' 
    ];

    // Liên kết ngược lại bảng đặt bàn (để sau này lấy tên bàn, tên khách nếu cần)
    public function datBan()
    {
        return $this->belongsTo(DatBan::class, 'dat_ban_id');
    }
}