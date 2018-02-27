<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters;
use App\Http\Resources\ChannelResource;
use App\Suggested;
use App\Traits\CachableChannel;
use App\Traits\CachableUser;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SuggestionController extends Controller
{
    use CachableUser, CachableChannel, Filters;

    public function __construct()
    {
        $this->middleware('voten-administrator', ['except' => ['channel', 'discover']]);
    }

    /**
     * Returns the suggested channel.
     *
     * @return \Illuminate\Support\Collection $channel
     */
    public function channel()
    {
        try {
            return new ChannelResource(
                Suggested::whereNotIn('channel_id', $this->subscriptions())->inRandomOrder()->firstOrFail()->channel
            );
        } catch (\Exception $e) {
            return res(200, 'No channel to suggest at this time.');
        }
    }

    /**
     * Returnes a collection of suggested channels for the auth user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function discover(Request $request)
    {
        // default
        if (!$request->filter && !$request->order_by) {
            $defaultChannels = ($request->exclude_subscribeds == 'true') ? Suggested::whereNotIn('channel_id', $this->subscriptions())
                ->orderBy('z_index', 'desc')->simplePaginate(20) : Suggested::orderBy('z_index', 'desc')->simplePaginate(20);

            $defaultChannels->setCollection($defaultChannels->pluck('channel'));

            return ChannelResource::collection($defaultChannels);
        }

        // searched
        if ($request->filter) {
            $channels = Channel::search($request->filter)->paginate(20);

            return ChannelResource::collection(($request->exclude_subscribeds == 'true') ? $this->noSubscribedFilter($channels) : $channels);
        }

        // sorted by an option
        $channels = (new Channel())->newQuery();

        if ($request->order_by == 'Newest') {
            $channels->orderBy('id', 'desc');
        } elseif ($request->order_by == 'Oldest') {
            $channels->orderBy('id', 'asc');
        } elseif ($request->order_by == 'Subscribers') {
            $channels->orderBy('subscribers', 'desc');
        } elseif ($request->order_by == 'Activity') {
            $channels->withCount('submissions')->orderBy('submissions_count', 'desc');
        }

        if ($request->exclude_subscribeds == 'true') {
            $channels->whereNotIn('id', $this->subscriptions());
        }

        return ChannelResource::collection($channels->simplePaginate(20));
    }

    /**
     * stores a new suggested channel record.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'channel_name'  => 'required',
            'z_index'       => 'required|integer',
        ]);

        $channel = $this->getChannelByName($request->channel_name);

        $suggested = new Suggested([
            'z_index'     => $request->z_index,
            'group'       => $request->group,
            'channel_id'  => $channel->id,
        ]);

        $suggested->save();

        Cache::forget('default-channels-ids');

        return Suggested::findOrFail($suggested->id);
    }

    /**
     * indexes all the models for admin panel.
     *
     * @return \Illuminate\Support\Collection
     */
    public function adminIndex()
    {
        return Suggested::all();
    }

    /**
     * destroys the record.
     *
     * @return response
     */
    public function destroy(Suggested $suggested)
    {
        $suggested->delete();

        Cache::forget('default-channels-ids');

        return res(200, 'Channel is no longer suggested. ');
    }
}
