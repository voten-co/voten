<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data', 'user_id', 'read_at',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    protected $with = ['owner'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
