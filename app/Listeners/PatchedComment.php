<?php

namespace App\Listeners;

use App\Events\CommentWasPatched;

class PatchedComment
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
     * @param CommentWasPatched $event
     *
     * @return void
     */
    public function handle(CommentWasPatched $event)
    {
        //
    }
}
