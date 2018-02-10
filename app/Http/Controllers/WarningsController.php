<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Mail\ChannelRemovalWarning;
use Carbon\Carbon;

class WarningsController extends Controller
{
    public function __construct()
    {
        $this->middleware('voten-administrator');
    }

    /**
     * Sends "ChannelRemovalWarning" email for inactive channels.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function channelsRemoval()
    {
        $inactive_channels = Channel::where('created_at', '<=', Carbon::now()->subMonths(2))
            ->whereDoesntHave('submissions', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subMonths(2));
            })->get();

        foreach ($inactive_channels as $channel) {
            $mods = $channel->moderators;

            foreach ($mods as $user) {
                if ($user->confirmed) {
                    \Mail::to($user->email)->queue(new ChannelRemovalWarning($user, $channel));
                }
            }
        }

        session()->flash('status', $inactive_channels->count().' channels are going to get a warning email. ');

        return back();
    }
}
