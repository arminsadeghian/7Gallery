<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    public const COOKIE_EXPIRE_TIME = 60; // Minute
    public const COOKIE_NAME = '7Gallery_cart';

    public function addToCart(int $productId)
    {
        $product = Product::findOrFail($productId);

        $cart = json_decode(Cookie::get(self::COOKIE_NAME), true);
//        dd($cart);

        // if shopping cart is empty
        if (!$cart) {
            $cart = [
                $product->id => [
                    'title' => $product->title,
                    'price' => $product->price,
                    'thumbnail_url' => $product->thumbnail_url,
                ]
            ];

            Cookie::queue(self::COOKIE_NAME, json_encode($cart), self::COOKIE_EXPIRE_TIME);

            return back()->with('success', 'محصول به سبد خرید اضافه شد');
        }

        // if product exists in shopping cart
        if (isset($cart[$product->id])) {
            return back()->with('success', 'محصول به سبد خرید اضافه شد');
        }

        // add new product to shopping cart
        $cart[$product->id] = [
            'title' => $product->title,
            'price' => $product->price,
            'thumbnail_url' => $product->thumbnail_url,
        ];

        Cookie::queue(self::COOKIE_NAME, json_encode($cart), self::COOKIE_EXPIRE_TIME);

        return back()->with('success', 'محصول به سبد خرید اضافه شد');
    }

    public function removeFromCart(int $productId)
    {
        $cart = json_decode(Cookie::get(self::COOKIE_NAME), true);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        Cookie::queue(self::COOKIE_NAME, json_encode($cart), self::COOKIE_EXPIRE_TIME);

        return back()->with('success', 'محصول از سبد خرید حذف شد');
    }

}
