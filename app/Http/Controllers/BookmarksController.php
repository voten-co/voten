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
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
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
     * @return \Illuminate\Support\Collection
     */
    public function bookmarkSubmission(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $submission = $this->getSubmissionById($request->id);

        $type = $submission->bookmark();

        if ($type == 'bookmarked') {
            $this->updateBookmarkedSubmissions(Auth::user()->id, $submission->id, true);
        } else {
            $this->updateBookmarkedSubmissions(Auth::user()->id, $submission->id, false);
        }

        return $type;
    }

    /**
     * (un)Bookmarks the comment.
     *
     * @return status
     */
    public function bookmarkComment(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $comment = $this->getCommentById($request->id);

        $type = $comment->bookmark();

        if ($type == 'bookmarked') {
            $this->updateBookmarkedComments(Auth::user()->id, $comment->id, true);
        } else {
            $this->updateBookmarkedComments(Auth::user()->id, $comment->id, false);
        }

        return $type;
    }

    /**
     * (un)Bookmarks the channel.
     *
     * @return status
     */
    public function bookmarkChannel(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $channel = $this->getChannelById($request->id);

        $type = $channel->bookmark();

        if ($type == 'bookmarked') {
            $this->updateBookmarkedChannels(Auth::user()->id, $channel->id, true);
        } else {
            $this->updateBookmarkedChannels(Auth::user()->id, $channel->id, false);
        }

        return $type;
    }

    /**
     * (un)Bookmarks the user.
     *
     * @return status
     */
    public function bookmarkUser(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $user = User::where('id', $request->id)->firstOrFail();

        $type = $user->bookmark();

        if ($type == 'bookmarked') {
            $this->updateBookmarkedUsers(Auth::user()->id, $user->id, true);
        } else {
            $this->updateBookmarkedUsers(Auth::user()->id, $user->id, false);
        }

        return $type;
    }
}
