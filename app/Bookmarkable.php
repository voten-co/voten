<?php

namespace App;

trait Bookmarkable
{
    /**
     * Fetch all bookmarks for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookmarks()
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    /**
     * Scope a query to records bookmarked by the given user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User                                  $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBookmarkedBy($query, User $user)
    {
        return $query->whereHas('bookmarks', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }

    /**
     * Determine if the model is bookmarked by the given user.
     *
     * @param User $user
     *
     * @return bool
     */
    public function isBookmarkedBy(User $user)
    {
        return $this->bookmarks()
                    ->where('user_id', $user->id)
                    ->exists();
    }

    /**
     * Have the authenticated user bookmark the model.
     * If the authenticated user has already bookmarked the model, un-bookmarks it.
     *
     * @return void
     */
    public function bookmark()
    {
        if ($this->isBookmarkedBy(auth()->user())) {
            $this->bookmarks()->where(['user_id' => auth()->id()])->delete();

            return 'unbookmarked';
        }

        $this->bookmarks()->save(
            new Bookmark(['user_id' => auth()->id()])
        );

        return 'bookmarked';
    }
}
