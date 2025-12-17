<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Thêm dòng này để xử lý thời gian

class ChiTietOrder extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_order';

    protected $fillable = [
        'order_id',
        'mon_an_id',
        'so_luong',
        'ghi_chu',
        'trang_thai',
        'loai_mon', // 'combo' hoặc 'goi_them'
    ];

    // ================= RELATIONSHIPS =================

    public function orderMon()
    {
        return $this->belongsTo(OrderMon::class, 'order_id');
    }

    public function monAn()
    {
        return $this->belongsTo(MonAn::class, 'mon_an_id');
    }

    // ================= ACCESSORS (Thuộc tính ảo) =================

    /**
     * Thêm hàm này để tạo thuộc tính ảo 'deadline'
     * Logic: Thời gian deadline = Giờ đặt + Thời gian chế biến
     */
    public function getDeadlineAttribute()
    {
        // Nếu chưa có thời gian tạo, trả về null
        if (!$this->created_at) {
            return null;
        }

        // Lấy thời gian chế biến từ bảng mon_an (nếu có)
        // Nếu không tìm thấy món hoặc không set thời gian, mặc định là 15 phút
        $phutCheBien = 15; 
        
        if ($this->monAn && isset($this->monAn->thoi_gian_che_bien)) {
            $val = (int)$this->monAn->thoi_gian_che_bien;
            if ($val > 0) {
                $phutCheBien = $val;
            }
        }

        // Trả về đối tượng Carbon (thời gian) đã cộng thêm phút
        // Dùng copy() để không làm thay đổi giá trị gốc của created_at
        return $this->created_at->copy()->addMinutes($phutCheBien);
    }

    /**
     * Tính thành tiền đơn giản
     */
    public function getThanhTienAttribute()
    {
        // Nếu là món trong gói combo thì giá = 0
        if ($this->loai_mon === 'combo') {
            return 0;
        }

        // Nếu là món gọi thêm thì tính tiền
        $gia = $this->monAn->gia ?? 0;
        return $gia * ($this->so_luong ?? 0);
    }
}