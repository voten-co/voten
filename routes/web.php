<?php

Route::group(['middleware' => ['maintenance', 'http2']], function () {
    Route::auth();
    Route::get('/logout', 'Auth\LoginController@logout');

    // Public Pages
    Route::get('/tos', 'PagesController@tos');
    Route::get('/', 'HomeController@homePage')->middleware('correct-view');
    Route::get('/credits', 'PagesController@credits');
    Route::get('/features', 'PagesController@features');
    Route::get('/about', 'PagesController@about');
    Route::get('/privacy-policy', 'PagesController@privacyPolicy');

    // guest browsing routes
    Route::get('/c/{category}', 'CategoryController@show')->middleware('correct-view');
    Route::get('/c/{category}/hot', 'CategoryController@redirect');
    Route::get('/c/{category}/{slug}', 'SubmissionController@show')->middleware('correct-view');
    Route::get('/help', 'HelpController@showHelpCenter')->middleware('correct-view');
    Route::get('/help/{help}', 'HelpController@show')->middleware('correct-view');

    Route::get('/@{username}', 'UserController@showSubmissions')->middleware('correct-view');
    Route::get('/@{username}/comments', 'UserController@showComments')->middleware('correct-view');

    // social logins
    Route::get('/login/google', 'Auth\LoginController@redirectToGoogle');
    Route::get('/login/google/callback', 'Auth\LoginController@handleGoogleCallback');

    // sitemaps
    Route::get('/sitemap.xml', 'SitemapsController@index');
    Route::get('/pages.xml', 'SitemapsController@pages');
    Route::get('/submissions.xml', 'SitemapsController@submissions');
    Route::get('/users.xml', 'SitemapsController@users');
    Route::get('/channels.xml', 'SitemapsController@categories');
    Route::get('/helps.xml', 'SitemapsController@helps');
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
Route::get('/backend/channels', 'BackendController@showCategories');
Route::get('/backend/channels/{category}', 'BackendController@showCategory');
Route::delete('/backend/channels/{category}/destroy', 'CategoryController@destroy');
Route::post('/backend/channels/{category}/takeover', 'BackendController@takeOverCategory');
Route::get('/backend/users', 'BackendController@showUsers');
Route::get('/backend/users/{user}', 'BackendController@showUser');
Route::delete('/backend/users/destroy', 'UserController@destroy');
Route::post('/ban-user', 'BanController@store');
Route::delete('/ban-user/destroy', 'BanController@destroy');
Route::post('/backend/firewall/ip/store', 'FirewallController@store');
Route::delete('/backend/firewall/ip/destroy', 'FirewallController@destroy');
Route::get('/backend/spam', 'BackendController@spam');
Route::get('/backend/update-comments-count', 'BackendController@updateCommentsCount');
Route::post('/forbidden-username/store', 'BackendController@storeForbiddenUsername');
Route::delete('/appointed/destroy/{appointed}', 'BackendController@destroyAppointed');
Route::post('/forbidden-category-name/store', 'BackendController@storeForbiddenCategoryName');
Route::delete('/forbidden-username/destroy/{forbidden}', 'BackendController@destroyForbiddenUsername');
Route::delete('/forbidden-category-name/destroy/{forbidden}', 'BackendController@destroyForbiddenCategoryName');

// ssh control
Route::get('/ssh/flush-all', 'SshController@flushAll');
Route::get('/ssh/cache-clear', 'SshController@clearCache');
Route::get('/ssh/stop-maintenance', 'SshController@stopMaintenanceMode');
Route::get('/ssh/start-maintenance', 'SshController@startMaintenanceMode');

// used for uploading photos via dropzone
Route::post('/upload-photo', 'PhotoController@upload');

// catch wild routes
Route::group(['middleware' => ['maintenance', 'http2', 'auth']], function () {
    Route::get('/{any}', 'PagesController@welcome')->where('any', '.*');
});
