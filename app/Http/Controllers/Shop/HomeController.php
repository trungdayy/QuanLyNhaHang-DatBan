<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ComboBuffet;
use App\Models\DanhMuc;
use App\Models\MonAn;
use App\Models\BanAn;
use App\Models\KhuVuc;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy danh sách món ăn MỚI NHẤT (để chạy Slider "Món mới ra lò")
        // Phần này thêm vào để phục vụ giao diện Quán Nhậu Tự Do
        $newDishes = MonAn::where('trang_thai', 'con')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // MỚI (Thêm with('monAn') để lấy danh sách món):
        $combos = ComboBuffet::with('monAn') // <--- QUAN TRỌNG NHẤT
                             ->where('trang_thai', 'dang_ban')
                             ->orderByDesc('id')
                             ->limit(6)
                             ->get();

        // 3. Lấy danh sách khu vực (Để hiển thị phần Hệ thống cơ sở)
        $khuVucs = KhuVuc::all();

        // 4. Dữ liệu cho form đặt bàn (Popup)
        $banAns = BanAn::whereNotIn('trang_thai', ['dang_phuc_vu', 'da_dat', 'khong_su_dung'])->get();
        
        // 5. (Tùy chọn) Lấy danh mục nếu bạn muốn hiển thị trang thực đơn riêng
        $danhMucs = DanhMuc::where('hien_thi', 1)->get();

        return view('restaurants.home', compact(
            'newDishes', // Biến mới thêm
            'combos', 
            'khuVucs', 
            'banAns',
            'danhMucs'
        ));
    }
    
    // Hàm xử lý form liên hệ (để tránh lỗi route)
    public function contact(Request $request)
    {
        return back()->with('success', 'Cảm ơn bạn đã liên hệ!');
    }
}