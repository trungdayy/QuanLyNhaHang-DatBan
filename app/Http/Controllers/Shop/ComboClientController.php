<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ComboBuffet;
use Illuminate\Http\Request;
use App\Models\DanhMuc;

class ComboClientController extends Controller
{
    /** Danh sách combo */
    public function index()
    {
        // 1. Lấy danh sách Combo đang bán, sắp xếp theo giá tăng dần
        $combos = ComboBuffet::where('trang_thai', 'dang_ban')
            ->orderBy('gia_co_ban', 'asc')
            ->get();

        // 2. Trả về View và truyền biến $combos sang
        return view('restaurants.combos._form', compact('combos'));
    }

    public function menu()
    {
        $danhMucs = DanhMuc::with(['monAn' => function ($q) {
            $q->where('trang_thai', 'con');
        }])->get();

        return view('restaurants.menu._form', compact('danhMucs'));
    }

    /** Chi tiết combo */
    public function show($id)
    {
        $combo = ComboBuffet::with([
            'monAn',                 // <--- Sửa danhSachMon thành monAn
            'monAn.thuVienAnh',      // <--- Sửa tiếp ở đây
            'monAn.danhMuc',
        ])
            ->where('id', $id)
            ->where('trang_thai', 'dang_ban')
            ->firstOrFail();

        return view('restaurants.combos.show', compact('combo'));
    }
}
