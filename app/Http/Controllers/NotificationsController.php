<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Auth;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * returns unread notifications of Auth user.
     *
     * @return NotificationResource
     */
    public function unreadIndex()
    {
        return NotificationResource::collection(Auth::user()->unreadNotifications);
    }

    /**
     * paginates the last notifications of Auth user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return NotificationResource
     */
    public function readIndex(Request $request)
    {
        return NotificationResource::collection(
            Auth::user()->notifications()
                ->where('read_at', '!=', null)
                ->simplePaginate(20)
        );
    }

    /**
     * marks all Auth user's notifications as read.
     *
     * @return (string) status
     */
    public function markAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        return res(200, 'All notifications have been marked as read.');
    }
}
