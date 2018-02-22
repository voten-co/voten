<?php

namespace App\Listeners;

use App\Activity;
use Illuminate\Auth\Events\Logout;

class UserLoggedOut
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param Logout $event
     *
     * @return void
     */
    public function handle(Logout $event)
    {
        if (!isset($event->user)) {
            return;
        }

        Activity::create([
            'subject_id'   => $event->user->id,
            'ip_address'   => getRequestIpAddress(),
            'user_agent'   => getRequestUserAgent(),
            'country'      => getRequestCountry(),
            'subject_type' => 'App\User',
            'name'         => 'logged_out_user',
            'user_id'      => $event->user->id,
        ]);
    }
}
