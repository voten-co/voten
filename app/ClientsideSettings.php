<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientsideSettings extends Model
{
    protected $fillable = [
        'json', 'user_id', 'platform',
    ];

    protected $casts = [
        'json'  => 'json',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
