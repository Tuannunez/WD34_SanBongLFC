<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    private const SOCIAL_PROVIDERS = ['google'];

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

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->status === false) {
                Auth::logout();
                return $this->loginErrorResponse($request, 'Tài khoản của bạn đã bị khóa.', 403);
            }

            $redirect = Auth::user()->role === 'admin'
                ? route('admin.dashboard')
                : route('home');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đăng nhập thành công.',
                    'redirect' => $redirect,
                ]);
            }

            return redirect()->intended($redirect);
        }

        return $this->loginErrorResponse($request, 'Email/SĐT hoặc mật khẩu không đúng.');
    }

    public function redirectToProvider(string $provider)
    {
        $this->ensureValidProvider($provider);

        if (!$this->socialProviderIsConfigured($provider)) {
            return redirect()->route('login')->withErrors([
                'login' => 'Phương thức đăng nhập này chưa được cấu hình.',
            ]);
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback(string $provider)
    {
        $this->ensureValidProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            $socialId = trim((string) $socialUser->getId());

            if ($socialId === '') {
                throw new \RuntimeException('OAuth provider returned an empty user ID.');
            }

            $providerIdColumn = $provider . '_id';
            $user = User::where($providerIdColumn, $socialId)->first();

            $email = $socialUser->getEmail();
            if (!$user && $email) {
                $user = User::where('email', strtolower(trim($email)))->first();
            }

            if ($user) {
                $user->forceFill([$providerIdColumn => $socialId])->save();
            } else {
                $email = $email ?: $provider . '_' . $socialId . '@oauth.local';
                $user = User::create([
                    'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: 'Người dùng ' . ucfirst($provider),
                    'email' => strtolower(trim($email)),
                    'password' => Hash::make(Str::random(40)),
                    $providerIdColumn => $socialId,
                ]);

                if ($socialUser->getEmail()) {
                    $user->forceFill(['email_verified_at' => now()])->save();
                }
            }

            if ($user->status === false) {
                return redirect()->route('home')->with('error', 'Tài khoản của bạn đã bị khóa.');
            }

            Auth::login($user, true);

            return redirect()->intended(route('home'));
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('home')->with('error', 'Đăng nhập bằng ' . ucfirst($provider) . ' không thành công.');
        }
    }

    private function ensureValidProvider(string $provider): void
    {
        abort_unless(in_array($provider, self::SOCIAL_PROVIDERS, true), 404);
    }

    private function socialProviderIsConfigured(string $provider): bool
    {
        $config = config("services.$provider");

        return is_array($config)
            && filled($config['client_id'] ?? null)
            && filled($config['client_secret'] ?? null)
            && filled($config['redirect'] ?? null);
    }

    private function loginErrorResponse(Request $request, string $message, int $status = 422)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        }

        throw ValidationException::withMessages(['login' => $message]);
    }
}
