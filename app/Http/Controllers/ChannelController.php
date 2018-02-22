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
        $this->middleware('auth', ['except' => ['show', 'submissions', 'get', 'moderators', 'fillStore', 'redirect']]);
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

            default:
                // hot
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
     * @return \Illuminate\Support\Collection
     */
    public function submissions(Request $request)
    {
        $this->validate($request, [
            'sort'         => 'in:hot,new,rising|nullable|max:25',
            'page'         => 'integer|min:1',
            'channel_name' => 'required|exists:channels,name',
        ]);

        return SubmissionResource::collection(
            $this->getSubmissions($request->channel_name, $request->sort)
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
     * Returns all the nesseccary information to fill the channelStore on front-end.
     *
     * @return Collection
     */
    public function get(Request $request)
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
            return response('During beta, channel creation requires a minimum of 10 xp points.
            Either do a bit of activity or contact administrators to lift the limits for your account.', 500);
        }

        $tooEarly = $this->tooEarlyToCreate();

        if ($tooEarly != false) {
            return response("Looks like you're over doing it. You can create another channel in ".$tooEarly.' seconds. Thank you for being patient.', 500);
        }

        $channel = Channel::create([
            'name'        => $request->name,
            'description' => $request->description,
            'nsfw'        => $request->nsfw,
            'avatar'      => '/imgs/channel-avatar.png',
            'color'       => 'Blue',
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
    public function patch(Request $request)
    {
        $this->validate($request, [
            'id'          => 'required|exists:channels',
            'description' => 'required|max:230|string',
            'cover_color' => 'required|in:Dark Blue,Blue,Red,Dark,Pink,Dark Green,Bright Green,Purple,Gray,Orange',
            'nsfw'        => 'required|boolean',
        ]);

        $channel = $this->getChannelById(request('id'));

        abort_unless($this->mustBeAdministrator($channel->id), 403);

        $channel->update([
            'description' => $request->description,
            'color'       => $request->cover_color,
            'nsfw'        => $request->nsfw,
        ]);

        event(new ChannelWasUpdated($channel));

        return res(200, 'The channel has been successfully updated');
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
