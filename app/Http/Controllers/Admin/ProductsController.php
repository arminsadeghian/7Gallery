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

        $admin = User::where('email', 'armin@gmail.com')->first();

        $createdProduct = Product::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
            'price' => $validatedData['price'],
            'owner_id' => $admin->id,
        ]);

        return $this->uploadImage($createdProduct, $validatedData);

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

        return $this->uploadImage($product, $validatedData);

    }

    private function uploadImage($createdProduct, $validatedData)
    {
        try {
            $basePath = 'products/' . $createdProduct->id . '/';
            $sourceImageFullPath = null;
            $data = [];

            if (isset($validatedData['source_url'])) {
                $sourceImageFullPath = $basePath . 'source_url_' . ImageUploader::fileNameToHash($validatedData['source_url']);
                ImageUploader::upload($validatedData['source_url'], $sourceImageFullPath);
                $data += ['source_url' => $sourceImageFullPath];
            }

            if (isset($validatedData['thumbnail_url'])) {
                $fullPath = $basePath . 'thumbnail_url' . '_' . ImageUploader::fileNameToHash($validatedData['thumbnail_url']);
                ImageUploader::upload($validatedData['thumbnail_url'], $fullPath, 'public_storage');
                $data += ['thumbnail_url' => $fullPath];
            }

            if (isset($validatedData['demo_url'])) {
                $fullPath = $basePath . 'demo_url' . '_' . ImageUploader::fileNameToHash($validatedData['demo_url']);
                ImageUploader::upload($validatedData['demo_url'], $fullPath, 'public_storage');
                $data += ['demo_url' => $fullPath];
            }

            $updatedProduct = $createdProduct->update($data);

            if (!$updatedProduct) {
                throw new FileNotUploadException('تصاویر آپلود نشدند!');
            }

            return back()->with('success', 'محصول آپدیت شد');

        } catch (FileNotUploadException $e) {
            return back()->with('failed', $e->getMessage());
        }
    }

}
