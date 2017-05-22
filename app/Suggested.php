<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Suggested extends Model
{
    /**
     * Fillable fields for the table.
     *
     * @var array
     */
    protected $fillable = ['z_index', 'group', 'category_id'];


    protected $with = ['category'];


    /**
     * A comment has an owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
