<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{

    public function __construct()
    {
        // Nếu đã login thì không cho vào lại trang login
        $this->middleware('guest')->except('logout');
    }
    public function showLoginForm()
    {
        return view('auth.login'); // tạo file resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(
            [
                'email' => 'required|email|exists:users,email',
                'password' => 'required'
            ],
            [
                'email.required' => "Email khong duoc de trong",
                'email.email' => "dinh dang email khong dung",
                'email.exists' => "email khong ton tai",
                'password.required' => "password khong duoc de trong"
            ]
        );

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.home'));
        }

        return back()->withErrors([
            'email' => 'Tài khoản hoặc mật khẩu không đúng.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form');
    }
}
