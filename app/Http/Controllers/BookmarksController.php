<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Filters;
use App\Traits\CachableCategory;
use App\Traits\CachableComment;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use App\User;
use Auth;
use Illuminate\Http\Request;

class BookmarksController extends Controller
{
    use Filters, CachableUser, CachableCategory, CachableSubmission, CachableComment;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Bookmarked submissions for the Auth user.
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getBookmarkedSubmissions()
    {
        return Auth::user()->bookmarkedSubmissions()->simplePaginate(10);
    }

    /**
     * Bookmarked comments for the Auth user.
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getBookmarkedComments()
    {
        return $this->withoutChildren(Auth::user()->bookmarkedComments()->simplePaginate(10));
    }

    /**
     * Bookmarked categories for the Auth user.
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getBookmarkedCategories()
    {
        return Auth::user()->bookmarkedCategories()->simplePaginate(10);
    }

    /**
     * Bookmarked users for the Auth user.
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getBookmarkedUsers()
    {
        return Auth::user()->bookmarkedUsers()->simplePaginate(10);
    }

    /**
     * Toggles bookmarking a Submission for the Auth user
     *
     * @param Request $request
     *
     * @return string Whether the Submission was 'bookmarked' or 'unbookmarked'
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
     * Toggles bookmarking a Comment for the Auth user
     *
     * @return string Whether the Comment was 'bookmarked' or 'unbookmarked'
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
     * Toggles bookmarking a Category(Channel) for the Auth user
     *
     * @return string Whether the Category was 'bookmarked' or 'unbookmarked'
     */
    public function bookmarkCategory(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $category = $this->getCategoryById($request->id);

        $type = $category->bookmark();

        if ($type == 'bookmarked') {
            $this->updateBookmarkedCategories(Auth::user()->id, $category->id, true);
        } else {
            $this->updateBookmarkedCategories(Auth::user()->id, $category->id, false);
        }

        return $type;
    }

    /**
     * Toggles bookmarking a User for the Auth user
     *
     * @return string Whether the User was 'bookmarked' or 'unbookmarked'
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
