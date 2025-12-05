<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        // Get products, categories, etc.
        return view('frontend.shop');
    }
}
