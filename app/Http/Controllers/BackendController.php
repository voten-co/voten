<?php

namespace App\Http\Controllers;

use App\Activity;
use App\AppointeddUser;
use App\Category;
use App\CategoryForbiddenName;
use App\Comment;
use App\Message;
use App\Report;
use App\Submission;
use App\User;
use App\UserForbiddenName;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BackendController extends Controller
{
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
     * Shows the category page
     *
     * @return \Illuminate\View\View
     */
    public function showCategory($category)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        $category = Category::where('name', $category)->firstOrFail();

        return view('backend.category', compact('category'));
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
     * shows the dashboard which currently displays site's statistics.
     *
     * @return view
     */
    public function dashboard()
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

        $submissionsTotal = Submission::all()->count();
        $submissionsToday = Submission::where('created_at', '>=', Carbon::now()->subDay())->count();

        $commentsTotal = Comment::all()->count();
        $commentsToday = Comment::where('created_at', '>=', Carbon::now()->subDay())->count();

        $messagesTotal = Message::all()->count();
        $messagesToday = Message::where('created_at', '>=', Carbon::now()->subDay())->count();

        $reportsTotal = Report::withTrashed()->get()->count();
        $reportsToday = Report::withTrashed()->where('created_at', '>=', Carbon::now()->subDay())->count();

        $activities = Activity::with('owner')->orderBy('id', 'desc')->simplePaginate(30);

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
            'users', 'activeUsersToday', 'activeUsersTotal', 'activities'
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
