<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = DB::table('users')
            ->where('id', Auth::id())
            ->first();

        $totalBookings = Schema::hasTable('bookings')
            ? DB::table('bookings')->where('user_id', Auth::id())->count()
            : 0;

        $pendingBookings = Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'status')
            ? DB::table('bookings')->where('user_id', Auth::id())->where('status', 'pending')->count()
            : 0;

        $confirmedBookings = Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'status')
            ? DB::table('bookings')->where('user_id', Auth::id())->where('status', 'confirmed')->count()
            : 0;

        $completedBookings = Schema::hasTable('bookings') && Schema::hasColumn('bookings', 'status')
            ? DB::table('bookings')->where('user_id', Auth::id())->where('status', 'completed')->count()
            : 0;

        $totalSpent = 0;

        if (Schema::hasTable('bookings')) {
            $moneyColumn = null;

            foreach (['total_price', 'total_amount', 'total', 'amount'] as $column) {
                if (Schema::hasColumn('bookings', $column)) {
                    $moneyColumn = $column;
                    break;
                }
            }

            if ($moneyColumn) {
                $totalSpent = DB::table('bookings')
                    ->where('user_id', Auth::id())
                    ->sum($moneyColumn);
            }
        }

        return view('user.profile.index', compact(
            'user',
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'completedBookings',
            'totalSpent'
        ));
    }

    public function update(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
        ]);

        DB::table('users')
            ->where('id', $userId)
            ->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('user.profile.index')
            ->with('success', 'Cập nhật hồ sơ cá nhân thành công.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        $user = DB::table('users')
            ->where('id', Auth::id())
            ->first();

        if (!$user || !Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors([
                    'current_password' => 'Mật khẩu hiện tại không đúng.',
                ])
                ->withInput();
        }

        $data = [
            'password' => Hash::make($request->password),
        ];

        if (Schema::hasColumn('users', 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::table('users')
            ->where('id', Auth::id())
            ->update($data);

        return redirect()
            ->route('user.profile.index')
            ->with('success', 'Đổi mật khẩu thành công.');
    }
}