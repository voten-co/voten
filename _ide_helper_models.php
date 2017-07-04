<?php
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */

namespace App{
    /**
     * App\Activity.
     *
     * @property int $id
     * @property int $subject_id
     * @property string $subject_type
     * @property string $name
     * @property int $user_id
     * @property string|null $ip_address
     * @property string $user_agent
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property string|null $country
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereCountry($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereIpAddress($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereSubjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereSubjectType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereUserAgent($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Activity whereUserId($value)
     */
    class Activity extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Announcement.
     *
     * @property int $id
     * @property string|null $category_name
     * @property int $user_id
     * @property string $title
     * @property string $body
     * @property \Carbon\Carbon|null $active_until
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \App\User $announcer
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereActiveUntil($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereBody($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereCategoryName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereUserId($value)
     */
    class Announcement extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\AppointeddUser.
     *
     * @property int $id
     * @property int $user_id
     * @property string $appointed_as
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \App\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\AppointeddUser whereAppointedAs($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\AppointeddUser whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\AppointeddUser whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\AppointeddUser whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\AppointeddUser whereUserId($value)
     */
    class AppointeddUser extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Ban.
     *
     * @property int $id
     * @property string $user_id
     * @property string|null $category
     * @property string|null $description
     * @property string|null $unban_at
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \App\User $user
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereCategory($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereUnbanAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Ban whereUserId($value)
     */
    class Ban extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\BlockedDomain.
     *
     * @property int $id
     * @property string $domain
     * @property string $category
     * @property string|null $description
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedDomain whereCategory($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedDomain whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedDomain whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedDomain whereDomain($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedDomain whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\BlockedDomain whereUpdatedAt($value)
     */
    class BlockedDomain extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Bookmark.
     *
     * @property int $id
     * @property int $user_id
     * @property int $bookmarkable_id
     * @property string $bookmarkable_type
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookmark whereBookmarkableId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookmark whereBookmarkableType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookmark whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookmark whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookmark whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Bookmark whereUserId($value)
     */
    class Bookmark extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Category.
     *
     * @property int $id
     * @property string $name
     * @property string $language
     * @property string $description
     * @property int $nsfw
     * @property string $color
     * @property string $avatar
     * @property int $public
     * @property int $active
     * @property int $subscribers
     * @property mixed|null $settings
     * @property string|null $deleted_at
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bookmark[] $bookmarks
     * @property-read \App\CommentCollection|\App\Comment[] $comments
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $moderators
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Rule[] $rules
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Submission[] $submissions
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $subscriptions
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category bookmarkedBy(\App\User $user)
     * @method static bool|null forceDelete()
     * @method static \Illuminate\Database\Query\Builder|\App\Category onlyTrashed()
     * @method static bool|null restore()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereAvatar($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereColor($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereLanguage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereNsfw($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category wherePublic($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereSettings($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereSubscribers($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereUpdatedAt($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Category withTrashed()
     * @method static \Illuminate\Database\Query\Builder|\App\Category withoutTrashed()
     */
    class Category extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\CategoryForbiddenName.
     *
     * @property int $id
     * @property string $name
     * @property string|null $deleted_at
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     *
     * @method static bool|null forceDelete()
     * @method static \Illuminate\Database\Query\Builder|\App\CategoryForbiddenName onlyTrashed()
     * @method static bool|null restore()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\CategoryForbiddenName whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\CategoryForbiddenName whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\CategoryForbiddenName whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\CategoryForbiddenName whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\CategoryForbiddenName whereUpdatedAt($value)
     * @method static \Illuminate\Database\Query\Builder|\App\CategoryForbiddenName withTrashed()
     * @method static \Illuminate\Database\Query\Builder|\App\CategoryForbiddenName withoutTrashed()
     */
    class CategoryForbiddenName extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Comment.
     *
     * @property int $id
     * @property int $submission_id
     * @property int $user_id
     * @property int $parent_id
     * @property int $category_id
     * @property int $level
     * @property float $rate
     * @property int $upvotes
     * @property int $downvotes
     * @property string $body
     * @property string|null $approved_at
     * @property string|null $deleted_at
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bookmark[] $bookmarks
     * @property-read \App\CommentCollection|\App\Comment[] $children
     * @property-read \App\User $notifiable
     * @property-read \App\User $owner
     * @property-read \App\Comment $parent
     * @property-read \App\Submission $submission
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment bookmarkedBy(\App\User $user)
     * @method static bool|null forceDelete()
     * @method static \Illuminate\Database\Query\Builder|\App\Comment onlyTrashed()
     * @method static bool|null restore()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereApprovedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereBody($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereDownvotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereLevel($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereParentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereRate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereSubmissionId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereUpvotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Comment whereUserId($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Comment withTrashed()
     * @method static \Illuminate\Database\Query\Builder|\App\Comment withoutTrashed()
     */
    class Comment extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Conversation.
     *
     * @property int $id
     * @property int $user_id
     * @property int $contact_id
     * @property int $message_id
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \App\User $contact
     * @property-read \App\Message $last_message
     * @property-read \App\User $owner
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversation whereContactId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversation whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversation whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversation whereMessageId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversation whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Conversation whereUserId($value)
     */
    class Conversation extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Feedback.
     *
     * @property int $id
     * @property string $subject
     * @property int $user_id
     * @property string $description
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read \App\User $owner
     *
     * @method static bool|null forceDelete()
     * @method static \Illuminate\Database\Query\Builder|\App\Feedback onlyTrashed()
     * @method static bool|null restore()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Feedback whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Feedback whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Feedback whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Feedback whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Feedback whereSubject($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Feedback whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Feedback whereUserId($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Feedback withTrashed()
     * @method static \Illuminate\Database\Query\Builder|\App\Feedback withoutTrashed()
     */
    class Feedback extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Help.
     *
     * @property int $id
     * @property string $title
     * @property string $body
     * @property int $index
     * @property string|null $deleted_at
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     *
     * @method static bool|null forceDelete()
     * @method static \Illuminate\Database\Query\Builder|\App\Help onlyTrashed()
     * @method static bool|null restore()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Help whereBody($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Help whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Help whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Help whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Help whereIndex($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Help whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Help whereUpdatedAt($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Help withTrashed()
     * @method static \Illuminate\Database\Query\Builder|\App\Help withoutTrashed()
     */
    class Help extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Invite.
     *
     * @property int $id
     * @property string $invitation
     * @property string|null $email
     * @property string|null $category
     * @property int $sent
     * @property string|null $claimed_at
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Invite whereCategory($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Invite whereClaimedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Invite whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Invite whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Invite whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Invite whereInvitation($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Invite whereSent($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Invite whereUpdatedAt($value)
     */
    class Invite extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Message.
     *
     * @property int $id
     * @property int $user_id
     * @property array $data
     * @property string|null $read_at
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \App\User $owner
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereData($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereReadAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Message whereUserId($value)
     */
    class Message extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Photo.
     *
     * @property int $id
     * @property int|null $submission_id
     * @property int $user_id
     * @property string $path
     * @property string $thumbnail_path
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \App\User $owner
     * @property-read \App\Submission|null $submission
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Photo whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Photo whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Photo wherePath($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Photo whereSubmissionId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Photo whereThumbnailPath($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Photo whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Photo whereUserId($value)
     */
    class Photo extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Report.
     *
     * @property int $id
     * @property int|null $category_id
     * @property int $reportable_id
     * @property string $reportable_type
     * @property string $subject
     * @property int $user_id
     * @property string|null $description
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property \Carbon\Carbon|null $deleted_at
     * @property-read \App\Comment $comment
     * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $reported
     * @property-read \App\User $reporter
     * @property-read \App\Submission $submission
     *
     * @method static bool|null forceDelete()
     * @method static \Illuminate\Database\Query\Builder|\App\Report onlyTrashed()
     * @method static bool|null restore()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereReportableId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereReportableType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereSubject($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereUserId($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Report withTrashed()
     * @method static \Illuminate\Database\Query\Builder|\App\Report withoutTrashed()
     */
    class Report extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Rule.
     *
     * @property int $id
     * @property string $title
     * @property int $category_id
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \App\Category $category
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Rule whereUpdatedAt($value)
     */
    class Rule extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Submission.
     *
     * @property int $id
     * @property string $slug
     * @property string $title
     * @property string $type
     * @property array $data
     * @property string $category_name
     * @property float $rate
     * @property int|null $resubmit_id
     * @property int $user_id
     * @property int $nsfw
     * @property int $category_id
     * @property int $upvotes
     * @property int $downvotes
     * @property int $comments_number
     * @property string|null $approved_at
     * @property string|null $deleted_at
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bookmark[] $bookmarks
     * @property-read \App\Category $category
     * @property-read \App\CommentCollection|\App\Comment[] $comments
     * @property-read \App\User $notifiable
     * @property-read \App\User $owner
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission bookmarkedBy(\App\User $user)
     * @method static bool|null forceDelete()
     * @method static \Illuminate\Database\Query\Builder|\App\Submission onlyTrashed()
     * @method static bool|null restore()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereApprovedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereCategoryName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereCommentsNumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereData($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereDownvotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereNsfw($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereRate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereResubmitId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereUpvotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Submission whereUserId($value)
     * @method static \Illuminate\Database\Query\Builder|\App\Submission withTrashed()
     * @method static \Illuminate\Database\Query\Builder|\App\Submission withoutTrashed()
     */
    class Submission extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Subscription.
     *
     * @property int $user_id
     * @property int $category_id
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Subscription whereUserId($value)
     */
    class Subscription extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\Suggested.
     *
     * @property int $id
     * @property int $category_id
     * @property string|null $group
     * @property string $language
     * @property int $z_index
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \App\Category $category
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Suggested whereCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Suggested whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Suggested whereGroup($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Suggested whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Suggested whereLanguage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Suggested whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\Suggested whereZIndex($value)
     */
    class Suggested extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\User.
     *
     * @property int $id
     * @property string $username
     * @property string|null $name
     * @property string|null $website
     * @property string|null $location
     * @property string $avatar
     * @property int $submission_karma
     * @property int|null $comment_karma
     * @property string $color
     * @property string|null $bio
     * @property int $active
     * @property int $confirmed
     * @property string|null $email
     * @property array $settings
     * @property array $info
     * @property int $verified
     * @property string $password
     * @property string|null $deleted_at
     * @property string|null $remember_token
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $bookmarkedCategories
     * @property-read \App\CommentCollection|\App\Comment[] $bookmarkedComments
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Submission[] $bookmarkedSubmissions
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $bookmarkedUsers
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bookmark[] $bookmarks
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $categoryRoles
     * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
     * @property-read \App\CommentCollection|\App\Comment[] $commentDownvotes
     * @property-read \App\CommentCollection|\App\Comment[] $commentUpvotes
     * @property-read \App\CommentCollection|\App\Comment[] $comments
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Conversation[] $contacts
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $conversations
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $feedHot
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $feedNew
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $feedRising
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $hiddenUsers
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Submission[] $hides
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $messages
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Submission[] $submissionDownvotes
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Submission[] $submissionUpvotes
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Submission[] $submissions
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $subscriptions
     * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
     *
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User bookmarkedBy(\App\User $user)
     * @method static bool|null forceDelete()
     * @method static \Illuminate\Database\Query\Builder|\App\User onlyTrashed()
     * @method static bool|null restore()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAvatar($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereBio($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereColor($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCommentKarma($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereConfirmed($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereInfo($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereLocation($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereSettings($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereSubmissionKarma($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUsername($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereVerified($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereWebsite($value)
     * @method static \Illuminate\Database\Query\Builder|\App\User withTrashed()
     * @method static \Illuminate\Database\Query\Builder|\App\User withoutTrashed()
     */
    class User extends \Eloquent
    {
    }
}

namespace App{
    /**
     * App\UserForbiddenName.
     *
     * @property int $id
     * @property string $username
     * @property string|null $deleted_at
     * @property \Carbon\Carbon|null $created_at
     * @property \Carbon\Carbon|null $updated_at
     *
     * @method static bool|null forceDelete()
     * @method static \Illuminate\Database\Query\Builder|\App\UserForbiddenName onlyTrashed()
     * @method static bool|null restore()
     * @method static \Illuminate\Database\Eloquent\Builder|\App\UserForbiddenName whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\UserForbiddenName whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\UserForbiddenName whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\UserForbiddenName whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|\App\UserForbiddenName whereUsername($value)
     * @method static \Illuminate\Database\Query\Builder|\App\UserForbiddenName withTrashed()
     * @method static \Illuminate\Database\Query\Builder|\App\UserForbiddenName withoutTrashed()
     */
    class UserForbiddenName extends \Eloquent
    {
    }
}
