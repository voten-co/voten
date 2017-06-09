<?php

namespace App\Http\Controllers;

use App\Filters;
use App\Traits\CachableUser;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use Filters, CachableUser;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['fillStore', 'submissions', 'comments', 'showSubmissions', 'showComments']]);
    }

    /**
     * shows the submissions page of users profile
     *
     * @return view
     */
    public function showSubmissions($username)
    {
    	if (Auth::check()) {
    		return view('welcome');
    	}

    	$user = User::withTrashed()->where('username', $username)->firstOrFail();

        $user->stats = $this->userStats($user->id);

        $submissions = User::where('username', $username)
                    ->firstOrFail()
                    ->submissions()
                    ->withTrashed()
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(10);

        return view('user.submissions', compact('user', 'submissions'));
    }

    /**
     * shows the comments page of users profile
     *
     * @return view
     */
    public function showComments($username)
    {
    	if (Auth::check()) {
    		return view('welcome');
    	}

    	$user = User::withTrashed()->where('username', $username)->firstOrFail();

        $user->stats = $this->userStats($user->id);

        $comments = $this->withoutChildren(User::where('username', $username)
                    ->firstOrFail()
                    ->comments()
                    ->withTrashed()
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(10));

        return view('user.comments', compact('user', 'comments'));
    }

    /**
     * Returns user's submissions.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Collections
     */
    public function submissions(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
        ]);

        return User::where('username', $request->username)
                    ->firstOrFail()
                    ->submissions()
                    ->withTrashed()
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(10);
    }

    /**
     * Returns every submission that user has upvoted.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Collections
     */
    public function upVotedSubmissions(Request $request)
    {
        return Auth::user()->submissionUpvotes()->simplePaginate(10);
    }

    /**
     * Returns every submission that user has upvoted.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Collections
     */
    public function downVotedSubmissions(Request $request)
    {
        return Auth::user()->submissionDownvotes()->simplePaginate(10);
    }

    /**
     * retunds a colleciton of user's submitted comments.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Collection
     */
    public function comments(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
        ]);

        return $this->withoutChildren(User::where('username', $request->username)
                    ->firstOrFail()
                    ->comments()
                    ->withTrashed()
                    ->orderBy('created_at', 'desc')
                    ->simplePaginate(10));
    }

    /**
     * Returns all the nesseccary info to fill userStore in forn-end.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return JSON
     */
    public function fillStore(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|max:25',
        ]);

        $user = User::withTrashed()->where('username', $request->username)->firstOrFail();

        $user->stats = $this->userStats($user->id);

        return $user;
    }

    /**
     * Returns all the needed info for Auth user.
     *
     * @return $collectiom
     */
    public function getAuth()
    {
        return Auth::user();
    }

    /**
     * Destroys a user record (and all its related records) from the database.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        if (!confirmPassword($request->password)) {
            return response('Password is incorrect. Please try again.', 422);
        }

        // remove all user's data stored on the database
        DB::table('submissions')->where('user_id', $user->id)->delete();
        DB::table('comments')->where('user_id', $user->id)->delete();
        DB::table('messages')->where('user_id', $user->id)->delete();
        DB::table('reports')->where('user_id', $user->id)->delete();
        DB::table('subscriptions')->where('user_id', $user->id)->delete();
        DB::table('hides')->where('user_id', $user->id)->delete();
        DB::table('votes')->where('user_id', $user->id)->delete();
        DB::table('activities')->where('user_id', $user->id)->delete();
        DB::table('feedbacks')->where('user_id', $user->id)->delete();
        DB::table('comment_votes')->where('user_id', $user->id)->delete();
        DB::table('photos')->where('user_id', $user->id)->delete();
        DB::table('bookmarks')->where('user_id', $user->id)->delete();
        DB::table('roles')->where('user_id', $user->id)->delete();
        DB::table('conversations')->where('user_id', $user->id)->orWhere('contact_id', $user->id)->delete();
        DB::table('hidden_users')->where('user_id', $user->id)->delete();
        DB::table('submission_upvotes')->where('user_id', $user->id)->delete();
        DB::table('submission_downvotes')->where('user_id', $user->id)->delete();
        DB::table('comment_upvotes')->where('user_id', $user->id)->delete();
        DB::table('comment_downvotes')->where('user_id', $user->id)->delete();
        DB::table('appointedd_users')->where('user_id', $user->id)->delete();

        // pull the trigger
        $user->forceDelete();

        return response('Your account is deleted now. You happy now?!', 200);
    }
}
