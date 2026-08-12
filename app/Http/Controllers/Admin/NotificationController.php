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
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Notification marked as read.']);
        }
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        \Illuminate\Notifications\DatabaseNotification::whereNull('read_at')->update(['read_at' => now()]);
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'All notifications marked as read.']);
        }
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy($id)
    {
        $notification = \Illuminate\Notifications\DatabaseNotification::findOrFail($id);
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }
}
