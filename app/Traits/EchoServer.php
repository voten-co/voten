<?php

namespace App\Traits;

trait EchoServer
{
    protected function echoAddress()
    {
        return config('broadcasting.connections.echo.host') . ':' . config('broadcasting.connections.echo.port');
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

        // filter with submission public channels only
        $list = $list->filter(function ($item, $key) {
            return $key = starts_with($key, 'submission.');
        });

        $list = $list->mapWithKeys(function ($item, $key) {
            return [str_after($key, 'submission.') => $item->subscription_count];
        });

        // sort
        $list = $list->toArray();
        arsort($list);

        return collect($list);
    }
}
