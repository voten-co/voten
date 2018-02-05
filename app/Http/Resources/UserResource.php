<?php

namespace App\Http\Resources;

use App\Traits\CachableUser;
use Auth;
use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    use CachableUser;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this->isSelf());
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'bio' => $this->bio,
            'avatar' => $this->avatar,
            'verified_email' => (bool) $this->confirmed,
            'cover_color' => $this->color,
            'created_at' => optional($this->created_at)->toDateTimeString(),

            'info' => $this->when(
                (bool) $request->with_info == true && $this->isSelf(),
                [
                    'location' => $this->location,
                    'twitter' => $this->info['twitter'],
                    'website' => $this->info['website'],
                ]),

            'stats' => $this->when(
                (bool) $request->with_stats == true && $this->isSelf(),
                [
                    'submissions_count' => $this->userStats($this->id)['submissionsCount'],
                    'comments_count' => $this->userStats($this->id)['commentsCount'],
                    'submission_xp' => $this->userStats($this->id)['submission_xp'],
                    'comment_xp' => $this->userStats($this->id)['comment_xp'],
                ]),

            'server_side_settings' => $this->when(
                $this->isSelf(),
                [
                    'nsfw' => $this->settings('nsfw') ?? false,
                    'show_nsfw_media' => $this->settings('nsfw_media') ?? false,
                    'notify_comments_replied' => $this->settings('notify_comments_replied') ?? false,
                    'notify_submissions_replied' => $this->settings('notify_submissions_replied') ?? false,
                    'notify_mentions' => $this->settings('notify_mentions') ?? false,
                ]),
        ];
    }
}
