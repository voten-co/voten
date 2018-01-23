<?php

namespace App\Listeners;

use App\Activity;
use Illuminate\Auth\Events\Registered;

class UserRegistered
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
     * @param Registered $event
     *
     * @return void
     */
    public function handle(Registered $event)
    {
        Activity::create([
            'subject_id'   => $event->user->id,
            'ip_address'   => getRequestIpAddress(),
            'user_agent'   => getRequestUserAgent(),
            'country'      => getRequestCountry(),
            'subject_type' => 'App\User',
            'name'         => 'created_user',
            'user_id'      => $event->user->id,
        ]);
    }
}
