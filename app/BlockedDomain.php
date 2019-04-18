<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlockedDomain extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blocked_domains';

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
