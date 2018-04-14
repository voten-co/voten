<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Rules\NotSelfId;
use App\Traits\CachableUser;
use App\User;

class BlockUsersController extends Controller
{
    use CachableUser;

    /**
     * Block/Unblock $user for auth user. 
     *
     * @param User $user 
     * 
     * @return response
     */
    public function block(User $user)
    {
        abort_if($user->id === Auth::id(), 400);

        $result = Auth::user()->hiddenUsers()->toggle($user->id);

        // blocked 
        if ($result['attached']) {
            $this->updateBlockedUsers(Auth::id(), $user->id, true);

            return res(201, "Blocked {$user->username} successfully.");
        }

        // unblocked 
        $this->updateBlockedUsers(Auth::id(), $user->id, false);

        return res(200, "{$user->username} is no longer blocked.");
    }
}
