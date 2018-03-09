<?php

namespace App\Http\Controllers;

use Artisan;
use Illuminate\Support\Facades\Cache;

class SshController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'administrator']);
    }

    /**
     * flushes all the cache in redis.
     *
     * @return redirect
     */
    public function flushAll()
    {
        Cache::flush();

        return back();
    }

    /**
     * clears the artisan cache.
     *
     * @return redirect
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');

        return back();
    }
}
