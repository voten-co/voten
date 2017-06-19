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

            'submissionKarma' => $user->submission_karma,
            'commentKarma'    => $user->comment_karma,

            'hiddenSubmissions' => $user->hiddenSubmissions(),
            'subscriptions'     => $user->subscriptions->pluck('id'),

            'blockedUsers' => $user->blockedUsers(),

            'bookmarkedSubmissions' => DB::table('bookmarks')->where(['user_id' => $user->id, 'bookmarkable_type' => 'App\Submission'])->pluck('bookmarkable_id'),
            'bookmarkedComments'    => DB::table('bookmarks')->where(['user_id' => $user->id, 'bookmarkable_type' => 'App\Comment'])->pluck('bookmarkable_id'),
            'bookmarkedCategories'  => DB::table('bookmarks')->where(['user_id' => $user->id, 'bookmarkable_type' => 'App\Category'])->pluck('bookmarkable_id'),
            'bookmarkedUsers'       => DB::table('bookmarks')->where(['user_id' => $user->id, 'bookmarkable_type' => 'App\User'])->pluck('bookmarkable_id'),

            'submissionUpvotes'   => $user->submissionUpvotesIds(),
            'submissionDownvotes' => $user->submissionDownvotesIds(),

            'commentUpvotes'   => $user->commentUpvotesIds(),
            'commentDownvotes' => $user->commentDownvotesIds(),
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
                        'submissionsCount', 'commentsCount', 'submissionKarma', 'commentKarma');

        // if user's data is not cached, then fetch it from database and then cache it
        if (json_decode($stats[0]) === null || json_decode($stats[1]) === null || json_decode($stats[2]) === null || json_decode($stats[3]) === null) {
            $stats = $this->cacheUserData($id);

            return collect([
                'submissionsCount' => $stats['submissionsCount'],
                'commentsCount'    => $stats['commentsCount'],
                'submission_karma' => $stats['submissionKarma'],
                'comment_karma'    => $stats['commentKarma'],
            ]);
        }

        return collect([
            'submissionsCount' => json_decode($stats[0]),
            'commentsCount'    => json_decode($stats[1]),
            'submission_karma' => json_decode($stats[2]),
            'comment_karma'    => json_decode($stats[3]),
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
    protected function bookmarkedCategories($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'bookmarkedCategories')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['bookmarkedCategories']);
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
    protected function updateBookmarkedCategories($id, $bookmarkable_id, $attach = true)
    {
        $bookmarkedCategories = $this->bookmarkedCategories($id);

        if ($attach === true) {
            array_push($bookmarkedCategories, $bookmarkable_id);
        } else {
            $bookmarkedCategories = array_values(array_diff($bookmarkedCategories, [$bookmarkable_id]));
        }

        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'bookmarkedCategories')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'bookmarkedCategories', json_encode($bookmarkedCategories));
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
     * updates the hiddenSubmissions records of the auth user.
     *
     * @param int $id
     * @param int $category_id
     *
     * @return void
     */
    protected function updateSubscriptions($id, $category_id, $newSubscribe = true)
    {
        $subscriptions = $this->subscriptions($id);

        if ($newSubscribe === true) {
            array_push($subscriptions, $category_id);
        } else {
            $subscriptions = array_values(array_diff($subscriptions, [$category_id]));
        }

        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'subscriptions')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'subscriptions', json_encode($subscriptions));
    }

    /**
     * Returns the IDs of auth uers's upvoted submissions.
     *
     * @return array
     */
    protected function submissionUpvotesIds($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'submissionUpvotes')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['submissionUpvotes']);
    }

    /**
     * Returns the IDs of auth uers's downvoted submissions.
     *
     * @return array
     */
    protected function submissionDownvotesIds($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'submissionDownvotes')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['submissionDownvotes']);
    }

    /**
     * Returns the IDs of auth uers's upvoted comments.
     *
     * @return array
     */
    protected function commentUpvotesIds($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'commentUpvotes')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['commentUpvotes']);
    }

    /**
     * Returns the IDs of auth uers's downvoted comments.
     *
     * @return array
     */
    protected function commentDownvotesIds($id = 0)
    {
        if ($id === 0) {
            $id = Auth::id();
        }

        if ($value = Redis::hget('user.'.$id.'.data', 'commentDownvotes')) {
            return json_decode($value);
        }

        $result = $this->cacheUserData($id);

        return json_decode($result['commentDownvotes']);
    }

    /**
     * updates the upvotes records of the auth user.
     *
     * @param int    $id
     * @param string $upvotes
     *
     * @return void
     */
    protected function updateSubmissionUpvotesIds($id, $upvotes)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'submissionUpvotes')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'submissionUpvotes', json_encode($upvotes));
    }

    /**
     * updates the downvotes records of the auth user.
     *
     * @param int   $user_id
     * @param array $downvotes
     *
     * @return void
     */
    protected function updateSubmissionDownvotesIds($id, $downvotes)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'submissionDownvotes')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'submissionDownvotes', json_encode($downvotes));
    }

    /**
     * updates the upvotes records of the auth user.
     *
     * @param int    $id
     * @param string $upvotes
     *
     * @return void
     */
    protected function updateCommentUpvotesIds($id, $upvotes)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'commentUpvotes')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'commentUpvotes', json_encode($upvotes));
    }

    /**
     * updates the downvotes records of the auth user.
     *
     * @param int   $user_id
     * @param array $downvotes
     *
     * @return void
     */
    protected function updateCommentDownvotesIds($id, $downvotes)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'commentDownvotes')) {
            $this->cacheUserData($id);
        }

        Redis::hset('user.'.$id.'.data', 'commentDownvotes', json_encode($downvotes));
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
     * updates the submission_karma of the author user.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateSubmissionKarma($id, $number)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'submissionKarma')) {
            $this->cacheUserData($id);
        }

        $newKarma = Redis::hincrby('user.'.$id.'.data', 'submissionKarma', $number);

        // for newbie users we update on each new vote
        if ($newKarma < 100) {
            DB::table('users')->where('id', $id)->update(['submission_karma' => $newKarma]);

            return;
        }

        // but for major ones, we do this once a 50 times
        if (($newKarma % 20) === 0) {
            DB::table('users')->where('id', $id)->update(['submission_karma' => $newKarma]);
        }
    }

    /**
     * updates the comment_karma of the author user.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateCommentKarma($id, $number)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('user.'.$id.'.data', 'commentKarma')) {
            $this->cacheUserData($id);
        }

        $newKarma = Redis::hincrby('user.'.$id.'.data', 'commentKarma', $number);

        // for newbie users we update on each new vote
        if ($newKarma < 100) {
            DB::table('users')->where('id', $id)->update(['comment_karma' => $newKarma]);

            return;
        }

        // but for major ones, we do this once a 20 times
        if (($newKarma % 20) === 0) {
            DB::table('users')->where('id', $id)->update(['comment_karma' => $newKarma]);
        }
    }
}
