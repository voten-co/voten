<?php

namespace App\Traits;

use App\User;
use Auth;
use DB;
use Illuminate\Support\Facades\Redis;

trait CachableUser
{
    /**
     * Fetches all the cachable data for the auth user and put it in the cache.
     *
     * @return void
     */
    protected function cacheUserData($id)
    {
        $user = User::where('id', $id)->firstOrFail();

        $userData = [
            'submissionsCount' => $user->submissions()->count(),
            'commentsCount'    => $user->comments()->count(),

            'submissionXp' => $user->submission_xp,
            'commentXp'    => $user->comment_xp,

            'hiddenSubmissions' => $user->hiddenSubmissions(),
            'hiddenChannels'    => $user->hiddenChannels(),
            'subscriptions'     => $user->subscriptions->pluck('id'),

            'blockedUsers' => $user->blockedUsers(),

            'bookmarkedSubmissions' => DB::table('bookmarks')->where(['user_id' => $user->id, 'bookmarkable_type' => 'App\Submission'])->pluck('bookmarkable_id'),
            'bookmarkedComments'    => DB::table('bookmarks')->where(['user_id' => $user->id, 'bookmarkable_type' => 'App\Comment'])->pluck('bookmarkable_id'),
            'bookmarkedChannels'    => DB::table('bookmarks')->where(['user_id' => $user->id, 'bookmarkable_type' => 'App\Channel'])->pluck('bookmarkable_id'),
            'bookmarkedUsers'       => DB::table('bookmarks')->where(['user_id' => $user->id, 'bookmarkable_type' => 'App\User'])->pluck('bookmarkable_id'),

            'submissionLikes'   => $user->submissionLikesIds(),

            'commentLikes'   => $user->commentLikesIds(),
        ];

        Redis::hmset('user.'.$id.'.data', $userData);

        return $userData;
    }

    /**
     * Returns all the stats of the auth user.
     *
     * @param int $id
     *
     * @return Illuminate\Support\Collection
     */
    protected function userStats($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        $stats = Redis::hmget('user.'.$id.'.data',
                        'submissionsCount', 'commentsCount', 'submissionXp', 'commentXp');

        // if user's data is not cached, then fetch it from database and then cache it
        if (json_decode($stats[0]) === null || json_decode($stats[1]) === null || json_decode($stats[2]) === null || json_decode($stats[3]) === null) {
            $stats = $this->cacheUserData($id);

            return collect([
                'submissionsCount' => $stats['submissionsCount'],
                'commentsCount'    => $stats['commentsCount'],
                'submission_xp'    => $stats['submissionXp'],
                'comment_xp'       => $stats['commentXp'],
            ]);
        }

        return collect([
            'submissionsCount' => json_decode($stats[0]),
            'commentsCount'    => json_decode($stats[1]),
            'submission_xp'    => json_decode($stats[2]),
            'comment_xp'       => json_decode($stats[3]),
        ]);
    }

