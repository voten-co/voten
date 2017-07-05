<?php

namespace App\Listeners;

use App\Events\CommentCreated;
use App\Events\CommentWasCreated;
use App\Notifications\CommentReplied;
use App\Notifications\SubmissionReplied;
use App\Notifications\UsernameMentioned;
use App\Permissions;
use App\Traits\CachableCategory;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use App\User;

class NewComment
{
    use CachableUser, CachableCategory, CachableSubmission, Permissions;

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

        // update submission
        $event->submission->update([
            'comments_number' => ($event->submission->comments_number + 1),
        ]);
        $this->putSubmissionInTheCache($event->submission);

        // if the commenter is banned from submitting to this cateogry (or "everywhere") we
        // soft-delete the comment without letting him know. This should keep spammers
        // busy over nothing.
        if ($this->isUserBanned($event->author->id, $event->submission->category_name)) {
            $event->comment->delete();

            return;
        }

        // broadcast the comment to the people online in the conversation
        event(new CommentCreated($event->comment));

        $this->handleMentions($event->comment, $event->submission);

        if (isset($event->parentComment) && !$this->mustBeOwner($event->parentComment)) {
            $event->parentComment->notifiable->notify(new CommentReplied($event->submission, $event->comment));
        } elseif (!$this->mustBeOwner($event->submission)) {
            $event->submission->notifiable->notify(new SubmissionReplied($event->submission, $event->comment));
        }
    }

    /**
     * Hanldes all the mentions in the comment. (sends notifications to mentioned usernames).
     *
     * @param \App\Comment    $comment
     * @param \App\Submission $submission
     */
    protected function handleMentions($comment, $submission)
    {
        if (!preg_match_all('/@([A-Za-z0-9\._]+)/', $comment->body, $mentionedUsernames)) {
            return;
        }

        foreach ($mentionedUsernames[1] as $username) {
            if ($user = User::whereUsername($username)->first()) {
                $user->notify(new UsernameMentioned(\Auth::user(), $submission));
            }
        }
    }
}
