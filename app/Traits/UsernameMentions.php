<?php

namespace App\Traits;

use Auth;
use App\User;
use App\Notifications\UsernameMentioned;

trait UsernameMentions
{
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
                $user->notify(new UsernameMentioned(Auth::user(), $submission));
            }
        }
    }
}
