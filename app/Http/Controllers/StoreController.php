<?php

namespace App\Http\Controllers;

use App\Traits\CachableChannel;
use App\Traits\CachableUser;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Http\Resources\UserStoreResource;

class StoreController extends Controller
{
    use CachableUser, CachableChannel;

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['sidebarChannels']]);
    }

    /**
     * Returns all the neccessary information for filling the Store. To reduce the number
     * of database requests, we're going to use HTML5's local-storage. Meaning that only
     * users with new browsers or when doing clear-cach a request will be sent here.
     *
     * @return collection
     */
    public function index()
    {
        return new UserStoreResource(
            [
                'submissionLikes'           => $this->submissionLikes(), // cached 
                'commentLikes'              => $this->commentLikes(), // cached
                'bookmarkedSubmissions'       => $this->bookmarkedSubmissions(), // cached
                'bookmarkedComments'          => $this->bookmarkedComments(), // cached
                'bookmarkedChannels'          => $this->bookmarkedChannels(), // cached
                'bookmarkedUsers'             => $this->bookmarkedUsers(), // cached
                'subscribedChannels'          => $this->subscribedChannels(),
                'moderatingChannels'          => $this->moderatingChannels(),
                'moderatingChannelsRecords'   => $this->moderatingChannelsRecords(),
                'bookmarkedChannelsRecords'   => $this->bookmarkedChannelsRecords(),
                'blockedUsers'                => $this->blockedUsers(), // cached
            ]
        );
    }

    protected function moderatingChannelsRecords()
    {
        return DB::table('roles')->where('user_id', Auth::id())->get();
    }

    // Returnes Auth user's moderated channels
    protected function moderatingChannels()
    {
        return Auth::user()->channelRoles->unique('name');
    }

    // Returns Auth user's (submission) likes records
    protected function submissionLikes()
    {
        return $this->submissionLikesIds();
    }

    // Returns Auth user's (submission) like records
    protected function commentLikes()
    {
        return $this->commentLikesIds();
    }

    // returns subscriptions of Auth user
    protected function subscribedChannels()
    {
        if (!Auth::check()) {
            return $this->getDefaultChannelRecords();
        }

        return Auth::user()->subscriptions;
    }

    protected function bookmarkedChannelsRecords()
    {
        return Auth::user()->bookmarkedChannels;
    }

    protected function sidebarChannels()
    {
        return $this->subscribedChannels();
    }
}
