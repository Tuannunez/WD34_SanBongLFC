<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function create()
    {
        $users = User::where('role', '!=', 'admin')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient' => 'required|in:all,user',
            'user_id' => 'nullable|exists:users,id',
            'type' => 'required|in:system,promotion,booking,payment',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $recipient = $request->input('recipient');
        $title = $request->input('title');
        $content = $request->input('content');
        $type = $request->input('type');
        $now = now();
        $notifications = [];

        if ($recipient === 'all') {
            $users = User::where('role', '!=', 'admin')
                ->where('status', true)
                ->get();

            foreach ($users as $user) {
                $notifications[] = [
                    'user_id' => $user->id,
                    'title' => $title,
                    'content' => $content,
                    'type' => $type,
                    'is_read' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        } else {
            $user = User::find($request->input('user_id'));

            if (! $user || $user->role === 'admin') {
                return redirect()->back()->withErrors(['user_id' => 'Người nhận không hợp lệ.']);
            }

            $notifications[] = [
                'user_id' => $user->id,
                'title' => $title,
                'content' => $content,
                'type' => $type,
                'is_read' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($notifications)) {
            DB::table('notifications')->insert($notifications);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Thông báo đã được gửi thành công.');
    }
}
