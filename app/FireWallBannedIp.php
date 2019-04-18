<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FireWallBannedIp extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fire_wall_banned_ips';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ip_address', 'description', 'unban_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'unban_at',
    ];
}
