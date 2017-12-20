<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Channel;
use App\ChannelForbiddenName;
use App\Comment;
use App\Events\ChannelWasUpdated;
use App\Filters;
use App\Report;
use App\Submission;
use App\Traits\CachableChannel;
use App\Traits\CachableUser;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $this->middleware('auth', ['except' => ['show', 'submissions', 'getChannel', 'moderators', 'fillStore', 'redirect']]);
    }

    /**
     * gets submissions.
     *
     * @param string $channel
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getSubmissions($channel, $sort)
    {
        $submissions = (new Submission())->newQuery();

        $submissions->where('channel_name', $channel);

        // exclude user's hidden submissions
        if (Auth::check()) {
            $submissions->whereNotIn('id', $this->hiddenSubmissions());
        }

        // exclude NSFW if user doens't want to see them or if the user is not authinticated
        if (!Auth::check() || !settings('nsfw')) {
            $submissions->where('nsfw', false);
        }

        if ($sort == 'new') {
            $submissions->orderBy('created_at', 'desc');
        }

        if ($sort == 'rising') {
            $submissions->where('created_at', '>=', Carbon::now()->subHour())
                        ->orderBy('rate', 'desc');
        }

        if ($sort == 'hot') {
            $submissions->orderBy('rate', 'desc');
        }

        return $submissions->simplePaginate(15);
    }

    /**
     * Get submissions API with ajax calls.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function submissions(Request $request)
    {
        $this->validate($request, [
            'sort'     => 'alpha_num|max:25',
            'page'     => 'Integer',
            'channel' => 'required|alpha_num|max:25',
        ]);

        return $this->getSubmissions($request->channel, $request->sort);
    }

    /**
     * shows the submission page to guests.
     *
     * @param string $channel
     * @param string $slug
     *
     * @return view
     */
    public function show($channel, Request $request)
    {
        $submissions = $this->getSubmissions($channel, $request->sort ?? 'hot');
        $channel = $this->getChannelByName($channel);

        return view('channel.show', compact('submissions', 'channel'));
    }

    /**
     * Returns all the nesseccary information to fill the channelStore on front-end.
     *
     * @return Collection
     */
    public function fillStore(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $channel = $this->getChannelByName($request->name);

        return $channel;
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
            'name'        => 'required|alpha_num|min:3|max:50|unique:channels',
            'description' => 'required|min:10|max:250',
        ]);

        if (Auth::user()->isShadowBanned()) {
            return response('I hate to break it to you but your account has been banned.', 500);
        }

        if (!$this->mustHaveMinimumKarma(10) && !$this->mustBeWhitelisted()) {
            return response('During beta, channel creation requires a minimum of 10 karma points. 
            Either do a bit of activity or contact administrators to lift the limits for your account.', 500);
        }

        $tooEarly = $this->tooEarlyToCreate();

        if ($tooEarly != false) {
            return response("Looks like you're over doing it. You can create another channel in ".$tooEarly.' seconds. Thank you for being patient.', 500);
        }

        if ($this->isForbiddenName($request->name)) {
            return response('This name is forbidden. Please pick another one.', 500);
        }

        $channel = Channel::create([
            'name'        => $request->name,
            'description' => $request->description,
            'nsfw' => $request->nsfw,
            'avatar'      => '/imgs/channel-avatar.png',
        ]);

        $this->setInitialUserToChannelRoles(Auth::user(), $channel);

        return $channel;
    }

    /**
     * sets intial subscriptions, roles, etc.
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
     * is the name in the blacklist names.
     *
     * @return bool
     */
    protected function isForbiddenName($name)
    {
        return ChannelForbiddenName::where('name', $name)->exists();
    }

    /**
     * Whether or the user is breaking the time limit for creating another channel.
     *
     * @return mixed
     */
    protected function tooEarlyToCreate()
    {
        // exclude white-listed users form this checking
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
    public function patch(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required|alpha_num|max:25',
            'description' => 'required|max:230',
            'color'       => 'required|in:Dark Blue,Blue,Red,Dark,Pink,Dark Green,Bright Green,Purple,Gray,Orange',
        ]);

        $channel = $this->getChannelByName($request->name);

        abort_unless($this->mustBeAdministrator($channel->id), 403);

        $channel->update([
            'description' => $request->description,
            'color'       => $request->color,
            'nsfw'        => ($request->nsfw ? true : false),
        ]);

        event(new ChannelWasUpdated($channel));

        return response('The channel has been successfully updated', 200);
    }

    /**
     * Searches channels. Mostly used for submiting new submissions.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function getChannels(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|alpha_num|max:25',
        ]);

        return Channel::where('name', 'like', '%'.$request->name.'%')
                    ->orderBy('subscribers', 'desc')
                    ->select('name')->take(100)->get()->pluck('name');
    }

    /**
     * looks for the channel by its name.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function getChannel(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        return $this->getChannelByName($request->name);
    }

    /**
     * @param \App\Channel $channel
     *
     * @return bool
     */
    protected function isNSWF($channel)
    {
        return $channel->nsfw == 1 && Auth::user()->settings['nsfw'] == 0;
    }

    /**
     * returns all the user models moderating the channel.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function moderators(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $channel = $this->getChannelByName($request->name);

        return $channel->moderators;
    }

    /**
     * redirects old channel URLs (/c/channel/hot) to the new one (/c/channel). This is just to
     * to prevent dead URLS and also to respect our old users who shared their channels on
     * social media to support us. To them!
     *
     * @return redirect
     */
    public function redirect($channel)
    {
        return redirect('/c/'.$channel);
    }

    /**
     * Destroys the channel model and all its related models. Currently only Voten administrators have such permission.
     *
     * @param Channel $channel
     */
    public function destroy(Channel $channel)
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        if (!confirmPassword(request('password'))) {
            session()->flash('warning', "Incorrect Password. What kind of an administrator doesn't remember his password? ");

            return back();
        }

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

        session()->flash('status', 'Channel and all its records has been deleted. ');

        return redirect('/backend/channels');
    }
}