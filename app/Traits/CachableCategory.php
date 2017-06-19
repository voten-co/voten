<?php

namespace App\Traits;

use App\Category;
use Auth;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

trait CachableCategory
{
    /**
     * Fetches all the cachable data for the category and put it in the cache.
     *
     * @param int $id
     *
     * @return void
     */
    protected function cacheCategoryData($id)
    {
        $category = Category::where('id', $id)->firstOrFail();

        $categoryData = [
            'submissionsCount' => $category->submissions()->count(),
            'commentsCount'    => $category->comments()->count(),
            'subscribersCount' => $category->subscriptions()->count(),

            'mods' => $category->mods(),
        ];

        Redis::hmset('category.'.$id.'.data', $categoryData);

        return $categoryData;
    }

    /**
     * mods of the category (both moderators and administrators).
     *
     * @return array
     */
    protected function categoryMods($id)
    {
        if ($value = Redis::hget('category.'.$id.'.data', 'mods')) {
            return json_decode($value);
        }

        $result = $this->cacheCategoryData($id);

        return collect(json_decode($result['mods']));
    }

    /**
     * updates the mods records of the category.
     *
     * @param int $id
     * @param int $user_id
     *
     * @return void
     */
    protected function updateCategoryMods($id, $user_id, $add = true)
    {
        $category = $this->getCategoryById($id);

        // we need to make sure the cached data exists
        if (!Redis::hget('category.'.$id.'.data', 'mods')) {
            $this->cacheCategoryData($id);
        }

        Redis::hset('category.'.$id.'.data', 'mods', json_encode($category->mods()));
    }

    /**
     * Returns all the stats of the auth category.
     *
     * @param int $id
     *
     * @return Illuminate\Support\Collection
     */
    protected function categoryStats($id)
    {
        $stats = Redis::hmget('category.'.$id.'.data',
                        'submissionsCount', 'commentsCount', 'subscribersCount');

        // if category's data is not cached, then fetch it from database and then cache it
        if (json_decode($stats[0]) === null || json_decode($stats[1]) === null || json_decode($stats[2]) === null) {
            return $this->cacheCategoryData($id);
        }

        return collect([
            'submissionsCount' => json_decode($stats[0]),
            'commentsCount'    => json_decode($stats[1]),
            'subscribersCount' => json_decode($stats[2]),
        ]);
    }

    /**
     * updates the submissionsCount of the category.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateCategorySubmissionsCount($id, $number = 1)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('category.'.$id.'.data', 'submissionsCount')) {
            $this->cacheCategoryData($id);
        }

        Redis::hincrby('category.'.$id.'.data', 'submissionsCount', $number);
    }

    /**
     * updates the commentsCount of the category.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateCategoryCommentsCount($id, $number = 1)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('category.'.$id.'.data', 'commentsCount')) {
            $this->cacheCategoryData($id);
        }

        Redis::hincrby('category.'.$id.'.data', 'commentsCount', $number);
    }

    /**
     * updates the subscribersCount of the category.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateCategorySubscribersCount($id, $number = 1)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('category.'.$id.'.data', 'subscribersCount')) {
            $this->cacheCategoryData($id);
        }

        $subscribersCount = Redis::hincrby('category.'.$id.'.data', 'subscribersCount', $number);

        // for newbie categories we update on each new subscription
        if ($subscribersCount < 1000) {
            DB::table('categories')->where('id', $id)->update(['subscribers' => $subscribersCount]);

            return;
        }
        // but for major ones, we do this once a 100 times
        if (($subscribersCount % 100) === 0) {
            DB::table('categories')->where('id', $id)->update(['subscribers' => $subscribersCount]);
        }
    }

    /**
     * Returns the Category model using the $id. First it tries to fetch it from Cache. In case it doesn't
     * exist in the cache, fetches it from the database, and then put it in the cache and then return it.
     *
     * @param string $id
     *
     * @return Illuminate\Support\Collection
     */
    protected function getCategoryById($id)
    {
        return Cache::remember('category.id.'.$id, 60 * 60 * 24, function () use ($id) {
            return Category::withTrashed()->findOrFail($id);
        });
    }

    /**
     * Returns the Category model using the name. First it tries to fetch it from Cache. In case it doesn't
     * exist in the cache, fetches it from the database, and then put it in the cache and then return it.
     *
     * @param string $name
     *
     * @return Illuminate\Support\Collection
     */
    protected function getCategoryByName($name)
    {
        return Cache::remember('category.name.'.$name, 60 * 60 * 24, function () use ($name) {
            return Category::withTrashed()->where('name', $name)->firstOrFail();
        });
    }

    /**
     * Put the category infto the cache. In case it already exists, updates it. Otherwise adds it.
     *
     * @param Illuminate\Support\Collection $category
     */
    protected function putCategoryInTheCache($category)
    {
        Cache::put('category.id.'.$category->id, $category, 60 * 60 * 24);

        Cache::put('category.name.'.$category->name, $category, 60 * 60 * 24);
    }

    /**
     * returns the IDs of the default categories.
     *
     * @return array
     */
    public function getDefaultCategories()
    {
        return Cache::remember('default-categories-ids', 60 * 60 * 24, function () {
            return \App\Suggested::groupBy('category_id')->select('id', 'category_id')->pluck('category_id');
        });
    }

    /**
     * returns the IDs of the default categories.
     *
     * @return array
     */
    protected function getDefaultCategoryRecords()
    {
        return Cache::remember('default-categories-records', 60 * 60 * 24, function () {
            $ids = \App\Suggested::groupBy('category_id')->select('id', 'category_id')->pluck('category_id');

            return Category::whereIn('id', $ids)->get();
        });
    }
}
