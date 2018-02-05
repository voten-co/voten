<?php

namespace App\Http\Controllers;

use App\Traits\CachableComment;
use App\Traits\CachableUser;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class CommentVotesController extends Controller
{
    use CachableUser, CachableComment;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * updates the vote rocords of the user in Redis.
     *
     * @param int   $voter_id
     * @param mixed $previous_vote
     * @param int   $comment_id
     *
     * @return void
     */
    protected function updateUserUpVotesRecords($voter_id, $author_id, $previous_vote, $comment_id)
    {
        $upvotes = $this->commentUpvotesIds($voter_id);

        // remove the $comment_id from the array
        if ($previous_vote == 'upvote') {
            $upvotes = array_values(array_diff($upvotes, [$comment_id]));
            $this->updateCommentXp($author_id, -1);
        }

        // remove the $comment_id from the downvotes array and add it to the upvotes array
        if ($previous_vote == 'downvote') {
            $downvotes = $this->commentDownvotesIds($voter_id);

            $downvotes = array_values(array_diff($downvotes, [$comment_id]));
            array_push($upvotes, $comment_id);

            $this->updateCommentDownvotesIds($voter_id, $downvotes);
            $this->updateCommentXp($author_id, 2);
        }

        // add the $comment_id to the array
        if ($previous_vote == null) {
            array_push($upvotes, $comment_id);
            $this->updateCommentXp($author_id, 1);
        }

        $this->updateCommentUpvotesIds($voter_id, $upvotes);
    }

    /**
     * updates the vote rocords of the user in Redis.
     *
     * @param int   $voter_id
     * @param int   $author_id
     * @param mixed $previous_vote
     * @param int   $comment_id
     *
     * @return void
     */
    protected function updateUserDownVotesRecords($voter_id, $author_id, $previous_vote, $comment_id)
    {
        $downvotes = $this->commentDownvotesIds($voter_id);

        // remove the $comment_id from the array
        if ($previous_vote == 'downvote') {
            $downvotes = array_values(array_diff($downvotes, [$comment_id]));
            $this->updateCommentXp($author_id, 1);
        }

        // remove the $comment_id from the downvotes array and add it to the upvotes array
        if ($previous_vote == 'upvote') {
            $upvotes = $this->commentUpvotesIds($voter_id);

            $upvotes = array_values(array_diff($upvotes, [$comment_id]));
            array_push($downvotes, $comment_id);

            $this->updateCommentUpvotesIds($voter_id, $upvotes);
            $this->updateCommentXp($author_id, -2);
        }

        // add the $comment_id to the array
        if ($previous_vote == null) {
            array_push($downvotes, $comment_id);
            $this->updateCommentXp($author_id, -1);
        }

        $this->updateCommentDownvotesIds($voter_id, $downvotes);
    }

    /**
     * Adds the upvote record for the auth user and (if the user is not trying to cheat) updates the vote points and rate
     * for the comment model.
     *
     * @return response
     */
    public function upVote(Request $request)
    {
        $this->validate($request, [
            'comment_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $comment = $this->getCommentById($request->comment_id);

        try {
            if ($request->previous_vote == 'upvote') {
                $new_upvotes = ($comment->upvotes - 1);
                $user->commentUpvotes()->detach($request->comment_id);
            } elseif ($request->previous_vote == 'downvote') {
                $new_upvotes = ($comment->upvotes + 1);
                $new_downvotes = ($comment->downvotes - 1);
                $user->commentDownvotes()->detach($request->comment_id);
                $user->commentUpvotes()->attach($request->comment_id, [
                    'ip_address' => getRequestIpAddress(),
                ]);
            } else {
                $new_upvotes = ($comment->upvotes + 1);
                $user->commentUpvotes()->attach($request->comment_id, [
                    'ip_address' => getRequestIpAddress(),
                ]);
            }

            $this->updateUserUpVotesRecords(
                $user->id, $comment->owner->id, $request->previous_vote, $request->comment_id
            );
        } catch (\Exception $e) {
            return response('invalid request', 500);
        }

        // If the user is cheating, we just send the "voted successfully" responsd. So
        // the voted model is added to the user's voted lists, but won't be counted
        // in calculating the rate of the model in database. Have fun cheating!
        if ($this->isCheating($user->id, $request->comment_id, 'upvote')) {
            return response('voted successfully ', 200);
        }

        $comment->upvotes = $new_upvotes ?? $comment->upvotes;
        $comment->downvotes = $new_downvotes ?? $comment->downvotes;
        $comment->rate = rate($new_upvotes ?? $comment->upvotes, $new_downvotes ?? $comment->downvotes, $comment->created_at);

        DB::table('comments')->where('id', $comment->id)->update([
            'upvotes'   => $comment->upvotes,
            'downvotes' => $comment->downvotes,
            'rate'      => $comment->rate,
        ]);

        $this->putCommentInTheCache($comment);

        return response('voted successfully ', 200);
    }

    /**
     * Adds the downvote record for the auth user and (if the user is not trying to cheat) updates the vote points and rate
     * for the comment model.
     *
     * @return response
     */
    public function downVote(Request $request)
    {
        $this->validate($request, [
            'comment_id' => 'required|integer',
        ]);

        $user = Auth::user();
        $comment = $this->getCommentById($request->comment_id);

        try {
            if ($request->previous_vote == 'downvote') {
                $new_downvotes = ($comment->downvotes - 1);
                $user->commentDownvotes()->detach($request->comment_id);
            } elseif ($request->previous_vote == 'upvote') {
                $new_downvotes = ($comment->downvotes + 1);
                $new_upvotes = ($comment->upvotes - 1);
                $user->commentUpvotes()->detach($request->comment_id);
                $user->commentDownvotes()->attach($request->comment_id, [
                'ip_address' => getRequestIpAddress(),
            ]);
            } else {
                $new_downvotes = ($comment->downvotes + 1);
                $user->commentDownvotes()->attach($request->comment_id, [
                'ip_address' => getRequestIpAddress(),
            ]);
            }

            $this->updateUserDownVotesRecords($user->id, $comment->owner->id, $request->previous_vote, $request->comment_id);
        } catch (\Exception $e) {
            return response('invalid request', 500);
        }

        // If the user is cheating, we just send the "voted successfully" responsd. So
        // the voted model is added to the user's voted lists, but won't be counted
        // in calculating the rate of the model in database. Have fun cheating!
        if ($this->isCheating($user->id, $request->comment_id, 'downvote')) {
            return response('voted successfully ', 200);
        }

        $comment->upvotes = $new_upvotes ?? $comment->upvotes;
        $comment->downvotes = $new_downvotes ?? $comment->downvotes;
        $comment->rate = rate($new_upvotes ?? $comment->upvotes, $new_downvotes ?? $comment->downvotes, $comment->created_at);

        DB::table('comments')->where('id', $comment->id)->update([
            'upvotes'   => $comment->upvotes,
            'downvotes' => $comment->downvotes,
            'rate'      => $comment->rate,
        ]);

        $this->putCommentInTheCache($comment);

        return response('voted successfully ', 200);
    }

    /**
     * Checks to see if the user is cheating in voting.
     *
     * (Beta version)
     *
     * Check to see if there is any record of auth user's IP-address (with different user_id)
     * already voted on the targetted comment. If there is any, return true which means
     * the user is cheating, otherwise return false which means we can update the rate.
     *
     * @param int    $comment_id
     * @param int    $user_id
     * @param string $type
     *
     * @return bool
     */
    public function isCheating($user_id, $comment_id, $type = 'upvote')
    {
        // we don't want new registered users do downvotes and mess with the averate vote numbers, so:
        if ($type == 'downvote' && Auth::user()->created_at > Carbon::now()->subDays(3)) {
            return true;
        }

        if ($type === 'upvote') {
            $table = 'comment_upvotes';
        } else {
            $table = 'comment_downvotes';
        }

        return DB::table($table)->where([
            ['user_id', '!=', $user_id],
            ['comment_id', $comment_id],
            ['ip_address', getRequestIpAddress()],
        ])->exists();
    }
}
