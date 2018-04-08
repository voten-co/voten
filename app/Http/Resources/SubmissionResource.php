<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class SubmissionResource extends Resource
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
            'slug'            => $this->slug,
            'channel_id'      => $this->channel_id,
            'channel_name'    => $this->channel_name,
            'user_id'         => $this->user_id,
            'title'           => $this->title,
            'type'            => $this->type,
            'nsfw'            => (bool) $this->nsfw,
            'content'         => $this->data,
            'rate'            => $this->rate,
            'likes_count'   => $this->likes,
            'comments_count'  => $this->comments_number,
            'approved_at'     => $this->approved_at,
            'disapproved_at'  => optional($this->deleted_at)->toDateTimeString(),
            'created_at'      => optional($this->created_at)->toDateTimeString(),
            'url'             => $this->url,
            'domain'          => $this->domain,

            'channel' => $this->when((bool) request('with_channel') == true, new ChannelResource($this->channel)),

            'author' => new UserResource($this->owner),
        ];
    }
}
