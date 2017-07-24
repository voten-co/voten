<?php

namespace App\Traits;

trait EchoServer
{
    protected function echoAddress()
    {
        return config('broadcasting.connections.echo.app_address');
    }

    protected function echoAppId()
    {
        return config('broadcasting.connections.echo.app_id');
    }

    protected function echoAuthKey()
    {
        return config('broadcasting.connections.echo.auth_key');
    }

    public function echoStatus()
    {
        if (config('broadcasting.service') != 'echo') {
            return;
        }

        return externalJson(
            $this->echoAddress().'/apps/'.$this->echoAppId().'/status?auth_key='.$this->echoAuthKey()
        );
    }
}
