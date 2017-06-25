<?php

namespace App;

use App\Traits\CachableUser;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        'active', 'info', 'comment_karma', 'submission_karma',
    ];

    protected $casts = [
        'settings' => 'json',
        'info'     => 'json',
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

    public function hiddenSubmissions()
    {
        return DB::table('hides')->where('user_id', $this->id)->get()->pluck('submission_id');
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

    public function categoryRoles()
    {
        return $this->belongsToMany(Category::class, 'roles');
    }

    public function roles()
    {
        return DB::table('roles')->where('user_id', $this->id)->select('role', 'category_id')->get();
    }

    public function moderatingIds()
    {
        return DB::table('roles')->where('user_id', $this->id)->select('category_id')->get()->pluck('category_id');
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Category::class, 'subscriptions');
    }

    /* --------------------------------------------------------------------- */
    /* -------------------------- Home Feed methods ------------------------ */
    /* --------------------------------------------------------------------- */

    public function feedHot()
    {
        return $this->belongsToMany(Category::class, 'subscriptions')->with(['submissions' => function ($query) {
            $query->orderBy('rate', 'desc');
        }]);
    }

    public function feedNew()
    {
        return $this->belongsToMany(Category::class, 'subscriptions')->with(['submissions' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);
    }

    public function feedRising()
    {
        return $this->belongsToMany(Category::class, 'subscriptions')->with(['submissions' => function ($query) {
            $query->orderBy('created_at', 'desc');
            $query->where('created_at', '>=', Carbon::now()->subHour());
        }]);
    }

    /* --------------------------------------------------------------------- */
    /* --------------------- submission voting methods --------------------- */
    /* --------------------------------------------------------------------- */

    public function submissionUpvotes()
    {
        return $this->belongsToMany(Submission::class, 'submission_upvotes')
                    ->withTimestamps()
                    ->orderBy('submission_upvotes.created_at', 'desc');
    }

    public function submissionUpvotesIds()
    {
        return DB::table('submission_upvotes')->where('user_id', $this->id)->get()->pluck('submission_id');
    }

    public function submissionDownvotes()
    {
        return $this->belongsToMany(Submission::class, 'submission_downvotes')
                    ->withTimestamps()
                    ->orderBy('submission_downvotes.created_at', 'desc');
    }

    public function submissionDownvotesIds()
    {
        return DB::table('submission_downvotes')->where('user_id', $this->id)->get()->pluck('submission_id');
    }

    /* --------------------------------------------------------------------- */
    /* ----------------------- comment voting methods ---------------------- */
    /* --------------------------------------------------------------------- */

    public function commentUpvotes()
    {
        return $this->belongsToMany(Comment::class, 'comment_upvotes')
                    ->withTimestamps()
                    ->orderBy('comment_upvotes.created_at', 'desc');
    }

    public function commentUpvotesIds()
    {
        return DB::table('comment_upvotes')->where('user_id', $this->id)->get()->pluck('comment_id');
    }

    public function commentDownvotes()
    {
        return $this->belongsToMany(Comment::class, 'comment_downvotes')
                    ->withTimestamps()
                    ->orderBy('comment_downvotes.created_at', 'desc');
    }

    public function commentDownvotesIds()
    {
        return DB::table('comment_downvotes')->where('user_id', $this->id)->get()->pluck('comment_id');
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

    public function bookmarkedCategories()
    {
        return $this->belongsToMany('App\Category', 'bookmarks', 'user_id', 'bookmarkable_id')
                    ->where('bookmarkable_type', 'App\Category')
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
        $users = Cache::remember('general.voten-administrators', 60 * 60 * 12, function () {
            $users = AppointeddUser::where('appointed_as', 'administrator')->pluck('user_id');
        });

        return !$users ? false : $users->contains($this->id);
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
     * Get the user settings.
     *
     * @return json
     */
    public function settings()
    {
        return new \App\Settings($this->settings, $this);
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
}
