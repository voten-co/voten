<?php

namespace App\Listeners;

use App\Photo;
use App\Report;
use App\Traits\CachableUser;
use App\Traits\CachableCategory;
use App\Traits\CachableSubmission;
use App\Events\SubmissionWasDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DestroyedSubmission
{
	use CachableUser, CachableSubmission, CachableCategory;

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
     * @param  SubmissionWasDeleted  $event
     * @return void
     */
    public function handle(SubmissionWasDeleted $event)
    {
    	if (! $event->submission->isForceDeleting()) {
    		return;
    	}

		$this->updateUserSubmissionsCount($event->submission->user_id, -1);

		$this->updateCategorySubmissionsCount($event->submission->category_id, -1);

    	$this->removeSubmissionFromCache($event->submission);

        Report::where([
            'reportable_id' => $event->submission->id,
            'reportable_type' => 'App\Submission'
        ])->forceDelete();

        if ($event->submission->type == "img") {
        	Photo::where("submission_id", $event->submission->id)->forceDelete();
        }
    }
}
