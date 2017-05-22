<?php

namespace App\Providers;

use Auth;
use App\User;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    	// uncommenting below code will enable the query logging which is great for testing
    	// \DB::listen(function($query) { \ Log::info($query->sql, $query->bindings); });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Settings', function() {
            return Auth::user()->settings();
        });
    }
}
