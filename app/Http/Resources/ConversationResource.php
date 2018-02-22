<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ConversationResource extends Resource
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
            'id'              => $this->id,
            'user_id'         => $this->contact_id,
            'created_at'      => optional($this->created_at)->toDateTimeString(),
            'last_message_id' => $this->message_id,

            'last_message' => $this->when((bool) request('with_last_message') == true, new MessageResource($this->last_message)),
            'contact'      => $this->when((bool) request('with_contact') == true, new UserResource($this->contact)),
        ];
    }
}
