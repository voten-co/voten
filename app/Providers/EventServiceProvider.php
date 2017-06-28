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
        // 'App\Events\SubmissionWasCreated' => [
        //     'App\Listeners\NewSubmission',
        // ],
        // 'App\Events\SubmissionWasDeleted' => [
        //     'App\Listeners\DestroyedSubmission',
        // ],

        'App\Events\CommentWasCreated' => [
            'App\Listeners\NewComment',
        ],
        'App\Events\CommentWasDeleted' => [
            'App\Listeners\DestroyedComment',
        ],
        'App\Events\CommentWasPatched' => [
            'App\Listeners\PatchedComment',
        ],

        // shouldn't this be UpdatedCategory?!
        // 'App\Events\CategoryWasUpdated' => [
        //     'App\Listeners\UpdatedComment',
        // ],

        'App\Events\ReportWasCreated' => [
            'App\Listeners\NewReport',
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
