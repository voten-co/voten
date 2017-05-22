<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Help extends Model
{
    use Searchable, SoftDeletes;

    /**
    *   The attributes that are mass assignable.
    *
    *   @var array
    */
    protected $fillable = [
        'body', 'title'
    ];
}
