<?php

namespace App;

use App\Traits\CachableUser;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use Notifiable, Bookmarkable, SoftDeletes, CachableUser, HasApiTokens, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'name', 'email', 'password', 'location', 'bio',
        'website', 'settings', 'color', 'avatar', 'confirmed',
        'active', 'info', 'comment_xp', 'submission_xp',
    ];

    protected $casts = [
        'settings'  => 'json',
        'info'      => 'json',
        'confirmed' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'deleted_at', 'email', 'settings', 'verified', 'active',
    ];

    /**
     * returns users stats (fetched from cache).
     *
     * @return Illuminate\Support\Collection
     */
    public function stats()
    {
        return $this->userStats($this->id);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Blocked submissions.
     *
     * @return Collection
     */
    public function hiddenSubmissions()
    {
        return DB::table('hides')->where('user_id', $this->id)->get()->pluck('submission_id');
    }

    /* --------------------------------------------------------------------- */
    /* --------------------------- blocked channels ------------------------ */
    /* --------------------------------------------------------------------- */

    /**
     * Blocked channels (all except these).
     *
     * @return Collection
     */
    public function hiddenChannels()
    {
        return DB::table('blocked_channels')->where('user_id', $this->id)->get()->pluck('channel_id');
    }

    public function blockedChannels()
    {
        return $this->belongsToMany(Channel::class, 'blocked_channels')
            ->withTimestamps()
            ->orderBy('blocked_channels.created_at', 'desc');
    }

    public function seenAnnouncements()
    {
        return DB::table('seen_announcements')->where('user_id', $this->id)->get()->pluck('announcement_id');
    }

    public function hides()
    {
        return $this->belongsToMany(Submission::class, 'hides');
    }

    public function hiddenUsers()
    {
        return $this->belongsToMany(self::class, 'hidden_users', 'user_id', 'blocked_user_id');
    }

    public function channelRoles()
    {
        return $this->belongsToMany(Channel::class, 'roles');
    }

    public function roles()
    {
        return DB::table('roles')->where('user_id', $this->id)->select('role', 'channel_id')->get();
    }

    public function moderatingIds()
    {
        return DB::table('roles')->where('user_id', $this->id)->select('channel_id')->get()->pluck('channel_id');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Channel::class, 'subscriptions')
            ->withTimestamps()
            ->orderBy('subscriptions.created_at', 'desc');
    }

    /* --------------------------------------------------------------------- */
    /* -------------------------- Home Feed methods ------------------------ */
    /* --------------------------------------------------------------------- */

    public function feedHot()
    {
        return $this->belongsToMany(Channel::class, 'subscriptions')->with(['submissions' => function ($query) {
            $query->orderBy('rate', 'desc');
        }]);
    }

    public function feedNew()
    {
        return $this->belongsToMany(Channel::class, 'subscriptions')->with(['submissions' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);
    }

    public function feedRising()
    {
        return $this->belongsToMany(Channel::class, 'subscriptions')->with(['submissions' => function ($query) {
            $query->orderBy('created_at', 'desc');
            $query->where('created_at', '>=', Carbon::now()->subHour());
        }]);
    }

    /* --------------------------------------------------------------------- */
    /* --------------------- submission voting methods --------------------- */
    /* --------------------------------------------------------------------- */

    public function likedSubmissions()
    {
        return $this->belongsToMany(Submission::class, 'submission_likes')
            ->withTimestamps()
            ->orderBy('submission_likes.created_at', 'desc');
    }

    public function submissionLikesIds()
    {
        return DB::table('submission_likes')->where('user_id', $this->id)->get()->pluck('submission_id');
    }

    public function submissionLikes()
    {
        return $this->belongsToMany(Submission::class, 'submission_likes')
            ->withTimestamps()
            ->orderBy('submission_likes.created_at', 'desc');
    }
 
    /* --------------------------------------------------------------------- */
    /* ----------------------- comment voting methods ---------------------- */
    /* --------------------------------------------------------------------- */

    public function likedComments()
    {
        return $this->belongsToMany(Comment::class, 'comment_likes')
            ->withTimestamps()
            ->orderBy('comment_likes.created_at', 'desc');
    }

    public function commentLikes()
    {
        return $this->belongsToMany(Comment::class, 'comment_likes')
            ->withTimestamps()
            ->orderBy('comment_likes.created_at', 'desc');
    }

    public function commentLikesIds()
    {
        return DB::table('comment_likes')->where('user_id', $this->id)->get()->pluck('comment_id');
    }

    /* --------------------------------------------------------------------- */
    /* -------------------------- Messaging methods ------------------------ */
    /* --------------------------------------------------------------------- */

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function conversations()
    {
        return $this->belongsToMany(Message::class, 'conversations')
            ->withTimestamps()
            ->orderBy('conversations.created_at', 'desc');
    }

    public function contacts()
    {
        return $this->hasMany(Conversation::class)
            ->groupBy('contact_id')
            ->select(DB::raw('*, max(id) as id, max(message_id) as message_id, max(created_at) as created_at'));
    }

    public function myContactIds()
    {
        return $this->hasMany(Conversation::class)->groupBy('contact_id')->pluck('contact_id');
    }

    public function blockedUsers()
    {
        return DB::table('hidden_users')->where('user_id', $this->id)->pluck('blocked_user_id');
    }

    /* --------------------------------------------------------------------- */
    /* -------------------------- bookmark methods ------------------------- */
    /* --------------------------------------------------------------------- */

    public function bookmarkedSubmissions()
    {
        return $this->belongsToMany(Submission::class, 'bookmarks', 'user_id', 'bookmarkable_id')
            ->where('bookmarkable_type', 'App\Submission')
            ->withTimestamps()
            ->orderBy('bookmarks.created_at', 'desc');
    }

    public function bookmarkedComments()
    {
        return $this->belongsToMany('App\Comment', 'bookmarks', 'user_id', 'bookmarkable_id')
            ->where('bookmarkable_type', 'App\Comment')
            ->withTimestamps()
            ->orderBy('bookmarks.created_at', 'desc');
    }

    public function bookmarkedChannels()
    {
        return $this->belongsToMany('App\Channel', 'bookmarks', 'user_id', 'bookmarkable_id')
            ->where('bookmarkable_type', 'App\Channel')
            ->withTimestamps()
            ->orderBy('bookmarks.created_at', 'desc');
    }

    public function bookmarkedUsers()
    {
        return $this->belongsToMany('App\User', 'bookmarks', 'user_id', 'bookmarkable_id')
            ->where('bookmarkable_type', 'App\User')
            ->withTimestamps()
            ->orderBy('bookmarks.created_at', 'desc');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'name'     => $this->name,
        ];
    }

    /**
     * Whether or not the auth user is a voten administrator.
     *
     * @return bool
     */
    public function isVotenAdministrator()
    {
        $users = Cache::rememberForever('general.voten-administrators', function () {
            return AppointeddUser::where('appointed_as', 'administrator')->pluck('user_id');
        });

        return $users->contains($this->id);
    }

    /**
     * user's country: either determinded by his registered IP or (in case it wasn't saved at the time) by his last activity's IP.
     *
     * @return string
     */
    public function country()
    {
        return Activity::where([
            'user_id' => $this->id,
            'name'    => 'created_user',
        ])->first()->country ?? Activity::where([
            'user_id' => $this->id,
        ])->orderBy('created_at', 'desc')->first()->country ?? 'unknown';
    }

    /**
     * user's country: either determinded by his registered IP or (in case it wasn't saved at the time) by his last activity's IP.
     *
     * @return string
     */
    public function registeredIpAddress()
    {
        return Activity::where([
            'user_id' => $this->id,
            'name'    => 'created_user',
        ])->first()->ip_address ?? Activity::where([
            'user_id' => $this->id,
        ])->orderBy('created_at', 'desc')->first()->ip_address ?? 'unknown';
    }

    /**
     * A user has many activities.
     *
     * @return Illuminate\Support\Collection
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * A user has many activities.
     *
     * @return Illuminate\Support\Collection
     */
    public function lastActivity()
    {
        return $this->activities()->orderBy('id', 'desc')->first();
    }

    /**
     * @return Settings
     */
    public function settings()
    {
        return new \App\Settings($this->settings, $this);
    }

    public function clientsideSettings($platform = 'Web')
    {
        $settings = \App\ClientsideSettings::where([
            ['user_id', $this->id],
            ['platform', $platform],
        ])->first();

        return optional($settings)->json;
    }

    /**
     * Is the auth user shadow banned.
     *
     * @return bool
     */
    public function isShadowBanned()
    {
        return !$this->active;
    }

    public function isSelf()
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->id === Auth::id();
    }

    /**
     * Rewrite for Passport's findForPassport() to allow for login via both "username" and "email".
     */
    public function findForPassport($identifier)
    {
        return $this->orWhere('email', $identifier)->orWhere('username', $identifier)->first();
    }
}
