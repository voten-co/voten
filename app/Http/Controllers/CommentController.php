<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Comment;
use App\Events\CommentWasCreated;
use App\Events\CommentWasDeleted;
use App\Events\CommentWasPatched;
use App\Http\Resources\CommentResource;
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
            'body'          => 'required|string|max:5000',
            'parent_id'     => 'nullable|integer',
            'submission_id' => 'required|integer|exists:submissions,id',
        ]);

        if ($this->tooEarlyToCreate(3)) {
            return res(429, "Looks like you're over doing it. You can't submit more than 3 comments per minute");
        }

        $submission = $this->getSubmissionById($request->submission_id);
        $author = Auth::user();
        $parentComment = (!is_null($request->parent_id) && $request->parent_id > 0) ? $this->getCommentById($request->parent_id) : null;

        $comment = Comment::create([
            'body'          => $request->body,
            'user_id'       => $author->id,
            'channel_id'    => $submission->channel_id,
            'parent_id'     => $request->parent_id,
            'level'         => $request->parent_id == null ? 0 : ($parentComment->level + 1),
            'submission_id' => $submission->id,
            'rate'          => firstRate(),
            'upvotes'       => 1,
            'downvotes'     => 0,
            'edited_at'     => null,
        ]);

        event(new CommentWasCreated($comment, $submission, $author, $parentComment));

        $this->firstVote($author, $comment->id);

        // save a query by setting the author:
        $comment->owner = $author;

        return new CommentResource($comment);
    }

    /**
     * Paginates the comments of a submission.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request, $submission_id)
    {
        $this->validate($request, [
            'sort'          => 'nullable|in:hot,new',
            'page'          => 'integer|min:1',
        ]);

        if ($request->sort == 'new') {
            return CommentResource::collection(
                Comment::where([
                    ['submission_id', $submission_id],
                    ['parent_id', 0],
                ])->orderBy('created_at', 'desc')->simplePaginate(20)
            );
        }

        // Sort by default which is 'hot'
        return CommentResource::collection(
            Comment::where([
                ['submission_id', $submission_id],
                ['parent_id', 0],
            ])->orderBy('rate', 'desc')->simplePaginate(20)
        );
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
    public function patch($comment_id)
    {
        $this->validate(request(), [
            'body' => 'required|string|max:5000',
        ]);

        $comment = $this->getCommentById($comment_id);

        abort_unless($this->mustBeOwner($comment), 403);

        // make sure the body has changed
        if (request('body') == $comment->body) {
            return res(200, 'The body is the same as it was before!.');
        }

        $comment->update([
            'body'      => request('body'),
            'edited_at' => Carbon::now(),
        ]);

        event(new CommentWasPatched($comment, $comment->submission));

        return res(200, 'Comment edited successfully');
    }

    /**
     * Destroys the comment record from the database.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy($comment_id)
    {
        $comment = $this->getCommentById($comment_id);

        abort_unless($this->mustBeOwner($comment), 403);

        $submission = $this->getSubmissionById($comment->submission_id);

        event(new CommentWasDeleted($comment, $submission, true));

        $comment->forceDelete();

        return res(200, 'Comment deleted successfully.');
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
        // white-listed users are fine
        if ($this->mustBeWhitelisted()) {
            return false;
        }

        $posted_comments_count = Activity::where([
            ['subject_type', 'App\Comment'],
            ['user_id', Auth::user()->id],
            ['name', 'created_comment'],
            ['created_at', '>=', Carbon::now()->subMinute()],
        ])->get()->count();

        return $posted_comments_count >= $limit_number ? true : false;
    }
}
