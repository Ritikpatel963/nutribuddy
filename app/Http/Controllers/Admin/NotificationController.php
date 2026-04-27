<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = \Illuminate\Notifications\DatabaseNotification::orderBy('created_at', 'desc')->get();
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = \Illuminate\Notifications\DatabaseNotification::findOrFail($id);
        $notification->markAsRead();
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy($id)
    {
        $notification = \Illuminate\Notifications\DatabaseNotification::findOrFail($id);
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }
}
