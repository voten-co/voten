<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EchoServerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'online_connections' => $this->subscription_count,
            'uptime' => round($this->uptime/60/60/24, 2), 
            'memory_usage' => rssForHumans($this->memory_usage->rss)
        ];
    }
}
