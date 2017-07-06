<?php

Route::group(['middleware' => ['maintenance', 'http2']], function () {
    Route::auth();
    Route::get('/logout', 'Auth\LoginController@logout');

    // Public Pages
    Route::get('/tos', 'PagesController@tos');
    Route::get('/', 'HomeController@homePage');
    Route::get('/credits', 'PagesController@credits');
    Route::get('/features', 'PagesController@features');
    Route::get('/about', 'PagesController@about');
    Route::get('/privacy-policy', 'PagesController@privacyPolicy');

    // guest browsing routes
    Route::get('/c/{category}', 'CategoryController@show');
    Route::get('/c/{category}/hot', 'CategoryController@redirect');
    Route::get('/c/{category}/{slug}', 'SubmissionController@show');

    Route::get('/@{username}', 'UserController@showSubmissions');
    Route::get('/@{username}/comments', 'UserController@showComments');

    // social logins
    Route::get('/login/google', 'Auth\LoginController@redirectToGoogle');
    Route::get('/login/facebook', 'Auth\LoginController@redirectToFacebook');
    Route::get('/login/google/callback', 'Auth\LoginController@handleGoogleCallback');
    Route::get('/login/facebook/callback', 'Auth\LoginController@handleFacebookCallback');
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
Route::get('/backend/forbidden-names', 'BackendController@forbiddenNames');
Route::get('/backend/appointed-users', 'BackendController@indexAppointedUsers');
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
