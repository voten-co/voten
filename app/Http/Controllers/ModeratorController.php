<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Comment;
use App\Events\CommentWasDeleted;
use App\Events\SubmissionWasDeleted;
use App\Http\Resources\ModeratorResource;
use App\Notifications\BecameModerator;
use App\Report;
use App\Rules\NotSelfUsername;
use App\Submission;
use App\Traits\CachableChannel;
use App\Traits\CachableComment;
use App\Traits\CachableSubmission;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class ModeratorController extends Controller
{
    use CachableSubmission, CachableChannel, CachableComment;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

    /**
     * Returns channel's list of moderators with their role.
     *
     * @param Channel $channel
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Channel $channel)
    {
        return ModeratorResource::collection($channel->moderators);
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

        abort_unless($this->mustBeModerator($submission->channel_id), 403);

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

        abort_unless($this->mustBeModerator($submission->channel_id), 403);

        $submission->update([
            'approved_at' => null,
            'deleted_at'  => Carbon::now(),
        ]);

        event(new SubmissionWasDeleted($submission, false));

        return response('Submission deleted successfully', 200);
    }

    /**
     * Approves the comment so it no longer can be reported.
     *
     * @param Comment $comment
     *
     * @return response
     */
    public function approveComment(Comment $comment)
    {
        abort_unless($this->mustBeModerator($comment->channel_id), 403);

        DB::table('comments')->where('id', $comment->id)->update([
            'approved_at' => Carbon::now(),
            'deleted_at'  => null,
        ]);

        // remove all the reports related to this model
        Report::where([
            'reportable_id'   => $comment->id,
            'reportable_type' => 'App\Comment',
        ])->delete();

        return res(200, 'Comment approved successfully');
    }

    /**
     * softDeletes the comment so that the owner can see it but it won't be visible in the channel.
     *
     * @param Comment $comment
     *
     * @return response
     */
    public function disapproveComment(Comment $comment)
    {
        abort_unless($this->mustBeModerator($comment->channel_id), 403);
        
        $submission = $this->getSubmissionById($comment->submission_id);

        event(new CommentWasDeleted($comment, $submission, false));

        DB::table('comments')->where('id', $comment->id)->update([
            'approved_at' => null,
            'deleted_at'  => Carbon::now(),
        ]);

        return res(200, 'Comment disapproved successfully');
    }

    /**
     * adds a new moderator to the channel.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function store(Request $request, Channel $channel)
    {
        $this->validate($request, [
            'user_id'   => 'required|exists:users,id',
            'role'       => 'in:administrator,moderator',
        ]);

        $channel->moderators()->attach(request('user_id'), [
            'role' => $request->role,
        ]);

        $new_moderator = User::find(request('user_id'));

        $new_moderator->notify(
            new BecameModerator($channel, $request->role)
        );

        $this->updateChannelMods($channel->id, request('user_id'));

        return res(201, 'New moderator added successfully');
    }

    /**
     * destroys the moderator record.
     *
     * @return response
     */
    public function destroy(Channel $channel, User $user)
    {
        $channel->moderators()->detach($user->id);

        $this->updateChannelMods($channel->id, $user->id);

        return res(200, $user->username.' is no longer a moderator at #'. $channel->name);
    }
}
