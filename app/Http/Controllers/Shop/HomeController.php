<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('layouts.restaurants.layout-shop');
    }

}
