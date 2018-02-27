<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Passport;

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
}
