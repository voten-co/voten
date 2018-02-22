<?php

namespace App;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Channel extends Model
{
    use Bookmarkable, RecordsActivity, SoftDeletes, Searchable;

    protected static $recordEvents = ['created', 'deleted'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at', 'updated_at', 'public', 'active', 'settings',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'language', 'avatar', 'color', 'nsfw', 'subscribers',
    ];

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(User::class, 'subscriptions');
    }

    public function rules()
    {
        return $this->hasMany(Rule::class);
    }

    public function bannedUsers()
    {
        return DB::table('bans')->where('channel', $this->name)
            ->where('unban_at', '>=', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->get()->pluck('user_id');
    }

    // IDs only
    public function mods()
    {
        return DB::table('roles')->where([
            ['channel_id', $this->id],
            ['role', 'administrator'],
        ])->orWhere([
            ['channel_id', $this->id],
            ['role', 'moderator'],
        ])->pluck('user_id');
    }

    /**
     * Who created the channel in the first place?
     *
     * @return \Illuminate\Support\Collection
     */
    public function creator()
    {
        return optional(
            \App\Activity::where([
                ['subject_type', 'App\Channel'],
                ['subject_id', $this->id],
                ['name', 'created_channel'],
            ])->first()
        )->owner;
    }

    /**
     * Who created the channel in the first place? (in case the user record
     * is deleted, returns 'deleted' instead of throwing a pain in the
     * butt exception.).
     *
     * @return string
     */
    public function createdByUsername()
    {
        $creator = \App\Activity::where([
            ['subject_type', 'App\Channel'],
            ['subject_id', $this->id],
            ['name', 'created_channel'],
        ])->first();

        return $creator ? $creator->owner->username : 'deleted';
    }

    // records
    public function moderators()
    {
        return $this->belongsToMany('App\User', 'roles')->withPivot('role');
    }

    /**
     * To save on queries we set a 'subscribers' field on channel models.
     * This updates it (in case we lose the actual number for any reason in the future.).
     *
     * @return void
     */
    public function updateSubscribers()
    {
        $this->update([
            'subscribers' => $this->subscriptions()->count(),
        ]);
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'subscribers' => $this->subscribers,
        ];
    }
}
