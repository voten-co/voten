<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Submission 
        'App\Events\SubmissionWasCreated' => [
            'App\Listeners\NewSubmission',
        ],
        'App\Events\SubmissionWasDeleted' => [
            'App\Listeners\DestroyedSubmission',
        ],

        // Comment 
        'App\Events\CommentWasCreated' => [
            'App\Listeners\NewComment',
        ],
        'App\Events\CommentWasDeleted' => [
            'App\Listeners\DestroyedComment',
        ],
        'App\Events\CommentWasPatched' => [
            'App\Listeners\PatchedComment',
        ],

        // Channel 
        'App\Events\ChannelWasUpdated' => [
            'App\Listeners\UpdatedChannel',
        ],

        // Report 
        'App\Events\ReportWasCreated' => [
            'App\Listeners\NewReport',
        ],

        'Illuminate\Auth\Events\Registered' => [
            'App\Listeners\UserRegistered',
        ],

        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\UserLoggedIn',
        ],

        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\UserLoggedOut',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
