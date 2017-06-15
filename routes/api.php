<?php

Route::group(['middleware' => 'auth:api'], function () {
    // Administrator routes
    Route::post('/big-daddy', 'AdminController@isAdministrator');
    Route::post('/admin/users', 'AdminController@indexUsers');
    Route::post('/admin/comments', 'AdminController@comments');
    Route::post('/admin/channels', 'AdminController@categories');
    Route::post('/admin/submissions', 'AdminController@submissions');
    Route::post('/admin/search-users', 'AdminController@searchUsers');
    Route::post('/admin/suggesteds', 'SuggestionController@adminIndex');
    Route::get('/admin/get-categories', 'AdminController@getCategories');
    Route::post('/admin/suggested/destroy', 'SuggestionController@destroy');
    Route::post('/admin/reported-comments', 'AdminController@reportedComments');
    Route::post('/admin/reported-submissions', 'AdminController@reportedSubmissions');

    // feedback
    Route::post('/feedback', 'FeedbacksController@store');
    Route::post('/feedback/delete', 'FeedbacksController@destroy');

    // help
    Route::post('/new-help', 'HelpController@store');
    Route::post('/edit-help', 'HelpController@patch');
    Route::post('/help-index', 'HelpController@index');
    Route::post('/delete-help', 'HelpController@destroy');

    // Find Channels
    Route::post('/find-categories', 'SuggestionController@findCategories');
    Route::post('/store-suggested-channel', 'SuggestionController@store');

    // User
    Route::post('/auth', 'UserController@getAuth');
    Route::get('/fill-basic-store', 'StoreController@index');
    Route::post('/delete-my-account', 'UserController@destroy');
    Route::post('/destroy-comment', 'CommentController@destroy');
    Route::post('/destroy-submission', 'SubmissionController@destroy');
    Route::post('/update-profile', 'UserSettingsController@updateProfile');
    Route::post('/update-account', 'UserSettingsController@updateAccount');
    Route::post('/update-email', 'UserSettingsController@updateEmail');
    Route::post('/update-password', 'UserSettingsController@updatePassword');
    Route::post('/upvoted-submissions', 'UserController@upVotedSubmissions');
    Route::post('/update-home-feed', 'UserSettingsController@updateHomeFeed');
    Route::post('/downvoted-submissions', 'UserController@downVotedSubmissions');

    // submission
    Route::post('/submit', 'SubmissionController@store');
    Route::post('/patch-text-submission', 'SubmissionController@patchTextSubmission');
    Route::post('/hide-submission', 'SubmissionController@hide');
    Route::get('/fetch-url-title', 'SubmissionController@getTitleAPI');
    Route::post('/mark-submission-sfw', 'NsfwController@markAsSFW');

    Route::post('/mark-submission-nsfw', 'NsfwController@markAsNSFW');

    Route::post('/remove-thumbnail', 'SubmissionController@removeThumbnail');

    Route::get('/notifications', 'NotificationsController@unreadIndex');

    // voting
    Route::post('/upvote-comment', 'CommentVotesController@upVote');
    Route::post('/downvote-comment', 'CommentVotesController@downVote');
    Route::post('/upvote-submission', 'SubmissionVotesController@upVote');
    Route::post('/downvote-submission', 'SubmissionVotesController@downVote');

    // bookmarks
    Route::post('/bookmark-user', 'BookmarksController@bookmarkUser');
    Route::post('/bookmark-comment', 'BookmarksController@bookmarkComment');
    Route::post('/bookmark-category', 'BookmarksController@bookmarkCategory');
    Route::post('/bookmarked-users', 'BookmarksController@getBookmarkedUsers');
    Route::post('/bookmark-submission', 'BookmarksController@bookmarkSubmission');
    Route::post('/bookmarked-comments', 'BookmarksController@getBookmarkedComments');
    Route::post('/bookmarked-categories', 'BookmarksController@getBookmarkedCategories');
    Route::post('/bookmarked-submissions', 'BookmarksController@getBookmarkedSubmissions');

    // Comment
    Route::post('/comment', 'CommentController@store');
    Route::post('/edit-comment', 'CommentController@patch');
    Route::get('/submission-comments', 'CommentController@index');

    // Category
    Route::post('/channel', 'CategoryController@store');
    Route::post('/category-patch', 'CategoryController@patch');
    Route::get('/get-categories', 'CategoryController@getCategories');

    // rule
    Route::post('/create-rule', 'RulesController@store');
    Route::post('/patch-rule', 'RulesController@patch');
    Route::post('/destroy-rule', 'RulesController@destroy');

    // block domain
    Route::post('/block-domain', 'BlockDomainController@store');
    Route::post('/blocked-domains', 'BlockDomainController@index');
    Route::post('/block-domain/destroy', 'BlockDomainController@destroy');

    // ban user
    Route::post('/ban-user', 'BanController@store');
    Route::post('/banned-users', 'BanController@index');
    Route::post('/ban-user/destroy', 'BanController@destroy');

    // moderation
    Route::post('/moderators', 'ModeratorController@index');
    Route::get('/users', 'ModeratorController@getUsers');
    Route::post('/add-moderator', 'ModeratorController@store');
    Route::post('/destroy-moderator', 'ModeratorController@destroy');
    Route::post('/approve-comment', 'ModeratorController@approveComment');
    Route::post('/approve-submission', 'ModeratorController@approveSubmission');
    Route::post('/disapprove-comment', 'ModeratorController@disapproveComment');
    Route::post('/disapprove-submission', 'ModeratorController@disapproveSubmission');

    // messages
    Route::post('/message', 'MessagesController@store');
    Route::get('/contacts', 'MessagesController@getContacts');
    Route::get('/messages', 'MessagesController@getMessages');
    Route::post('/message-read', 'MessagesController@markAsRead');
    Route::post('/block-contact', 'MessagesController@blockUser');
    Route::get('/contact-info', 'MessagesController@contactInfo');
    Route::post('/search-contacts', 'MessagesController@searchContact');
    Route::post('/delete-messages', 'MessagesController@destroyMessages');
    Route::post('/leave-conversation', 'MessagesController@leaveConversation');
    Route::post('/conversation-read', 'MessagesController@broadcastConversaionAsRead');

    // Photo uploading
    Route::post('/user-avatar-crop', 'PhotoController@cropUserAvatar');
    Route::post('/upload-temp-avatar', 'PhotoController@uploadTempAvatar');
    Route::post('/categroy-avatar', 'PhotoController@categoryAvatarAPI');
    Route::post('/category-avatar-crop', 'PhotoController@cropCategoryAvatar');

    // notification
    Route::post('/all-notifications', 'NotificationsController@readIndex');
    Route::post('/mark-notifications-read', 'NotificationsController@markAsRead');

    // subscribe
    Route::post('/subscribe', 'SubscribeController@subscribeToggle');
    Route::get('/is-subscribed', 'SubscribeController@isSubscribed');

    // report
    Route::post('/report-comment', 'ReportsController@comment');
    Route::post('/report-submission', 'ReportsController@submission');
    Route::post('/reported-comments', 'ReportsController@reportedComments');
    Route::post('/reported-submissions', 'ReportsController@reportedSubmissions');
});

