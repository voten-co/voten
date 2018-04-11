<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChannelResource;
use App\Traits\CachableChannel;
use App\Traits\CachableUser;
use Illuminate\Http\Request;
use App\Channel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscribeController extends Controller
{
    use CachableUser, CachableChannel;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return ChannelResource::collection(
            Auth::user()->subscriptions()->simplePaginate(20)
        );
    }

    /**
     * Toggle subscription to a channel.
     *
     * @param integer $channel 
     *
     * @return Response 
     */
    public function subscribe(Channel $channel)
    {
        if ($isUnsubscribed = $this->hasAlreadySubscribed($channel->id)) {
            Auth::user()->subscriptions()->detach($channel->id);
        } else {
            Auth::user()->subscriptions()->attach($channel->id);
        }

        $this->handleCache(Auth::id(), $channel->id, $isUnsubscribed);

        return $isUnsubscribed ? res(200, "Unsubscribed from {$channel->name} channel successfully.") : res(201, "Subscribed to {$channel->name} successfully.");
    }

    /**
     * Handles cache records that need modifications because of the new subscriptio record. 
     * 
     * @param integer $user_id 
     * @param integer $channel_id 
     * @param boolean $isUnsubscribed 
     * 
     * @return void 
     */
    protected function handleCache($user_id, $channel_id, $isUnsubscribed)
    {
        if ($isUnsubscribed) {
            $this->updateSubscriptions($user_id, $channel_id, false);
            $this->updateChannelSubscribersCount($channel_id, -1);

            return;
        }

        $this->updateSubscriptions($user_id, $channel_id, true);
        $this->updateChannelSubscribersCount($channel_id);
    }

    /**
    * Is Auth user already subscribed to the channel. 
    * 
    * @param integer $channel_id 

    * @return boolean 
    */
    protected function hasAlreadySubscribed($channel_id)
    {
        return DB::table('subscriptions')->where([
            ['user_id', Auth::id()], 
            ['channel_id', $channel_id]
        ])->exists();    
    }
}
