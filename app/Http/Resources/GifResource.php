<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GifResource extends JsonResource
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
            'id'             => $this->id,
            'submission_id'  => $this->submission_id,
            'user_id'        => $this->user_id,
            'mp4_path'           => $this->mp4_path,
            'thumbnail_path' => $this->thumbnail_path,
            'created_at'     => optional($this->created_at)->toDateTimeString(),

            'uploader' => $this->when((bool) $request->with_uploader == true, new UserResource($this->owner)),

            'submission' => $this->when((bool) $request->with_submission == true, new SubmissionResource($this->submission)),
        ];
    }
}
