<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Filters;
use App\Http\Resources\CommentResource;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\UserResource;
use App\Rules\CurrentPassword;
use App\Submission;
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
        $this->middleware('auth', ['except' => ['getById', 'getByUsername', 'submissions', 'comments', 'showSubmissions', 'showComments']]);
    }

    /**
     * shows the submissions page of users profile.
     *
     * @return view
     */
    public function showSubmissions($username)
    {
        $user = new UserResource(
            User::withTrashed()->where('username', $username)->firstOrFail(),
            true
        );

        $submissions = SubmissionResource::collection(
            Submission::whereUserId($user->id)
            ->withTrashed()
            ->orderBy('created_at', 'desc')
            ->simplePaginate(15)
        );

        return view('user.submissions', compact('user', 'submissions'));
    }

    /**
     * shows the comments page of users profile.
     *
     * @return view
     */
    public function showComments($username)
    {
        $user = new UserResource(
            User::withTrashed()->where('username', $username)->firstOrFail(),
            true
        );

        $comments = CommentResource::collection(
            Comment::whereUserId($user->id)
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
    public function submissions(User $user)
    {
        return SubmissionResource::collection(
            $user->submissions()
                ->withTrashed()
                ->orderBy('created_at', 'desc')
                ->simplePaginate(15)
        );
    }
    
    public function submissionsByUsername(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|exists:users',
        ]);

        $user = User::whereUsername(request('username'))->firstOrFail();

        return SubmissionResource::collection(
            $user->submissions()
                ->withTrashed()
                ->orderBy('created_at', 'desc')
                ->simplePaginate(15)
        );
    }

    /**
     * Returns every submission that user has liked.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Collections
     */
    public function likedSubmissions(Request $request)
    {
        return SubmissionResource::collection(
            Auth::user()->submissionLikes()->simplePaginate(15)
        );
    }

    /**
     * retunds a colleciton of user's submitted comments.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Collection
     */
    public function comments(User $user)
    {
        return CommentResource::collection(
            $user->comments()
                ->withTrashed()
                ->orderBy('created_at', 'desc')
                ->simplePaginate(15)
        );
    }
    public function commentsByUsername(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|exists:users',
        ]);

        $user = User::whereUsername(request('username'))->firstOrFail();

        return CommentResource::collection(
            $user->comments()
                ->withTrashed()
                ->orderBy('created_at', 'desc')
                ->simplePaginate(15)
        );
    }

    /**
     * Get user model using the username. 
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return UserResource
     */
    public function getByUsername(Request $request)
    {
        $this->validate($request, [
            'username'   => 'required_without:id|exists:users',
            'with_info'  => 'boolean',
            'with_stats' => 'boolean',
        ]);

        return new UserResource(
            User::withTrashed()->where('username', $request->username)->first()
        );
    }

    /**
     * Get user model via the id. 
     *
     * @param \Illuminate\Http\Request $request
     * @param integer $user
     *
     * @return UserResource
     */
    public function getById(Request $request, User $user)
    {
        $this->validate($request, [
            'with_info'  => 'boolean',
            'with_stats' => 'boolean',
        ]);

        return new UserResource($user);
    }

    /**
     * Destroy all auth user's data.
     *
     * @return Response
     */
    public function destroyAsAuth(Request $request)
    {
        $this->validate($request, [
            'password' => ['required', new CurrentPassword()],
        ]);

        $user = Auth::user();

        $this->removeAllUserData($user->id);

        $temp = $user->username;

        $user->forceDelete();

        return res(200, 'Account deleted successfully.');
    }

    /**
     * Destroy all user's data.
     *
     * @return Response
     */
    public function destroyAsVotenAdministrator(Request $request)
    {
        $this->validate($request, [
            'password' => ['required', new CurrentPassword()],
            'user_id'  => 'required|exists:users,id',
        ]);

        $user = User::findOrFail(request('user_id'));

        $this->removeAllUserData($user->id);

        $temp = $user->username;

        $user->forceDelete();

        return res(200, 'Account deleted successfully.');
    }

    protected function removeAllUserData($user_id)
    {
        DB::table('submissions')->where('user_id', $user_id)->delete();
        DB::table('comments')->where('user_id', $user_id)->delete();
        DB::table('messages')->where('user_id', $user_id)->delete();
        DB::table('reports')->where('user_id', $user_id)->delete();
        DB::table('subscriptions')->where('user_id', $user_id)->delete();
        DB::table('hides')->where('user_id', $user_id)->delete();
        DB::table('activities')->where('user_id', $user_id)->delete();
        DB::table('feedbacks')->where('user_id', $user_id)->delete();
        DB::table('photos')->where('user_id', $user_id)->delete();
        DB::table('bookmarks')->where('user_id', $user_id)->delete();
        DB::table('roles')->where('user_id', $user_id)->delete();
        DB::table('conversations')->where('user_id', $user_id)->orWhere('contact_id', $user_id)->delete();
        DB::table('hidden_users')->where('user_id', $user_id)->delete();
        DB::table('submission_likes')->where('user_id', $user_id)->delete();
        DB::table('comment_likes')->where('user_id', $user_id)->delete();
        DB::table('appointedd_users')->where('user_id', $user_id)->delete();
    }
}
