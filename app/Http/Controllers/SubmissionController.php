<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Channel;
use App\Events\SubmissionWasCreated;
use App\Events\SubmissionWasDeleted;
use App\Filters;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\SubmissionResource;
use App\Photo;
use App\PhotoTools;
use App\Rules\NotBannedFromChannel;
use App\Rules\NotBlockedDomain;
use App\Submission;
use App\Traits\CachableChannel;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use App\Traits\Submit;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    use Filters, PhotoTools, CachableUser, CachableSubmission, CachableChannel, Submit;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['get', 'getPhotos', 'show']]);
    }

    /**
     * Shows the submission page to guests.
     *
     * @param string $channel
     * @param string $slug
     *
     * @return view
     */
    public function show($channel_name, $submission_slug)
    {
        $submission = new SubmissionResource(
            $this->getSubmissionBySlug($submission_slug)
        );

        $channel = new ChannelResource(
            $this->getChannelByName($channel_name)
        );

        return view('submission.show', compact('submission', 'channel'));
    }

    /**
     * Stores the submitted submission.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'channel_name' => ['required', 'exists:channels,name', new NotBannedFromChannel()],
            'type'         => 'required|in:link,img,text,gif',
            'title'        => 'required|string|between:7,150',
            'url'          => ['required_if:type,link', 'url', new NotBlockedDomain()],
            'photos_id'    => 'required_if:type,img|array|max:20',
            'gif_id'       => 'required_if:type,gif|integer',
        ]);

        // Make sure user is not overdoing it.
        if ($this->tooEarlyToCreate(2)) {
            return res(429, "Looks like you're over doing it. You can't submit more than 2 posts per minute.");
        }

        switch ($request->type) {
            case 'link':
                $data = $this->linkSubmission($request);
                break;

            case 'img':
                $data = $this->imgSubmission($request);
                break;

            case 'gif':
                $data = $this->gifSubmission($request);
                break;

            case 'text':
                $data = $this->textSubmission($request);
                break;
        }

        $channel = $this->getChannelByName($request->channel_name);

        try {
            $submission = Submission::create([
                'title'        => $request->title,
                'slug'         => $slug = $this->slug($request->title),
                'url'          => $request->type === 'link' ? $request->url : config('app.url').'/c/'.$channel->name.'/'.$slug,
                'domain'       => $request->type === 'link' ? domain($request->url) : null,
                'type'         => $request->type,
                'channel_name' => $request->channel_name,
                'channel_id'   => $channel->id,
                'nsfw'         => $request->nsfw,
                'rate'         => firstRate(),
                'user_id'      => Auth::id(),
                'data'         => $data,
            ]);

            event(new SubmissionWasCreated($submission));
        } catch (\Exception $exception) {
            app('sentry')->captureException($exception);

            return res(500, 'Ooops, something went wrong. We will take a look at it to fix.');
        }

        if ($request->type === 'img' || $request->type === 'gif') {
            $this->updateSubmissionIdForUploadedFile($request, $submission->id);
        }

        $this->firstVote($submission->id);

        return new SubmissionResource($submission);
    }

    /**
     * Updates the 'submission_id' field in the uploaded file (photo/gif).
     *
     * @param Request $request
     * @param int     $submission_id
     */
    protected function updateSubmissionIdForUploadedFile($request, $submission_id)
    {
        if ($request->type === 'img') {
            DB::table('photos')
                ->whereIn('id', $request->input('photos_id'))
                ->update(['submission_id' => $submission_id]);
        }

        if ($request->type === 'gif') {
            DB::table('gifs')
                ->where('id', $request->gif_id)
                ->where('user_id', Auth::id())
                ->update(['submission_id' => $submission_id]);
        }
    }

    /**
     * Fetches the title from an external URL.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string title
     */
    public function getTitleAPI(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|url',
        ]);

        return response([
            'data' => [
                'title' => $this->getTitle($request->url),
            ],
        ], 200);
    }

    /**
     * Returns the submission (even if it's been soft-deleted).
     *
     * @param int $submission_id
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(Request $request)
    {
        $this->validate($request, [
            'slug' => 'required_without:id|exists:submissions',
            'id'   => 'required_without:slug|exists:submissions',
        ]);

        if ($request->filled('slug')) {
            return new SubmissionResource(
                $submission = $this->getSubmissionBySlug($request->slug)
            );
        }

        return new SubmissionResource($this->getSubmissionById($request->id));
    }

    /**
     * Returns all the uploaded photos for a specific submission.
     *
     * @param int $submission_id
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPhotos()
    {
        request()->validate([
            'submission_id' => 'required|exists:submissions,id',
        ]);

        return PhotoResource::collection(
            Photo::where('submission_id', request('submission_id'))->get()
        );
    }

    /**
     * Destroys the submisison record from the database.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Request $request, $submission_id)
    {
        $submission = $this->getSubmissionById($submission_id);

        abort_unless($this->mustBeOwner($submission), 403);

        event(new SubmissionWasDeleted($submission, true));

        $submission->forceDelete();

        return res(200, 'Submission deleted successfully.');
    }

    /**
     * Removes the thumbnail.
     *
     * @param int $submission_id
     *
     * @return response
     */
    public function removeThumbnail($submission_id)
    {
        $submission = $this->getSubmissionById($submission_id);

        abort_unless($this->mustBeOwner($submission), 403);

        $submission->update([
            'data' => [
                'url'           => $submission->data['url'],
                'title'         => $submission->data['title'],
                'description'   => $submission->data['description'],
                'type'          => $submission->data['type'],
                'embed'         => $submission->data['embed'],
                'img'           => null,
                'thumbnail'     => null,
                'providerName'  => $submission->data['providerName'],
                'publishedTime' => $submission->data['publishedTime'],
                'domain'        => $submission->data['domain'] ?? domain($submission->data['url']),
            ],
        ]);

        $this->putSubmissionInTheCache($submission);

        return res(200, 'thumbnail removed. ');
    }

    /**
     * Patches the Text Submission.
     *
     * @return reponse
     */
    public function patchTextSubmission(Request $request, $submission_id)
    {
        $submission = $this->getSubmissionById($submission_id);

        abort_unless($this->mustBeOwner($submission), 403);
        // make sure submission's type is "text" (at the moment submission editing is only available for text submissions)
        if ($submission->type !== 'text') {
            return res(400, 'Right now, only text submissions are editable.');
        }

        $submission->update([
            'data' => array_only($request->all(), ['text']),
        ]);

        // so next time it'll fetch the updated copy
        $this->removeSubmissionFromCache($submission);

        return res(200, 'Text Submission has been updated.');
    }

    /**
     * Whether or the user is breaking the time limit for creating another submission.
     *
     * @return mixed
     */
    protected function tooEarlyToCreate($limit_number)
    {
        // white-listed users are fine
        if ($this->mustBeWhitelisted()) {
            return false;
        }

        $posted_submissions_count = Activity::where([
            ['subject_type', 'App\Submission'],
            ['user_id', Auth::user()->id],
            ['name', 'created_submission'],
            ['created_at', '>=', Carbon::now()->subMinute()],
        ])->get()->count();

        return $posted_submissions_count >= $limit_number ? true : false;
    }
}
