<?php

namespace App\Listeners;

use App\Traits\CachableUser;
use App\Traits\CachableCategory;
use App\Events\CommentWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewComment
{
	use CachableUser, CachableCategory;

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
     * @param  CommentWasCreated  $event
     * @return void
     */
    public function handle(CommentWasCreated $event)
    {
    	$this->updateUserCommentsCount($event->comment->user_id);

		$this->updateCategoryCommentsCount($event->comment->category_id);
    }
}
