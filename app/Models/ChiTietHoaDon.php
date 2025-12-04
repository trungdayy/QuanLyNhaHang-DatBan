<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietHoaDon extends Model
{
    protected $table = 'chi_tiet_hoa_don';

    protected $fillable = [
        'hoa_don_id',
        'ten_khach',
        'sdt_khach',
        'email_khach',
        'so_khach',
        'nguoi_lon',
        'tre_em',
        'ban_so',
        'khu_vuc',
        'tang',
        'so_ghe',
        'ma_dat_ban',
        'gio_vao',
        'gio_ra',
        'thoi_gian_phuc_vu_phut',
        'thoi_gian_quy_dinh_phut',
        'thoi_gian_vuot_phut',
        'so_lan_10_phut',
        'phu_thu_thoi_gian',
        'ten_combo',
        'gia_combo_per_person',
        'tong_tien_combo',
        'tong_tien_mon_goi_them',
        'danh_sach_mon',
        'tong_tien_combo_mon',
        'tien_giam_voucher',
        'tong_tien_sau_voucher',
        'tien_coc',
        'phu_thu_tu_dong',
        'phu_thu_thu_cong',
        'tong_phu_thu',
        'phai_thanh_toan',
        'tien_khach_dua',
        'tien_tra_lai',
        'phuong_thuc_tt',
        'ma_voucher',
    ];

    protected $casts = [
        'danh_sach_mon' => 'array',
        'gio_vao' => 'datetime',
        'gio_ra' => 'datetime',
        'tong_tien_combo' => 'decimal:2',
        'tong_tien_mon_goi_them' => 'decimal:2',
        'tong_tien_combo_mon' => 'decimal:2',
        'tien_giam_voucher' => 'decimal:2',
        'tong_tien_sau_voucher' => 'decimal:2',
        'tien_coc' => 'decimal:2',
        'phu_thu_tu_dong' => 'decimal:2',
        'phu_thu_thu_cong' => 'decimal:2',
        'tong_phu_thu' => 'decimal:2',
        'phai_thanh_toan' => 'decimal:2',
        'tien_khach_dua' => 'decimal:2',
        'tien_tra_lai' => 'decimal:2',
    ];

    /**
     * Quan hệ: Một chi tiết hóa đơn thuộc về một Hóa đơn
     */
    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'hoa_don_id');
    }
}