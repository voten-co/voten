<?php

namespace App\Traits;

use App\Comment;
use Illuminate\Support\Facades\Cache;

trait CachableComment
{
    /**
     * Returns the Comment model using the id. First it tries to fetch it from Cache. In case it does not
     * exist in the cache, fetches it from the database, and then put it in the cache and then return it.
     *
     * @param int $id
     *
     * @return Illuminate\Support\Collection
     */
    protected function getCommentById($id)
    {
        return Cache::remember('comment.id.'.$id, 60 * 60 * 12, function () use ($id) {
            return Comment::withTrashed()->findOrFail($id);
        });
    }

    /**
     * Removes the Comment from cache.
     *
     * @param Illuminate\Support\Collection $comment
     */
    protected function removeCommentFromCache($comment)
    {
        Cache::forget('comment.id.'.$comment->id);
    }

    /**
     * Put the Comment infto the cache. In case it already exists, updates it. Otherwise adds it.
     *
     * @param Illuminate\Support\Collection $comment
     */
    protected function putCommentInTheCache($comment)
    {
        Cache::put('comment.id.'.$comment->id, $comment, 60 * 60 * 12);
    }
}
