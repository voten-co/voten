<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     *   Bootstrap any application services.
     *
     *   @return void
     */
    public function boot()
    {
        Broadcast::routes();

        /*
        *   Authenticate the user's personal channel...
        */
        Broadcast::channel('App.User.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        /*
        *   Authenticate the user's submissionPage channel
        */
        Broadcast::channel('submission.*', function ($user) {
            return ['id' => $user->id];
        });

        /*
        *   Authenticate the user's chat channel
        */
  //       Broadcast::channel('App.User.*', function ($user) {
        //     return ['id' => $user->id];
        // });
    }
}
