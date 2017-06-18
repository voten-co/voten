<?php

namespace App\Http\Controllers;

use App\Submission;
use App\Traits\CachableSubmission;
use Illuminate\Http\Request;

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
     * @param Illuminate\Http\Request $request
     *
     * @return response
     */
    public function markAsNSFW(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $submission = $this->getSubmissionById($request->id);

        abort_unless(
            $this->mustBeOwner($submission) || $this->mustBeModerator($submission->category_id),
            403
        );

        $submission->update([
            'nsfw' => true,
        ]);

        $this->putSubmissionInTheCache($submission);

        return response('Submission was marked as NSFW', 200);
    }

    /**
     * marks the submission model as SFW (safe for work).
     *
     * @param Illuminate\Http\Request $request
     *
     * @return response
     */
    public function markAsSFW(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $submission = $this->getSubmissionById($request->id);

        abort_unless(
            $this->mustBeOwner($submission) || $this->mustBeModerator($submission->category_id),
            403
        );

        $submission->update([
            'nsfw' => false,
        ]);

        $this->putSubmissionInTheCache($submission);

        return response('Submission was marked as SFW', 200);
    }
}
