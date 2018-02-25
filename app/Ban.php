<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    use RecordsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'channel', 'description', 'unban_at',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
