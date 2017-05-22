<?php

namespace App\Listeners;

use App\Report;
use App\Traits\CachableUser;
use App\Traits\CachableCategory;
use App\Traits\CachableComment;
use App\Events\CommentWasDeleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DestroyedComment
{
	use CachableUser, CachableCategory, CachableComment;

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
     * @param  CommentWasDeleted  $event
     * @return void
     */
    public function handle(CommentWasDeleted $event)
    {
        if (! $event->comment->isForceDeleting()) {
    		return;
    	}

		$this->updateUserCommentsCount($event->comment->user_id, -1);

		$this->updateCategoryCommentsCount($event->comment->category_id, -1);

		$this->removeCommentFromCache($event->comment);

        Report::where([
            'reportable_id' => $event->comment->id,
            'reportable_type' => 'App\Comment'
        ])->forceDelete();
    }
}
