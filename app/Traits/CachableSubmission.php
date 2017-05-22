<?php

namespace App\Traits;

use App\Submission;
use Illuminate\Support\Facades\Cache;

trait CachableSubmission
{
    /**
     * Returns the Submission model using the id. First it tries to fetch it from Cache. In case it does not
     * exist in the cache, fetches it from the database, and then put it in the cache and then return it.
     *
     * @param int $id
     *
     * @return Illuminate\Support\Collection
     */
    protected function getSubmissionById($id)
    {
        return Cache::remember('submission.id.'.$id, 60 * 60 * 24, function () use ($id) {
            return Submission::withTrashed()->findOrFail($id);
        });
    }

    /**
     * Returns the Submission model using the slug. First it tries to fetch it from Cache. In case it doesn't
     * exist in the cache, fetches it from the database, and then put it in the cache and then return it.
     *
     * @param string $slug
     *
     * @return Illuminate\Support\Collection
     */
    protected function getSubmissionBySlug($slug)
    {
        return Cache::remember('submission.slug.'.$slug, 60 * 60 * 24, function () use ($slug) {
            return Submission::withTrashed()->where('slug', $slug)->firstOrFail();
        });
    }

    /**
     * Removes the submission from cache.
     *
     * @param Illuminate\Support\Collection $submission
     */
    protected function removeSubmissionFromCache($submission)
    {
        Cache::forget('submission.id.'.$submission->id);

        Cache::forget('submission.slug.'.$submission->slug);
    }

    /**
     * Put the submission infto the cache. In case it already exists, updates it. Otherwise adds it.
     *
     * @param Illuminate\Support\Collection $submission
     */
    protected function putSubmissionInTheCache($submission)
    {
        Cache::put('submission.id.'.$submission->id, $submission, 60 * 60 * 24);

        Cache::put('submission.slug.'.$submission->slug, $submission, 60 * 60 * 24);
    }
}
