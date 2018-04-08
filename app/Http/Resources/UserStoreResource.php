<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserStoreResource extends JsonResource
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
            'submissions' => [
                'likeds' => data_get($this, 'submissionLikes'),
                'bookmarkeds' => data_get($this, 'bookmarkedSubmissions')
            ],

            'comments' => [
                'likeds' => data_get($this, 'commentLikes'),
                'bookmarkeds' => data_get($this, 'bookmarkedComments')
            ], 
            
            'channels' => [
                'bookmarkeds' => data_get($this, 'bookmarkedChannels'),
                'bookmarkeds_records' => ChannelResource::collection(data_get($this, 'bookmarkedChannelsRecords')), 
                'subscribeds' => ChannelResource::collection(data_get($this, 'subscribedChannels')), 
                'moderatings' => data_get($this, 'moderatingChannels'),
                'moderatings_records' => data_get($this, 'moderatingChannelsRecords')
                // 'blocked' to-do
            ], 
            
            'users' => [
                'bookmarkeds' => data_get($this, 'bookmarkedUsers'), 
                'blockeds_conversations' => data_get($this, 'blockedUsers')
                // 'blocked_submissions'?! to-do
                // 'blocked_commnets'?! to-do
            ], 
        ];
    }
}
