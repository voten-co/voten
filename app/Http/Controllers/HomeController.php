<?php

namespace App\Http\Controllers;

use App\Submission;
use App\Traits\CachableCategory;
use App\Traits\CachableSubmission;
use App\Traits\CachableUser;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use CachableUser, CachableSubmission, CachableCategory;

    /**
     * Displays the home page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return view
     */
    public function homePage(Request $request)
    {
        $submissions = $this->guestHome($request);

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
        ]);

        if (! Auth::check()) {
            return $this->guestHome($request);
        }

        $submissions = (new Submission())->newQuery();

        switch ($request->filter) {
            case 'all':
                // guest what? we don't have to do anything :|
                break;

            case 'moderating': 
                $submissions->whereIn('category_id', Auth::user()->moderatingIds());
                break; 

            case 'bookmarked': 
                $submissions->whereIn('category_id', $this->bookmarkedCategories());
                break; 

            case 'by-bookmarked-users': 
                $submissions->whereIn('user_id', $this->bookmarkedUsers());
                break; 

            default: // subscribed
                $submissions->whereIn('category_id', $this->subscriptions());
                break;
        }

        // exclude user's blocked categories
        $submissions->whereNotIn('category_id', $this->hiddenCategories());

        // exclude user's hidden submissions
        $submissions->whereNotIn('id', $this->hiddenSubmissions());

        // exclude NSFW if user doens't want to see them
        if (! settings('nsfw')) {
            $submissions->where('nsfw', false);
        }
      
        if ($request->exclude_upvoted_submissions == 'true') {
            $submissions->whereNotIn('id', $this->submissionUpvotesIds());
        }
      
        if ($request->exclude_downvoted_submissions == 'true') {
            $submissions->whereNotIn('id', $this->submissionDownvotesIds());
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

        return $submissions->simplePaginate(15);
    }

    /**
     * returns submisisons from default categories. by time we're gonna improve this.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    protected function guestHome(Request $request)
    {
        $submissions = (new Submission())->newQuery();

        $submissions->whereIn(
            'category_id', $this->getDefaultCategories()
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
