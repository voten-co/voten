<?php

namespace App\Http\Controllers;

use App\Filters;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\UserResource;
use App\Traits\CachableChannel;
use App\Traits\CachableComment;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use App\User;
use Auth;
use Illuminate\Http\Request;
use App\Submission;
use App\Comment;
use App\Channel;

class BookmarksController extends Controller
{
    use Filters, CachableUser, CachableChannel, CachableSubmission, CachableComment;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Favorited submissions by Auth user.
     *
     * @return SubmissionResource
     */
    public function getBookmarkedSubmissions()
    {
        return SubmissionResource::collection(
            Auth::user()->bookmarkedSubmissions()->simplePaginate(20)
        );
    }

    /**
     * Favorited comments by Auth user.
     *
     * @return CommentResource
     */
    public function getBookmarkedComments()
    {
        return CommentResource::collection(
            Auth::user()->bookmarkedComments()->simplePaginate(20)
        );
    }

    /**
     * Favorited channels by Auth user.
     *
     * @return ChannelResource
     */
    public function getBookmarkedChannels()
    {
        return ChannelResource::collection(
            Auth::user()->bookmarkedChannels()->simplePaginate(20)
        );
    }

    /**
     * Favorited channels by Auth user.
     *
     * @return UserResource
     */
    public function getBookmarkedUsers()
    {
        return UserResource::collection(
            Auth::user()->bookmarkedUsers()->simplePaginate(20)
        );
    }

    /**
     * Favorited submissions by Auth user.
     *
     * @return response 
     */
    public function bookmarkSubmission(Submission $submission)
    {
        $type = $submission->bookmark();

        if ($type === 'bookmarked') {
            $this->updateBookmarkedSubmissions(Auth::id(), $submission->id, true);

            return res(201, "Bookmarked successfully.");
        }

        $this->updateBookmarkedSubmissions(Auth::id(), $submission->id, false);

        return res(200, "Undid bookmark successfully.");
    }

    /**
     * Favorited submissions by Auth user.
     *
     * @return response
     */
    public function bookmarkComment(Comment $comment)
    {
        $type = $comment->bookmark();

        if ($type === 'bookmarked') {
            $this->updateBookmarkedComments(Auth::id(), $comment->id, true);

            return res(201, "Bookmarked successfully.");
        }

        $this->updateBookmarkedComments(Auth::id(), $comment->id, false);

        return res(200, "Undid bookmark successfully.");
    }

    /**
     * Favorited submissions by Auth user.
     *
     * @return response
     */
    public function bookmarkChannel(Channel $channel)
    {
        $type = $channel->bookmark();

        if ($type === 'bookmarked') {
            $this->updateBookmarkedChannels(Auth::id(), $channel->id, true);

            return res(201, "Bookmarked successfully.");
        }

        $this->updateBookmarkedChannels(Auth::id(), $channel->id, false);

        return res(200, "Undid bookmark successfully.");
    }

    /**
     * (un)Bookmarks the user.
     *
     * @return response
     */
    public function bookmarkUser(User $user)
    {
        $type = $user->bookmark();

        if ($type === 'bookmarked') {
            $this->updateBookmarkedUsers(Auth::id(), $user->id, true);

            return res(201, "Bookmarked successfully.");
        }

        $this->updateBookmarkedUsers(Auth::id(), $user->id, false);

        return res(200, "Undid bookmark successfully.");
    }
}
