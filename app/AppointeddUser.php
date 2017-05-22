<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointeddUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'appointed_as',
    ];

    /**
     * A comment has an user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
                    ->select(['id', 'username', 'avatar']);
    }
}
