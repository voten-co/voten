<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Events\CommentCreated;
use App\Notifications\CommentReplied;
use App\Notifications\SubmissionReplied;
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

        if ($request->parent_id > 0) {
            $parentComment = $this->getCommentById($request->parent_id);
        }

        $comment = Comment::create([
            'body'          => $request->body,
            'user_id'       => $author->id,
            'category_id'   => $submission->category_id,
            'parent_id'     => $request->parent_id,
            'level'         => $request->parent_id == 0 ? 0 : ($parentComment->level + 1),
            'submission_id' => $submission->id,
            'rate'          => firstRate(),
        ]);

        // it's better to move this to the CommentWasCreated Event
        $this->updateUserCommentsCount($comment->user_id);
        $this->updateCategoryCommentsCount($comment->category_id);
        // end

        try {
            $this->firstVote($author, $comment->id);
        } catch (Exception $exception) {
            app('sentry')->captureException($exception);
        }

        $submission->update([
            'comments_number' => ($submission->comments_number + 1),
        ]);

        $this->putSubmissionInTheCache($submission);

        // if the commenter is banned from submitting to this cateogry (or "everywhere") we soft-delete the comment
        // without letting him know. This should keep spammers busy over nothing.
        if ($this->isUserBanned($author->id, $submission->category_name)) {
            $comment->delete();
        } else {
            // broadcast the comment to the people online in the conversation
            event(new CommentCreated($comment));

            if (isset($parentComment) && !$this->mustBeOwner($parentComment)) {
                $parentComment->notifiable->notify(new CommentReplied($submission, $comment));
            } elseif (!$this->mustBeOwner($submission)) {
                $submission->notifiable->notify(new SubmissionReplied($submission, $comment));
            }
        }

        return Comment::withTrashed()->find($comment->id);
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
        $user->commentUpvotes()->attach($comment_id, ['ip_address' => getRequestIpAddress()]);

        $upvotes = $this->commentUpvotesIds($user->id);

        array_push($upvotes, $comment_id);

        $this->updateCommentUpvotesIds($user->id, $upvotes);
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

        abort_unless($this->mustBeOwner($comment), 403);

        $submission = $this->getSubmissionById($comment->submission_id);

        // it's better to use the CommentWasDeleted event later for this
        $this->updateUserCommentsCount($comment->user_id, -1);
        $this->updateCategoryCommentsCount($comment->category_id, -1);
        $this->removeCommentFromCache($comment);
        \App\Report::where([
            'reportable_id'   => $comment->id,
            'reportable_type' => 'App\Comment',
        ])->forceDelete();
        // end

        $comment->forceDelete();

        $submission->update([
            'comments_number' => ($submission->comments_number - 1),
        ]);

        $this->putSubmissionInTheCache($submission);

        return response('Successfully deleted', 200);
    }
}
