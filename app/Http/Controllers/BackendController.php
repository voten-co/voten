<?php

namespace App\Http\Controllers;

use App\Activity;
use App\AppointeddUser;
use App\Category;
use App\CategoryForbiddenName;
use App\Comment;
use App\Message;
use App\Notifications\BecameModerator;
use App\Report;
use App\Submission;
use App\Traits\CachableCategory;
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
    use EchoServer, CachableCategory;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * shows the forbidden names and usernames page.
     *
     * @return view
     */
    public function forbiddenNames()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $forbiddenUsernames = UserForbiddenName::paginate(30);

        $forbiddenCategoryNames = CategoryForbiddenName::paginate(30);

        $blockedDomains = \App\BlockedDomain::where('category', 'all')->paginate(30);

        return view('backend.forbidden-names', compact('forbiddenUsernames', 'forbiddenCategoryNames', 'blockedDomains'));
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
        abort_unless($this->mustBeVotenAdministrator(), 403);

        if ($request->has('filter')) {
            $categories = Category::search($request->filter)->take(20)->get();
        } else {
            $categories = Category::orderBy('id', 'desc')->paginate(30);
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
        abort_unless($this->mustBeVotenAdministrator(), 403);

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
        abort_unless($this->mustBeVotenAdministrator(), 403);

        if ($request->has('filter')) {
            $users = User::search($request->filter)->take(20)->get();
        } else {
            $users = User::orderBy('id', 'desc')->paginate(30);
        }

        return view('backend.users', compact('users'));
    }

    /**
     * Shwos the spam page. A few tools to fight spammers.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function spam()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $query = <<<'SQL'
select a1.user_id  from activities as a1
inner join 
(
  	select ip_address
		from activities  
			where name = 'created_user'
		group by ip_address
		having count(id) > 1
) as a2 on a1.ip_address = a2.ip_address 
group by user_id
SQL;

        $user_ids = collect(DB::select($query))->pluck('user_id');

        $users = User::whereIn('id', $user_ids)->get();

        foreach ($users as $user) {
            $user->ip = $user->registeredIpAddress();
        }

        $groupedByIpUsers = $users->groupBy('ip');

        return view('backend.spam', compact('groupedByIpUsers'));
    }

    /**
     * shows the dashboard which currently displays site's statistics.
     *
     * @return view
     */
    public function dashboard(Request $request)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

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

        if ($request->has('name')) {
            $activities->where('name', $request->name);
        }
        if ($request->has('user_id')) {
            $activities->where('user_id', $request->user_id);
        }
        if ($request->has('ip_address')) {
            $activities->where('ip_address', $request->ip_address);
        }
        if ($request->has('country')) {
            $activities->where('country', $request->country);
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
        abort_unless($this->mustBeVotenAdministrator(), 403);

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
        abort_unless($this->mustBeVotenAdministrator(), 403);

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
        abort_unless($this->mustBeVotenAdministrator(), 403);

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
        abort_unless($this->mustBeVotenAdministrator(), 403);

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
        abort_unless($this->mustBeVotenAdministrator(), 403);

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
        abort_unless($this->mustBeVotenAdministrator(), 403);

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
        abort_unless($this->mustBeVotenAdministrator(), 403);

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
        abort_unless($this->mustBeVotenAdministrator(), 403);

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
        abort_unless($this->mustBeVotenAdministrator(), 403);

        if (Auth::user()->id == $appointed->user->id) {
            return "I don't think you really mean it.";
        }

        $appointed->delete();

        Cache::forget('general.voten-administrators');
        Cache::forget('general.whitelisted');

        return back();
    }
}
