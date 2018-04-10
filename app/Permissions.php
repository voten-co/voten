<?php

namespace App;

use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Cache;

trait Permissions
{
    /**
     * Is Auth user a Voten official administrator.
     *
     * @param $user_id
     *
     * @return bool
     */
    protected function mustBeVotenAdministrator($user_id = 0)
    {
        if (!Auth::check()) {
            return false;
        }

        if ($user_id === 0) {
            $user_id = Auth::id();
        }

        $users = $this->getVotenAdministrators();

        return $users->contains($user_id);
    }

    /**
     * Is Auth user a Voten official administrator.
     *
     * @return bool
     */
    protected function mustBeWhitelisted()
    {
        $users = $this->getWhitelistedUsers();

        return $users->contains(Auth::id()) || $this->mustBeVotenAdministrator();
    }

    /**
     * Is Auth user the administrator of a channel.
     *
     * @param int $channel
     *
     * @return bool
     */
    protected function mustBeAdministrator($channel, $exclude = false)
    {
        if ($exclude) {
            return Auth::user()->roles()->where('channel_id', $channel)->pluck('role')->contains('administrator');
        }

        return $this->mustBeVotenAdministrator() || Auth::user()->roles()->where('channel_id', $channel)->pluck('role')->contains('administrator');
    }

    /**
     * Is Auth user the (at least ) moderator of channel.
     *
     * @param int $channel
     *
     * @return bool
     */
    protected function mustBeModerator($channel)
    {
        $roles = Auth::user()->roles()->where('channel_id', $channel)->pluck('role');

        return $this->mustBeVotenAdministrator() || $roles->contains('moderator') || $roles->contains('administrator');
    }

    /**
     * Is Auth user the (at least ) subscriber of channel.
     *
     * @param int $channel
     *
     * @return bool
     */
    protected function mustBeSubscriber($channel)
    {
        $roles = Auth::user()->roles()->where('channel_id', $channel)->pluck('role');

        return $roles->contains('subscriber') || $roles->contains('moderator') || $roles->contains('administrator');
    }

    /**
     * Wether or not the Auth user owns the model(the model could be anything: submissions, comment, photo etc...).
     *
     * @param Collection $model
     *
     * @return bool
     */
    protected function mustBeOwner($model)
    {
        return $model->ownedBy(Auth::user());
    }

    /**
     * Wether or not the Auth user is in the blocked list of the User $user.
     *
     * @param int $user1
     * @param int $user2
     *
     * @return bool
     */
    protected function areBlockedToEachOther($user1, $user2)
    {
        return DB::table('hidden_users')->where([
            'user_id'         => $user1,
            'blocked_user_id' => $user2,
        ])->orWhere([
            'user_id'         => $user2,
            'blocked_user_id' => $user1,
        ])->count() > 0;
    }

    /**
     * Is the domain of the submitted $url blocked in $channel or everwhere(specified
     * by voten-administrators).
     *
     * @param string $url
     * @param string $channel
     *
     * @return bool
     */
    protected function isDomainBlocked($url, $channel)
    {
        return BlockedDomain::where([
            ['domain', domain($url)],
            ['channel', 'all'],
        ])->orWhere([
            ['domain', domain($url)],
            ['channel', $channel],
        ])->exists();
    }

    /**
     * Whether or not the Auth user is banned from submitting to this channel.
     *
     * @param string $channel
     * @param int    $user_id
     *
     * @return bool
     */
    protected function isUserBannedFromChannel($user_id, $channel)
    {
        if ($user_id === 0) {
            $user_id = Auth::user()->id;
        }

        return Ban::where([
            ['user_id', $user_id],
            ['channel', $channel],
            ['unban_at', '>=', Carbon::now()],
        ])->orWhere([
            ['user_id', $user_id],
            ['channel', 'all'],
            ['unban_at', '>=', Carbon::now()],
        ])->exists();
    }

    /**
     * Does the auth user have the required minimum xp points?
     *
     * @param int $number
     *
     * @return bool
     */
    protected function mustHaveMinimumXp($number)
    {
        return (Auth::user()->submission_xp + Auth::user()->comment_xp) >= $number; 

        $stats = $this->userStats(Auth::id());

        return ($stats['submission_xp'] + $stats['comment_xp']) >= $number;
    }

    /* --------------------------------------------------------------------- */
    /* ------------------------------- Getters ----------------------------- */
    /* --------------------------------------------------------------------- */

    protected function getVotenAdministrators()
    {
        return Cache::rememberForever('general.voten-administrators', function () {
            return AppointeddUser::where('appointed_as', 'administrator')->pluck('user_id');
        });
    }

    protected function getWhitelistedUsers()
    {
        return Cache::rememberForever('general.whitelisted', function () {
            return AppointeddUser::where('appointed_as', 'whitelisted')->pluck('user_id');
        });
    }
}
