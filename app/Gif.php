<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gif extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'submission_id', 'user_id', 'mp4_path', 'thumbnail_path',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ownedBy(User $user)
    {
        return $this->user_id == $user->id;
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }
}
