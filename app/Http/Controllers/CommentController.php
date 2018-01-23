<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Comment;
use App\Events\CommentWasCreated;
use App\Events\CommentWasDeleted;
use App\Events\CommentWasPatched;
use App\Traits\CachableChannel;
use App\Traits\CachableComment;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use CachableSubmission, CachableUser, CachableComment, CachableChannel;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

    /**
     * Stores the submitted comment.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection $comment
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'body'          => 'required',
            'parent_id'     => 'required|integer',
            'submission_id' => 'required|integer',
        ]);

        if ($this->tooEarlyToCreate(3)) {
            return response("Looks like you're over doing it. You can't submit more than one comments per minute.", 500);
        }

        $submission = $this->getSubmissionById($request->submission_id);
        $author = Auth::user();
        $parentComment = ($request->parent_id > 0) ? $this->getCommentById($request->parent_id) : null;

        $comment = Comment::create([
            'body'          => $request->body,
            'user_id'       => $author->id,
            'channel_id'    => $submission->channel_id,
            'parent_id'     => $request->parent_id,
            'level'         => $request->parent_id == 0 ? 0 : ($parentComment->level + 1),
            'submission_id' => $submission->id,
            'rate'          => firstRate(),
            'upvotes'       => 1,
            'downvotes'     => 0,
            'edited_at'     => null,
        ]);

        event(new CommentWasCreated($comment, $submission, $author, $parentComment));

        $this->firstVote($author, $comment->id);

        // set proper relation values:
        $comment->owner = $author;
        $comment->children = [];

        return $comment;
    }

    /**
     * Paginates the comments of a submission.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'submission_slug' => 'required',
            'sort'            => 'required',
        ]);

        $submission = $this->getSubmissionBySlug($request->submission_slug);

        if ($request->sort == 'new') {
            return $submission->comments()
                        ->where('parent_id', 0)
                        ->orderBy('created_at', 'desc')
                        ->simplePaginate(20);
        }

        // Sort by default which is 'hot'
        return $submission->comments()
                        ->where('parent_id', 0)
                        ->orderBy('rate', 'desc')
                        ->simplePaginate(20);
    }

    /**
     * Up-votes on comment.
     *
     * @param collection $user
     * @param int        $comment_id
     *
     * @return void
     */
    protected function firstVote($user, $comment_id)
    {
        try {
            $user->commentUpvotes()->attach($comment_id, ['ip_address' => getRequestIpAddress()]);
            $upvotes = $this->commentUpvotesIds($user->id);
            array_push($upvotes, $comment_id);
            $this->updateCommentUpvotesIds($user->id, $upvotes);
        } catch (Exception $exception) {
            app('sentry')->captureException($exception);
        }
    }

    /**
     * Patches the comment record.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function patch(Request $request)
    {
        $this->validate($request, [
            'comment_id' => 'required|integer',
            'body'       => 'required',
        ]);

        $comment = Comment::findOrFail($request->comment_id);

        abort_unless($this->mustBeOwner($comment), 403);

        // make sure the body has changed
        if ($request->body == $comment->body) {
            return response('Comment has not been really edited.', 422);
        }

        $comment->update([
            'body'      => $request->body,
            'edited_at' => Carbon::now(),
        ]);

        event(new CommentWasPatched($comment, $comment->submission));

        return response('comment edited successfully', 200);
    }

    /**
     * Destroys the comment record from the database.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
        ]);

        $comment = $this->getCommentById($request->id);
        $submission = $this->getSubmissionById($comment->submission_id);
        abort_unless($this->mustBeOwner($comment), 403);

        event(new CommentWasDeleted($comment, $submission, true));

        $comment->forceDelete();

        return response('Comment deleted successfully.', 200);
    }

    /**
     * Whether or the user is breaking the time limit for creating another comment.
     *
     * @param int $limit_number
     *
     * @return mixed
     */
    protected function tooEarlyToCreate($limit_number)
    {
        // exclude white-listed users form this checking
        if ($this->mustBeWhitelisted()) {
            return false;
        }

        $comments_count = Activity::where([
            ['subject_type', 'App\Comment'],
            ['user_id', Auth::user()->id],
            ['name', 'created_comment'],
            ['created_at', '>=', Carbon::now()->subMinute()],
        ])->get()->count();

        return $comments_count >= $limit_number ? true : false;
    }
}
