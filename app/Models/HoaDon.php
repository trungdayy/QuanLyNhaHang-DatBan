<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    // Bảng trong DB
    protected $table = 'hoa_don'; // Lưu ý: Đảm bảo tên bảng trong DB đúng là 'hoa_don' (số ít) hay 'hoa_dons' (số nhiều)

    // Các trường được phép gán hàng loạt
    protected $fillable = [
        'dat_ban_id',
        'voucher_id',
        'tong_tien',
        'tien_giam',
        'phu_thu',
        'da_thanh_toan',
        'phuong_thuc_tt',
        'ma_hoa_don',
        'trang_thai', // <--- ĐÃ THÊM DÒNG QUAN TRỌNG NÀY
    ];

    /**
     * Quan hệ: Một hoá đơn thuộc về một Đặt Bàn
     */
    public function datBan()
    {
        return $this->belongsTo(DatBan::class, 'dat_ban_id');
    }

    /**
     * Quan hệ: Một hoá đơn thuộc về một Voucher (có thể là null)
     */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }

    /**
     * Quan hệ: Một hóa đơn có một chi tiết hóa đơn
     */
    public function chiTietHoaDon()
    {
        return $this->hasOne(ChiTietHoaDon::class, 'hoa_don_id');
    }

    /**
     * Tính tiền phải thanh toán cuối cùng
     */
    public function tinhDaThanhToan(): float
    {
        $tongTien = $this->tong_tien ?? 0;
        $tienGiam = $this->tien_giam ?? 0;
        $phuThu = $this->phu_thu ?? 0;
        $tienCoc = $this->datBan->tien_coc ?? 0;

        $phaiThanhToan = $tongTien - $tienGiam - $tienCoc + $phuThu;

        return max(0, $phaiThanhToan);
    }

    /**
     * Tính tiền trả lại cho khách
     */
    public function tienTraLai(float $tienKhachDua): float
    {
        return max(0, $tienKhachDua - $this->tinhDaThanhToan());
    }
}
