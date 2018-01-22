<?php

namespace App\Http\Controllers;

use App\Traits\CachableUser;
use Auth;
use DB;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $request->validate([
            'channel_id' => 'required|exists:channels,id',
        ]);

        try {
            DB::table('hidden_channels')->insert([
                'user_id'     => Auth::id(),
                'channel_id'  => $request->channel_id,
            ]);

            // update the cach record for hiddenSubmissions:
            $this->updateHiddenChannels(Auth::id(), $request->channel_id);

            return response('Channel blocked successfully.', 200);
        } catch (\Exception $e) {
            return response('Something went wrong!', 500);
        }
    }

    /**
     * Remove the hidden_channel record from storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'channel_id' => 'required|exists:channels,id',
        ]);

        DB::table('hidden_channels')->where([
            ['user_id', Auth::id()],
            ['channel_id', $request->channel_id],
        ])->delete();

        return response('Channel unblocked successfully.', 200);
    }
}
