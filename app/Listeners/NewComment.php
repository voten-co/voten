<?php

namespace App\Listeners;

use App\Events\CommentWasCreated;
use App\Traits\CachableCategory;
use App\Traits\CachableUser;

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
     * @param CommentWasCreated $event
     *
     * @return void
     */
    public function handle(CommentWasCreated $event)
    {
        $this->updateUserCommentsCount($event->comment->user_id);

        $this->updateCategoryCommentsCount($event->comment->category_id);
    }
}
