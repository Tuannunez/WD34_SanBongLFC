<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = DB::table('notifications')
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereNull('user_id');
            })
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('user.notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereNull('user_id');
            })
            ->first();

        if (!$notification) {
            abort(404);
        }

        if (!is_null($notification->user_id)) {
            DB::table('notifications')
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->update(['is_read' => true, 'updated_at' => now()]);
        }

        return view('user.notifications.show', compact('notification'));
    }
}
