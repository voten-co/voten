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
        if (! $this->mustBeModerator(request('channel_id'))) {
            return res(403, "You don't have necessary permissions");
        }

        return $next($request);
    }
}
