<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Resources\ReportedCommentResource;
use App\Report;
use App\Submission;
use App\Traits\CachableChannel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Channel;

class ReportCommentsController extends Controller
{
    use CachableChannel;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Stores a new report for a comment.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function store(Request $request, Comment $comment)
    {
        $this->validate($request, [
            'subject' => [
                'required',
                Rule::in(
                    [
                        "It's spam", 
                        "It doesn't follow channel's exclusive rules", 
                        "It doesn't follow Voten's general rules",
                        "It's harassing me or someone that I know", 
                        "Other"
                    ]
                ),
            ],
            'description' => 'nullable|string|max:5000'
        ]);

        if ($comment->approved_at === null) {
            Report::create([
                'subject'         => $request->subject,
                'reportable_type' => "App\Comment",
                'reportable_id'   => $comment->id,
                'user_id'         => Auth::id(),
                'channel_id'      => $comment->channel_id,
                'description'     => $request->description,
            ]);
        }

        return res(200, 'Report submitted successfully.');
    }

    /**
     * Indexes the reported comments.
     *
     * @param \Illuminate\Http\Request $request
     * @param Channel $channel
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request, Channel $channel)
    {
        $this->validate($request, [
            'type' => 'nullable|in:solved,unsolved',
        ]);

        if ($request->type == 'solved') {
            return ReportedCommentResource::collection(
                Report::onlyTrashed()->whereHas('comment')->whereHas('reporter')->where([
                    'channel_id'      => $channel->id,
                    'reportable_type' => 'App\Comment',
                ])->with('reporter', 'comment.submission')->orderBy('created_at', 'desc')->simplePaginate(50)
            );
        }

        // default type which is "unsolved"
        return ReportedCommentResource::collection(
            Report::whereHas('comment')->whereHas('reporter')->where([
                'channel_id'      => $channel->id,
                'reportable_type' => 'App\Comment',
            ])->with('reporter', 'comment.submission')->orderBy('created_at', 'desc')->simplePaginate(50)
        );
    }
}
