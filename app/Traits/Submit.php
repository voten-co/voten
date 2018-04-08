<?php

namespace App\Traits;

use App\Gif;
use App\Photo;
use App\Submission;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Embed\Embed;

trait Submit
{    
    /**
     * Creates a unique slug for the submission.
     *
     * @param string $title
     *
     * @return string
     */
    protected function slug($title)
    {
        $slug = str_slug($title);
        $submissions = Submission::withTrashed()->where('slug', 'like', $slug.'%')->get();

        if (!$submissions->contains('slug', $slug)) {
            return $slug;
        }

        $slugNumber = 1;
        $newSlug = $slug;

        while ($submissions->contains('slug', $newSlug)) {
            $newSlug = $slug.'-'.$slugNumber;
            $slugNumber++;
        }

        return $newSlug;
    }

    /**
     * Likes submission.
     *
     * @param collection $user
     * @param int        $submission_id
     *
     * @return void
     */
    protected function firstLike($submission_id)
    {
        $user = Auth::user();

        try {
            $user->likedSubmissions()->attach($submission_id, ['ip_address' => getRequestIpAddress()]);

            $likes = $this->submissionLikesIds($user->id);

            array_push($likes, $submission_id);

            $this->updateSubmissionLikesIds($user->id, $likes);
        } catch (\Exception $exception) {}
    }

    /**
     * @param  Request instance
     *
     * @return json data
     */
    protected function linkSubmission(Request $request)
    {
        try {
            $info = Embed::create($request->url);

            return [
                'url'           => $info->url,
                'title'         => $info->title,
                'description'   => $info->description,
                'type'          => $info->type,
                'embed'         => $info->embed,
                'img'           => $this->downloadImg($info->image),
                'thumbnail'     => $this->createThumbnail($info->image, 600, null, "submissions/link/thumbs"),
                'providerName'  => $info->providerName,
                'publishedTime' => $info->publishedTime,
                'domain'        => domain($request->url),
            ];
        } catch (\Exception $e) {
            return [
                'url'           => $request->url,
                'title'         => $request->title,
                'description'   => null,
                'type'          => 'link',
                'embed'         => null,
                'img'           => null,
                'thumbnail'     => null,
                'providerName'  => null,
                'publishedTime' => null,
                'domain'        => domain($request->url),
            ];
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function imgSubmission(Request $request)
    {
        $photo = Photo::where('id', $request->input('photos_id')[0])->firstOrFail();

        return [
            'path'           => $photo->path,
            'thumbnail_path' => $photo->thumbnail_path,
            'album'          => (count($request->input('photos_id')) > 1),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function gifSubmission(Request $request)
    {
        $gif = Gif::findOrFail($request->gif_id);

        return [
            'mp4_path'       => $gif->mp4_path,
            'thumbnail_path' => $gif->thumbnail_path,
        ];
    }

    /**
     * @param Request $request $request
     *
     * @return array
     */
    protected function textSubmission(Request $request)
    {
        return array_only($request->all(), ['text']);
    }
}
