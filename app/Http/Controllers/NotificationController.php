<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);
        $unreadCount = UserNotification::where('user_id', Auth::id())->whereNull('read_at')->count();

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function dropdown()
    {
        $notifications = UserNotification::where('user_id', Auth::id())
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'type' => $notification->type,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'action_url' => $notification->action_url,
                    'is_read' => $notification->read_at !== null,
                    'created_at' => $notification->created_at,
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => UserNotification::where('user_id', Auth::id())->whereNull('read_at')->count(),
        ]);
    }

    public function markRead(UserNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) abort(403);
        $notification->markRead();
        return back()->with('success', 'Notification marked as read');
    }

    public function markAllRead(NotificationService $service)
    {
        $service->markAllRead(Auth::id());
        return back()->with('success', 'All notifications marked as read');
    }

    public function destroy(UserNotification $notification)
    {
        if ($notification->user_id !== Auth::id()) abort(403);
        $notification->delete();
        return back()->with('success', 'Notification deleted');
    }

    public function unreadCount()
    {
        return response()->json([
            'unread_count' => UserNotification::where('user_id', Auth::id())->whereNull('read_at')->count(),
        ]);
    }
}
