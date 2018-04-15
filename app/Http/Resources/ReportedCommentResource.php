<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ReportedCommentResource extends Resource
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
            'user_id'     => $this->user_id,
            'channel_id'  => $this->channel_id,
            'description' => $this->description,
            'subject'     => $this->subject,
            'user_id'     => $this->id,
            'comment_id'  => $this->reportable_id,
            'created_at'  => optional($this->created_at)->toDateTimeString(),
            'solved_at'   => optional($this->deleted_at)->toDateTimeString(),

            'comment'  => $this->when((bool) $request->with_comment == true, new CommentResource($this->comment)),
            'reporter' => $this->when((bool) $request->with_reporter == true, new UserResource($this->reporter)),
        ];
    }
}
