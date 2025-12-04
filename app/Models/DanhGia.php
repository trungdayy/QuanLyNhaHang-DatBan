<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    use HasFactory;

    protected $table = 'danh_gia'; // Tên bảng trong DB

    protected $fillable = [
        'ten_khach',
        'sdt',
        'email',
        'nghe_nghiep',
        'noi_dung',
        'so_sao',
        'trang_thai' // cho_duyet, hien_thi, an
    ];
}