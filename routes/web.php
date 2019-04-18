<?php

// redirect a few commonly used pages to their correct address.
Route::permanentRedirect('/api', 'https://api.voten.co');
Route::permanentRedirect('/help', 'https://help.voten.co');
Route::permanentRedirect('/help-center', 'https://help.voten.co');
Route::permanentRedirect('/source-code', 'https://github.com/voten-co/voten');
Route::permanentRedirect('/blog', 'https://medium.com/voten');
Route::permanentRedirect('/dev', '/c/votendev');
Route::permanentRedirect('/developers', '/c/votendev');

Route::group(['middleware' => ['http2']], function () {
    // Authintication routes
    Route::auth();
    Route::get('/logout', 'Auth\LoginController@logout');

    // social logins
    Route::get('/login/google', 'Auth\LoginController@redirectToGoogle');
    Route::get('/login/google/callback', 'Auth\LoginController@handleGoogleCallback');

    Route::get('/email/verify', 'Auth\VerificationController@verifyEmailAddress');

    // Public Pages
    Route::get('/', 'HomeController@homePage')->middleware('correct-view');
    Route::get('/credits', 'PagesController@welcome');
    Route::get('/tos', 'PagesController@welcome');
    Route::get('/about', 'PagesController@welcome');
    Route::get('/privacy-policy', 'PagesController@welcome');

    // guest browsing routes
    Route::get('/c/{channel}', 'ChannelController@show')->middleware('correct-view');
    Route::get('/c/{channel}/{slug}', 'SubmissionController@show')->middleware('correct-view');
    Route::get('/@{username}', 'UserController@showSubmissions')->middleware('correct-view');
    Route::get('/@{username}/comments', 'UserController@showComments')->middleware('correct-view');

    // sitemaps
    Route::get('/sitemap.xml', 'SitemapsController@index');
    Route::get('/pages.xml', 'SitemapsController@pages');
    Route::get('/submissions.xml', 'SitemapsController@submissions');
    Route::get('/users.xml', 'SitemapsController@users');
    Route::get('/channels.xml', 'SitemapsController@channels');
});

Route::group(['prefix' => 'api'], function () {
    // Authintication routes
    Route::auth();
    Route::get('/logout', 'Auth\LoginController@logout');
    Route::post('/guest/login', 'Auth\LoginController@login');
});

// backend-admin
Route::get('/backend', 'BackendController@dashboard');
Route::post('/block-domain', 'BlockDomainController@store');
Route::post('/appointed/store', 'BackendController@storeAppointed');
Route::get('/backend/announcements', 'AnnouncementController@show');
Route::post('/create-announcement', 'AnnouncementController@store');
Route::delete('/announcement/destroy/{announcement}', 'AnnouncementController@destroy');
Route::delete('/block-domain/destroy', 'BlockDomainController@destroy');
Route::get('/backend/server-control', 'BackendController@serverControls');
Route::get('/backend/firewall', 'BackendController@firewall');
Route::get('/backend/appointed-users', 'BackendController@indexAppointedUsers');
Route::get('/backend/channels', 'BackendController@showChannels');
Route::get('/backend/channels/{channel}', 'BackendController@showChannel');
Route::get('/backend/users', 'BackendController@showUsers');
Route::get('/backend/users/{user}', 'BackendController@showUser');
Route::delete('/backend/users/destroy', 'UserController@destroy');
Route::post('/ban-user', 'BanController@storeAsVotenAdministrator');
Route::delete('/ban-user/destroy', 'BanController@destroyAsVotenAdministrator');
Route::post('/backend/firewall/ip/store', 'FirewallController@store');
Route::delete('/backend/firewall/ip/destroy', 'FirewallController@destroy');
Route::get('/backend/spams/multiple-accounts', 'Backend\SpamsController@multipleAccounts');
Route::get('/backend/spams/submissions', 'Backend\SpamsController@submissions');
Route::get('/backend/spams/comments', 'Backend\SpamsController@comments');
Route::post('/approve-comment', 'ModeratorController@approveComment');
Route::post('/approve-submission', 'ModeratorController@approveSubmission');
Route::post('/disapprove-comment', 'ModeratorController@disapproveComment');
Route::post('/disapprove-submission', 'ModeratorController@disapproveSubmission');
Route::post('/backend/update-comments-count', 'BackendController@updateCommentsCount');
Route::post('/forbidden-username/store', 'BackendController@storeForbiddenUsername');
Route::delete('/appointed/destroy/{appointed}', 'BackendController@destroyAppointed');
Route::post('/forbidden-channel-name/store', 'BackendController@storeForbiddenChannelName');
Route::delete('/forbidden-username/destroy/{forbidden}', 'BackendController@destroyForbiddenUsername');
Route::delete('/forbidden-channel-name/destroy/{forbidden}', 'BackendController@destroyForbiddenChannelName');
Route::get('/backend/emails', 'EmailsController@index');
Route::post('/emails/announcement/store', 'EmailsController@store');
Route::post('/emails/announcement/send', 'EmailsController@send');
Route::post('/backend/channel-removal-warnings/send', 'WarningsController@channelsRemoval');
Route::get('/emails/announcement/preview', 'EmailsController@preview');

// ssh control
Route::post('/ssh/flush-all', 'SshController@flushAll');
Route::post('/ssh/cache-clear', 'SshController@clearCache');

// Passport
// Route::get('/apps', 'OAuthController@show');

// catch wild routes
Route::group(['middleware' => ['http2', 'auth']], function () {
    Route::get('/{any}', 'PagesController@welcome')->where('any', '.*');
});
