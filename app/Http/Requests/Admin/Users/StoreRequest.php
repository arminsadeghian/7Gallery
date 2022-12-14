<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|min:3|max:128',
            'last_name' => 'required|string|min:3|max:256',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|digits:11|unique:users,mobile',
            'password' => 'required',
            'role' => 'required|in:user,admin',
        ];
    }
}
