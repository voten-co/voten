<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HelpVote extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'help_id', 'ip_address', 'type',
    ];
}
