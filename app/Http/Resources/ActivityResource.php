<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
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
            'id' => $this->id,
            'browser_name' => $this->browser_name,
            'browser_version' => $this->browser_version,
            'country_short_name' => $this->country,
            'country_flag' => $this->getCountryFlag($this->country),
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'ip_address' => $this->ip_address,
            'activity_type' => $this->name,
            'os' => $this->os,
            'user' => $this->owner,
            'subject_id' => $this->subject_id,
            'subject_type' => $this->subject_type,
            'user_agent' => $this->user_agent,
        ];
    }

    protected function getCountryFlag($short_name)
    {
        if ($short_name == 'unknown') {
            return null;
        }

        return 'https://lipis.github.io/flag-icon-css/flags/4x3/' . strtolower($short_name) . '.svg';
    }
}
