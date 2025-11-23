<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ComboBuffet;
use Illuminate\Http\Request;

class ComboClientController extends Controller
{
    /** Danh sách combo */
    public function index()
    {
        $combos = ComboBuffet::where('trang_thai', 'dang_ban')
            ->orderByDesc('created_at')
            ->get();

        return view('restaurants.combos.index', compact('combos'));
    }

    /** Chi tiết combo */
    public function show($id)
    {
        $combo = ComboBuffet::with(['danhSachMon'])
            ->where('id', $id)
            ->where('trang_thai', 'dang_ban')
            ->firstOrFail();
        return view('restaurants.combos.show', compact('combo'));
    }


}
