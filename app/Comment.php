<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Comment extends Model
{
    use Bookmarkable, RecordsActivity, SoftDeletes, Searchable;

    protected static $recordEvents = ['created'];

    // protected $events = [
    //     "created" => CommentWasCreated::class,
    //     "deleted" => CommentWasDeleted::class
    // ];

    /**
     * Fillable fields for the table.
     *
     * @var array
     */
    protected $fillable = ['body', 'upvotes', 'rate', 'downvotes', 'submission_id', 'level', 'parent_id', 'category_id', 'user_id'];

    protected $with = ['owner', 'children'];

    /**
     * A comment has an owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id')
                    ->select(['id', 'username', 'avatar']);
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

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id'   => $this->id,
            'body' => $this->body,
            'rate' => $this->rate,
        ];
    }
}
