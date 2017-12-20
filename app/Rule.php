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
    protected $fillable = ['title', 'channel_id'];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
