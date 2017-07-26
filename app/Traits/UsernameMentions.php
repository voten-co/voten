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
        if (!preg_match_all('/@([A-Za-z0-9\._]+)/', $comment->body, $mentionedUsernames)) {
            return;
        }

        foreach ($mentionedUsernames[1] as $key => $username) {
            // set a limit so they can't just mention the whole website! lol
            if ($key === 5) {
                return;
            }

            if ($user = User::whereUsername($username)->first()) {
                $user->notify(new UsernameMentioned(Auth::user(), $submission));
            }
        }
    }
}
