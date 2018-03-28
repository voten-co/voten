<?php

namespace App\Http\Controllers;

use App\Traits\CachableSubmission;
use Illuminate\Http\Request;
use App\Submission;

class NsfwController extends Controller
{
    use CachableSubmission;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * marks the submission model as NSFW (not safe for work).
     *
     * @param \App\Submission $submission
     *
     * @return response
     */
    public function store(Submission $submission)
    {
        abort_unless(
            $this->mustBeOwner($submission) || $this->mustBeModerator($submission->channel_id
        ), 403);

        $submission->update([
            'nsfw' => true,
        ]);

        $this->putSubmissionInTheCache($submission);

        return res(200, 'Submission is no longer safe for work.');
    }

    /**
     * marks the submission model as SFW (safe for work).
     *
     * @param \App\Submission $submission
     *
     * @return response
     */
    public function destroy(Submission $submission)
    {
        abort_unless(
            $this->mustBeOwner($submission) || $this->mustBeModerator($submission->channel_id
        ), 403);

        $submission->update([
            'nsfw' => false,
        ]);

        $this->putSubmissionInTheCache($submission);

        return res(200, 'Submission is now safe for work.');
    }
}
