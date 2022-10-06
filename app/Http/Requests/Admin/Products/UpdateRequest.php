<?php

namespace App\Http\Requests\Admin\Products;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|min:3|max:256',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'thumbnail_url' => 'nullable|image|mimes:png,jpeg,jpg',
            'demo_url' => 'nullable|image|mimes:png,jpeg,jpg',
            'source_url' => 'nullable|image|mimes:png,jpeg,jpg',
            'description' => 'required|string|min:10',
        ];
    }
}
