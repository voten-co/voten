<?php

namespace App\Http\Controllers;

use App\Filters;
use App\Traits\CachableUser;
use App\User;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use Filters, CachableUser;

    public function __construct()
    {
        $this->middleware('auth');
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
     * Destroys a user record from the database.
     *
     * @return response
     */
    public function destroy(Request $request)
    {
    	// check for confirm password
    	// check for confirm password
    	// check for confirm password

    	$user = Auth::user();

    	$user->submissions()->forceDelete();
    	$user->comments()->forceDelete();
    	$user->messages()->delete();
    	$user->conversations()->delete();

    	$user->forceDelete();

    	return response('Your account is deleted now. You happy now?!', 200);
    }
}
