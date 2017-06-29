<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Events\CommentWasCreated;
use App\Events\CommentWasDeleted;
use App\Events\CommentWasPatched;
use App\Traits\CachableCategory;
use App\Traits\CachableComment;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use Auth;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use CachableSubmission, CachableUser, CachableComment, CachableCategory;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

    /**
     * Stores the submitted comment.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Support\Collection $comment
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'body'          => 'required',
            'parent_id'     => 'required|integer',
            'submission_id' => 'required|integer',
        ]);

        $submission = $this->getSubmissionById($request->submission_id);
        $author = Auth::user();
        $parentComment = ($request->parent_id > 0) ? $this->getCommentById($request->parent_id) : null;

        $comment = Comment::create([
            'body'          => $request->body,
            'user_id'       => $author->id,
            'category_id'   => $submission->category_id,
            'parent_id'     => $request->parent_id,
            'level'         => $request->parent_id == 0 ? 0 : ($parentComment->level + 1),
            'submission_id' => $submission->id,
            'rate'          => firstRate(),
            'upvotes'       => 1,
            'downvotes'     => 0
        ]);

        event(new CommentWasCreated($comment, $submission, $author, $parentComment));

        $this->firstVote($author, $comment->id);

        // set proper relation values:
        $comment->owner = $author;
        $comment->children = [];

        return $comment;
    }

    /**
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Support\Collection
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
     * patches the comment model.
     *
     * @return response
     */
    public function patch(Request $request)
    {
        $this->validate($request, [
            'comment_id' => 'required|integer',
            'body'       => 'required',
        ]);

        $comment = Comment::findOrFail($request->comment_id);

        abort_unless($this->mustBeOwner($comment), 403);

        $comment->update([
            'body' => $request->body,
        ]);

        event(new CommentWasPatched($comment));

        return response('comment edited successfully', 200);
    }

    /**
     * Destroys the comment record from the database.
     *
     * @param Illuminate\Http\Request $request
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

        event(new CommentWasDeleted($comment, $submission));

        $comment->forceDelete();

        return response('Successfully deleted', 200);
    }
}
