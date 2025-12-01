<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatBan extends Model
{
    use HasFactory;

    protected $table = 'dat_ban';

    protected $fillable = [
        'ma_dat_ban',
        'ten_khach',
        'email_khach',
        'sdt_khach',
        'nguoi_lon', 
        'tre_em',    
        'ban_id',
        // 'combo_id', // Đã xóa vì chuyển sang bảng dat_ban_combo
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

    /**
     * [MỚI] Quan hệ: Một đơn đặt bàn có nhiều dòng chi tiết trong bảng dat_ban_combo
     * Dùng để lấy số lượng khách của từng combo
     */
    public function chiTietDatBan()
    {
        return $this->hasMany(ChiTietDatBan::class, 'dat_ban_id');
    }

    /**
     * [MỚI] Quan hệ Many-to-Many: Lấy trực tiếp danh sách các ComboBuffet
     * Dùng để hiển thị tên combo, giá...
     */
    public function combos()
    {
        return $this->belongsToMany(ComboBuffet::class, 'dat_ban_combo', 'dat_ban_id', 'combo_id')
                    ->withPivot('so_luong')
                    ->withTimestamps();
    }

    /**
     * Quan hệ tương thích ngược: Lấy chi tiết combo đầu tiên
     * Dùng để tương thích với code cũ đang sử dụng comboBuffet
     */
    public function comboBuffetChiTiet()
    {
        return $this->hasOne(ChiTietDatBan::class, 'dat_ban_id')->orderBy('id');
    }

    /**
     * Quan hệ tương thích ngược: Lấy combo đầu tiên từ danh sách combos
     * Dùng để tương thích với code cũ đang sử dụng comboBuffet
     * Sử dụng hasOneThrough để lấy combo đầu tiên thông qua chiTietDatBan
     */
    public function comboBuffet()
    {
        return $this->hasOneThrough(
            ComboBuffet::class,
            ChiTietDatBan::class,
            'dat_ban_id', // Foreign key on chi_tiet_dat_ban table
            'id', // Foreign key on combo_buffet table
            'id', // Local key on dat_ban table
            'combo_id' // Local key on chi_tiet_dat_ban table
        );
    }

    /**
     * Accessor: Tính tổng số khách từ nguoi_lon + tre_em
     * Dùng để tương thích với code cũ đang sử dụng so_khach
     */
    public function getSoKhachAttribute()
    {
        return ($this->nguoi_lon ?? 0) + ($this->tre_em ?? 0);
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