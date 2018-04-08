<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Submission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\CachableUser;
use App\Traits\CachableSubmission;

class SubmissionLikesController extends Controller
{
    use CachableUser, CachableSubmission;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Like/undo-like
     *
     * @param string $submission_id
     */
    public function like(Submission $submission)
    {
        if ($undoLike = $this->hasAlreadyLiked($submission->id)) {
            Auth::user()->likedSubmissions()->detach($submission->id);

            $submission->likes--;
        } else {
            Auth::user()->likedSubmissions()->attach($submission->id, [
                'ip_address' => getRequestIpAddress(),
            ]);

            $submission->likes++;
        }

        $this->updateCache(
            Auth::user()->id, 
            $submission->owner->id, 
            $undoLike, 
            $submission->id,
            $isCheating = $this->isCheating($submission->id)
        );

        if ($isCheating) {
            return $undoLike ? res(200, "Undid like successfully.") : res(201, "Liked successfully."); 
        }

        DB::table('submissions')->where('id', $submission->id)->update([
            'likes' => $submission->likes,
            'rate' => rate($submission->likes, $submission->created_at),
        ]);

        $this->putSubmissionInTheCache($submission);

        return $undoLike ? res(200, "Undid like successfully.") : res(201, "Liked successfully.");
    }

    /**
     * Is the item previosly liked by the authenticated user 
     * 
     * @param integer $submission_id 
     * @return boolean 
     */
    protected function hasAlreadyLiked($submission_id)
    {
        return DB::table('submission_likes')->where([
            ['user_id', Auth::id()], 
            ['submission_id', $submission_id]
        ])->exists(); 
    }

    /**
     * Has the authenticated user already liked the item using another account? 
     * 
     * @param integer $submission_id 
     * @return boolean 
     */
    protected function isCheating($submission_id)
    {
        // white-listed users are off the hook 
        if ($this->mustBeWhitelisted()) {
            return false;
        }

        return DB::table('submission_likes')->where([
            ['user_id', '!=', Auth::id()],
            ['submission_id', $submission_id],
            ['ip_address', getRequestIpAddress()],
        ])->exists();
    }

    /**
     * updates the like rocords of the user in Redis.
     *
     * @param int   $voter_id
     * @param int   $author_id
     * @param bool  $alreadyLiked
     * @param int   $submission_id
     * @param bool  $isCheating
     *
     * @return void
     */
    protected function updateCache($voter_id, $author_id, $alreadyLiked, $submission_id, $isCheating)
    {
        $likes = $this->submissionLikesIds($voter_id);

        if ($alreadyLiked) {
            $likes = array_values(array_diff($likes, [$submission_id]));
        } else {
            array_push($likes, $submission_id);
        }

        if (! $isCheating) {
            $this->updateSubmissionXp($author_id, $alreadyLiked ? -1 : 1);
        }

        $this->updateSubmissionLikesIds($voter_id, $likes);
    }
}