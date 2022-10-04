<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Categories\StoreRequest;
use App\Models\Category;

class CategoriesController extends Controller
{

    public function all()
    {
        $allCategories = Category::paginate(10);
        return view('admin.categories.all', compact('allCategories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();

        $createdCategory = Category::create([
            'title' => $validatedData['title'],
            'slug' => $validatedData['slug']
        ]);

        if (!$createdCategory) {
            return back()->with('failed', 'دسته بندی ایجاد نشد!');
        }

        return back()->with('success', 'دسته بندی ایجاد شد');
    }

    public function delete(int $categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $deletedCategory = $category->delete();

        if (!$deletedCategory) {
            return back()->with('failed', 'دسته بندی حذف نشد!');
        }

        return back()->with('success', 'دسته بندی حذف شد');
    }
}