Route::group(['prefix' => 'auth', 'middleware' => 'auth:api'], function () {
    Route::get('/home', 'HomeController@feed');
    Route::get('/category-submissions', 'CategoryController@submissions');
    Route::get('/sidebar-categories', 'StoreController@sidebarCategories');
    Route::get('/suggested-category', 'SuggestionController@category');
});

// For both logged in users and guests
Route::get('/home', 'HomeController@feed');
Route::get('/get-submission', 'SubmissionController@getBySlug');
Route::get('/get-submission-by-id', 'SubmissionController@getById');
Route::get('/submission-comments', 'CommentController@index');
Route::get('/category-moderators', 'CategoryController@moderators');
Route::get('/rules', 'RulesController@index');
Route::get('/emoji-list', 'EmojiController@index');
Route::get('/submission-photos', 'SubmissionController@getPhotos');
Route::get('/search', 'SearchController@index');
Route::get('/home', 'HomeController@feed');
Route::get('/category-submissions', 'CategoryController@submissions');
Route::get('/get-category-store', 'CategoryController@fillStore');
Route::get('/suggested-category', 'SuggestionController@category');
Route::get('/get-user-store', 'UserController@fillStore');
Route::get('/user-submissions', 'UserController@submissions');
Route::get('/user-comments', 'UserController@comments');
Route::get('/sidebar-categories', 'StoreController@sidebarCategories');
