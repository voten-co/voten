<?php

namespace App;

use App\Events\ReportWasCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use SoftDeletes, RecordsActivity;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'reportable_type', 'reportable_id', 'user_id', 'channel_id', 'description',
    ];

    protected $dispatchesEvents = [
        'created' => ReportWasCreated::class,
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Returns the reported submission.
    public function submission()
    {
        return $this->belongsTo(Submission::class, 'reportable_id')->withTrashed();
    }

    // Returns the reported comment.
    public function comment()
    {
        return $this->belongsTo(Comment::class, 'reportable_id')->withTrashed();
    }

    // Returns the item that was reported
    public function reported()
    {
        return $this->morphTo();
    }
}
