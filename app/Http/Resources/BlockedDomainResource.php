<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class BlockedDomainResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'channel_name' => $this->channel,
            'description'  => $this->description,
            'domain'       => $this->domain,
            'created_at'   => optional($this->created_at)->toDateTimeString(),
        ];
    }
}
