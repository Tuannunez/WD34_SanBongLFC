<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                return redirect('/');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $roles = Role::orderBy('id')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $data['status'] = $request->has('status');

        Role::create($data);

        return redirect()->route('roles.index')->with('success', 'Vai trò đã được tạo.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $data['status'] = $request->has('status');

        $role->update($data);

        return redirect()->route('roles.index')->with('success', 'Vai trò đã được cập nhật.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Vai trò đã được xóa.');
    }
}
