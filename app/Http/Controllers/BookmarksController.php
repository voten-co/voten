<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\User;
use App\Comment;
use App\Filters;
use App\Category;
use App\Submission;
use App\Http\Requests;
use App\Traits\CachableUser;
use App\Traits\CachableComment;
use App\Traits\CachableCategory;
use App\Traits\CachableSubmission;
use Illuminate\Http\Request;


class BookmarksController extends Controller
{
	use Filters, CachableUser, CachableCategory, CachableSubmission, CachableComment;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Favorited submissions by Auth user
     *
     * @return Illuminate\Support\Collection
     */
    public function getBookmarkedSubmissions()
    {
    	return Auth::user()->bookmarkedSubmissions()->simplePaginate(10);
    }


    /**
     * Favorited comments by Auth user
     *
     * @return Illuminate\Support\Collection
     */
    public function getBookmarkedComments()
    {
    	return $this->withoutChildren( Auth::user()->bookmarkedComments()->simplePaginate(10) );
    }

    /**
     * Favorited categories by Auth user
     *
     * @return Illuminate\Support\Collection
     */
    public function getBookmarkedCategories()
    {
    	return Auth::user()->bookmarkedCategories()->simplePaginate(10);
    }

    /**
     * Favorited categories by Auth user
     *
     * @return Illuminate\Support\Collection
     */
    public function getBookmarkedUsers()
    {
    	return Auth::user()->bookmarkedUsers()->simplePaginate(10);
    }

    /**
     * Favorited submissions by Auth user
     *
     * @return Illuminate\Support\Collection
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
     * (un)Bookmarks the comment
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
     * (un)Bookmarks the category
     *
     * @return status
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
     * (un)Bookmarks the user
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
