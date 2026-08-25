<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // Vẫn giữ route /login nếu bạn cần dùng riêng
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

    $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL)
        ? 'email'
        : 'phone';

    $credentials = [
        $fieldType => $loginField,
        'password' => $password,
    ];

    if (!Auth::attempt($credentials)) {

        return response()->json([
            'success' => false,
            'message' => 'Email/SĐT hoặc mật khẩu không đúng.'
        ], 422);
    }

    $request->session()->regenerate();

    $user = Auth::user();

    if ($user->status === false) {

        Auth::logout();

        return response()->json([
            'success' => false,
            'message' => 'Tài khoản của bạn đã bị khóa.'
        ], 403);
    }

    return response()->json([
        'success' => true,
        'message' => 'Đăng nhập thành công.',
        'redirect' => $user->role === 'admin'
            ? route('admin.dashboard')
            : url('/')
    ]);
}
}