<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ShadowBan
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        abort_unless(Auth::check(), 401);

        if (Auth::user()->isShadowBanned()) {
            return res(423, 'I hate to break it to you but your account has been banned.');
        }

        return $next($request);
    }
}
