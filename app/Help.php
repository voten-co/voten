<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Help extends Model
{
    use Searchable, SoftDeletes;

    /**
     *   The attributes that are mass assignable.
     *
     *   @var array
     */
    protected $fillable = [
        'body', 'title',
    ];
}
