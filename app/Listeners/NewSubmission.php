<?php

namespace App\Listeners;

use App\Traits\CachableUser;
use App\Traits\CachableCategory;
use App\Traits\CachableSubmission;
use App\Events\SubmissionWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewSubmission
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
     * @param  SubmissionWasCreated  $event
     * @return void
     */
    public function handle(SubmissionWasCreated $event)
    {
    	$this->updateUserSubmissionsCount($event->submission->user_id);

		$this->updateCategorySubmissionsCount($event->submission->category_id);
    }
}
