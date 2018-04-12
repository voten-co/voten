<?php

namespace App\Http;

use App\Http\Middleware\Firewall;
use App\Http\Middleware\LoadDefaultViewForAuthenticatedUsers;
use App\Http\Middleware\MustBeAdministrator;
use App\Http\Middleware\MustBeModerator;
use App\Http\Middleware\MustBeVotenAdministrator;
use App\Http\Middleware\ShadowBan;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        Firewall::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Laravel\Passport\Http\Middleware\CreateFreshApiToken::class,
        ],

        'api' => [
            'throttle:120,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'                => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic'          => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings'            => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can'                 => \Illuminate\Auth\Middleware\Authorize::class,
        'guest'               => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'            => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'http2'               => \JacobBennett\Http2ServerPush\Middleware\AddHttp2ServerPush::class,
        'correct-view'        => LoadDefaultViewForAuthenticatedUsers::class,
        'voten-administrator' => MustBeVotenAdministrator::class,
        'administrator'       => MustBeAdministrator::class,
        'moderator'           => MustBeModerator::class,
        'shadow-ban'         => ShadowBan::class,
    ];
}
