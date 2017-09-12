<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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

    protected function clearRedisCache()
    {
        \Illuminate\Support\Facades\Cache::flush();
    }
}
