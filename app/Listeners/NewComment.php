<?php

namespace App\Listeners;

use App\Events\CommentWasCreated;
use App\Notifications\CommentReplied;
use App\Notifications\SubmissionReplied;
use App\Permissions;
use App\Traits\CachableChannel;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use App\Traits\UsernameMentions;

class NewComment
{
    use CachableUser, CachableChannel, CachableSubmission, Permissions, UsernameMentions;

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
        $this->updateChannelCommentsCount($event->comment->channel_id);

        // update submission
        $event->submission->update([
            'comments_number' => ($event->submission->comments_number + 1),
        ]);
        $this->putSubmissionInTheCache($event->submission);

        // if the commenter is banned from submitting to this cateogry (or "everywhere") we
        // soft-delete the comment without letting him know. This should keep spammers
        // busy over nothing.
        if ($this->isUserBannedFromChannel($event->author->id, $event->submission->channel_name)) {
            $event->comment->delete();

            return;
        }

        if (isset($event->parentComment) && !$this->mustBeOwner($event->parentComment)) {
            $event->parentComment->notifiable->notify(new CommentReplied($event->submission, $event->comment));
            $notifiableUser = $event->parentComment->notifiable;
        } elseif (!$this->mustBeOwner($event->submission)) {
            $event->submission->notifiable->notify(new SubmissionReplied($event->submission, $event->comment));
            $notifiableUser = $event->submission->notifiable;
        } else {
            $notifiableUser = null;
        }

        $this->handleMentions($event->comment, $event->submission, $notifiableUser);
    }
}
