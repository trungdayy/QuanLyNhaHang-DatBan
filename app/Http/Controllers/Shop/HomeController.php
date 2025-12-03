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
    /**
     * 1. TRANG CHỦ (HOMEPAGE)
     * Hiển thị Slider, Món mới, Combo và Modal đặt bàn.
     */
    public function index()
    {
        // Lấy 10 món ăn mới nhất
        $newDishes = MonAn::where('trang_thai', 'con')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Lấy TẤT CẢ combo đang mở bán để chia Tab đầy đủ
        // Sắp xếp theo giá tăng dần
        // QUAN TRỌNG: Thêm with('danhSachMon') để lấy kèm món ăn cho Popup
        $combos = ComboBuffet::with('danhSachMon') 
            ->where('trang_thai', 'dang_ban')
            ->orderBy('gia_co_ban', 'asc') 
            ->get();

        // Lấy danh sách khu vực (cho bộ lọc hoặc footer)
        $khuVucs = KhuVuc::all();

        // Lấy danh sách bàn khả dụng cho Modal đặt bàn (Loại trừ bàn bận/hỏng)
        $banAns = BanAn::whereNotIn('trang_thai', ['dang_phuc_vu', 'da_dat', 'khong_su_dung'])->get();
        
        // Lấy danh mục hiển thị
        $danhMucs = DanhMuc::where('hien_thi', 1)->get();

        return view('restaurants.home', compact(
            'newDishes', 
            'combos', 
            'khuVucs', 
            'banAns',
            'danhMucs'
        ));
    }

    /* =====================================================================================================
       CÁC TRANG NỘI DUNG (PAGES)
       Khu vực xử lý các trang tĩnh: Giới thiệu, Dịch vụ, Team...
    ====================================================================================================== */

    /* ==========================================================
       Trang giới thiệu (About Us)
    ========================================================== */
    public function about()
    {
        return view('restaurants.about');
    }

    /* ==========================================================
       Trang dịch vụ (Services)
    ========================================================== */
    public function service()
    {
        return view('restaurants.service');
    }

    /* ==========================================================
       Trang đội ngũ nhà hàng (Team)
    ========================================================== */
    public function team()
    {
        return view('restaurants.team');
    }

    /* ==========================================================
       Trang đánh giá khách hàng (Testimonial)
    ========================================================== */
    public function testimonial()
    {
        return view('restaurants.testimonial');
    }

    /* ==========================================================
       Thực đơn menu
    ========================================================== */
    public function menu()
    {
        $danhMucs = DanhMuc::where('hien_thi', 1)->with('monAn')->get();
        return view('restaurants.menu', compact('danhMucs'));
    }

    /* ==========================================================
       CHỨC NĂNG LIÊN HỆ (CONTACT)
    ========================================================== */

    /**
     * Hiển thị Form Liên Hệ (Method: GET)
     */
    public function contact()
    {
        return view('restaurants.contact');
    }
    
    /**
     * Xử lý gửi Form Liên Hệ (Method: POST)
     */
    public function sendContact(Request $request)
    {
        // TODO: Thêm logic validate và lưu vào CSDL tại đây
        return back()->with('success', 'Cảm ơn bạn đã liên hệ!');
    }
}