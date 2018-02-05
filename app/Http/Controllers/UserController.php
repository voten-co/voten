<?php

namespace App\Http\Controllers;

use App\Filters;
use App\Http\Resources\CommentResource;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\UserResource;
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
        $this->middleware('auth', ['except' => ['get', 'submissions', 'comments', 'showSubmissions', 'showComments']]);
    }

    /**
     * shows the submissions page of users profile.
     *
     * @return view
     */
    public function showSubmissions($username)
    {
        $user = User::withTrashed()->where('username', $username)->firstOrFail();

        $user->stats = $this->userStats($user->id);

        $submissions = User::where('username', $username)
            ->firstOrFail()
            ->submissions()
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->simplePaginate(15);

        return view('user.submissions', compact('user', 'submissions'));
    }

    /**
     * shows the comments page of users profile.
     *
     * @return view
     */
    public function showComments($username)
    {
        $user = User::withTrashed()->where('username', $username)->firstOrFail();

        $user->stats = $this->userStats($user->id);

        $comments = CommentResource::collection(
            User::where('username', $username)
                ->firstOrFail()
                ->comments()
                ->withTrashed()
                ->orderBy('created_at', 'desc')
                ->simplePaginate(15)
        );

        return view('user.comments', compact('user', 'comments'));
    }

    /**
     * Returns user's submissions.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Collections
     */
    public function submissions(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
        ]);

        return SubmissionResource::collection(User::where('username', $request->username)
                ->firstOrFail()
                ->submissions()
                ->withTrashed()
                ->orderBy('created_at', 'desc')
                ->simplePaginate(15));
    }

    /**
     * Returns every submission that user has upvoted.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Collections
     */
    public function upVotedSubmissions(Request $request)
    {
        return SubmissionResource::collection(Auth::user()->submissionUpvotes()->simplePaginate(15));
    }

    /**
     * Returns every submission that user has upvoted.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Collections
     */
    public function downVotedSubmissions(Request $request)
    {
        return SubmissionResource::collection(Auth::user()->submissionDownvotes()->simplePaginate(15));
    }

    /**
     * retunds a colleciton of user's submitted comments.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Collection
     */
    public function comments(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|exists:users',
        ]);

        return CommentResource::collection(
            User::where('username', $request->username)
                ->first()
                ->comments()
                ->withTrashed()
                ->orderBy('created_at', 'desc')
                ->simplePaginate(15)
        );
    }

    /**
     * Returns all the nesseccary info to fill userStore in forn-end.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return JSON
     */
    public function get(Request $request)
    {
        $this->validate($request, [
            'username' => 'required_without:id|max:25|exists:users',
            'id' => 'required_without:username|exists:users',
            'with_info' => 'boolean',
            'with_stats' => 'boolean',
        ]);

        if ($request->filled('username')) {
            return new UserResource(
                User::withTrashed()->where('username', $request->username)->first()
            );
        }

        return new UserResource(
            User::withTrashed()->where('id', $request->id)->first()
        );
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
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        if (!$request->ajax()) {
            // request sent from backend panel
            abort_unless($this->mustBeVotenAdministrator(), 403);

            $user = User::findOrFail($request->user_id);
        } else {
            // request sent via ajax by user
            $user = Auth::user();
        }

        if (!confirmPassword($request->password)) {
            if (!$request->ajax()) {
                // request sent from backend panel
                session()->flash('warning', "Incorrect Password. What kind of an administrator doesn't remember his password? ");

                return back();
            }

            // request sent via ajax by user
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

        $temp = $user->username;

        // pull the trigger
        $user->forceDelete();

        if ($request->ajax()) {
            return response('Your account is deleted now. You happy now?!', 200);
        }

        session()->flash('status', "All @{$temp}'s records have been deleted.");

        return redirect('/backend/users');
    }
}
