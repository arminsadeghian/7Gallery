<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductsController extends Controller
{
    public function show(int $productId)
    {
        $product = Product::findOrFail($productId);
        return view('frontend.products.show', compact('product'));
    }
}
