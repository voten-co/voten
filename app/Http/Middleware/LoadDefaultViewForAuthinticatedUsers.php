<?php

namespace App\Http\Middleware;

use Closure;

class LoadDefaultViewForAuthenticatedUsers
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
        if (\Auth::check()) {
            return response()->view('welcome');
        }

        return $next($request);
    }
}
