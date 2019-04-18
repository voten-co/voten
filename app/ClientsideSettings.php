<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientsideSettings extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clientside_settings';

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
