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
        'user_id', 'category', 'description', 'unban_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
