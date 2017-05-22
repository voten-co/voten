<?php

namespace App\Listeners;

use App\Events\ReportWasCreated;
use App\Traits\CachableCategory;
use App\Notifications\CommentReported;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\SubmissionReported;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewReport
{
    use CachableCategory;

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
     * @param  ReportWasCreated  $event
     * @return void
     */
    public function handle(ReportWasCreated $event)
    {
        $category = $this->getCategoryById($event->report->category_id);

        $category_mods = $category->moderators;

        foreach ($category_mods as $user) {
            if ($event->report->reportable_type == 'App\Submission') {
                $user->notify( new SubmissionReported($category, $event->report->submission) );
            } elseif ($event->report->reportable_type == 'App\Comment') {
                $user->notify( new CommentReported($category, $event->report->comment) );
            }
        }
    }
}