    /**
     * Returns the IDs of auth uers's hidden submissions.
     *
     * @return array
     */
    protected function hiddenSubmissions($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'hiddenSubmissions')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['hiddenSubmissions']);
    }

    /**
     * Returns the IDs of auth uers's hidden channels.
     *
     * @return array
     */
    protected function hiddenChannels($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'hiddenChannels')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['hiddenChannels']);
    }

    /**
     * updates the hiddenSubmissions records of the auth user.
     *
     * @param int $id
     * @param int $submission_id
     *
     * @return void
     */
    protected function updateHiddenSubmissions($id, $submission_id)
    {
        $hiddenSubmissions = $this->hiddenSubmissions($id);

        array_push($hiddenSubmissions, $submission_id);

        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'hiddenSubmissions')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'hiddenSubmissions', json_encode($hiddenSubmissions));
    }

    /**
     * updates the blockedChannel records of the auth user.
     *
     * @param int $id
     * @param int $channel_id
     * @param boolean $alreadyBlocked
     *
     * @return void
     */
    protected function updateBlockedChannels($id, $channel_id, $alreadyBlocked)
    {
        $hiddenChannels = $this->hiddenChannels($id);

        if ($alreadyBlocked) {
            $hiddenChannels = array_values(array_diff($hiddenChannels, [$channel_id]));
        } else {
            array_push($hiddenChannels, $channel_id);
        }

        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'hiddenChannels')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'hiddenChannels', json_encode($hiddenChannels));
    }

    /**
     * Returns the IDs of auth uers's hidden submissions.
     *
     * @return array
     */
    protected function blockedUsers($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'blockedUsers')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['blockedUsers']);
    }

    /**
     * Returns the IDs of auth uers's hidden submissions.
     *
     * @return array
     */
    protected function bookmarkedSubmissions($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'bookmarkedSubmissions')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['bookmarkedSubmissions']);
    }

    /**
     * Returns the IDs of auth uers's hidden submissions.
     *
     * @return array
     */
    protected function bookmarkedComments($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'bookmarkedComments')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['bookmarkedComments']);
    }

    /**
     * Returns the IDs of auth uers's hidden submissions.
     *
     * @return array
     */
    protected function bookmarkedUsers($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'bookmarkedUsers')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['bookmarkedUsers']);
    }

    /**
     * Returns the IDs of auth uers's hidden submissions.
     *
     * @return array
     */
    protected function bookmarkedChannels($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'bookmarkedChannels')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['bookmarkedChannels']);
    }

    /**
     * updates the bookmark records for the model.
     *
     * @param int  $id
     * @param int  $bookmarkable_id
     * @param bool $attach
     *
     * @return void
     */
    protected function updateBookmarkedSubmissions($id, $bookmarkable_id, $attach = true)
    {
        $bookmarkedSubmissions = $this->bookmarkedSubmissions($id);

        if ($attach === true) {
            array_push($bookmarkedSubmissions, $bookmarkable_id);
        } else {
            $bookmarkedSubmissions = array_values(array_diff($bookmarkedSubmissions, [$bookmarkable_id]));
        }

        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'bookmarkedSubmissions')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'bookmarkedSubmissions', json_encode($bookmarkedSubmissions));
    }

    /**
     * updates the bookmark records for the model.
     *
     * @param int  $id
     * @param int  $bookmarkable_id
     * @param bool $attach
     *
     * @return void
     */
    protected function updateBookmarkedComments($id, $bookmarkable_id, $attach = true)
    {
        $bookmarkedComments = $this->bookmarkedComments($id);

        if ($attach === true) {
            array_push($bookmarkedComments, $bookmarkable_id);
        } else {
            $bookmarkedComments = array_values(array_diff($bookmarkedComments, [$bookmarkable_id]));
        }

        Redis::hset('user.'.$id.'.data', 'bookmarkedComments', json_encode($bookmarkedComments));
    }

    /**
     * updates the bookmark records for the model.
     *
     * @param int  $id
     * @param int  $bookmarkable_id
     * @param bool $attach
     *
     * @return void
     */
    protected function updateBookmarkedUsers($id, $bookmarkable_id, $attach = true)
    {
        $bookmarkedUsers = $this->bookmarkedUsers($id);

        if ($attach === true) {
            array_push($bookmarkedUsers, $bookmarkable_id);
        } else {
            $bookmarkedUsers = array_values(array_diff($bookmarkedUsers, [$bookmarkable_id]));
        }

        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'bookmarkedUsers')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'bookmarkedUsers', json_encode($bookmarkedUsers));
    }

    /**
     * updates the bookmark records for the model.
     *
     * @param int  $id
     * @param int  $bookmarkable_id
     * @param bool $attach
     *
     * @return void
     */
    protected function updateBookmarkedChannels($id, $bookmarkable_id, $attach = true)
    {
        $bookmarkedChannels = $this->bookmarkedChannels($id);

        if ($attach === true) {
            array_push($bookmarkedChannels, $bookmarkable_id);
        } else {
            $bookmarkedChannels = array_values(array_diff($bookmarkedChannels, [$bookmarkable_id]));
        }

        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'bookmarkedChannels')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'bookmarkedChannels', json_encode($bookmarkedChannels));
    }

    /**
     * updates the hiddenSubmissions records of the auth user.
     *
     * @param int $id
     * @param int $contact_id
     *
     * @return void
     */
    protected function updateBlockedUsers($id, $contact_id, $block = true)
    {
        $blockedUsers = $this->blockedUsers($id);

        if ($block === true) {
            array_push($blockedUsers, $contact_id);
        } else {
            $blockedUsers = array_values(array_diff($blockedUsers, [$contact_id]));
        }

        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'blockedUsers')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'blockedUsers', json_encode($blockedUsers));
    }

    /**
     * Returns the IDs of auth uers's hidden submissions.
     *
     * @return array
     */
    protected function subscriptions($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'subscriptions')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['subscriptions']);
    }

    /**
     * Updates the subscriptions records of the auth user.
     *
     * @param int $id
     * @param int $channel_id
     *
     * @return void
     */
    protected function updateSubscriptions($id, $channel_id, $newSubscribe = true)
    {
        $subscriptions = $this->subscriptions($id);

        if ($newSubscribe === true) {
            array_push($subscriptions, $channel_id);
        } else {
            $subscriptions = array_values(array_diff($subscriptions, [$channel_id]));
        }

        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'subscriptions')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'subscriptions', json_encode($subscriptions));
    }

    /**
     * Returns the IDs of auth uers's liked submissions.
     *
     * @return array
     */
    protected function submissionLikesIds($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'submissionLikes')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['submissionLikes']);
    }

    /**
     * Returns the IDs of auth uers's liked comments.
     *
     * @return array
     */
    protected function commentLikesIds($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'commentLikes')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['commentLikes']);
    }

    /**
     * updates the likes records of the auth user.
     *
     * @param int    $id
     * @param string $likes
     *
     * @return void
     */
    protected function updateSubmissionLikesIds($id, $likes)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'submissionLikes')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'submissionLikes', json_encode($likes));
    }

    /**
     * updates the likes records of the auth user.
     *
     * @param int    $id
     * @param string $likes
     *
     * @return void
     */
    protected function updateCommentLikesIds($id, $likes)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'commentLikes')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'commentLikes', json_encode($likes));
    }

    /**
     * updates the submissionsCount of the author user.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateUserSubmissionsCount($id, $number = 1)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'submissionsCount')) {
            $this->cacheUserData($id);
        }

        Redis::hincrby('user.'.$id.'.data', 'submissionsCount', $number);
    }

    /**
     * updates the commentsCount of the author user.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateUserCommentsCount($id, $number = 1)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'commentsCount')) {
            $this->cacheUserData($id);
        }

        Redis::hincrby('user.'.$id.'.data', 'commentsCount', $number);
    }

    /**
     * updates the submission_xp of the author user.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateSubmissionXp($id, $number)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'submissionXp')) {
            $this->cacheUserData($id);
        }

        $newXp = Redis::hincrby('user.'.$id.'.data', 'submissionXp', $number);

        // for newbie users we update on each new vote
        if ($newXp < 100) {
            DB::table('users')->where('id', $id)->update(['submission_xp' => $newXp]);

            return;
        }

        // but for major ones, we do this once a 50 times
        if (($newXp % 20) === 0) {
            DB::table('users')->where('id', $id)->update(['submission_xp' => $newXp]);
        }
    }

    /**
     * updates the comment_xp of the author user.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateCommentXp($id, $number)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'commentXp')) {
            $this->cacheUserData($id);
        }

        $newXp = Redis::hincrby('user.'.$id.'.data', 'commentXp', $number);

        // for newbie users we update on each new vote
        if ($newXp < 100) {
            DB::table('users')->where('id', $id)->update(['comment_xp' => $newXp]);

            return;
        }

        // but for major ones, we do this once a 20 times
        if (($newXp % 20) === 0) {
            DB::table('users')->where('id', $id)->update(['comment_xp' => $newXp]);
        }
    }
}
