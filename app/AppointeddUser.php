<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointeddUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'appointedd_users';
    
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
