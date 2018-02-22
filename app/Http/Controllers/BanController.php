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
    public function storeAsChannelModerator(Request $request)
    {
        $this->validate($request, [
            'username'    => ['required', 'exists:users', new NotSelfUsername()],
            'channel_id'  => 'required|exists:channels,id',
            'duration'    => 'required|integer|min:0|max:999',
            'description' => 'nullable|string|max:5000',
        ]);

        $channel = $this->getChannelById($request->channel_id);
        $user = User::where('username', $request->username)->firstOrFail();

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
            'username'    => ['required', 'exists:users', new NotSelfUsername()],
            'duration'    => 'integer|min:0|max:999',
            'description' => 'nullable|string|max:5000',
        ]);

        // remove all user's data that might have been spam and harmful to others
        DB::table('submissions')->where('user_id', $user->id)->delete();
        DB::table('comments')->where('user_id', $user->id)->delete();
        DB::table('messages')->where('user_id', $user->id)->delete();
        DB::table('reports')->where('user_id', $user->id)->delete();
        DB::table('feedbacks')->where('user_id', $user->id)->delete();
        DB::table('roles')->where('user_id', $user->id)->delete();
        DB::table('conversations')->where('user_id', $user->id)->orWhere('contact_id', $user->id)->delete();

        // BAN DURATION: if the duration is set as 0 we set a really big number like 17 years!
        if ($request->duration == 0) {
            $unban_at = Carbon::now()->addYears(17);
        } else {
            $unban_at = Carbon::now()->addDays($request->duration);
        }

        // set active to 0 (to make future checkings easier)
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function indexAsChannelModerator(Request $request)
    {
        $this->validate($request, [
            'channel_id' => 'required|exists:channels,id',
        ]);

        $channel = $this->getChannelById($request->channel_id);

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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function indexAsVotenAdministrator(Request $request)
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
    public function destroyAsChannelModerator(Request $request)
    {
        $this->validate($request, [
            'user_id'    => 'required|exists:users,id',
            'channel_id' => 'required|exists:channels:id',
        ]);

        $channel = $this->getChannelById(request('channel_id'));

        Ban::where('user_id', $request->user_id)->where('channel', $channel->name)->delete();

        return res(200, 'User unbanned successfully. ');
    }

    /**
     * Unban.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroyAsVotenAdministrator(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,id',
        ]);

        Ban::where('user_id', $request->user_id)->where('channel', 'all')->delete();

        User::where('id', $request->user_id)->update(['active' => true]);

        return res(200, 'User unbanned successfully. ');
    }
}
