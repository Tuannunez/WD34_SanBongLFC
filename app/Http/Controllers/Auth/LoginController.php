<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $messages = [
            'login.required' => 'Vui lòng nhập email hoặc số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ];

        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], $messages);

        $loginField = trim($validated['login']);
        $password = $validated['password'];

        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $fieldType => $loginField,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->status === false) {
                Auth::logout();
                return redirect()->route('login')->withErrors(['login' => 'Tài khoản của bạn đã bị khóa.']);
            }

            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect('/');
        }

        throw ValidationException::withMessages([
            'login' => 'Email/SĐT hoặc mật khẩu không đúng.',
        ]);
    }
}
