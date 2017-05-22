<?php

namespace App;

use DB;
use Auth;
use App\AppointeddUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

trait Permissions
{
    /**
     * Is Auth user a Voten official administrator
     *
     * @param $user_id
     * @return boolean
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
     * Is Auth user a Voten official administrator
     *
     * @return boolean
     */
    protected function mustBeWhitelisted()
    {
        $users = $this->getWhitelistedUsers();

        return $users->contains(Auth::user()->id);
    }


    /**
     * Is Auth user the administrator of category
     *
     * @param integer $category
     * @return boolean
     */
    protected function mustBeAdministrator($category)
    {
        return $this->mustBeVotenAdministrator() || Auth::user()->roles()->where('category_id', $category)->pluck('role')->contains('administrator');
    }


    /**
     * Is Auth user the (at least ) moderator of category
     *
     * @param integer $category
     * @return boolean
     */
    protected function mustBeModerator($category)
    {
        $roles = Auth::user()->roles()->where('category_id', $category)->pluck('role');

        return ($this->mustBeVotenAdministrator() || $roles->contains('moderator') || $roles->contains('administrator'));
    }


    /**
     * Is Auth user the (at least ) subscriber of category
     *
     * @param integer $category
     * @return boolean
     */
    protected function mustBeSubscriber($category)
    {
        $roles = Auth::user()->roles()->where('category_id', $category)->pluck('role');

        return ($roles->contains('subscriber') || $roles->contains('moderator') || $roles->contains('administrator'));
    }


    /**
     * Wether or not the Auth user owns the model(the model could be anything: submissions, comment, photo etc...)
     *
     * @param Collection $model
     * @return boolean
     */
    protected function mustBeOwner($model)
    {
        return $model->ownedBy(Auth::user());
    }


    /**
     * Wether or not the Auth user is in the blocked list of the User $user
     *
     * @param integer $user1
     * @param integer $user2
     * @return boolean
     */
    protected function areBlockedToEachOther($user1, $user2)
    {
    	return DB::table('hidden_users')->where([
    		'user_id' => $user1,
    		'blocked_user_id' => $user2
		])->orWhere([
    		'user_id' => $user2,
    		'blocked_user_id' => $user1
		])->count() > 0;
    }


    /**
     * Is the domain of the submitted $url blocked in $category or everwhere(specified
     * by voten-administrators).
     *
     * @param  String $url
     * @param  String $category
     * @return Boolean
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
     * @param  string $category
     * @param  integer $user_id
     * @return boolean
     */
    protected function isUserBanned($user_id = 0, $category)
    {
        if ($user_id === 0) {
            $user_id = Auth::user()->id;
        }

        return Ban::where([
            ['user_id', $user_id],
            ['category', $category],
        ])->orWhere([
            ['user_id', $user_id],
            ['category', 'all'],
        ])->exists();
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
