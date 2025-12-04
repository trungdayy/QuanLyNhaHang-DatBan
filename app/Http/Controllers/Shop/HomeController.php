<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ComboBuffet;
use App\Models\DanhMuc;
use App\Models\MonAn;
use App\Models\BanAn;
use App\Models\KhuVuc;
use Illuminate\Http\Request;
use App\Models\DanhGia;

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

        // Lấy TẤT CẢ combo đang mở bán
        $combos = ComboBuffet::with('danhSachMon') 
            ->where('trang_thai', 'dang_ban')
            ->orderBy('gia_co_ban', 'asc') 
            ->get();

        // Lấy danh sách khu vực
        $khuVucs = KhuVuc::all();

        // Lấy danh sách bàn khả dụng
        $banAns = BanAn::whereNotIn('trang_thai', ['dang_phuc_vu', 'da_dat', 'khong_su_dung'])->get();
        
        // Lấy danh mục hiển thị
        $danhMucs = DanhMuc::where('hien_thi', 1)->get();

        // [MỚI] Lấy danh sách đánh giá để hiện lên Carousel ở trang chủ
        // Chỉ lấy những đánh giá đã duyệt (hien_thi), lấy mới nhất, giới hạn khoảng 6-10 cái
        $danhGias = DanhGia::where('trang_thai', 'hien_thi')
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();

        return view('restaurants.home', compact(
            'newDishes', 
            'combos', 
            'khuVucs', 
            'banAns',
            'danhMucs',
            'danhGias' // <--- Đã thêm biến này vào view
        ));
    }

    /* ==========================================================
       CÁC TRANG NỘI DUNG (PAGES)
    ========================================================== */

    public function about()
    {
        return view('restaurants.about');
    }

    public function service()
    {
        return view('restaurants.service');
    }

    public function team()
    {
        return view('restaurants.team');
    }

    public function menu()
    {
        $danhMucs = DanhMuc::where('hien_thi', 1)->with('monAn')->get();
        return view('restaurants.menu', compact('danhMucs'));
    }

    /* ==========================================================
       CHỨC NĂNG LIÊN HỆ (CONTACT)
    ========================================================== */

    public function contact()
    {
        return view('restaurants.contact');
    }
    
    public function sendContact(Request $request)
    {
        return back()->with('success', 'Cảm ơn bạn đã liên hệ!');
    }

    /* ==========================================================
       CHỨC NĂNG ĐÁNH GIÁ (TESTIMONIAL) - Trang riêng
    ========================================================== */

    public function testimonial()
    {
        // Trang này dùng phân trang (paginate)
        $danhGias = DanhGia::where('trang_thai', 'hien_thi')
                            ->orderBy('created_at', 'desc')
                            ->paginate(9);

        return view('restaurants.testimonial', compact('danhGias'));
    }
}