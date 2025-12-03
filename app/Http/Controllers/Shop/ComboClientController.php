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
        $danhMucs = DanhMuc::with(['monAn' => function ($q) {
            $q->where('trang_thai', 'con');
        }])->get();

        return view('restaurants.combos._form', compact('danhMucs'));
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
            'danhSachMon',
            'danhSachMon.thuVienAnh',   // ← Load thư viện ảnh cho món
            'danhSachMon.danhMuc'        // ← Load danh mục món
        ])
            ->where('id', $id)
            ->where('trang_thai', 'dang_ban')
            ->firstOrFail();

        return view('restaurants.combos.show', compact('combo'));
    }
}
