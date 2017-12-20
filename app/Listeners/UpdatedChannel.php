<?php

namespace App\Listeners;

use App\Events\ChannelWasUpdated;
use App\Traits\CachableChannel;

class UpdatedChannel
{
    use CachableChannel;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ChannelWasUpdated $event
     *
     * @return void
     */
    public function handle(ChannelWasUpdated $event)
    {
        $this->putChannelInTheCache($event->channel);
    }
}
