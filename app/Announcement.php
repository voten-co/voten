<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'channel_name', 'user_id', 'title', 'body', 'active_until',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'active_until',
    ];

    /**
     * who announced the Announcement model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function announcer()
    {
        return $this->belongsTo(User::class, 'user_id')
                    ->select(['id', 'username', 'avatar']);
    }
}
