<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Auth;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * Index notifications of Auth user.
     *
     * @return NotificationResource
     */
    public function index(Request $request)
    {
        if ($request->filter == 'seen') {
            return NotificationResource::collection(
                Auth::user()->notifications()
                    ->where('read_at', '!=', null)
                    ->simplePaginate(20)
            );
        } elseif ($request->filter == 'unseen') {
            return NotificationResource::collection(
                Auth::user()->unreadNotifications
            );
        }

        return NotificationResource::collection(
            Auth::user()->notifications
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
