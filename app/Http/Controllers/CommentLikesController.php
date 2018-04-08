<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\CachableUser;
use App\Traits\CachableComment;

class CommentLikesController extends Controller
{
    use CachableUser, CachableComment;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Like/undo-like
     *
     * @param string $comment_id
     */
    public function like(Comment $comment)
    {
        if ($undoLike = $this->hasAlreadyLiked($comment->id)) {
            Auth::user()->likedComments()->detach($comment->id);

            $comment->likes--;
        } else {
            Auth::user()->likedComments()->attach($comment->id, [
                'ip_address' => getRequestIpAddress(),
            ]);

            $comment->likes++;
        }

        $this->updateCache(
            Auth::user()->id, 
            $comment->owner->id, 
            $undoLike, 
            $comment->id,
            $isCheating = $this->isCheating($comment->id)
        );

        if ($this->isCheating($comment->id)) {
            return $undoLike ? res(200, "Undid like successfully.") : res(201, "Liked successfully."); 
        }

        DB::table('comments')->where('id', $comment->id)->update([
            'likes' => $comment->likes,
            'rate' => rate($comment->likes, $comment->created_at),
        ]);

        $this->putCommentInTheCache($comment);

        return $undoLike ? res(200, "Undid like successfully.") : res(201, "Liked successfully.");
    }

    /**
     * Is the item previosly liked by the authenticated user 
     * 
     * @param integer $comment_id 
     * @return boolean 
     */
    protected function hasAlreadyLiked($comment_id)
    {
        return DB::table('comment_likes')->where([
            ['user_id', Auth::id()], 
            ['comment_id', $comment_id]
        ])->exists(); 
    }

    /**
     * Has the authenticated user already liked the item using another account? 
     * 
     * @param integer $comment_id 
     * @return boolean 
     */
    protected function isCheating($comment_id)
    {
        // white-listed users are off the hook 
        if ($this->mustBeWhitelisted()) {
            return false;
        }

        return DB::table('comment_likes')->where([
            ['user_id', '!=', Auth::id()],
            ['comment_id', $comment_id],
            ['ip_address', getRequestIpAddress()],
        ])->exists();
    }

    /**
     * updates the like rocords of the user in Redis.
     *
     * @param int   $voter_id
     * @param int   $author_id
     * @param bool  $alreadyLiked
     * @param int   $comment_id
     * @param bool  $isCheating
     *
     * @return void
     */
    protected function updateCache($voter_id, $author_id, $alreadyLiked, $comment_id, $isCheating)
    {
        $likes = $this->commentLikesIds($voter_id);

        if ($alreadyLiked) {
            $likes = array_values(array_diff($likes, [$comment_id]));
        } else {
            array_push($likes, $comment_id);
        }

        if (! $isCheating) {
            $this->updateCommentXp($author_id, $alreadyLiked ? -1 : 1);
        }

        $this->updateCommentLikesIds($voter_id, $likes);
    }
}
