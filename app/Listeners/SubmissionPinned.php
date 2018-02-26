<?php

namespace App\Listeners;

use App\Activity;
use App\Events\SubmissionWasPinned;
use Illuminate\Auth\Events\Registered;

class SubmissionPinned
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
     * @param SubmissionWasPinned $event
     *
     * @return void
     */
    public function handle(SubmissionWasPinned $event)
    {
        Activity::create([
            'subject_id'   => $event->submission->id,
            'ip_address'   => getRequestIpAddress(),
            'user_agent'   => getRequestUserAgent(),
            'country'      => getRequestCountry(),
            'subject_type' => 'App\Submission',
            'name'         => 'pinned_submission',
            'user_id'      => $event->user_id,
        ]);
    }
}
