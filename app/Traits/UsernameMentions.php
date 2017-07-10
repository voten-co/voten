<?php

namespace App\Traits;

use App\Notifications\UsernameMentioned;
use App\User;
use Auth;

trait UsernameMentions
{
    /**
     * Handles all the mentions in the comment. (sends notifications to mentioned usernames).
     *
     * @param \App\Comment    $comment
     * @param \App\Submission $submission
     */
    protected function handleMentions($comment, $submission)
    {
        if (!preg_match_all('/\s@([A-Za-z0-9\._]+)/', $comment->body, $mentionedUsernames)) {
            return;
        }

        foreach ($mentionedUsernames[1] as $username) {
            if ($user = User::whereUsername($username)->first()) {
                $user->notify(new UsernameMentioned(Auth::user(), $submission));
            }
        }
    }
}
