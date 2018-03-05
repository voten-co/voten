<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function signIn($user = null)
    {
        $this->clearRedisCache();

        $user = $user ?: create('App\User');

        $this->actingAs($user);

        return $this;
    }
    
    protected function signInViaPassport($user = null)
    {
        $this->clearRedisCache();

        $user = $user ?: create('App\User');

        Passport::actingAs($user);

        return $this;
    }

    protected function clearRedisCache()
    {
        \Illuminate\Support\Facades\Cache::flush();
    }

    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(\Exception $e) {}
            public function render($request, \Exception $e) {
                throw $e;
            }
        });
    }
}
