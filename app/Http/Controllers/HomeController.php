<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    // Home page with slider
    public function index()
    {
        $sliderProducts = Product::latest()->take(5)->get(); // example for slider
        return view('frontend.home', compact('sliderProducts'));
    }

    // Shop page
    public function shop()
    {
        $products = Product::paginate(12); // paginated products
        return view('frontend.shop', compact('products'));
    }

    // Cart page
    public function cart()
    {
        return view('frontend.cart');
    }

    public function checkout()
    {
        return view('frontend.checkout');
    }

    public function updateCart(Request $request)
    {
        // your cart update logic
    }

    public function removeCart(Request $request)
    {
        // your cart remove logic
    }
}
