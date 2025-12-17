<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    use HasFactory;

    protected $table = 'danh_muc_mon';

    protected $fillable = [
        'ten_danh_muc',
        'mo_ta',
        'hien_thi',
    ];

    public function monAn()
    {
        return $this->hasMany(MonAn::class, 'danh_muc_id');
    }

    // BẠN CẦN THÊM 2 HÀM NÀY VÀO ĐỂ LÀM ĐẸP GIAO DIỆN

    /**
     * Accessor: Tự động dịch 'hien_thi' sang Tiếng Việt
     * Cách dùng trong Blade: $dm->hien_thi_display
     */
    public function getHienThiDisplayAttribute()
    {
        // $this->hien_thi sẽ là 1 (true) hoặc 0 (false)
        return $this->hien_thi ? 'Hiển thị' : 'Ẩn';
    }

    /**
     * Accessor: Tự động trả về class màu sắc cho trạng thái
     * Cách dùng trong Blade: $dm->hien_thi_badge
     */
    public function getHienThiBadgeAttribute()
    {
        return $this->hien_thi ? 'bg-success' : 'bg-secondary text-white';
    }
}