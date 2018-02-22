<?php

namespace App\Http\Resources;

use App\Traits\CachableUser;
use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
    use CachableUser;

    protected $withStats = false;

    public function __construct($resource, $withStats = false)
    {
        $this->resource = $resource;
        $this->withStats = $withStats;
    }

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
            'id'             => $this->id,
            'username'       => $this->username,
            'name'           => $this->name,
            'bio'            => $this->bio,
            'avatar'         => $this->avatar,
            'email'          => $this->when($this->isSelf(), $this->email),
            'verified_email' => (bool) $this->confirmed,
            'cover_color'    => $this->color,
            'created_at'     => optional($this->created_at)->toDateTimeString(),

            'info' => [
                'location' => $this->location,
                'twitter'  => $this->info['twitter'],
                'website'  => $this->info['website'],
            ],

            'stats' => $this->when(
                (bool) $request->with_stats == true || $this->withStats,
                [
                    'submissions_count' => $this->userStats($this->id)['submissionsCount'],
                    'comments_count'    => $this->userStats($this->id)['commentsCount'],
                    'submission_xp'     => $this->userStats($this->id)['submission_xp'],
                    'comment_xp'        => $this->userStats($this->id)['comment_xp'],
                ]),

            'server_side_settings' => $this->when($this->isSelf(), $this->serverSideSettings()),
        ];
    }

    protected function serverSideSettings()
    {
        if ($this->isSelf()) {
            return [
                'notifications' => [
                    'notify_comments_replied'    => $this->settings()->notify_comments_replied ?? false,
                    'notify_submissions_replied' => $this->settings()->notify_submissions_replied ?? false,
                    'notify_mentions'            => $this->settings()->notify_mentions ?? false,
                ],
            ];
        }

        return [];
    }
}
