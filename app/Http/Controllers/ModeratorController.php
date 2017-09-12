<?php

/*
|--------------------------------------------------------------------------
| Moderator Controller
|--------------------------------------------------------------------------
|
| One quick note about approve and disapproving submission and comments:
| In Reports models approving means getting softDeleted but in the model
| itself it means setting the approved_at witha a timestamp.
|
| Disapproving means getting soft-deleted in both reports and model
| itself. Which means the model is  visible in user's profile but
| not in the channel's pages (submission's page and hot, new...)
|
*/

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Events\CommentWasDeleted;
use App\Events\SubmissionWasDeleted;
use App\Notifications\BecameModerator;
use App\Report;
use App\Submission;
use App\Traits\CachableCategory;
use App\Traits\CachableComment;
use App\Traits\CachableSubmission;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class ModeratorController extends Controller
{
    use CachableSubmission, CachableCategory, CachableComment;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Returns a collection of users who are probably about to get banned or become a moderator.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function getUsers(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'category' => 'required',
        ]);

        $category = $this->getCategoryByName($request->category);

        // To make it easier for moderators, we're gonna exlcude users that should
        // not be banned/moderator in a channel. This includes users who are
        // already bannd and moderators (administrators and moderators).
        $bannedUsers = $category->bannedUsers();

        $mods = $this->categoryMods($category->id);

        return User::where('username', 'like', '%'.$request->username.'%')
                    ->whereNotIn('id', $bannedUsers)
                    ->whereNotIn('id', $mods)
                    ->select('username')->take(100)->get()->pluck('username');
    }

    /**
     * returns cateogry's mods with their role.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'category_name' => 'required',
        ]);

        return Category::where('name', $request->category_name)->firstOrFail()->moderators;
    }

    /**
     * Approves the submission so it no longer can be reported.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function approveSubmission(Request $request)
    {
        $this->validate($request, [
            'submission_id' => 'required|integer',
        ]);

        $submission = Submission::withTrashed()->findOrFail($request->submission_id);

        abort_unless($this->mustBeModerator($submission->category_id), 403);

        $submission->update([
            'approved_at' => Carbon::now(),
            'deleted_at'  => null,
        ]);

        $this->putSubmissionInTheCache($submission);

        // remove all the reports related to this model
        Report::where([
            'reportable_id'   => $request->submission_id,
            'reportable_type' => 'App\Submission',
        ])->delete();

        return response('Submission approved', 200);
    }

    /**
     * softDeletes the submission so that the owner can see it but it won't be visible in the channel.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function disapproveSubmission(Request $request)
    {
        $this->validate($request, [
            'submission_id' => 'required|integer',
        ]);

        $submission = $this->getSubmissionById($request->submission_id);

        abort_unless($this->mustBeModerator($submission->category_id), 403);

        $submission->update([
            'approved_at' => null,
            'deleted_at'  => Carbon::now(),
        ]);

        event(new SubmissionWasDeleted($submission, false));

        return response('Submission deleted successfully.', 200);
    }

    /**
     * Approves the comment so it no longer can be reported.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function approveComment(Request $request)
    {
        $this->validate($request, [
            'comment_id' => 'required|integer',
        ]);

        abort_unless($this->mustBeModerator(Comment::withTrashed()->where('id', $request->comment_id)->value('category_id')), 403);

        DB::table('comments')->where('id', $request->comment_id)->update([
            'approved_at' => Carbon::now(),
            'deleted_at'  => null,
        ]);

        // remove all the reports related to this model
        Report::where([
            'reportable_id'   => $request->comment_id,
            'reportable_type' => 'App\Comment',
        ])->delete();

        return response('Comment approved', 200);
    }

    /**
     * softDeletes the comment so that the owner can see it but it won't be visible in the channel.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function disapproveComment(Request $request)
    {
        $this->validate($request, [
            'comment_id' => 'required|integer',
        ]);

        $comment = $this->getCommentById($request->comment_id);
        $submission = $this->getSubmissionById($comment->submission_id);

        abort_unless($this->mustBeModerator($comment->category_id), 403);

        event(new CommentWasDeleted($comment, $submission, false));

        DB::table('comments')->where('id', $request->comment_id)->update([
            'approved_at' => null,
            'deleted_at'  => Carbon::now(),
        ]);

        return response('Comment deleted successfully.', 200);
    }

    /**
     * adds a new moderator to the category.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_name' => 'required',
            'username'      => 'required',
            'role'          => 'in:administrator,moderator',
        ]);

        $category = Category::where('name', $request->category_name)->firstOrFail();

        abort_unless($this->mustBeAdministrator($category->id) && $request->username != Auth::user()->username, 403);

        $user = User::where('username', $request->username)->firstOrFail();

        $category->moderators()->attach($user->id, [
            'role' => $request->role,
        ]);

        $user->notify(new BecameModerator($category, $request->role));

        $this->updateCategoryMods($category->id, $user->id);

        return response('New moderator added successfully', 200);
    }

    /**
     * destroys the moderator record.
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'category_name' => 'required',
            'username'      => 'required',
        ]);

        $category = Category::where('name', $request->category_name)->firstOrFail();

        abort_unless($this->mustBeAdministrator($category->id) && $request->username != Auth::user()->username, 403);

        $user_id = User::where('username', $request->username)->value('id');

        $category->moderators()->detach($user_id);

        $this->updateCategoryMods($category->id, $user_id);

        return response($request->username.' is no longer a moderator at #'.$request->category_name, 200);
    }
}
