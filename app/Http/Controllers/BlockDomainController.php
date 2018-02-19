<?php

namespace App\Http\Controllers;

use App\BlockedDomain;
use App\Channel;
use App\Http\Resources\BlockedDomainResource;
use App\Traits\CachableChannel;
use Illuminate\Http\Request;

class BlockDomainController extends Controller
{
    use CachableChannel;

    /**
     * Stores a BlockedDomain record.
     *
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Collection $blockedDomain
     */
    public function storeAsChannelModerator(Request $request)
    {
        $this->validate($request, [
            'domain'      => 'required|url',
            'channel_id'  => 'required|exists:channels,id',
            'description' => 'nullable|string|max:5000',
        ]);

        $channel = $this->getChannelById(request('channel_id'));

        $blockedDomain = BlockedDomain::create([
            'channel'     => $channel->name,
            'domain'      => domain($request->domain),
            'description' => $request->description,
        ]);

        return new BlockedDomainResource($blockedDomain);
    }

    /**
     * Stores a BlockedDomain record.
     *
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Collection $blockedDomain
     */
    public function storeAsVotenAdministrator(Request $request)
    {
        $this->validate($request, [
            'domain'      => 'required|url',
            'description' => 'nullable|string|max:5000',
        ]);

        $blockedDomain = BlockedDomain::create([
            'channel'     => 'all',
            'domain'      => domain($request->domain),
            'description' => $request->description,
        ]);

        return new BlockedDomainResource($blockedDomain);
    }

    /**
     * Returns all the domains that are blocked for submitting(url type submission) to this channel.
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

        $channel = $this->getChannelById(request('channel_id'));

        return BlockedDomainResource::collection(
            BlockedDomain::where('channel', $channel->name)
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    /**
     * Returns all the domains that are blocked for submitting(url type submission) to this channel.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function indexVotenAdministrator(Request $request)
    {
        return BlockedDomainResource::collection(
            BlockedDomain::where('channel', 'all')
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    /**
     * Unblock.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroyAsChannelModerator(Request $request)
    {
        $this->validate($request, [
            'domain'     => 'required',
            'channel_id' => 'required|exists:channels,id',
        ]);

        $channel = $this->getChannelById(request('channel_id'));

        BlockedDomain::where('domain', $request->domain)
            ->where('channel', $channel->name)
            ->delete();

        return res(200, 'Domain unblocked successfully. ');
    }

    /**
     * Unblock.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroyAsVotenAdministrator(Request $request)
    {
        $this->validate($request, [
            'domain' => 'required',
        ]);

        BlockedDomain::where('domain', $request->domain)
            ->where('channel', 'all')
            ->delete();

        return res(200, 'Domain unblocked successfully. ');
    }
}
