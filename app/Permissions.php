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
        if ($user_id === 0) {
            $user_id = Auth::user()->id;
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

        return $users->contains(Auth::user()->id);
    }

    /**
     * Is Auth user the administrator of a category.
     *
     * @param int $category
     *
     * @return bool
     */
    protected function mustBeAdministrator($category, $exclude = false)
    {
        if ($exclude) {
            return Auth::user()->roles()->where('category_id', $category)->pluck('role')->contains('administrator');
        }

        return $this->mustBeVotenAdministrator() || Auth::user()->roles()->where('category_id', $category)->pluck('role')->contains('administrator');
    }

    /**
     * Is Auth user the (at least ) moderator of category.
     *
     * @param int $category
     *
     * @return bool
     */
    protected function mustBeModerator($category)
    {
        $roles = Auth::user()->roles()->where('category_id', $category)->pluck('role');

        return $this->mustBeVotenAdministrator() || $roles->contains('moderator') || $roles->contains('administrator');
    }

    /**
     * Is Auth user the (at least ) subscriber of category.
     *
     * @param int $category
     *
     * @return bool
     */
    protected function mustBeSubscriber($category)
    {
        $roles = Auth::user()->roles()->where('category_id', $category)->pluck('role');

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
     * Is the domain of the submitted $url blocked in $category or everwhere(specified
     * by voten-administrators).
     *
     * @param string $url
     * @param string $category
     *
     * @return bool
     */
    protected function isDomainBlocked($url, $category)
    {
        return BlockedDomain::where([
            ['domain', domain($url)],
            ['category', 'all'],
        ])->orWhere([
            ['domain', domain($url)],
            ['category', $category],
        ])->exists();
    }

    /**
     * Whether or not the Auth user is banned from submitting to this category.
     *
     * @param string $category
     * @param int    $user_id
     *
     * @return bool
     */
    protected function isUserBanned($user_id, $category)
    {
        if ($user_id === 0) {
            $user_id = Auth::user()->id;
        }

        return Ban::where([
            ['user_id', $user_id],
            ['category', $category],
            ['unban_at', '>=', Carbon::now()],
        ])->orWhere([
            ['user_id', $user_id],
            ['category', 'all'],
            ['unban_at', '>=', Carbon::now()],
        ])->exists();
    }

    /**
     * Does the auth user have the required minimum karma points?
     *
     * @param integer $number
     *
     * @return bool
     */
    protected function mustHaveMinimumKarma($number)
    {
        $stats = $this->userStats(Auth::id());

        return ($stats['submission_karma'] + $stats['comment_karma']) >= $number;
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
