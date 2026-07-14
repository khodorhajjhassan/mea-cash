<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = auth()->user()->notifications()->latest()->limit(6)->get();
        $locale = app()->getLocale();

        return response()->json([
            'unread_count' => auth()->user()->unreadNotifications()->count(),
            'notifications' => $notifications->map(fn ($notification) => [
                'id' => $notification->id,
                'type' => $notification->data['type'] ?? __('Notification'),
                'message' => $notification->data['message'] ?? '',
                'icon' => $notification->data['icon'] ?? 'notifications',
                'read_at' => $notification->read_at?->toIso8601String(),
                'created_at_human' => $notification->created_at->diffForHumans(),
                'read_url' => route('store.notifications.read', ['id' => $notification->id, 'locale' => $locale]),
            ])->values(),
        ]);
    }

    public function read(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['link'] ?? route('store.dashboard'));
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', __('Notifications marked as read.'));
    }
}
