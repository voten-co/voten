<?php

namespace App\Http\Middleware;

use App\Permissions;
use Closure;
use Illuminate\Support\Facades\Auth;

class MustBeModerator
{
    use Permissions;

    /**
     * Handle an incoming request.
     * Note that routes using this middleware MUST have request('channel_id') filled.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        abort_unless($this->mustBeModerator($request->route('channel')->id ?? request('channel_id')), 403);

        return $next($request);
    }
}
