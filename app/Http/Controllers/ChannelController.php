<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Channel;
use App\Comment;
use App\Events\ChannelWasUpdated;
use App\Filters;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\SubmissionResource;
use App\Report;
use App\Submission;
use App\Traits\CachableChannel;
use App\Traits\CachableUser;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Rules\CurrentPassword;

class ChannelController extends Controller
{
    use Filters, CachableUser, CachableChannel;

    /**
     * makes sure the user is logged in.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show', 'submissions', 'submissionsByChannelName', 'get', 'getByName', 'moderators', 'fillStore', 'redirect']]);
    }

    /**
     * Returns submissions.
     *
     * @param string $channel
     * @param string $sort
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getSubmissions($channel, $sort = 'hot')
    {
        $submissions = (new Submission())->newQuery();

        $submissions->where('channel_name', $channel);

        // exclude user's hidden submissions
        if (Auth::check()) {
            $submissions->whereNotIn('id', $this->hiddenSubmissions());
        }

        // exclude NSFW for guests
        if (!Auth::check()) {
            $submissions->where('nsfw', false);
        }

        switch ($sort) {
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

        return $submissions->simplePaginate(15);
    }

    /**
     * Get submissions API with ajax calls.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return SubmissionResource
     */
    public function submissionsByChannelName(Request $request)
    {
        $this->validate($request, [
            'sort'         => 'in:hot,new,rising|nullable',
            'page'         => 'integer|min:1',
            'channel_name' => 'required|exists:channels,name',
        ]);

        return SubmissionResource::collection(
            $this->getSubmissions($request->channel_name, $request->sort)
        );
    }

    /**
     * Get submissions API with ajax calls.
     *
     * @param \Illuminate\Http\Request $request
     * @param integer $channel
     *
     * @return SubmissionResource
     */
    public function submissions(Request $request, Channel $channel)
    {
        $this->validate($request, [
            'sort'         => 'nullable|in:hot,new,rising',
            'page'         => 'integer|min:1',
        ]);

        return SubmissionResource::collection(
            $this->getSubmissions($channel->name, strtolower($request->input('sort', 'hot')))
        );
    }

    /**
     * shows the submission page to guests.
     *
     * @param string $channel
     * @param string $slug
     *
     * @return view
     */
    public function show($channel_name, Request $request)
    {
        $submissions = SubmissionResource::collection(
            $this->getSubmissions($channel_name, $request->sort ?? 'hot')
        );

        $channel = new ChannelResource(
            $this->getChannelByName($channel_name)
        );

        return view('channel.show', compact('submissions', 'channel'));
    }

    /**
     * Returns all the necessary information to fill the channelStore on front-end.
     *
     * @return Collection
     */
    public function getById(Channel $channel)
    {
        return new ChannelResource($channel);
    }
    
    /**
     * Returns all the nesseccary information to fill the channelStore on front-end.
     *
     * @return Collection
     */
    public function getByName(Request $request)
    {
        $this->validate($request, [
            'name' => 'required_without:id|exists:channels',
            'id'   => 'required_without:name|exists:channels',
        ]);

        if (request()->filled('name')) {
            return new ChannelResource(
                $this->getChannelByName($request->name)
            );
        }

        return new ChannelResource(
            $this->getChannelById($request->id)
        );
    }

    /**
     * Creates a new channel.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string $name
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'        => ['required', 'alpha_num', 'min:3', 'max:50', 'unique:channels', new \App\Rules\NotForbiddenChannelName()],
            'description' => 'required|min:10|max:250',
        ]);

        if (!$this->mustHaveMinimumXp(10) && !$this->mustBeWhitelisted()) {
            return res(
                403, 
                'During beta, channel creation requires a minimum of 10 xp points.
                Either do a bit of activity or contact administrators to lift the limits for your account.'
            );
        }

        $tooEarly = $this->tooEarlyToCreate();

        if ($tooEarly != false) {
            return res(423, "Looks like you're over doing it. You can create another channel in ".$tooEarly.' seconds. Thank you for being patient.');
        }

        $channel = Channel::create([
            'name'        => $request->name,
            'description' => $request->description,
            'nsfw'        => $request->input('nsfw', 0),
            'avatar'      => '/imgs/channel-avatar.png',
            'color'       => 'Blue',
        ]);

        $this->setInitialUserToChannelRoles(Auth::user(), $channel);

        return new ChannelResource($channel);
    }

    /**
     * sets initial subscriptions, roles, etc.
     *
     * @param Illuminate\Support\Collection $user
     * @param Illuminate\Support\Collection $channel
     *
     * @return void
     */
    protected function setInitialUserToChannelRoles($user, $channel)
    {
        // subscribes user to channel that was just created
        $user->subscriptions()->toggle($channel->id);
        $this->updateSubscriptions($user->id, $channel->id, true);

        // Set the creator of channel as the administrator of it
        $user->channelRoles()->attach($channel, [
            'role' => 'administrator',
        ]);
    }

    /**
     * Whether or the user is breaking the time limit for creating another channel.
     *
     * @return mixed
     */
    protected function tooEarlyToCreate()
    {
        // white-listed users are fine
        if ($this->mustBeWhitelisted()) {
            return false;
        }

        $lastCreated = Activity::where([
            ['subject_type', 'App\Channel'],
            ['user_id', Auth::user()->id],
            ['name', 'created_channel'],
        ])->orderBy('created_at', 'desc')->first();

        if ($lastCreated) {
            $timeDiff = time() - strtotime($lastCreated->created_at);

            if ($timeDiff < (60 * 60 * 24 * 3)) {
                return (60 * 60 * 24 * 3) - $timeDiff;
            }
        }

        return false;
    }

    /**
     * Patches the channel model with recently send info.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function patch(Request $request, Channel $channel)
    {
        $this->validate($request, [
            'description' => 'required|max:230|string',
            'cover_color' => 'required|in:Dark Blue,Blue,Red,Dark,Pink,Dark Green,Bright Green,Purple,Gray,Orange',
        ]);

        $channel->update([
            'description' => $request->description,
            'color'       => $request->cover_color,
            'nsfw'        => $request->input('nsfw', 0),
        ]);

        event(new ChannelWasUpdated($channel));

        return res(200, 'Channel has been updated successfully.');
    }

    /**
     * Destroys the channel record and all its related records. Currently only Voten administrators have such permission.
     *
     * @param Channel $channel
     * 
     * @return response 
     */
    public function destroy(Channel $channel)
    {
        request()->validate([
            'password' => ['required', new CurrentPassword], 
        ]);

        // remove all channel's data stored on the database
        Submission::where('channel_id', $channel->id)->forceDelete();
        Comment::where('channel_id', $channel->id)->forceDelete();
        Report::where('channel_id', $channel->id)->forceDelete();
        DB::table('subscriptions')->where('channel_id', $channel->id)->delete();
        DB::table('roles')->where('channel_id', $channel->id)->delete();

        // pull the trigger
        $channel->forceDelete();

        // clear cache
        Cache::flush();

        return res(200, 'Channel deleted successfully.');
    }
}
