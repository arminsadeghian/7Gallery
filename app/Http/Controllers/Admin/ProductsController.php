<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\FileNotUploadException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\StoreRequest;
use App\Http\Requests\Admin\Products\UpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Utils\ImageUploader;

class ProductsController extends Controller
{
    public function all()
    {
        $products = Product::paginate(10);
        return view('admin.products.all', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();

        $admin = User::where('email', 'admin@gmail.com')->first();

        $createdProduct = Product::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
            'price' => $validatedData['price'],
            'owner_id' => $admin->id,
        ]);

        try {
            $basePath = 'products/' . $createdProduct->id . '/';

            if (isset($validatedData['source_url'])) {
                $sourceImageFullPath = $basePath . 'source_url_' . ImageUploader::fileNameToHash($validatedData['source_url']);
            }

            $images = [
                'thumbnail_url' => $validatedData['thumbnail_url'],
                'demo_url' => $validatedData['demo_url'],
            ];

            $imagesPath = ImageUploader::uploadMany($images, $basePath);

            ImageUploader::upload($validatedData['source_url'], $sourceImageFullPath);

            $updatedProduct = $createdProduct->update([
                'thumbnail_url' => $imagesPath['thumbnail_url'],
                'demo_url' => $imagesPath['demo_url'],
                'source_url' => $sourceImageFullPath,
            ]);

            if (!$updatedProduct) {
                throw new FileNotUploadException('تصاویر آپلود نشدند!');
            }

            return back()->with('success', 'محصول ایجاد شد');

        } catch (FileNotUploadException $e) {
            return back()->with('failed', $e->getMessage());
        }

    }

    public function downloadDemo(int $productId)
    {
        $product = Product::findOrFail($productId);
        return response()->download(public_path($product->demo_url));
    }

    public function downloadSource(int $productId)
    {
        $product = Product::findOrFail($productId);
        return response()->download(storage_path('app/local_storage/' . $product->source_url));
    }

    public function delete(int $productId)
    {
        $product = Product::findOrFail($productId);
        $deletedProduct = $product->delete();

        if (!$deletedProduct) {
            return back()->with('failed', 'محصول حذف نشد، لطفا دوباره امتحان کنید!');
        }

        return back()->with('success', 'محصول حذف شد');
    }

    public function edit(int $productId)
    {
        $categories = Category::all();
        $product = Product::findOrFail($productId);
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateRequest $request, int $productId)
    {
        $validatedData = $request->validated();
        $product = Product::findOrFail($productId);
        $updatedProduct = $product->update([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
            'price' => $validatedData['price'],
        ]);

    }

}
