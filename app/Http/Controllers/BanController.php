<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Channel;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BanController extends Controller
{
    /**
     * Stores a App\Ban record.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'channel'  => 'required|alpha_num|max:25',
            'duration' => 'integer|min:0|max:999',
        ]);

        if ($request->channel != 'all') {
            $channel = Channel::where('name', $request->channel)->firstOrFail();
        }

        $user = User::where('username', $request->username)->firstOrFail();

        // make sure only voten-administrators are able to ban users everywhere
        if ($request->channel == 'all') {
            abort_unless($this->mustBeVotenAdministrator() && $request->username != Auth::user()->username, 403);

            // remove all user's data that might have been spam and harmful to others
            DB::table('submissions')->where('user_id', $user->id)->delete();
            DB::table('comments')->where('user_id', $user->id)->delete();
            DB::table('messages')->where('user_id', $user->id)->delete();
            DB::table('reports')->where('user_id', $user->id)->delete();
            DB::table('feedbacks')->where('user_id', $user->id)->delete();
            DB::table('roles')->where('user_id', $user->id)->delete();
            DB::table('conversations')->where('user_id', $user->id)->orWhere('contact_id', $user->id)->delete();

            // set active to 0 (to make future checkings easier)
            $user->update(['active' => false]);
        } else {
            abort_unless($this->mustBeModerator($channel->id) && $request->username != Auth::user()->username, 403);
        }

        // BAN DURATION: if the duration is set as 0 we set a really big number like 17 years!
        if (!$request->duration || $request->duration == 0) {
            $unban_at = Carbon::now()->addYears(17);
        } else {
            $unban_at = Carbon::now()->addDays($request->duration);
        }

        $blockedUser = new Ban([
            'user_id'     => $user->id,
            'channel'     => $request->channel,
            'description' => $request->description,
            'unban_at'    => $unban_at,
        ]);
        $blockedUser->save();

        $blockedUser->user = $user;

        if (!$request->ajax()) {
            // request sent from backend panel
            session()->flash('status', "{$user->username} has been banned.");

            return back();
        }

        return $blockedUser;
    }

    /**
     * Returns all the users that are banned from submitting to targeted channel.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'channel' => 'required|max:25',
        ]);

        if ($request->channel != 'all') {
            $channel = Channel::where('name', $request->channel)->firstOrFail();
        }

        // make sure only voten-administrators are able to ban users everywhere
        if ($request->channel == 'all') {
            abort_unless($this->mustBeVotenAdministrator(), 403);
        } else {
            abort_unless($this->mustBeModerator($channel->id), 403);
        }

        return Ban::where('channel', $request->channel)
                    ->with('user')
                    ->where('unban_at', '>=', Carbon::now())
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Unban.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'user_id'  => 'required|integer',
            'channel'  => 'required|alpha_num|max:25',
        ]);

        if ($request->channel != 'all') {
            $channel = Channel::where('name', $request->channel)->firstOrFail();
        }

        // make sure only voten-administrators are able to ban users everywhere
        if ($request->channel == 'all') {
            abort_unless($this->mustBeVotenAdministrator(), 403);
        } else {
            abort_unless($this->mustBeModerator($channel->id), 403);
        }

        Ban::where('user_id', $request->user_id)
                    ->where('channel', $request->channel)
                    ->delete();

        if ($request->channel == 'all') {
            User::where('id', $request->user_id)->update(['active' => true]);
        }

        if (!$request->ajax()) {
            // request sent from backend panel
            session()->flash('status', 'User is no longer banned.');

            return back();
        }

        return response('Unbanned from '.$request->channel, 200);
    }
}
