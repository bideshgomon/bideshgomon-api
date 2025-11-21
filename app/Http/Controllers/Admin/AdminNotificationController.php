<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $recent = UserNotification::with('user')
            ->latest()
            ->limit(50)
            ->get();
        $stats = [
            'total' => UserNotification::count(),
            'unread' => UserNotification::whereNull('read_at')->count(),
            'critical' => UserNotification::where('priority', 'critical')->count(),
        ];

        return Inertia::render('Admin/Notifications/Index', [
            'recent' => $recent,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request, NotificationService $service)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'body' => 'required|string|max:2000',
            'priority' => 'nullable|in:normal,high,critical',
            'scope' => 'nullable|in:all,user_ids',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $priority = $request->priority ?? 'normal';
        $scope = $request->scope ?? 'all';

        if ($scope === 'all') {
            $ids = User::pluck('id')->all();
            $count = $service->sendBulk($ids, $request->title, $request->body, [
                'priority' => $priority,
                'type' => 'admin_broadcast',
            ]);
        } else {
            $ids = $request->user_ids ?? [];
            $count = $service->sendBulk($ids, $request->title, $request->body, [
                'priority' => $priority,
                'type' => 'admin_targeted',
            ]);
        }

        return back()->with('success', "Broadcast sent to {$count} users");
    }
}
