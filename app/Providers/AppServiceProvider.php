<?php

namespace App\Providers;

use Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

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
        if (!\App::runningInConsole()) {
            $this->app->singleton('App\Settings', function () {
                return Auth::user()->settings();
            });
        }
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
    }
}
