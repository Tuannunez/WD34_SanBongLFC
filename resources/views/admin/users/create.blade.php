@extends('admin.layouts.app')

@section('title', 'Tạo người dùng')
@section('page-title', 'Tạo người dùng mới')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Tên</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vai trò</label>
                    <select name="role" class="form-control" required>
                        @foreach($roles as $slug => $name)
                            <option value="{{ $slug }}" {{ old('role') === $slug ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="status" class="form-check-input" id="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Kích hoạt</label>
                </div>

                <button class="btn btn-primary">Tạo</button>
            </form>
        </div>
    </div>
@endsection
