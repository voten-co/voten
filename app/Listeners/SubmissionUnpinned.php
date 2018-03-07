<?php

namespace App\Listeners;

use App\Activity;
use App\Events\SubmissionWasUnpinned;
use Illuminate\Auth\Events\Registered;

class SubmissionUnpinned
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
     * @param SubmissionWasUnpinned $event
     *
     * @return void
     */
    public function handle(SubmissionWasUnpinned $event)
    {
        Activity::create([
            'subject_id'   => $event->submission->id,
            'ip_address'   => getRequestIpAddress(),
            'user_agent'   => getRequestUserAgent(),
            'country'      => getRequestCountry(),
            'subject_type' => 'App\Submission',
            'name'         => 'unpinned_submission',
            'user_id'      => $event->user_id,
        ]);
    }
}
