<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use Bookmarkable, RecordsActivity, SoftDeletes;

    protected static $recordEvents = ['created', 'deleted'];

    /**
     * Fillable fields for the table.
     *
     * @var array
     */
    protected $fillable = [
        'body', 'likes', 'rate', 'submission_id', 'level', 'parent_id', 'channel_id', 'user_id', 'edited_at',
    ];

    protected $with = [
        'owner', 'children',
    ];

    protected $dates = [
        'approved_at',
        'deleted_at'
    ];

    /**
     * A comment has an owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * used for notifying.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notifiable()
    {
        return $this->belongsTo(User::class, 'user_id')
                    ->select(['id', 'settings']);
    }

    /**
     * A comment has an submission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    /**
     * A comment has many children.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function ownedBy(User $user)
    {
        return $this->user_id == $user->id;
    }

    /**
     * Use a custom collection for all comments.
     *
     * @param array $models
     *
     * @return CustomCollection
     */
    public function newCollection(array $models = [])
    {
        return new CommentCollection($models);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
