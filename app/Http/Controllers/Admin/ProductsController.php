<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\FileNotUploadException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\StoreRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Utils\ImageUploader;

class ProductsController extends Controller
{
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

            $sourceImageFullPath = $basePath . 'source_url_' . ImageUploader::fileNameToHash($validatedData['source_url']);

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

}