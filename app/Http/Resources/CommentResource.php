<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CommentResource extends Resource
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
            'id' => $this->id,
            'submission_id' => $this->submission_id,
            'user_id' => $this->user_id,
            'channel_id' => $this->channel_id,
            'parent_id' => $this->parent_id == 0 ? null : $this->parent_id,
            'nested_level' => $this->level,
            'rate' => $this->rate,
            'likes_count' => $this->likes,

            'content' => [
                'text' => $this->body,
            ],

            'approved_at' => optional($this->approved_at)->toDateTimeString(),
            'disapproved_at' => optional($this->deleted_at)->toDateTimeString(),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'edited_at' => optional($this->edited_at)->toDateTimeString(),

            'author' => new UserResource($this->owner),

            'children' => $this->when((bool) $request->with_children == true, self::collection($this->children)),
        ];
    }
}
