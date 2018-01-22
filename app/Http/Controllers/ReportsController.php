<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Comment;
use App\Report;
use App\Submission;
use App\Traits\CachableChannel;
use Auth;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    use CachableChannel;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Stores a new report for a submission.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function submission(Request $request)
    {
        $this->validate($request, [
            'submission_id' => 'required|numeric',
            'subject'       => 'required',
        ]);

        if (Submission::where('id', $request->submission_id)->where('approved_at', '!=', null)->exists()) {
            return response("can't report an approved submission", 200);
        }

        $report = new Report([
            'subject'         => $request->subject,
            'reportable_type' => "App\Submission",
            'reportable_id'   => $request->submission_id,
            'user_id'         => Auth::user()->id,
            'channel_id'      => Submission::where('id', $request->submission_id)->value('channel_id'),
            'description'     => $request->description,
        ]);
        $report->save();

        return response('Report submitted', 200);
    }

    /**
     * Stores a new report for a comment.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function comment(Request $request)
    {
        $this->validate($request, [
            'comment_id' => 'required|numeric',
            'subject'    => 'required',
        ]);

        if (Comment::where('id', $request->comment_id)->where('approved_at', '!=', null)->exists()) {
            return response("can't report an approved comment", 200);
        }

        $report = new Report([
            'subject'         => $request->subject,
            'reportable_type' => "App\Comment",
            'reportable_id'   => $request->comment_id,
            'user_id'         => Auth::user()->id,
            'channel_id'      => Comment::where('id', $request->comment_id)->value('channel_id'),
            'description'     => $request->description,
        ]);
        $report->save();

        return response('Report submitted', 200);
    }

    /**
     * Indexes the reported submissions.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function reportedSubmissions(Request $request)
    {
        $this->validate($request, [
            'channel'  => 'required',
            'type'     => 'required',
        ]);

        $channel_id = Channel::where('name', $request->channel)->value('id');

        abort_unless($this->mustBeModerator($channel_id), 403);

        if ($request->type == 'solved') {
            return Report::onlyTrashed()->whereHas('submission')->whereHas('reporter')->where([
                'channel_id'      => $channel_id,
                'reportable_type' => 'App\Submission',
            ])->with('reporter', 'submission')->orderBy('created_at', 'desc')->simplePaginate(50);
        }

        // default type which is "unsolved"
        return Report::whereHas('submission')->whereHas('reporter')->where([
            'channel_id'      => $channel_id,
            'reportable_type' => 'App\Submission',
        ])->with('reporter', 'submission')->orderBy('created_at', 'desc')->simplePaginate(50);
    }

    /**
     * Indexes the reported comments.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function reportedComments(Request $request)
    {
        $this->validate($request, [
            'channel'  => 'required',
            'type'     => 'required',
        ]);

        $channel_id = Channel::where('name', $request->channel)->value('id');

        abort_unless($this->mustBeModerator($channel_id), 403);

        if ($request->type == 'solved') {
            return Report::onlyTrashed()->whereHas('comment')->whereHas('reporter')->where([
                'channel_id'      => $channel_id,
                'reportable_type' => 'App\Comment',
            ])->with('reporter', 'comment.submission')->orderBy('created_at', 'desc')->simplePaginate(50);
        }

        // default type which is "unsolved"
        return Report::whereHas('comment')->whereHas('reporter')->where([
            'channel_id'      => $channel_id,
            'reportable_type' => 'App\Comment',
        ])->with('reporter', 'comment.submission')->orderBy('created_at', 'desc')->simplePaginate(50);
    }
}
