<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Channel;
use App\Comment;
use App\Filters;
use App\Http\Resources\ActivityResource;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\EchoServerResource;
use App\Http\Resources\SubmissionResource;
use App\Http\Resources\UserResource;
use App\Message;
use App\Report;
use App\Submission;
use App\Traits\EchoServer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use Filters, EchoServer;

    public function __construct()
    {
        $this->middleware('voten-administrator', ['except' => ['isAdministrator']]);
    }

    public function activities()
    {
        $activities = (new Activity())->newQuery();

        return ActivityResource::collection(
            $activities->with('owner')->orderBy('id', 'desc')->simplePaginate(30)
        );
    }

    public function echoServer()
    {
        return new EchoServerResource(
            $this->echoStatus()
        );
    }

    public function statistics()
    {
        $stats = Cache::remember('voten-general-statistics', 60, function () {
            return [
                'data' => [
                    'users' => [
                        'today' => User::where('created_at', '>=', now()->subDay())->count(),
                        'week' => User::where('created_at', '>=', now()->subWeek())->count(),
                        'month' => User::where('created_at', '>=', now()->subMonth())->count(),
                        'total' => User::all()->count(),
                    ],

                    'active_users' => [
                        'today' => User::has('activities', '>=', 2)->whereHas('activities', function ($query) {
                            $query->where('created_at', '>=', now()->subDay());
                        })->count(),

                        'week' => User::has('activities', '>=', 2)->whereHas('activities', function ($query) {
                            $query->where('created_at', '>=', now()->subWeek());
                        })->count(),

                        'month' => User::has('activities', '>=', 2)->whereHas('activities', function ($query) {
                            $query->where('created_at', '>=', now()->subMonth());
                        })->count(),

                        'total' => User::has('activities', '>=', 2)->count(),
                    ],

                    'subscriptions' => [
                        'today' => DB::table('subscriptions')->where('created_at', '>=', now()->subDay())->count(),
                        'week' => DB::table('subscriptions')->where('created_at', '>=', now()->subWeek())->count(),
                        'month' => DB::table('subscriptions')->where('created_at', '>=', now()->subMonth())->count(),
                        'total' => DB::table('subscriptions')->count(),
                    ],

                    'channels' => [
                        'today' => Channel::where('created_at', '>=', now()->subDay())->count(),
                        'week' => Channel::where('created_at', '>=', now()->subWeek())->count(),
                        'month' => Channel::where('created_at', '>=', now()->subMonth())->count(),
                        'total' => Channel::all()->count(),
                    ],

                    'comments' => [
                        'today' => Comment::where('created_at', '>=', now()->subDay())->count(),
                        'week' => Comment::where('created_at', '>=', now()->subWeek())->count(),
                        'month' => Comment::where('created_at', '>=', now()->subMonth())->count(),
                        'total' => Comment::all()->count(),
                    ],

                    'submissions' => [
                        'today' => Submission::where('created_at', '>=', now()->subDay())->count(),
                        'week' => Submission::where('created_at', '>=', now()->subWeek())->count(),
                        'month' => Submission::where('created_at', '>=', now()->subMonth())->count(),
                        'total' => Submission::all()->count(),
                    ],

                    'messages' => [
                        'today' => Message::where('created_at', '>=', now()->subDay())->count(),
                        'week' => Message::where('created_at', '>=', now()->subWeek())->count(),
                        'month' => Message::where('created_at', '>=', now()->subMonth())->count(),
                        'total' => Message::all()->count(),
                    ],

                    'reports' => [
                        'today' => Report::where('created_at', '>=', now()->subDay())->count(),
                        'week' => Report::where('created_at', '>=', now()->subWeek())->count(),
                        'month' => Report::where('created_at', '>=', now()->subMonth())->count(),
                        'total' => Report::all()->count(),
                    ],
                ],
            ];
        });

        return collect($stats);
    }

    /**
     * Is the authinticated user a Voten administrator?
     *
     * @return string
     */
    public function isAdministrator()
    {
        return $this->mustBeVotenAdministrator() ? 'true' : 'false';
    }

    /**
     * Returns the latest submissions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function indexSubmissions(Request $request)
    {
        return SubmissionResource::collection(
            Submission::orderBy('id', 'desc')->simplePaginate(10)
        );
    }

    /**
     * Returns the latest submitted comments.
     *
     * @return \Illuminate\Support\Collection
     */
    public function indexComments()
    {
        return CommentResource::collection(
            Comment::orderBy('id', 'desc')->simplePaginate(30)
        );
    }

    /**
     * Returns the latest created channels.
     *
     * @return ChannelResource
     */
    public function indexChannels()
    {
        return ChannelResource::collection(
            Channel::orderBy('id', 'desc')->simplePaginate(30)
        );
    }

    /**
     * Returns a list of channels that have been inactive for more than 2 months.
     *
     * @return ChannelResource
     */
    public function inactiveChannels()
    {
        return ChannelResource::collection(
            Channel::where('created_at', '<=', now()->subMonths(2))
                ->whereDoesntHave('submissions', function ($query) {
                    $query->where('created_at', '>=', now()->subMonths(2));
                })->get()
        );
    }

    /**
     * Returns the latest registered users.
     *
     * @return \Illuminate\Support\Collection
     */
    public function indexUsers()
    {
        return UserResource::collection(
            User::orderBy('id', 'desc')->simplePaginate(90)
        );
    }

    /**
     * Indexes the reported submissions.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function reportedSubmissions(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
        ]);

        if ($request->type == 'solved') {
            return Report::onlyTrashed()->whereHas('submission')->whereHas('reporter')->where([
                'reportable_type' => 'App\Submission',
            ])->with('reporter', 'submission')->orderBy('created_at', 'desc')->simplePaginate(50);
        }

        // default type which is "unsolved"
        return Report::whereHas('submission')->whereHas('reporter')->where([
            'reportable_type' => 'App\Submission',
        ])->with('reporter', 'submission')->orderBy('created_at', 'desc')->simplePaginate(50);
    }

    /**
     * Indexes the reported comments.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function reportedComments(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
        ]);

        if ($request->type == 'solved') {
            return Report::onlyTrashed()->whereHas('comment')->whereHas('reporter')->where([
                'reportable_type' => 'App\Comment',
            ])->with('reporter', 'comment')->orderBy('created_at', 'desc')->simplePaginate(50);
        }

        // default type which is "unsolved"
        return Report::whereHas('comment')->whereHas('reporter')->where([
            'reportable_type' => 'App\Comment',
        ])->with('reporter', 'comment')->orderBy('created_at', 'desc')->simplePaginate(50);
    }
}
