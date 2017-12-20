<?php

namespace App\Listeners;

use App\Events\CommentWasDeleted;
use App\Report;
use App\Traits\CachableChannel;
use App\Traits\CachableComment;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;

class DestroyedComment
{
    use CachableUser, CachableChannel, CachableComment, CachableSubmission;

    /**
     * Handle the event.
     *
     * @param CommentWasDeleted $event
     *
     * @return void
     */
    public function handle(CommentWasDeleted $event)
    {
        $this->updateChannelCommentsCount($event->comment->channel_id, -1);

        $event->submission->update([
            'comments_number' => ($event->submission->comments_number - 1),
        ]);

        $this->putSubmissionInTheCache($event->submission);

        if ($event->deletedByAuthor) {
            $this->deletedByAuthor($event);

            return;
        }

        $this->deletedByModerator($event);
    }

    /**
     * Handle event, if record was deleted by its author.
     *
     * @param $event
     */
    protected function deletedByAuthor($event)
    {
        $this->removeCommentFromCache($event->comment);
        $this->updateUserCommentsCount($event->comment->user_id, -1);

        Report::where([
            'reportable_id'   => $event->comment->id,
            'reportable_type' => 'App\Comment',
        ])->forceDelete();
    }

    /**
     * Handle event, if record was deleted by a moderator.
     *
     * @param $event
     */
    protected function deletedByModerator($event)
    {
        Report::where([
            'reportable_id'   => $event->comment->id,
            'reportable_type' => 'App\Comment',
        ])->delete();
    }
}
