<?php

namespace App\Http\Controllers;

use App\Activity;
use App\AppointeddUser;
use App\Category;
use App\CategoryForbiddenName;
use App\Comment;
use App\FireWallBannedIp;
use App\Message;
use App\Notifications\BecameModerator;
use App\Report;
use App\Submission;
use App\Traits\CachableCategory;
use App\Traits\CachableUser;
use App\Traits\EchoServer;
use App\User;
use App\UserForbiddenName;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BackendController extends Controller
{
    use EchoServer, CachableCategory, CachableUser;

    public function __construct()
    {
        $this->middleware(['administrator']);
    }

    /**
     * shows the forbidden names and usernames page.
     *
     * @return view
     */
    public function firewall()
    {
        $forbiddenUsernames = UserForbiddenName::orderBy('created_at', 'desc')->paginate(30);

        $forbiddenCategoryNames = CategoryForbiddenName::orderBy('created_at', 'desc')->paginate(30);

        $blockedDomains = \App\BlockedDomain::where('category', 'all')->orderBy('created_at', 'desc')->paginate(30);

        $banned_ip_addresses = FireWallBannedIp::orderBy('created_at', 'desc')->paginate(50);

        return view('backend.firewall', compact('forbiddenUsernames', 'forbiddenCategoryNames', 'blockedDomains', 'banned_ip_addresses'));
    }

    /**
     * Shows categories.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function showCategories(Request $request)
    {
        if ($request->filled('filter')) {
            $categories = Category::search($request->filter)->take(20)->get();
        } else {
            $categories = (new Category())->newQuery();

            if ($request->filled('sort_by')) {
                if ($request->sort_by == 'subscribers') {
                    $categories->orderBy('subscribers', 'desc');
                } elseif ($request->sort_by == 'submissions_count') {
                    $categories->withCount('submissions')->orderBy('submissions_count', 'desc');
                } elseif ($request->sort_by == 'comments_count') {
                    $categories->withCount('comments')->orderBy('comments_count', 'desc');
                }
            } else {
                $categories->orderBy('id', 'desc');
            }

            $categories = $categories->paginate(30);
        }

        return view('backend.categories', compact('categories'));
    }

    /**
     * Shows the category page.
     *
     * @return \Illuminate\View\View
     */
    public function showCategory($category)
    {
        $category = Category::where('name', $category)->firstOrFail();

        $isAdministrator = $this->mustBeAdministrator($category->id, true);

        return view('backend.category', compact('category', 'isAdministrator'));
    }

    /**
     * Shows users.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function showUsers(Request $request)
    {
        if ($request->filled('filter')) {
            $users = User::search($request->filter)->take(20)->get();
        } else {
            $users = User::orderBy('id', 'desc')->paginate(30);
        }

        return view('backend.users', compact('users'));
    }

    /**
     * Shows the user page.
     *
     * @return \Illuminate\View\View
     */
    public function showUser($user)
    {
        $user = User::where('username', $user)->firstOrFail();

        $user->stats = $this->userStats($user->id);

        return view('backend.user', compact('user'));
    }

    /**
     * shows the dashboard which currently displays site's statistics.
     *
     * @return view
     */
    public function dashboard(Request $request)
    {
        $usersTotal = User::all()->count();
        $usersToday = User::where('created_at', '>=', Carbon::now()->subDay())->count();

        $activeUsersTotal = User::has('activities', '>=', 10)->count();
        $activeUsersToday = User::has('activities', '>=', 2)->whereHas('activities', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDay());
        })->count();

        $categoriesTotal = Category::all()->count();
        $categoriesToday = Category::where('created_at', '>=', Carbon::now()->subDay())->count();

        $subscriptionsTotal = DB::table('subscriptions')->count();
        $subscriptionsToday = DB::table('subscriptions')->where('created_at', '>=', Carbon::now()->subDay())->count();

        $submissionsTotal = Submission::all()->count();
        $submissionsToday = Submission::where('created_at', '>=', Carbon::now()->subDay())->count();

        $commentsTotal = Comment::all()->count();
        $commentsToday = Comment::where('created_at', '>=', Carbon::now()->subDay())->count();

        $messagesTotal = Message::all()->count();
        $messagesToday = Message::where('created_at', '>=', Carbon::now()->subDay())->count();

        $reportsTotal = Report::withTrashed()->get()->count();
        $reportsToday = Report::withTrashed()->where('created_at', '>=', Carbon::now()->subDay())->count();

        // Activities start
        $activities = (new Activity())->newQuery();

        if ($request->filled('name')) {
            $activities->where('name', $request->name);
        }
        if ($request->filled('user_id')) {
            $activities->where('user_id', $request->user_id);
        }
        if ($request->filled('ip_address')) {
            $activities->where('ip_address', $request->ip_address);
        }
        if ($request->filled('device')) {
            $activities->where('device', $request->device);
        }
        if ($request->filled('country')) {
            $activities->where('country', $request->country);
        }
        if ($request->filled('os')) {
            $activities->where('os', $request->os);
        }
        if ($request->filled('browser_name')) {
            $activities->where('browser_name', $request->browser_name);
        }

        $activities = $activities->with('owner')->orderBy('id', 'desc')->simplePaginate(30);
        // Activities end

        $echo_server_status = $this->echoStatus();

        // total numer of submission votes: (upvotes + downvotes) - numberOfSubmissions
        $submissionVotesTotal = (DB::table('submission_upvotes')->count() + DB::table('submission_downvotes')->count()) - $submissionsTotal;
        $submissionVotesToday = (DB::table('submission_upvotes')->where('created_at', '>=', Carbon::now()->subDay())->count() + DB::table('submission_downvotes')->where('created_at', '>=', Carbon::now()->subDay())->count()) - $submissionsToday;

        // total numer of comment votes: (upvotes + downvotes) - numberOfSubmissions
        $commentVotesTotal = (DB::table('comment_upvotes')->count() + DB::table('comment_downvotes')->count()) - $commentsTotal;
        $commentVotesToday = (DB::table('comment_upvotes')->where('created_at', '>=', Carbon::now()->subDay())->count() + DB::table('comment_downvotes')->where('created_at', '>=', Carbon::now()->subDay())->count()) - $commentsToday;

        $users = User::orderBy('id', 'desc')->paginate(30);

        return view('backend.dashboard', compact(
            'usersTotal', 'usersToday', 'categoriesTotal', 'categoriesToday', 'submissionsTotal', 'submissionsToday', 'commentsTotal', 'commentsToday', 'messagesTotal', 'messagesToday', 'reportsTotal',
            'reportsToday', 'submissionVotesTotal', 'submissionVotesToday', 'commentVotesTotal', 'commentVotesToday',
            'users', 'activeUsersToday', 'activeUsersTotal', 'activities', 'echo_server_status', 'subscriptionsToday', 'subscriptionsTotal'
            )
        );
    }

    /**
     * indexes the backend page.
     *
     * @return view
     */
    public function indexAppointedUsers()
    {
        $appointed_users = AppointeddUser::all();

        return view('backend.appointed-users', compact('appointed_users'));
    }

    /**
     * indexes the backend page.
     *
     * @return view
     */
    public function serverControls()
    {
        return view('backend.server-controls');
    }

    /**
     * stores a new forbidden model in the database. This when a user is registering or changing his
     * username. We don't want them to use these names.
     *
     * @param Request $request
     *
     * @return redirect
     */
    public function storeForbiddenUsername(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|min:3|max:25|unique:users|regex:/^[A-Za-z0-9\._]+$/',
        ]);

        UserForbiddenName::create([
            'username' => $request->username,
        ]);

        return back();
    }

    /**
     * destroys a forbidden-username model.
     *
     * @param \App\UserForbiddenName $forbidden
     *
     * @return redirect
     */
    public function destroyForbiddenUsername(UserForbiddenName $forbidden)
    {
        $forbidden->delete();

        return back();
    }

    /**
     * stores a new forbidden model in the database. This when a user is registering or changing his
     * username. We don't want them to use these names.
     *
     * @param Request $request
     *
     * @return redirect
     */
    public function storeForbiddenCategoryName(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:categories',
        ]);

        CategoryForbiddenName::create([
            'name' => $request->name,
        ]);

        return back();
    }

    /**
     * destroys a forbidden-username model.
     *
     * @param \App\CategoryForbiddenName $forbidden
     *
     * @return redirect
     */
    public function destroyForbiddenCategoryName(CategoryForbiddenName $forbidden)
    {
        $forbidden->delete();

        return back();
    }

    /**
     * Takes over the category. (gives you a role in the category as "administrator").
     *
     * @return redirect
     */
    public function takeOverCategory(Category $category)
    {
        $category->moderators()->attach(Auth::id(), [
            'role' => 'administrator',
        ]);

        Auth::user()->notify(new BecameModerator($category, 'administrator'));

        $this->updateCategoryMods($category->id, Auth::id());

        session()->flash('status', "You're not an administrator of #".$category->name.'. Go knock yourself out. ');

        return back();
    }

    /**
     * stores a new AppointedUser record in the databas and cache.
     *
     * @param Request $request
     *
     * @return redirect
     */
    public function storeAppointed(Request $request)
    {
        $this->validate($request, [
            'username'     => 'required',
            'appointed_as' => 'in:administrator,moderator,whitelisted',
        ]);

        $user = User::where('username', $request->username)->firstOrFail();

        AppointeddUser::create([
            'user_id'      => $user->id,
            'appointed_as' => $request->appointed_as,
        ]);

        Cache::forget('general.voten-administrators');
        Cache::forget('general.whitelisted');

        return back();
    }

    /**
     * destroys a AppointedUser record in the databas and cache.
     *
     * @param \App\AppointeddUser $appointed
     *
     * @return redirect
     */
    public function destroyAppointed(AppointeddUser $appointed)
    {
        if (Auth::user()->id == $appointed->user->id) {
            return "I don't think you really mean it.";
        }

        $appointed->delete();

        Cache::forget('general.voten-administrators');
        Cache::forget('general.whitelisted');

        return back();
    }

    /**
     * will clean it later! too busy now!
     */
    public function updateCommentsCount()
    {
        $submissions = Submission::all();

        foreach ($submissions as $submission) {
            $submission->update([
                'comments_number' => $submission->comments()->count(),
            ]);
        }

        return 'all good';
    }
}
