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
use App\Report;
use App\Submission;
use App\Traits\EchoServer;
use App\User;
use Illuminate\Http\Request;

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
    public function submissions(Request $request)
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
    public function comments()
    {
        return CommentResource::collection(
            Comment::orderBy('id', 'desc')->simplePaginate(30)
        );
    }

    /**
     * Returns the latest created channels.
     *
     * @return \Illuminate\Support\Collection
     */
    public function channels()
    {
        return ChannelResource::collection(
            Channel::orderBy('id', 'desc')->simplePaginate(30)
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
            User::orderBy('id', 'desc')->simplePaginate(30)
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

    /**
     * searches the channels.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function getChannels(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        return Channel::where('name', 'like', '%'.$request->name.'%')
                    ->select('id', 'name')->take(100)->get()->pluck('name');
    }
}
