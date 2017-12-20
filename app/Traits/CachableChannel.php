<?php

namespace App\Traits;

use App\Channel;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

trait CachableChannel
{
    /**
     * Fetches all the cachable data for the channel and put it in the cache.
     *
     * @param int $id
     *
     * @return void
     */
    protected function cacheChannelData($id)
    {
        $channel = Channel::where('id', $id)->firstOrFail();

        $channelData = [
            'submissionsCount' => $channel->submissions()->count(),
            'commentsCount'    => $channel->comments()->count(),
            'subscribersCount' => $channel->subscriptions()->count(),

            'mods' => $channel->mods(),
        ];

        Redis::hmset('channel.'.$id.'.data', $channelData);

        return $channelData;
    }

    /**
     * mods of the channel (both moderators and administrators).
     *
     * @return array
     */
    protected function channelMods($id)
    {
        if ($value = Redis::hget('channel.'.$id.'.data', 'mods')) {
            return json_decode($value);
        }

        $result = $this->cacheChannelData($id);

        return collect(json_decode($result['mods']));
    }

    /**
     * updates the mods records of the channel.
     *
     * @param int $id
     * @param int $user_id
     *
     * @return void
     */
    protected function updateChannelMods($id, $user_id, $add = true)
    {
        $channel = $this->getChannelById($id);

        // we need to make sure the cached data exists
        if (!Redis::hget('channel.'.$id.'.data', 'mods')) {
            $this->cacheChannelData($id);
        }

        Redis::hset('channel.'.$id.'.data', 'mods', json_encode($channel->mods()));
    }

    /**
     * Returns all the stats of the auth channel.
     *
     * @param int $id
     *
     * @return Illuminate\Support\Collection
     */
    protected function channelStats($id)
    {
        $stats = Redis::hmget('channel.'.$id.'.data',
                        'submissionsCount', 'commentsCount', 'subscribersCount');

        // if channel's data is not cached, then fetch it from database and then cache it
        if (json_decode($stats[0]) === null || json_decode($stats[1]) === null || json_decode($stats[2]) === null) {
            return $this->cacheChannelData($id);
        }

        return collect([
            'submissionsCount' => json_decode($stats[0]),
            'commentsCount'    => json_decode($stats[1]),
            'subscribersCount' => json_decode($stats[2]),
        ]);
    }

    /**
     * updates the submissionsCount of the channel.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateChannelSubmissionsCount($id, $number = 1)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('channel.'.$id.'.data', 'submissionsCount')) {
            $this->cacheChannelData($id);
        }

        Redis::hincrby('channel.'.$id.'.data', 'submissionsCount', $number);
    }

    /**
     * updates the commentsCount of the channel.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateChannelCommentsCount($id, $number = 1)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('channel.'.$id.'.data', 'commentsCount')) {
            $this->cacheChannelData($id);
        }

        Redis::hincrby('channel.'.$id.'.data', 'commentsCount', $number);
    }

    /**
     * updates the subscribersCount of the channel.
     *
     * @param int $id
     * @param int $number
     *
     * @return void
     */
    protected function updateChannelSubscribersCount($id, $number = 1)
    {
        // we need to make sure the cached data exists
        if (!Redis::hget('channel.'.$id.'.data', 'subscribersCount')) {
            $this->cacheChannelData($id);
        }

        $subscribersCount = Redis::hincrby('channel.'.$id.'.data', 'subscribersCount', $number);

        // for newbie channels we update on each new subscription
        if ($subscribersCount < 10000) {
            DB::table('channels')->where('id', $id)->update(['subscribers' => $subscribersCount]);

            return;
        }
        // but for major ones, we do this once a 100 times
        if (($subscribersCount % 100) === 0) {
            DB::table('channels')->where('id', $id)->update(['subscribers' => $subscribersCount]);
        }
    }

    /**
     * Returns the Channel model using the $id. First it tries to fetch it from Cache. In case it doesn't
     * exist in the cache, fetches it from the database, and then put it in the cache and then return it.
     *
     * @param string $id
     *
     * @return Illuminate\Support\Collection
     */
    protected function getChannelById($id)
    {
        return Cache::remember('channel.id.'.$id, 60 * 60 * 24, function () use ($id) {
            return Channel::withTrashed()->findOrFail($id);
        });
    }

    /**
     * Returns the Channel model using the name. First it tries to fetch it from Cache. In case it doesn't
     * exist in the cache, fetches it from the database, and then put it in the cache and then return it.
     *
     * @param string $name
     *
     * @return Illuminate\Support\Collection
     */
    protected function getChannelByName($name)
    {
        return Cache::remember('channel.name.'.$name, 60 * 60 * 24, function () use ($name) {
            return Channel::withTrashed()->where('name', $name)->firstOrFail();
        });
    }

    /**
     * Put the channel infto the cache. In case it already exists, updates it. Otherwise adds it.
     *
     * @param Illuminate\Support\Collection $channel
     */
    protected function putChannelInTheCache($channel)
    {
        Cache::put('channel.id.'.$channel->id, $channel, 60 * 60 * 24);

        Cache::put('channel.name.'.$channel->name, $channel, 60 * 60 * 24);
    }

    /**
     * returns the IDs of the default channels.
     *
     * @return array
     */
    public function getDefaultChannels()
    {
        return Cache::remember('default-channels-ids', 60 * 60 * 24, function () {
            return \App\Suggested::groupBy('channel_id')->select('id', 'channel_id')->pluck('channel_id');
        });
    }

    /**
     * returns the IDs of the default channels.
     *
     * @return array
     */
    protected function getDefaultChannelRecords()
    {
        return Cache::remember('default-channels-records', 60 * 60 * 24, function () {
            $ids = \App\Suggested::groupBy('channel_id')->select('id', 'channel_id')->pluck('channel_id');

            return Channel::whereIn('id', $ids)->get();
        });
    }
}
