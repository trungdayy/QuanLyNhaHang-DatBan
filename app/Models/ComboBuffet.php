<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ComboBuffet extends Model
{
    use HasFactory;

    protected $table = 'combo_buffet';

    protected $fillable = [
        'ten_combo',
        'loai_combo',
        'gia_co_ban',
        'thoi_luong_phut',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'anh', // ✅ THÊM DÒNG NÀY
        'trang_thai',
    ];

    protected $casts = [
        'thoi_gian_bat_dau' => 'datetime',
        'thoi_gian_ket_thuc' => 'datetime',
    ];

    public function monTrongCombo()
    {
        return $this->hasMany(MonTrongCombo::class, 'combo_id');
    }

    public function getTrangThaiDisplayAttribute()
    {
        switch ($this->trang_thai) {
            case 'dang_ban':
                return 'Đang bán';
            case 'ngung_ban':
                return 'Ngừng bán';
            default:
                return '—';
        }
    }

    public function getTrangThaiBadgeAttribute()
    {
        switch ($this->trang_thai) {
            case 'dang_ban':
                return 'bg-success';
            case 'ngung_ban':
                return 'bg-danger text-white';
            default:
                return 'bg-secondary';
        }
    }

    public function getLoaiComboDisplayAttribute()
    {
        switch ($this->loai_combo) {
            case 'nguoi_lon':
                return 'Người lớn';
            case 'tre_em':
                return 'Trẻ em';
            case 'vip':
                return 'VIP';
            case 'khuyen_mai':
                return 'Khuyến mãi';
            default:
                return '—';
        }
    }

    public function getLoaiComboBadgeAttribute()
    {
        switch ($this->loai_combo) {
            case 'nguoi_lon':
                return 'badge-primary';
            case 'tre_em':
                return 'badge-info';
            case 'vip':
                return 'badge-warning text-dark';
            case 'khuyen_mai':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    public function getStartTimeDisplayAttribute()
    {
        return $this->thoi_gian_bat_dau
            ? $this->thoi_gian_bat_dau->format('d/m/Y H:i')
            : '—';
    }

    public function getEndTimeDisplayAttribute()
    {
        return $this->thoi_gian_ket_thuc
            ? $this->thoi_gian_ket_thuc->format('d/m/Y H:i')
            : '—';
    }
    public function monAn()
    {
        return $this->belongsToMany(
            MonAn::class,
            'mon_trong_combo',
            'combo_id',
            'mon_an_id'
        )->withPivot(['gioi_han_so_luong', 'phu_phi_goi_them']);
    }
public function chiTietDatBan()
    {
        return $this->hasMany(ChiTietDatBan::class, 'combo_id', 'id');
    }

    
}
