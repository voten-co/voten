<?php

namespace App\Http\Controllers;

use App\Traits\CachableUser;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Channel;

class BlockChannelsController extends Controller
{
    use CachableUser;
    
    /**
     * Store a newly created hidden_channel record in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function block(Request $request, Channel $channel)
    {
        if ($unblock = $this->hasAlreadyBlocked($channel->id)) {
            Auth::user()->blockedChannels()->detach($channel->id);
        } else {
            Auth::user()->blockedChannels()->attach($channel->id);
        }

        $this->updateBlockedChannels(Auth::id(), $channel->id, $unblock);

        return $unblock ? res(200, "Unblocked channel successfully.") : res(201, "Blocked channel successfully.");
    }

    /**
     * Is the item previosly blocked by the authenticated user 
     * 
     * @param integer $channel_id 
     * @return boolean 
     */
    protected function hasAlreadyBlocked($channel_id)
    {
        return DB::table('blocked_channels')->where([
            ['user_id', Auth::id()], 
            ['channel_id', $channel_id]
        ])->exists(); 
    }
}
