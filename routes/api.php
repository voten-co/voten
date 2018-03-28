<?php

Route::group(['middleware' => ['auth:api']], function () {
    // Administrator routes
    Route::post('/admin/check', 'AdminController@isAdministrator'); // checked 
    Route::get('/admin/users', 'AdminController@indexUsers'); // checked 
    Route::get('/admin/comments', 'AdminController@indexComments'); // checked 
    Route::get('/admin/channels', 'AdminController@indexChannels'); // checked 
    Route::get('/admin/channels/inactive', 'AdminController@inactiveChannels'); // checked
    Route::get('/admin/submissions', 'AdminController@indexSubmissions'); // checked 
    Route::get('/admin/suggesteds', 'SuggestionController@adminIndex'); // checked 
    Route::post('/admin/suggesteds', 'SuggestionController@store'); // checked 
    Route::delete('/admin/suggesteds/{suggested}', 'SuggestionController@destroy'); // checked 
    Route::get('/admin/reports/comments', 'AdminController@reportedComments'); // checked 
    Route::get('/admin/reports/submissions', 'AdminController@reportedSubmissions'); // checked 
    Route::get('/admin/activities', 'AdminController@activities'); // checked 
    Route::get('/admin/echo', 'AdminController@echoServer'); // checked 
    Route::get('/admin/statistics', 'AdminController@statistics'); // checked 

    // feedback
    Route::get('/feedbacks/{feedback}', 'FeedbacksController@get')->middleware('voten-administrator'); // checked
    Route::get('/feedbacks', 'FeedbacksController@index')->middleware('voten-administrator'); // checked
    Route::post('/feedbacks', 'FeedbacksController@store')->middleware('shaddow-ban'); // checked
    Route::delete('/feedbacks/{feedback}', 'FeedbacksController@destroy')->middleware('voten-administrator'); // checked

    // Find Channels
    Route::get('/channels/discover', 'SuggestionController@discover'); // checked 

    // User
    Route::get('/users/store', 'StoreController@index'); // checked 
    Route::delete('/users', 'UserController@destroyAsAuth'); // checked 
    Route::delete('/admin/users', 'UserController@destroyAsVotenAdministrator')->middleware('voten-administrator'); // checked 
    Route::patch('/users/profile', 'UserSettingsController@profile'); // checked 
    Route::patch('/users/account', 'UserSettingsController@account'); // checked 
    Route::patch('/users/email', 'UserSettingsController@email'); // checked 
    Route::patch('/users/password', 'UserSettingsController@password'); // checked 
    Route::get('/users/submissions/upvoteds', 'UserController@upVotedSubmissions'); // checked 
    Route::get('/users/submissions/downvoteds', 'UserController@downVotedSubmissions'); // checked 
    Route::post('/email/verify/resend', 'Auth\VerificationController@resendVerifyEmailAddress'); // checked 
    Route::post('/users/clientside-settings', 'ClientsideSettingsController@store'); // checked 
    Route::get('/users/clientside-settings', 'ClientsideSettingsController@get'); // checked 

    // submission
    Route::post('/submissions', 'SubmissionController@store')->middleware('shaddow-ban'); // checked 
    Route::patch('/submissions/{submission}', 'SubmissionController@patchTextSubmission');
    Route::delete('/submissions/{submission}', 'SubmissionController@destroy');
    Route::post('/hide-submission', 'BlockSubmissionsController@store');
    Route::get('/links/title', 'SubmissionController@getTitleAPI');
    Route::post('/mark-submission-sfw', 'NsfwController@markAsSFW');

    Route::post('/mark-submission-nsfw', 'NsfwController@markAsNSFW');

    Route::delete('/submissions/{submission}/thumbnail', 'SubmissionController@removeThumbnail');

    Route::get('/notifications/unseen', 'NotificationsController@unreadIndex');

    // voting
    Route::post('/upvote-comment', 'CommentVotesController@upVote')->middleware('shaddow-ban');
    Route::post('/downvote-comment', 'CommentVotesController@downVote')->middleware('shaddow-ban');
    Route::post('/upvote-submission', 'SubmissionVotesController@upVote')->middleware('shaddow-ban');
    Route::post('/downvote-submission', 'SubmissionVotesController@downVote')->middleware('shaddow-ban');

    // bookmarks
    Route::post('/bookmark-user', 'BookmarksController@bookmarkUser');
    Route::post('/bookmark-comment', 'BookmarksController@bookmarkComment');
    Route::post('/bookmark-channel', 'BookmarksController@bookmarkChannel');
    Route::get('/bookmarked-users', 'BookmarksController@getBookmarkedUsers');
    Route::post('/bookmark-submission', 'BookmarksController@bookmarkSubmission');
    Route::get('/bookmarked-comments', 'BookmarksController@getBookmarkedComments');
    Route::get('/bookmarked-channels', 'BookmarksController@getBookmarkedChannels');
    Route::get('/bookmarked-submissions', 'BookmarksController@getBookmarkedSubmissions');

    // Comment
    Route::post('/comments', 'CommentController@store')->middleware('shaddow-ban'); // check
    Route::patch('/comments/{comment}', 'CommentController@patch'); // check
    Route::delete('/comments/{comment}', 'CommentController@destroy'); // check

    // Channel
    Route::post('/channels', 'ChannelController@store')->middleware('shaddow-ban');
    Route::patch('/channels', 'ChannelController@patch');
    Route::post('/channel-block', 'BlockChannelsController@store');
    Route::delete('/channel-unblock', 'BlockChannelsController@destroy');
    Route::post('/channels/{channel}', 'ChannelController@destroy')->middleware('voten-administrator');
    Route::get('/get-channels', 'ChannelController@getChannels');
    Route::get('/subscribed-channels', 'SubscribeController@index');

    // rule
    Route::post('/channels/rules', 'RulesController@store');
    Route::patch('/channels/rules', 'RulesController@patch');
    Route::delete('/channels/rules', 'RulesController@destroy');

    // block domain
    Route::get('/channels/domains/block', 'BlockDomainController@indexAsChannelModerator')->middleware('moderator');
    Route::post('/channels/domains/block', 'BlockDomainController@storeAsChannelModerator')->middleware('moderator');
    Route::delete('/channels/domains/block', 'BlockDomainController@destroyAsChannelModerator')->middleware('moderator');
    // (admin)
    Route::get('/admin/domains/block', 'BlockDomainController@indexAsVotenAdministrator')->middleware('voten-administrator');
    Route::post('/admin/domains/block', 'BlockDomainController@storeAsVotenAdministrator')->middleware('voten-administrator');
    Route::delete('/admin/domains/block', 'BlockDomainController@destroyAsVotenAdministrator')->middleware('voten-administrator');

    // ban user
    Route::post('/channels/users/bans', 'BanController@storeAsChannelModerator')->middleware('moderator');
    Route::get('/channels/users/bans', 'BanController@indexasChannelModerator')->middleware('moderator');
    Route::delete('/channels/users/bans', 'BanController@destroyAsChannelModerator')->middleware('moderator');
    // (admin)
    Route::post('/admin/users/bans', 'BanController@storeAsVotenAdministrator')->middleware('voten-administrator');
    Route::get('/admin/users/bans', 'BanController@indexAsVotenAdministrator')->middleware('voten-administrator');
    Route::delete('/admin/users/bans', 'BanController@destroyAsVotenAdministrator')->middleware('voten-administrator');

    // moderation
    Route::post('/moderators', 'ModeratorController@store');
    Route::post('/destroy-moderator', 'ModeratorController@destroy');
    Route::post('/approve-comment', 'ModeratorController@approveComment');
    Route::post('/approve-submission', 'ModeratorController@approveSubmission');
    Route::post('/disapprove-comment', 'ModeratorController@disapproveComment');
    Route::post('/disapprove-submission', 'ModeratorController@disapproveSubmission');

    // messages
    Route::post('/messages', 'MessagesController@store')->middleware('shaddow-ban');
    Route::get('/messages', 'MessagesController@index');
    Route::delete('/messages', 'MessagesController@destroy');
    Route::post('/messages/read', 'MessagesController@markAsRead');

    // conversations
    Route::get('/conversations', 'ConversationsController@index');
    Route::delete('/conversations', 'ConversationsController@destroy');
    Route::post('/conversations/read', 'ConversationsController@broadcastConversaionAsRead');
    Route::post('/conversations/block', 'ConversationsController@block');
    Route::get('/conversations/search', 'SearchController@conversations');

    // Photo uploading
    Route::post('/channels/avatar', 'PhotoController@channelAvatar');
    Route::post('/users/avatar', 'PhotoController@userAvatar')->middleware('shaddow-ban');
    Route::post('/photos', 'PhotoController@store')->middleware('shaddow-ban');
    // Route::post('/gifs', 'GifController@store')->middleware('shaddow-ban');

    // notification
    Route::get('/notifications', 'NotificationsController@readIndex');
    Route::post('/notifications/seen', 'NotificationsController@markAsRead');

    // subscribe
    Route::post('/subscribe', 'SubscribeController@subscribeToggle')->middleware('shaddow-ban');
    Route::get('/is-subscribed', 'SubscribeController@isSubscribed');

    // report
    Route::post('/comments/reports', 'ReportCommentsController@store')->middleware('shaddow-ban');
    Route::get('/comments/reports', 'ReportCommentsController@index');
    Route::post('/submissions/reports', 'ReportSubmissionsController@store')->middleware('shaddow-ban');
    Route::get('/submissions/reports', 'ReportSubmissionsController@index');

    Route::post('/announcement/seen', 'AnnouncementController@seen');

    Route::get('/suggested-channel', 'SuggestionController@channel');

    ////////////////////////////////////////////////////////////////////////
    // Below routes have a twin route prefixed with "guest"
    ////////////////////////////////////////////////////////////////////////
    Route::get('/users', 'UserController@get');
    Route::get('/feed', 'HomeController@feed');
    Route::get('/channels/submissions', 'ChannelController@submissions');
    Route::get('/announcement', 'AnnouncementController@get');

    Route::get('/submissions', 'SubmissionController@get');
    Route::get('/submissions/{submission}/comments', 'CommentController@index');
    Route::get('/moderators', 'ModeratorController@index');
    Route::get('/channels/rules', 'RulesController@index');
    Route::get('/emojis', 'EmojiController@index');
    Route::get('/submissions/photos', 'SubmissionController@getPhotos');
    Route::get('/search', 'SearchController@index');
    Route::get('/channels', 'ChannelController@get');
    Route::get('/users/submissions', 'UserController@submissions');
    Route::get('/users/comments', 'UserController@comments');
    Route::get('/submissions/comments', 'CommentController@index');
});

////////////////////////////////////////////////////////////////////////
// Below routes are the twin routes for guests
////////////////////////////////////////////////////////////////////////
Route::prefix('guest')->group(function () {
    Route::get('/users', 'UserController@get');
    Route::get('/feed', 'HomeController@feed');
    Route::get('/channels/submissions', 'ChannelController@submissions');
    Route::get('/announcement', 'AnnouncementController@get');

    Route::get('/submissions', 'SubmissionController@get');
    Route::get('/submissions/{submission}/comments', 'CommentController@index');
    Route::get('/moderators', 'ModeratorController@index');
    Route::get('/channels/rules', 'RulesController@index');
    Route::get('/emojis', 'EmojiController@index');
    Route::get('/submissions/photos', 'SubmissionController@getPhotos');
    Route::get('/search', 'SearchController@index');
    Route::get('/channels', 'ChannelController@get');
    Route::get('/users/submissions', 'UserController@submissions');
    Route::get('/users/comments', 'UserController@comments');
    Route::get('/submissions/comments', 'CommentController@index');
});

Route::post('/token/login', 'Auth\LoginController@getAccessToken');
Route::post('/token/register', 'Auth\RegisterController@getAccessToken');