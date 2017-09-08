<?php

namespace App\Http\Controllers;

use App\FireWallBannedIp;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FirewallController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'administrator']);
    }

    /**
     * Store a FireWallBannedIp record (ban IP address).
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|unique:fire_wall_banned_ips|ip',
        ]);

        FireWallBannedIp::create([
            'ip_address' => $request->ip_address,
            'unban_at'   => Carbon::now()->addYears(1),
        ]);

        Cache::forget('firewall-banned-ips');

        return back();
    }

    /**
     * Destroy a FireWallBannedIp record (unban IP address).
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'ip_address' => 'required',
        ]);

        FireWallBannedIp::where('ip_address', $request->ip_address)->delete();

        Cache::forget('firewall-banned-ips');

        return back();
    }
}
