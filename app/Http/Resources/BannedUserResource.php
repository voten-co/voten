<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class BannedUserResource extends Resource
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
            'user_id'      => $this->user_id,
            'unban_at'     => optional($this->unban_at)->toDateTimeString(),
            'created_at'   => optional($this->created_at)->toDateTimeString(),

            'user' => new UserResource($this->user),
        ];
    }
}
