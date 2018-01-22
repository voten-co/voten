<?php

namespace App\Http\Controllers;

use App\BlockedDomain;
use App\Channel;
use Illuminate\Http\Request;

class BlockDomainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Stores a BlockedDomain record.
     *
     * Hint: since general bann happens from through backend form (and not via ajax) we check for it.
     * If it isn't via ajax, it means it's been from backend and done by a VotenAdministrator.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Collection $blockedDomain
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'domain'   => 'required|url',
            'channel'  => 'alpha_num|max:25',
        ]);

        if (!($blockEverywhere = !$request->ajax() && $this->mustBeVotenAdministrator())) {
            $channel = Channel::where('name', $request->channel)->firstOrFail();
            abort_unless($this->mustBeModerator($channel->id), 403);
        }

        $blockedDomain = new BlockedDomain([
            'channel'     => $blockEverywhere ? 'all' : $request->channel,
            'domain'      => domain($request->domain),
            'description' => $request->description,
        ]);
        $blockedDomain->save();

        return $request->ajax() ? $blockedDomain : back();
    }

    /**
     * Returns all the domains that are blocked for submitting(url type submission) to this channel.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Support\Collection
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'channel' => 'required|max:25',
        ]);

        return BlockedDomain::where('channel', $request->channel)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Unblock.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'domain'   => 'required',
            'channel'  => 'required|alpha_num|min:3|max:50',
        ]);

        if (!($blockEverywhere = !$request->ajax() && $this->mustBeVotenAdministrator())) {
            $channel = Channel::where('name', $request->channel)->firstOrFail();
            abort_unless($this->mustBeModerator($channel->id), 403);
        }

        BlockedDomain::where('domain', $request->domain)
                    ->where('channel', $blockEverywhere ? 'all' : $request->channel)
                    ->delete();

        return $blockEverywhere ? back() : response('Unblocked in '.$request->channel, 200);
    }
}
