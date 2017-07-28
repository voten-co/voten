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

    public function echoHotConversations()
    {
        $list = externalJson(
            $this->echoAddress().'/apps/'.$this->echoAppId().'/channels?auth_key='.$this->echoAuthKey()
        );

        $list = collect($list->channels);

        // filter to submission public channels only
        $list = $list->filter(function ($item, $key) {
            return starts_with($key, 'submission.');
        });

        // map
//        $list->transform(function ($item, $key) {
//            return str_after($key, 'submission.');
//        });

        // sort
        $list = $list->sortBy('subscription_count', 1, true);

        return $list;
    }
}
