<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

class NotificationsController extends Controller
{
    /**
    * returns unread notifications of Auth user
    *
    * @return \Illuminate\Support\Collection
    */
    public function unreadIndex()
    {
        return Auth::user()->unreadNotifications;
    }


    /**
    * paginates the last notifications of Auth user
    *
    * @param Illuminate\Http\Request $request
    * @return \Illuminate\Support\Collection
    */
    public function readIndex(Request $request)
    {
        return Auth::user()->notifications()
            ->where('read_at', '!=', null)
            ->simplePaginate(20);
    }


    /**
    * marks all Auth user's notifications as read
    *
    * @return (string) status
    */
    public function markAsRead()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => Carbon::now()]);

        return 'All notifications have been marked as read.';
    }
}
