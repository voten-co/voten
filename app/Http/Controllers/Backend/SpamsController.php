<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Report;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpamsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['administrator']);
    }

    /**
     * Loads the "reported submissions" page.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function submissions(Request $request)
    {
        if ($request->filled('type') == 'solved') {
            $reports = Report::onlyTrashed()->whereHas('submission')->whereHas('reporter')->where([
                'reportable_type' => 'App\Submission',
            ])->with('reporter', 'submission')->orderBy('created_at', 'desc')->paginate(50);
        } else {
            // default type which is "unsolved"
            $reports = Report::whereHas('submission')->whereHas('reporter')->where([
                'reportable_type' => 'App\Submission',
            ])->with('reporter', 'submission')->orderBy('created_at', 'desc')->paginate(50);
        }

        return view('backend.spams.submissions', compact('reports'));
    }

    /**
     * Loads the "reported comments" page.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function comments(Request $request)
    {
        if ($request->filled('type') == 'solved') {
            $reports = Report::onlyTrashed()->whereHas('comment')->whereHas('reporter')->where([
                'reportable_type' => 'App\Comment',
            ])->with('reporter', 'comment')->orderBy('created_at', 'desc')->paginate(50);
        } else {
            // default type which is "unsolved"
            $reports = Report::whereHas('comment')->whereHas('reporter')->where([
                'reportable_type' => 'App\Comment',
            ])->with('reporter', 'comment')->orderBy('created_at', 'desc')->paginate(50);
        }

        return view('backend.spams.comments', compact('reports'));
    }

    /**
     * Shows the spam page. A few tools to fight spammers.
     *
     * @param Request $request
     *
     * @return \Illuminate\View\View
     */
    public function multipleAccounts()
    {
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

        return view('backend.spams.multiple-accounts', compact('groupedByIpUsers'));
    }
}
