<?php

namespace App\Http\Controllers;

use App\Ban;
use App\Channel;
use App\Http\Resources\BannedUserResource;
use App\Rules\NotSelfUsername;
use App\Traits\CachableChannel;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class BanController extends Controller
{
    use CachableChannel;

    /**
     * Moderator banning a user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function storeAsChannelModerator(Request $request, Channel $channel)
    {
        $this->validate($request, [
            'user_id'    => 'required|exists:users,id',
            'duration'    => 'required|integer|min:0|max:999',
            'description' => 'nullable|string|max:5000',
        ]);

        $user = User::find(request('user_id'));

        // BAN DURATION: if the duration is set as 0 we set a really big number like 17 years!
        if ($request->duration == 0) {
            $unban_at = Carbon::now()->addYears(17);
        } else {
            $unban_at = Carbon::now()->addDays($request->duration);
        }

        $bannedUser = Ban::create([
            'user_id'     => $user->id,
            'channel'     => $channel->name,
            'description' => $request->description,
            'unban_at'    => $unban_at,
        ]);

        $bannedUser->user = $user;

        return new BannedUserResource($bannedUser);
    }

    /**
     * Voten administrator banning a user (bans everwhere and has more consequences).
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function storeAsVotenAdministrator(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
            'duration'    => 'integer|min:0|max:999',
            'description' => 'nullable|string|max:5000',
            'delete_posts' => 'boolean',
        ]);

        $user = User::find(request('user_id'));

        // remove all user's data that might have been spam and harmful to others
        if ($request->delete_posts) {
            DB::table('submissions')->where('user_id', $user->id)->delete();
            DB::table('comments')->where('user_id', $user->id)->delete();
            DB::table('messages')->where('user_id', $user->id)->delete();
            DB::table('reports')->where('user_id', $user->id)->delete();
            DB::table('feedbacks')->where('user_id', $user->id)->delete();
            DB::table('conversations')->where('user_id', $user->id)->orWhere('contact_id', $user->id)->delete();
        }

        // BAN DURATION: if the duration is set as 0 we set a really big number like 17 years!
        if ($request->duration == 0) {
            $unban_at = Carbon::now()->addYears(17);
        } else {
            $unban_at = Carbon::now()->addDays($request->duration);
        }

        // set active to 0 (to make future checking easier)
        $user->update(['active' => false]);

        $bannedUser = Ban::create([
            'user_id'     => $user->id,
            'channel'     => 'all',
            'description' => $request->description,
            'unban_at'    => $unban_at,
        ]);

        $bannedUser->user = $user;

        return new BannedUserResource($bannedUser);
    }

    /**
     * Returns all the users that are banned from submitting to targeted channel.
     *
     * @param Channel $channel
     *
     * @return \BannedUserResource
     */
    public function indexAsChannelModerator(Channel $channel)
    {
        return BannedUserResource::collection(
            Ban::where('channel', $channel->name)
                ->with('user')
                ->where('unban_at', '>=', now())
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    /**
     * All the banned users (everywhere).
     *
     * @return \BannedUserResource
     */
    public function indexAsVotenAdministrator()
    {
        return BannedUserResource::collection(
            Ban::where('channel', 'all')
                ->with('user')
                ->where('unban_at', '>=', now())
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    /**
     * Unban.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroyAsChannelModerator(Channel $channel, $user_id)
    {
        abort_unless(Ban::where('user_id', $user_id)->where('channel', $channel->name)->exists(), 404);

        Ban::where('user_id', $user_id)->where('channel', $channel->name)->delete();
        
        return res(200, 'User unbanned successfully.');
    }

    /**
     * Unban.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroyAsVotenAdministrator($user_id)
    {
        abort_unless(Ban::where('user_id', $user_id)->where('channel', 'all')->exists(), 404);

        Ban::where('user_id', $user_id)->where('channel', 'all')->delete();

        User::where('id', $user_id)->update(['active' => true]);

        return res(200, 'User unbanned successfully. ');
    }
}
