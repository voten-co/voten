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

    public function homePage(Request $request)
    {
        if (!Auth::check()) {
        	$submissions = $this->guestHome($request);

            return view('home', compact('submissions'));
        }

        return view('welcome');
    }

    /**
     * Returns the submissions for the homepage of Auth user.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Support\Collection
     */
    public function feed(Request $request)
    {
        $this->validate($request, [
            'sort' => 'required|in:hot,new,rising',
            'page' => 'required|integer',
        ]);

        if (!Auth::check()) {
            return $this->guestHome($request);
        }

        $submissions = (new Submission())->newQuery();

        $submissions->whereIn('category_id', $this->subscriptions())
                    ->whereNotIn('id', $this->hiddenSubmissions()); // exclude user's hidden submissions

        // exclude NSFW if user doens't want to see them
        if (!settings('nsfw')) {
            $submissions->where('nsfw', false);
        }

        if (settings('exclude_upvoted_submissions')) {
            $submissions->whereNotIn('id', $this->submissionUpvotesIds());
        }

        if (settings('exclude_downvoted_submissions')) {
            $submissions->whereNotIn('id', $this->submissionDownvotesIds());
        }

        if ($request->sort == 'new') {
            $submissions->orderBy('created_at', 'desc');
        }

        if ($request->sort == 'rising') {
            $submissions->where('created_at', '>=', Carbon::now()->subHour())
                        ->orderBy('rate', 'desc');
        }

        if ($request->sort == 'hot') {
            $submissions->orderBy('rate', 'desc');
        }

        return $submissions->simplePaginate(10);
    }

    /**
     * returns submisisons from default categories. by time we're gonna improve this.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return Illuminate\Support\Collection
     */
    protected function guestHome(Request $request)
    {
        $submissions = (new Submission())->newQuery();

        $submissions->whereIn(
            'category_id', $this->getDefaultCategories()
        );

        $submissions->where('nsfw', false);

        if ($request->sort == 'new') {
            $submissions->orderBy('created_at', 'desc');
        }

        if ($request->sort == 'rising') {
            $submissions->where('created_at', '>=', Carbon::now()->subHour())
                        ->orderBy('rate', 'desc');
        }

        if ($request->sort == 'hot') {
            $submissions->orderBy('rate', 'desc');
        }

        return $submissions->simplePaginate(10);
    }
}
