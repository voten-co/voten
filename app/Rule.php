<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use RecordsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rules';

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
