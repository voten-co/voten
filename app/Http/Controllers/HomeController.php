<?php

namespace App\Http\Controllers;

use App\Http\Resources\SubmissionResource;
use App\Submission;
use App\Traits\CachableChannel;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $submissions = $this->guestFeed(request());

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
            'exclude_liked_submissions' => 'boolean', 
            'exclude_bookmarked_submissions' => 'boolean',
            'include_nsfw_submissions' => 'nullable|boolean',
            'filter' => 'nullable|in:all,moderating,bookmarked,by-bookmarked-users,subscribed,All,Moderating,Bookmarked,By-bookmarked-users,Subscribed',
            'submissions_type' => 'nullable|in:gif,Gif,GIF,link,Link,image,Image,text,Text,all,All', 
            'sort' => 'nullable|in:hot,Hot,new,New,rising,Rising'
        ]);
        $submissions = (new Submission())->newQuery();

        switch (strtolower($request->input('filter', 'subscribed'))) {
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
            
            case 'subscribed':
                $submissions->whereIn('channel_id', Auth::user()->subscriptions()->pluck('id')); 
                break;
        }

        switch (strtolower($request->input('submissions_type', 'All'))) {
            case 'gif':
                $submissions->where('type', 'gif');
                break;

            case 'link':
                $submissions->where('type', 'link');
                break;

            case 'image':
                $submissions->where('type', 'img');
                break;

            case 'text':
                $submissions->where('type', 'text');
                break;
            
            case 'all':
                break;
        }

        // exclude user's blocked channels
        $submissions->whereNotIn('channel_id', $this->hiddenChannels());

        // exclude user's hidden submissions
        $submissions->whereNotIn('id', $this->hiddenSubmissions());

        if (! $request->input('include_nsfw_submissions', 0)) {
            $submissions->where('nsfw', false);
        } 

        if ($request->input('exclude_liked_submissions', 0)) {
            $submissions->whereNotIn('id', $this->submissionLikesIds());
        }

        if ($request->input('exclude_bookmarked_submissions', 0)) {
            $submissions->whereNotIn('id', $this->bookmarkedSubmissions());
        }

        switch (strtolower($request->input('sort', 'hot'))) {
            case 'new':
                $submissions->orderBy('created_at', 'desc');
                break;

            case 'rising':
                $submissions->where('created_at', '>=', Carbon::now()->subHour())
                    ->orderBy('rate', 'desc');
                break;
            
            case 'hot':
                $submissions->orderBy('rate', 'desc');
                break;
        }

        // Prevent duplicate news: 
        $submissions->groupBy('url');

        return SubmissionResource::collection(
            $submissions->simplePaginate(15)
        );
    }

    /**
     * Returns submissions from default channels. by time we're gonna improve this.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function guestFeed(Request $request)
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

        // Prevent duplicate news:         
        $submissions->groupBy('url');
        
        return SubmissionResource::collection(
            $submissions->simplePaginate(15)
        );
    }
}
