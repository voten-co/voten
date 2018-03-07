<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeToVoten;
use App\PhotoTools;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Faker\Factory as Faker;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
use WhichBrowser\Parser;
use App\Traits\ApiAuthentication;

class LoginController extends Controller
{
    use PhotoTools, AuthenticatesUsers, ApiAuthentication;

    protected $username = 'username';
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($request->expectsJson()) {
            return res(200, 'Logged in successfully.');
        }
    }

    /* --------------------------------------------------------------------- */
    /* ------------------------- Laravel Socialite ------------------------- */
    /* --------------------------------------------------------------------- */

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return Response
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/');
        }

        $authUser = $this->findOrCreateUser($user);

        Auth::login($authUser, true);

        return redirect($this->redirectTo);
    }

    /**
     * Return user if exists; create and return if doesn't.
     *
     * @param $providerUser
     *
     * @return User
     */
    private function findOrCreateUser($providerUser)
    {
        if ($authUser = User::where('email', $providerUser->email)->first()) {
            return $authUser;
        }

        $user = User::create([
            'username'  => $this->generateUsername($providerUser),
            'name'      => $providerUser->getName(),
            'email'     => $providerUser->getEmail(),
            'confirmed' => 1,
            'password'  => bcrypt(str_random(20)),
            'avatar'    => $this->downloadImg($providerUser->getAvatar(), 'users/avatars'),

            'settings' => [
                'notify_submissions_replied'    => true,
                'notify_comments_replied'       => true,
                'notify_mentions'               => true,
            ],

            'info' => [
                'website' => null,
                'twitter' => null,
            ],
        ]);

        \Mail::to($user->email)->queue(new WelcomeToVoten($user->username));

        // set user's default data into cache to save few queries
        $this->storeInRedis($user);

        $user_agent_parser = new Parser($_SERVER['HTTP_USER_AGENT']);
        \App\Activity::create([
            'subject_id'           => $user->id,
            'ip_address'           => $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'user_agent'           => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'country'              => $_SERVER['HTTP_CF_IPCOUNTRY'] ?? 'unknown',
            'device'               => $user_agent_parser->device->model ?? 'unknown',
            'os'                   => $user_agent_parser->os->toString() ?? 'unknown',
            'browser_name'         => $user_agent_parser->browser->name ?? 'unknown',
            'browser_version'      => $user_agent_parser->browser->version->toString() ?? 'unknown',
            'subject_type'         => 'App\User',
            'name'                 => 'created_user',
            'user_id'              => $user->id,
        ]);

        $this->redirectTo = '/discover-channels?newbie=1&sidebar=0';

        return $user;
    }

    /**
     * Generates a unique username. First we check if the provider contains a valid username, if it does,
     * we just make sure it's a unique one and that's it. If it doesn't we give it another try with the
     * str_slug($string, '.') version of it. It that fails as well or if the nickname is not available.
     *
     * we try the same logic this time for the name provided. If that fails too, then we try it with the
     * email address mines after @ pattern. If that fails as well (which is unlikely) then we just go
     * ahead and generate a random username using the faker library which is included in Laravel.
     *
     * @param Socialite $providerUser
     *
     * @return string
     */
    protected function generateUsername($providerUser)
    {
        // it would be great if the provider already has a valid username
        if ($nickname = $providerUser->getNickname()) {
            if ($this->isUsernameInValidFormat($nickname)) {
                return $this->makeSureUsernameIsUnique($nickname);
            }

            if ($this->isUsernameInValidFormat($nickname = str_slug($nickname, '.'))) {
                return $this->makeSureUsernameIsUnique($nickname);
            }
        }

        // now lets try it with the name
        if ($name = $providerUser->getName()) {
            if ($this->isUsernameInValidFormat($name)) {
                return $this->makeSureUsernameIsUnique($name);
            }

            if ($this->isUsernameInValidFormat($name = str_slug($name, '.'))) {
                return $this->makeSureUsernameIsUnique($name);
            }
        }

        // lets give the provider its last shot
        $email = $providerUser->getEmail();
        $parts = explode('@', $email);
        $emailUsername = $parts[0];

        if ($this->isUsernameInValidFormat($emailUsername)) {
            return $this->makeSureUsernameIsUnique($emailUsername);
        }

        // if we don't have a unique username by now, then it's not gonna work! let's just generate one using the faker library!
        $faker = Faker::create();
        $fakeUsername = $faker->username;
        if ($this->isUsernameInValidFormat($fakeUsername)) {
            return $this->makeSureUsernameIsUnique($fakeUsername);
        }

        // I give up :|
        return $this->makeSureUsernameIsUnique(str_random(8));
    }

    /**
     * makes sure the username is unique (if it's not, creates one).
     *
     * @return string
     */
    public function makeSureUsernameIsUnique($username)
    {
        if (!User::where('username', $username)->exists()) {
            return $username;
        }

        $usernameNumber = 1;
        $newUsername = $username;

        $users = User::where('username', 'LIKE', $username.'%')->get();

        while ($users->contains($newUsername)) {
            $newUsername = $username.$usernameNumber;
            $usernameNumber++;
        }

        return $newUsername;
    }

    /**
     * Is the $username in the right format?
     *
     * @param string $username
     *
     * @return void
     */
    protected function isUsernameInValidFormat($username)
    {
        return preg_match('/^[A-Za-z0-9\._]+$/i', $username);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $username = request()->input('username');

        $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$field => $username]);

        return $field;
    }

    /* --------------------------------------------------------------------- */
    /* ----------------------------- API Methods --------------------------- */
    /* --------------------------------------------------------------------- */

    /**
     * Logins and createa a valid access token. 
     * 
     * @return JSON 
     */
    public function getAccessToken(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $this->clearLoginAttempts($request);

            return $this->generateAccessToken($this->guard()->user());
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }
}
