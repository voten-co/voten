<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class FeedbackResource extends Resource
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
            'id'          => $this->id,
            'subject'     => $this->subject,
            'user_id'     => $this->user_id,
            'description' => $this->description,
            'created_at'  => optional($this->created_at)->toDateTimeString(),

            'author' => new UserResource($this->owner),
        ];
    }
}
