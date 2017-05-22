<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use RecordsActivity;

    /**
     * Fillable fields for the table.
     *
     * @var array
     */
    protected $fillable = ['title', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
