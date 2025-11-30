<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatBan extends Model
{
    use HasFactory;

    protected $table = 'dat_ban';

    // Đã sửa lại fillable khớp với DB
    protected $fillable = [
        'ma_dat_ban',
        'ten_khach',
        'email_khach',
        'sdt_khach',
        'nguoi_lon', 
        'tre_em',    
        'ban_id',
        'combo_id',
        'nhan_vien_id',
        'gio_den',
        'thoi_luong_phut',
        'tien_coc',
        'trang_thai',
        'xac_thuc_ma',
        'la_dat_online',
        'ghi_chu',
    ];

    public function banAn()
    {
        return $this->belongsTo(BanAn::class, 'ban_id');
    }

    public function comboBuffet()
    {
        return $this->belongsTo(ComboBuffet::class, 'combo_id');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id');
    }

    public function orderMon()
    {
        return $this->hasMany(OrderMon::class, 'dat_ban_id');
    }

    public function hoaDon()
    {
        return $this->hasOne(HoaDon::class, 'dat_ban_id');
    }
}