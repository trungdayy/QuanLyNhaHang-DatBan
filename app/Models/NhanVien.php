<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Kế thừa cho mục đích xác thực
use Illuminate\Notifications\Notifiable;

class NhanVien extends Authenticatable // Kế thừa Authenticatable
{
    use HasFactory, Notifiable; // Bỏ HasApiTokens để tránh lỗi

    protected $table = 'nhan_vien';

    protected $fillable = [
        'ho_ten',
        'sdt',
        'email',
        'mat_khau',
        'vai_tro',
        'trang_thai',
        'hinh_anh',
    ];

    // Các thuộc tính cần ẩn (đã bao gồm remember_token chuẩn Laravel)
    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    // Khai báo cột mật khẩu tên là 'mat_khau'
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    // ============================================================
    // CÁC HÀM LOGIC CỦA BẠN (GIỮ NGUYÊN)
    // ============================================================

    /**
     * Quan hệ: Một nhân viên có thể có nhiều đơn đặt bàn (phục vụ)
     */
    public function datBans()
    {
        // Lưu ý: Đảm bảo bạn đã có model DatBan
        return $this->hasMany(DatBan::class, 'nhan_vien_id', 'id');
    }

    /**
     * Kiểm tra nhân viên có phải quản lý không
     */
    public function isQuanLy()
    {
        return $this->vai_tro === 'quan_ly';
    }

    /**
     * Kiểm tra nhân viên có phải bếp không
     */
    public function isBep()
    {
        return $this->vai_tro === 'bep';
    }

    /**
     * Kiểm tra nhân viên có phải phục vụ/lễ tân không
     */
    public function isPhucVu()
    {
        return $this->vai_tro === 'phuc_vu' || $this->vai_tro === 'le_tan'; // Thêm check 'le_tan' để bao quát
    }

    /**
     * Lấy trạng thái dạng chữ theo giá trị số
     */
    public function getTrangThaiText()
    {
        switch ($this->trang_thai) {
            case 0:
                return 'Nghỉ';
            case 1:
                return 'Đang làm';
            case 2:
                return 'Khóa';
            default:
                return 'Không xác định';
        }
    }

    /**
     * Kiểm tra trạng thái nhân viên (Đang làm)
     */
    public function isDangLam()
    {
        return $this->trang_thai === 1;
    }

    public function isNghi()
    {
        return $this->trang_thai === 0;
    }

    public function isKhoa()
    {
        return $this->trang_thai === 2;
    }
}