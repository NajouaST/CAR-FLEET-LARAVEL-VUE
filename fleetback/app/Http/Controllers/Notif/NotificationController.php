<?php

namespace App\Http\Controllers\Notif;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HasQueryFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    /**
     * Get the authenticated user's notifications.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $onlyUnread = $request->query('unread', false);
        $limit = $request->query('limit', 20);

        $query = $onlyUnread ? $user->unreadNotifications() : $user->notifications();

        return response()->json($query->take($limit)->get());
    }

    /**
     * Get count of unread notifications.
     */
    public function countUnread(Request $request)
    {
        $count = $request->user()->unreadNotifications()->count();
        return response()->json(['unread_count' => $count]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id, Request $request)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Delete notifications older than 1 month.
     */
    public function deleteOld(Request $request)
    {
        $deletedCount = $request->user()->notifications()
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->delete();

        return response()->json([
            'message' => "$deletedCount old notifications deleted"
        ]);
    }
}
