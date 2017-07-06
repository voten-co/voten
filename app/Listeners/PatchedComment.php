<?php

namespace App\Listeners;

use App\Events\CommentWasPatched;
use App\Traits\UsernameMentions;

class PatchedComment
{
    use UsernameMentions;

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
     * @param CommentWasPatched $event
     *
     * @return void
     */
    public function handle(CommentWasPatched $event)
    {
        $this->handleMentions($event->comment, $event->submission);
    }
}
