<?php

namespace App\Traits;

use App\Gif;
use App\Photo;
use App\Submission;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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
     * Up-votes on submission.
     *
     * @param collection $user
     * @param int        $submission_id
     *
     * @return void
     */
    protected function firstVote($submission_id)
    {
        $user = Auth::user();

        try {
            $user->submissionUpvotes()->attach($submission_id, ['ip_address' => getRequestIpAddress()]);

            $upvotes = $this->submissionUpvotesIds($user->id);

            array_push($upvotes, $submission_id);

            $this->updateSubmissionUpvotesIds($user->id, $upvotes);
        } catch (\Exception $exception) {
            app('sentry')->captureException($exception);
        }
    }

    /**
     * @param  Request instance
     *
     * @return json data
     */
    protected function linkSubmission(Request $request)
    {
        try {
            $apiURL = 'https://midd.voten.co/link-submission?url='.urlencode($request->url);

            $info = json_decode(file_get_contents($apiURL));

            return [
                'url'           => $info->url,
                'title'         => $info->title,
                'description'   => $info->description,
                'type'          => $info->type,
                'embed'         => $info->embed,
                'img'           => $info->img,
                'thumbnail'     => $info->thumbnail,
                'providerName'  => $info->providerName,
                'publishedTime' => $info->publishedTime,
                'domain'        => $info->domain,
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

    /**
     * Fetches the title from an external URL.
     *
     * @param  $url
     *
     * @return string title
     */
    protected function getTitle($url)
    {
        $apiURL = 'https://midd.voten.co/embed/title?url='.$url;

        try {
            $title = file_get_contents($apiURL);
        } catch (\Exception $exception) {
            return res(400, 'Invalid URL');
        }

        return $title;
    }

    /**
     * whether or not the title has already been posted.
     *
     * @return bool
     */
    protected function isDuplicateTitle($title, $channel)
    {
        return Submission::withTrashed()->where('title', $title)->where('channel_name', $channel)->exists();
    }

    /**
     * has user upvoted for this submission before?
     *
     * @return bool
     */
    protected function hasUserUpvotedSubmission($user_id, $submission_id)
    {
        return DB::table('submission_upvotes')->where('user_id', $user_id)->where('submission_id', $submission_id)->exists();
    }
}
