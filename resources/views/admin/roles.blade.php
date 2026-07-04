@extends('admin.layouts.app')

@section('title', 'Vai trò')
@section('page-title', 'Quản lý vai trò')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0">Danh sách các chức vụ/quyền hạn trong hệ thống</h5>
            <p class="text-muted mb-0">Đây là danh sách các chức vụ/quyền hạn trong hệ thống.</p>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Vai trò</th>
                            <th>Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Admin</td>
                            <td>Quản lý toàn bộ website</td>
                        </tr>
                        <tr>
                            <td>Nhân viên</td>
                            <td>Quản lý đơn đặt sân</td>
                        </tr>
                        <tr>
                            <td>Khách hàng</td>
                            <td>Đặt sân, xem lịch</td>
                        </tr>
                        <tr>
                            <td>Quản lý sân</td>
                            <td>Quản lý sân bóng của mình</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
