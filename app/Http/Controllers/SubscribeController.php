<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChannelResource;
use App\Traits\CachableChannel;
use App\Traits\CachableUser;
use Auth;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    use CachableUser, CachableChannel;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return ChannelResource::collection(Auth::user()->subscriptions()->simplePaginate(20));
    }

    /**
     * subscribing/unsubscrbing to channels.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return status
     */
    public function subscribeToggle(Request $request)
    {
        $this->validate($request, [
            'channel_id' => 'required|integer',
        ]);

        $user = Auth::user();

        try {
            $result = $user->subscriptions()->toggle($request->channel_id);
        } catch (\Exception $e) {
            return response('duplicate action', 200);
        }

        // subscibed
        if ($result['attached']) {
            $this->updateSubscriptions($user->id, $request->channel_id, true);

            $this->updateChannelSubscribersCount($request->channel_id);

            return response('Subscribed', 200);
        }

        // unsubscribed
        $this->updateSubscriptions($user->id, $request->channel_id, false);

        $this->updateChannelSubscribersCount($request->channel_id, -1);

        return response('Unsubscribed', 200);
    }

    /**
     * whether or not the user is subscribed to the channel.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function isSubscribed(Request $request)
    {
        $this->validate($request, [
            'channel_id' => 'required|integer',
        ]);

        $subscriptions = $this->subscriptions();

        return in_array($request->channel_id, $subscriptions) ? 'true' : 'false';
    }
}
