<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Traits\CachableChannel;

class ChannelResource extends Resource
{
    use CachableChannel;

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
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description,
            'nsfw'              => (bool) $this->nsfw,
            'cover_color'       => $this->color,
            'avatar'            => $this->avatar,
            'subscribers_count' => $this->channelStats($this->id)['subscribersCount'],
            'comments_count' => $this->channelStats($this->id)['commentsCount'],
            'submissions_count' => $this->channelStats($this->id)['submissionsCount'],
            'created_at'        => optional($this->created_at)->toDateTimeString(),
        ];
    }
}