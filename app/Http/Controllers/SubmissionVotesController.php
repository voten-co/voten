<?php

namespace App\Http\Controllers;

use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class SubmissionVotesController extends Controller
{
    use CachableUser, CachableSubmission;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * updates the vote rocords of the user in Redis.
     *
     * @param int   $voter_id
     * @param mixed $previous_vote
     * @param int   $submission_id
     *
     * @return void
     */
    protected function updateUserUpVotesRecords($voter_id, $author_id, $previous_vote, $submission_id)
    {
        $upvotes = $this->submissionUpvotesIds($voter_id);

        // remove the $submission_id from the array
        if ($previous_vote == 'upvote') {
            $upvotes = array_values(array_diff($upvotes, [$submission_id]));
            $this->updateSubmissionXp($author_id, -1);
        }

        // remove the $submission_id from the downvotes array and add it to the upvotes array
        if ($previous_vote == 'downvote') {
            $downvotes = $this->submissionDownvotesIds($voter_id);

            $downvotes = array_values(array_diff($downvotes, [$submission_id]));
            array_push($upvotes, $submission_id);

            $this->updateSubmissionDownvotesIds($voter_id, $downvotes);
            $this->updateSubmissionXp($author_id, 2);
        }

        // add the $submission_id to the array
        if ($previous_vote == null) {
            array_push($upvotes, $submission_id);
            $this->updateSubmissionXp($author_id, 1);
        }

        $this->updateSubmissionUpvotesIds($voter_id, $upvotes);
    }

    /**
     * updates the vote rocords of the user in Redis.
     *
     * @param int   $voter_id
     * @param int   $author_id
     * @param mixed $previous_vote
     * @param int   $submission_id
     *
     * @return void
     */
    protected function updateUserDownVotesRecords($voter_id, $author_id, $previous_vote, $submission_id)
    {
        $downvotes = $this->submissionDownvotesIds($voter_id);

        // remove the $submission_id from the array
        if ($previous_vote == 'downvote') {
            $downvotes = array_values(array_diff($downvotes, [$submission_id]));
            $this->updateSubmissionXp($author_id, 1);
        }

        // remove the $submission_id from the downvotes array and add it to the upvotes array
        if ($previous_vote == 'upvote') {
            $upvotes = $this->submissionUpvotesIds($voter_id);

            $upvotes = array_values(array_diff($upvotes, [$submission_id]));
            array_push($downvotes, $submission_id);

            $this->updateSubmissionUpvotesIds($voter_id, $upvotes);
            $this->updateSubmissionXp($author_id, -2);
        }

        // add the $submission_id to the array
        if ($previous_vote == null) {
            array_push($downvotes, $submission_id);
            $this->updateSubmissionXp($author_id, -1);
        }

        $this->updateSubmissionDownvotesIds($voter_id, $downvotes);
    }

    /**
     * Adds the upvote record for the auth user and (if the user is not trying to cheat) updates the vote points and rate
     * for the submission model.
     *
     * @return response
     */
    public function upVote(Request $request)
    {
        $this->validate($request, [
            'submission_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $submission = $this->getSubmissionById($request->submission_id);

        try {
            if ($request->previous_vote == 'upvote') {
                $new_upvotes = ($submission->upvotes - 1);
                $user->submissionUpvotes()->detach($request->submission_id);
            } elseif ($request->previous_vote == 'downvote') {
                $new_upvotes = ($submission->upvotes + 1);
                $new_downvotes = ($submission->downvotes - 1);
                $user->submissionDownvotes()->detach($request->submission_id);
                $user->submissionUpvotes()->attach($request->submission_id, [
                    'ip_address' => getRequestIpAddress(),
                ]);
            } else {
                $new_upvotes = ($submission->upvotes + 1);
                $user->submissionUpvotes()->attach($request->submission_id, [
                    'ip_address' => getRequestIpAddress(),
                ]);
            }

            $this->updateUserUpVotesRecords(
                $user->id, $submission->owner->id, $request->previous_vote, $request->submission_id
            );
        } catch (\Exception $e) {
            return response('invalid request', 500);
        }

        // If the user is cheating, we just send the "voted successfully" responsd. So
        // the voted model is added to the user's voted lists, but won't be counted
        // in calculating the rate of the model in database. Have fun cheating!
        if ($this->isCheating($user->id, $request->submission_id, 'upvote')) {
            return response('voted successfully ', 200);
        }

        $submission->upvotes = $new_upvotes ?? $submission->upvotes;
        $submission->downvotes = $new_downvotes ?? $submission->downvotes;
        $submission->rate = rate($new_upvotes ?? $submission->upvotes, $new_downvotes ?? $submission->downvotes, $submission->created_at);

        DB::table('submissions')->where('id', $submission->id)->update([
            'upvotes'   => $submission->upvotes,
            'downvotes' => $submission->downvotes,
            'rate'      => $submission->rate,
        ]);

        $this->putSubmissionInTheCache($submission);

        return response('voted successfully ', 200);
    }

    /**
     * Adds the downvote record for the auth user and (if the user is not trying to cheat) updates the vote points and rate
     * for the submission model.
     *
     * @return response
     */
    public function downVote(Request $request)
    {
        $this->validate($request, [
            'submission_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $submission = $this->getSubmissionById($request->submission_id);

        try {
            if ($request->previous_vote == 'downvote') {
                $new_downvotes = ($submission->downvotes - 1);
                $user->submissionDownvotes()->detach($request->submission_id);
            } elseif ($request->previous_vote == 'upvote') {
                $new_downvotes = ($submission->downvotes + 1);
                $new_upvotes = ($submission->upvotes - 1);
                $user->submissionUpvotes()->detach($request->submission_id);
                $user->submissionDownvotes()->attach($request->submission_id, [
                    'ip_address' => getRequestIpAddress(),
                ]);
            } else {
                $new_downvotes = ($submission->downvotes + 1);
                $user->submissionDownvotes()->attach($request->submission_id, [
                    'ip_address' => getRequestIpAddress(),
                ]);
            }

            $this->updateUserDownVotesRecords($user->id, $submission->owner->id, $request->previous_vote, $request->submission_id);
        } catch (\Exception $e) {
            return response('invalid request', 500);
        }

        // If the user is cheating, we just send the "voted successfully" responsd. So
        // the voted model is added to the user's voted lists, but won't be counted
        // in calculating the rate of the model in database. Have fun cheating!
        if ($this->isCheating($user->id, $request->submission_id, 'downvote')) {
            return response('voted successfully ', 200);
        }

        $submission->upvotes = $new_upvotes ?? $submission->upvotes;
        $submission->downvotes = $new_downvotes ?? $submission->downvotes;
        $submission->rate = rate($new_upvotes ?? $submission->upvotes, $new_downvotes ?? $submission->downvotes, $submission->created_at);

        DB::table('submissions')->where('id', $submission->id)->update([
            'upvotes'   => $submission->upvotes,
            'downvotes' => $submission->downvotes,
            'rate'      => $submission->rate,
        ]);

        $this->putSubmissionInTheCache($submission);

        return response('voted successfully ', 200);
    }

    /**
     * Checks to see if the user is cheating in voting.
     *
     * (Beta version)
     *
     * Check to see if there is any record of auth user's IP-address (with different user_id)
     * already voted on the targetted submission. If there is any, return true which means
     * the user is cheating, otherwise return false which means we can update the rate.
     *
     * @param int    $submission_id
     * @param int    $user_id
     * @param string $type
     *
     * @return bool
     */
    public function isCheating($user_id, $submission_id, $type = 'upvote')
    {
        // we don't want new registered users do downvotes and mess with the averate vote numbers, so:
        if ($type == 'downvote' && Auth::user()->created_at > Carbon::now()->subDays(3)) {
            return true;
        }

        if ($type === 'upvote') {
            $table = 'submission_upvotes';
        } else {
            $table = 'submission_downvotes';
        }

        return DB::table($table)->where([
            ['user_id', '!=', $user_id],
            ['submission_id', $submission_id],
            ['ip_address', getRequestIpAddress()],
        ])->exists();
    }
}
