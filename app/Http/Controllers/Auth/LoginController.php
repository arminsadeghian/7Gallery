<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginView()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();

        $email = $validatedData['email'];
        $password = $validatedData['password'];
        $remember = $validatedData['remember'];

//        $credentials = $request->only('email', 'password');

        if (Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            return redirect('admin/dashboard');
        }

        return back()->with('failed', 'کاربری با این مشخصات وجود ندارد');
    }
}
