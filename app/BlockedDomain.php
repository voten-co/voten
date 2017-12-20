<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockedDomain extends Model
{
    use RecordsActivity;

    /**
     *   The attributes that are mass assignable.
     *
     *   @var array
     */
    protected $fillable = [
        'domain', 'channel', 'description',
    ];
}
