<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'message_id', 'user_id', 'contact_id',
    ];

    protected $with = ['last_message', 'contact'];

    /**
     *   The attributes that should be hidden for arrays.
     *
     *   @var array
     */
    protected $hidden = [
        'updated_at',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')
                    ->select('id', 'username', 'name', 'avatar');
    }

    public function last_message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

    public function contact()
    {
        return $this->belongsTo(User::class, 'contact_id')
                    ->select('id', 'username', 'name', 'avatar');
    }
}
