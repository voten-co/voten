<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'activities';

    protected $fillable = [
        'subject_id', 'subject_type', 'name', 'user_id', 'ip_address', 'user_agent', 'country',
        'browser_name', 'browser_version', 'os', 'device',
    ];

    /**
     * An Activity record is owned by a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
