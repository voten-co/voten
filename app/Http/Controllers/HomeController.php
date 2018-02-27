<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubmissionResource;
use App\Submission;
use App\Traits\CachableChannel;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use CachableUser, CachableSubmission, CachableChannel;

    /**
     * Displays the home page.h.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return view
     */
    public function homePage()
    {
        $submissions = SubmissionResource::collection(
            $this->guestHome(request())
        );

        return view('home', compact('submissions'));
    }

    /**
     * Returns the submissions for the homepage of Auth user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function feed(Request $request)
    {
        $this->validate($request, [
            'page' => 'required|integer|min:1',
            'exclude_upvoted_submissions' => 'boolean', 
            'exclude_downvoted_submissions' => 'boolean', 
            'exclude_bookmarked_submissions' => 'boolean',
            'include_nsfw_submissions' => 'boolean', 
        ]);

        if (!Auth::check()) {
            return SubmissionResource::collection(
                $this->guestHome($request)
            );
        }

        $submissions = (new Submission())->newQuery();

        switch ($request->filter) {
            case 'all':
                // guest what? we don't have to do anything :|
                break;

            case 'moderating':
                $submissions->whereIn('channel_id', Auth::user()->moderatingIds());
                break;

            case 'bookmarked':
                $submissions->whereIn('channel_id', $this->bookmarkedChannels());
                break;

            case 'by-bookmarked-users':
                $submissions->whereIn('user_id', $this->bookmarkedUsers());
                break;

            default: // subscribed
                $submissions->whereIn('channel_id', $this->subscriptions());
                break;
        }

        switch ($request->type) {
            case 'GIF':
                $submissions->where('type', 'gif');
                break;

            case 'Link':
                $submissions->where('type', 'link');
                break;

            case 'Image':
                $submissions->where('type', 'img');
                break;

            case 'Text':
                $submissions->where('type', 'text');
                break;

            default: // subscribed
                // guest what? we don't have to do anything :|
                break;
        }

        // exclude user's blocked channels
        $submissions->whereNotIn('channel_id', $this->hiddenChannels());

        // exclude user's hidden submissions
        $submissions->whereNotIn('id', $this->hiddenSubmissions());

        if (! $request->include_nsfw_submissions) {
            $submissions->where('nsfw', false);
        } 

        if ($request->exclude_upvoted_submissions) {
            $submissions->whereNotIn('id', $this->submissionUpvotesIds());
        }

        if ($request->exclude_downvoted_submissions) {
            $submissions->whereNotIn('id', $this->submissionDownvotesIds());
        }

        if ($request->exclude_bookmarked_submissions) {
            $submissions->whereNotIn('id', $this->bookmarkedSubmissions());
        }

        switch ($request->sort) {
            case 'new':
                $submissions->orderBy('created_at', 'desc');
                break;

            case 'rising':
                $submissions->where('created_at', '>=', Carbon::now()->subHour())
                    ->orderBy('rate', 'desc');
                break;

            default: // 'hot'
                $submissions->orderBy('rate', 'desc');
                break;
        }

        $submissions->groupBy('url');

        return SubmissionResource::collection(
            $submissions->simplePaginate(15)
        );
    }

    /**
     * returns submisisons from default channels. by time we're gonna improve this.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    protected function guestHome(Request $request)
    {
        $submissions = (new Submission())->newQuery();

        $submissions->whereIn(
            'channel_id', $this->getDefaultChannels()
        );

        $submissions->where('nsfw', false);

        switch ($request->sort) {
            case 'new':
                $submissions->orderBy('created_at', 'desc');
                break;

            case 'rising':
                $submissions->where('created_at', '>=', Carbon::now()->subHour())->orderBy('rate', 'desc');
                break;

            default: // hot
                $submissions->orderBy('rate', 'desc');
                break;
        }

        $submissions->groupBy('url');

        return $submissions->simplePaginate(15);
    }
}
