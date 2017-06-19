<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Invite;
use App\Mail\NewRegistration;
use App\Mail\WelcomeToVoten;
use App\User;
use App\UserForbiddenName;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if (isset($data['email'])) {
            return Validator::make($data, [
                'username' => 'required|min:3|max:25|unique:users|regex:/^[A-Za-z0-9\._]+$/',
                'email'    => 'required|email|max:255|unique:users',
                'password' => 'required|min:6|confirmed',
            ]);
        }

        // if the user doesn't wanna share his email address with us
        return Validator::make($data, [
            'username' => 'required|min:3|max:25|unique:users|regex:/^[A-Za-z0-9\._]+$/',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /* --------------------------------------------------------------------- */
    /* ------------------------ Overwritten Methods ------------------------ */
    /* --------------------------------------------------------------------- */

    /**
     * Show the application registration form. Also make sure the user owns a registeration invite code.
     * This of course is just for our Beta Phase which is invite-only (Yiiiks).
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request)
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        // make sure the username is not in the blacklist
        if ($this->isForbiddenUsername($request->username)) {
            return response('This username is forbidden. Please pick another one.', 500);
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect('/find-channels?newbie=1&sidebar=0');
    }

    /**
     * The user has been registered. Let's set some default settings to make him/her happy like ":)".
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User                $user
     *
     * @return void
     */
    protected function registered(Request $request, $user)
    {
        $user->update([
            'confirmed' => 0, // User has received the invitation code so clearly his email address is for real.
            'settings'  => [
                'font'                          => 'Lato',
                'sidebar_color'                 => 'Gray',
                'nsfw'                          => false,
                'nsfw_media'                    => false,
                'notify_submissions_replied'    => true,
                'notify_comments_replied'       => true,
                'exclude_upvoted_submissions'   => false,
                'exclude_downvoted_submissions' => true,
                'submission_small_thumbnail'    => true,
            ],
            'info' => [
                'website' => null,
                'twitter' => null,
            ],
        ]);

        // in case there is an email address
        if ($user->email) {
            \Mail::to($user->email)->queue(new WelcomeToVoten($user->username));
        }

        // Email sully every 10 new registers :D
        if (User::count() % 10 == 0) {
            \Mail::to('fischersully@gmail.com')->queue(new NewRegistration($user->username));
        }

        // set user's default data into cache to save few queries
        $userData = [
            'submissionsCount' => 0,
            'commentsCount'    => 0,

            'submissionKarma' => 0,
            'commentKarma'    => 0,

            'hiddenSubmissions' => collect(),
            'subscriptions'     => collect(),

            'blockedUsers' => collect(),

            'submissionUpvotes'   => collect(),
            'submissionDownvotes' => collect(),

            'bookmarkedSubmissions' => collect(),
            'bookmarkedComments'    => collect(),
            'bookmarkedCategories'  => collect(),
            'bookmarkedUsers'       => collect(),

            'commentUpvotes'   => collect(),
            'commentDownvotes' => collect(),
        ];

        Redis::hmset('user.'.$user->id.'.data', $userData);

        \App\Activity::create([
            'subject_id'   => $user->id,
            'ip_address'   => $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'user_agent'   => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'country'      => $_SERVER['HTTP_CF_IPCOUNTRY'] ?? 'unknown',
            'subject_type' => 'App\User',
            'name'         => 'created_user',
            'user_id'      => $user->id,
        ]);
    }

    /**
     * is the username forbidden for users?
     *
     * @return bool
     */
    protected function isForbiddenUsername($username)
    {
        return UserForbiddenName::where('username', $username)->exists();
    }
}
