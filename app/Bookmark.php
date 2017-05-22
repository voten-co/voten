<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    /**
     * Fillable fieldsfor a favorite.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'bookmarkable_id', 'bookmarkable_type'];
}
