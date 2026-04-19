<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
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
