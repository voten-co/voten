<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

trait ApiAuthentication
{
    /**
     * Set user's default data into cache to save few queries. 
     * 
     * @param User $user 
     * @return void 
     */
    protected function storeInRedis($user)
    {
        $userData = [
            'submissionsCount' => 0,
            'commentsCount'    => 0,

            'submissionXp' => 0,
            'commentXp'    => 0,

            'hiddenSubmissions' => collect(),
            'subscriptions'     => collect(),

            'blockedUsers' => collect(),

            'submissionLikes'   => collect(),

            'bookmarkedSubmissions' => collect(),
            'bookmarkedComments'    => collect(),
            'bookmarkedChannels'    => collect(),
            'bookmarkedUsers'       => collect(),

            'commentLikes'   => collect(),
        ];

        Redis::hmset('user.'.$user->id.'.data', $userData);
    }
    
    /**
     * Generates a valid personal access token. 
     * 
     * @param User $user 
     * @return JSON 
     */
    protected function generateAccessToken($user)
    {
        $token = $user->createToken($user->username . ' - ' . now()->toDateTimeString()); 

        return response()->json([
            'token_type' => 'Bearer',
            'access_token' => $token->accessToken,
            'expires_in' => 60 * 60 * 24 * 365
        ]);
    }
}
