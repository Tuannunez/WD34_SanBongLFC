<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = DB::table('notifications')
            ->leftJoin('users', 'notifications.user_id', '=', 'users.id')
            ->select(
                'notifications.*',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->orderByDesc('notifications.created_at')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }
}
