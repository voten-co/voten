<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PhotoResource extends Resource
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
            'path' => $this->path,
            'thumbnail_path' => $this->thumbnail_path,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            "expires_after_secs" => $this->submission_id === null ? $this->calculateExpiresAfterSeconds($this->created_at) : null,

            'uploader' => $this->when((bool) $request->with_uploader == true, new UserResource($this->owner)),

            'submission' => $this->when((bool) $request->with_submission == true, new SubmissionResource($this->submission)),
        ];
    }

    /**
     * How many seconds is it left until the auto cronjob removes all unused uploaded fileS? 
     * 
     * @param Carbon $created_at 
     * 
     * @return integer 
     */
    protected function calculateExpiresAfterSeconds($created_at)
    {
        return $created_at->addHours(24)->diffInSeconds(now());
    }
}
