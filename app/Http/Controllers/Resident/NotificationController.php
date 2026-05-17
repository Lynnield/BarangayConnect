<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        return view('resident.notifications.index', compact('notifications'));
    }

    public function markRead(DatabaseNotification $notification)
    {
        abort_if($notification->notifiable_id !== auth()->id(), 403);
        $notification->markAsRead();

        return back();
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(DatabaseNotification $notification)
    {
        abort_if($notification->notifiable_id !== auth()->id(), 403);
        $notification->delete();

        return back()->with('success', 'Notification removed.');
    }
}
