<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                return redirect('/');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::orderBy('id')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'slug');
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string'],
            'status' => ['nullable'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['status'] = $request->boolean('status', true);

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã được tạo thành công.');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'slug');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'string'],
            'status' => ['nullable'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['status'] = $request->boolean('status', true);

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Thông tin người dùng đã được cập nhật.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Người dùng đã bị xóa.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => !$user->status]);

        return redirect()->route('admin.users.index')->with('success', 'Trạng thái người dùng đã được cập nhật.');
    }
}
