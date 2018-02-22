<?php

/**
 * All the general filtering functions that we might need in different
 * controllers. For example for excluding the hidden submissions
 * from a collection of submissions.
 */

namespace App;

use Auth;

trait Filters
{
    /**
     * Makes sure the collection is sugare coded:
     * 1. No blocked submission
     * 2. Doesn't containt NSFW (if the auth user doesn't wanna see it).
     *
     * @param Illuminate\Support\Collection
     * @param Illuminate\Pagination\Paginator
     *
     * @return Illuminate\Support\Collection
     * @return Illuminate\Pagination\Paginator
     */
    protected function sugarFilter($collection)
    {
        // in case the user is not authinticated there's nothing much this method can do, so let's just return what we recieved
        if (!Auth::check()) {
            return $collection;
        }

        if (get_class($collection) === 'Illuminate\Pagination\Paginator') {
            return $collection->setCollection($this->removeHiddens($collection));
        }

        return $this->removeHiddens($collection);
    }

    /**
     * Removes the submisisons that user has hidden.
     *
     * @return Illuminate\Support\Collection $collection
     */
    protected function removeHiddens($collection)
    {
        $myHiddenSubmissions = collect($this->hiddenSubmissions());

        return $collection->filter(function ($value, $key) use ($myHiddenSubmissions) {
            return !$myHiddenSubmissions->contains($value->id);
        });
    }

    /**
     * Collection of $users minus the ones auth
     * user is already in a conversation with.
     *
     * @param Illuminate\Support\Collection
     *
     * @return Illuminate\Support\Collection
     */
    protected function noAlreadyContact($collection)
    {
        $myContacts = Auth::user()->myContactIds();

        return $collection->filter(function ($value, $key) use ($myContacts) {
            return !$myContacts->contains($value->id) && Auth::user()->id != $value->id;
        });
    }

    /**
     * Collection of $users minus the ones auth user has blocked.
     *
     * @param Illuminate\Support\Collection $collection
     *
     * @return Illuminate\Support\Collection $collection
     */
    protected function UsersFilter($collection)
    {
        $myHiddenUsers = Auth::user()->blockedUsers();

        return $collection->filter(function ($value, $key) use ($myHiddenUsers) {
            return !$myHiddenUsers->contains($value->id);
        });
    }

    /**
     * Removes the subscribed channels from the colleciton.
     *
     * @param Illuminate\Support\Collection $collection
     *
     * @return Illuminate\Support\Collection $collection
     */
    protected function noSubscribedFilter($collection)
    {
        $subscribedChannels = collect($this->subscriptions());

        if (get_class($collection) === 'Illuminate\Pagination\Paginator' || get_class($collection) === 'Illuminate\Pagination\LengthAwarePaginator') {
            return $collection->setCollection(
                $collection->filter(function ($value, $key) use ($subscribedChannels) {
                    return !$subscribedChannels->contains($value->id);
                })->unique()
            );
        }

        return $collection->filter(function ($value, $key) use ($subscribedChannels) {
            return !$subscribedChannels->contains($value->id);
        })->unique();
    }
}
