<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportedSubmissionResource;
use App\Report;
use App\Submission;
use App\Traits\CachableChannel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Events\ReportWasCreated;
use App\Channel;

class ReportSubmissionsController extends Controller
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
    public function store(Request $request, Submission $submission)
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

        if ($submission->approved_at === null) {
            Report::create([
                'subject'         => $request->subject,
                'reportable_type' => "App\Submission",
                'reportable_id'   => $submission->id,
                'user_id'         => Auth::id(),
                'channel_id'      => $submission->channel_id, 
                'description'     => $request->description,
            ]);  
        }

        return res(200, 'Report submitted successfully.');
    }

    /**
     * Indexes the reported submissions.
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
            return ReportedSubmissionResource::collection(
                Report::onlyTrashed()->whereHas('submission')->whereHas('reporter')->where([
                    'channel_id'      => $channel->id,
                    'reportable_type' => 'App\Submission',
                ])->with('reporter', 'submission')->orderBy('created_at', 'desc')->simplePaginate(50)
            );
        }

        // default type which is "unsolved"
        return ReportedSubmissionResource::collection(
            Report::whereHas('submission')->whereHas('reporter')->where([
                'channel_id'      => $channel->id,
                'reportable_type' => 'App\Submission',
            ])->with('reporter', 'submission')->orderBy('created_at', 'desc')->simplePaginate(50)
        );
    }
}
