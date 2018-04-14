<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Submission extends Model
{
    use Bookmarkable, SoftDeletes, Searchable, RecordsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data', 'title', 'slug', 'type', 'channel_id', 'channel_name', 'rate',
        'likes', 'user_id', 'data', 'nsfw', 'approved_at',
        'deleted_at', 'comments_number', 'url', 'domain',
    ];

    protected $casts = [
        'data' => 'json',
    ];
    
    protected $dates = [
        'approved_at',
        'deleted_at'
    ];

    protected $with = ['owner'];

    protected static $recordEvents = ['created', 'deleted'];

    /**
     * A submission is owned by a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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

    public function ownedBy(User $user)
    {
        return $this->user_id == $user->id;
    }

    /**
     * A Submission belongs to a Channel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    /**
     * A submission can have many comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Load a threaded set of comments for the post.
     *
     * @return App\CommentsCollection
     */
    public function getThreadedComments()
    {
        return $this->comments()->with('owner')->get()->threaded();
    }

    /**
     * A helper to generate a valid URL to the submission.
     *
     * @return string
     */
    public function url()
    {
        return '/c/'.$this->channel_name.'/'.$this->slug;
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id'    => $this->id,
            'title' => $this->title,
            'url'  => $this->url,
            'rate'  => $this->rate,
        ];
    }
}
