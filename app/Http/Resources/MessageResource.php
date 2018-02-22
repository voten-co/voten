<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class MessageResource extends Resource
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
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'read_at'    => optional($this->read_at)->toDateTimeString(),
            'content'    => $this->data,

            'author' => new UserResource($this->owner),
        ];
    }
}
