<?php

namespace App\Http\Controllers\Home;

use App\Exceptions\FilterClassNotFoundException;
use App\Exceptions\FilterMethodNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    private const BASE_FILTERS_NAMESPACE = "App\Filters\\";

    public function index(Request $request)
    {
        $products = null;

        if (isset($request->filter, $request->action)) {
            $products = $this->findFilter($request?->filter, $request?->action);
        } else if ($request->has('search')) {
            $products = Product::where('title', 'LIKE', '%' . $request->input('search') . '%')->get();
        } else {
            $products = Product::all();
        }

        $categories = Category::all();

        return view('frontend.products.all', compact('products', 'categories'));
    }

    public function show(int $productId)
    {
        $product = Product::findOrFail($productId);
        $relatedProducts = Product::where('category_id', $product->category_id)->take(4)->get();
        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * @throws FilterClassNotFoundException
     * @throws FilterMethodNotFoundException
     */
    private function findFilter(string|null $className, string|null $methodName)
    {
        $className = self::BASE_FILTERS_NAMESPACE . (ucfirst($className) . 'Filter');

        if (!class_exists($className)) {
            throw new FilterClassNotFoundException();
        }

        $object = new $className;

        if (!method_exists($object, $methodName)) {
            throw new FilterMethodNotFoundException();
        }

        return $object->{$methodName}();
    }

}
