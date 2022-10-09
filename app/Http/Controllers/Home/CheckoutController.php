<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

class CheckoutController extends Controller
{
    public function show()
    {
//        $products = json_decode(Cookie::get('7Gallery_cart'), true);
        $products = !is_null(Cookie::get('7Gallery_cart')) ? json_decode(Cookie::get('7Gallery_cart'), true) : [];
        $productsTotalPrice = array_sum(array_column($products, 'price'));
        return view('frontend.checkout.show', compact('products', 'productsTotalPrice'));
    }
}
