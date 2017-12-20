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
    protected $fillable = ['z_index', 'group', 'channel_id'];

    protected $with = ['channel'];

    /**
     * A comment has an owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }
}
