<?php

namespace App\Http\Controllers;

use Artisan;
use Illuminate\Support\Facades\Cache;

class SshController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * flushes all the cache in redis.
     *
     * @return redirect
     */
    public function flushAll()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

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
        abort_unless($this->mustBeVotenAdministrator(), 403);

        Artisan::call('cache:clear');

        return back();
    }

    /**
     * clears the artisan cache.
     *
     * @return redirect
     */
    public function startMaintenanceMode()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        Artisan::call('down');

        return back();
    }

    /**
     * clears the artisan cache.
     *
     * @return redirect
     */
    public function stopMaintenanceMode()
    {
        abort_unless($this->mustBeVotenAdministrator(), 403);

        Artisan::call('up');

        return back();
    }
}
