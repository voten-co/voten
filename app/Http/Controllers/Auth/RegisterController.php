<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailAddress;
use App\Rules\NotForbiddenUsername;
use App\Rules\Recaptcha;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $this->middleware('guest', ['except' => ['verifyEmailAddress']]);
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
        return Validator::make($data, [
            'username'             => ['required', 'min:3', 'max:25', 'unique:users', 'regex:/^[A-Za-z0-9\._]+$/', new NotForbiddenUsername()],
            'email'                => 'sometimes|email|max:255|unique:users|nullable',
            'password'             => 'required|min:6|confirmed',
            'g-recaptcha-response' => ['required', new Recaptcha()],
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
            'username'  => $data['username'],
            'email'     => $data['email'],
            'password'  => bcrypt($data['password']),

            'settings'  => [
                'font'                          => 'Lato',
                'sidebar_color'                 => 'Gray',
                'nsfw'                          => false,
                'nsfw_media'                    => false,
                'notify_submissions_replied'    => true,
                'notify_comments_replied'       => true,
                'notify_mentions'               => true,
                'exclude_upvoted_submissions'   => false,
                'exclude_downvoted_submissions' => true,
                'submission_small_thumbnail'    => true,
            ],
            'info'    => [
                'website' => null,
                'twitter' => null,
            ],
        ]);
    }

    /**
     * Create a valid token and email it to user's email address.
     *
     * @param \App\User $user
     *
     * @return void
     */
    protected function pleaseConfirmEmailAddress($user)
    {
        $token = str_random(60);

        DB::table('email_verifications')->insert([
            'email'      => $user->email,
            'user_id'    => $user->id,
            'token'      => $token,
            'created_at' => now(),
        ]);

        \Mail::to($user->email)->queue(new VerifyEmailAddress($user->username, $token));
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

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect('/discover-channels?newbie=1&sidebar=0');
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
        // in case there is an email address
        if ($user->email) {
            $this->pleaseConfirmEmailAddress($user);
        }

        // set user's default data into cache to save few queries
        $userData = [
            'submissionsCount' => 0,
            'commentsCount'    => 0,

            'submissionXp' => 0,
            'commentXp'    => 0,

            'hiddenSubmissions' => collect(),
            'subscriptions'     => collect(),

            'blockedUsers' => collect(),

            'submissionUpvotes'   => collect(),
            'submissionDownvotes' => collect(),

            'bookmarkedSubmissions' => collect(),
            'bookmarkedComments'    => collect(),
            'bookmarkedChannels'    => collect(),
            'bookmarkedUsers'       => collect(),

            'commentUpvotes'   => collect(),
            'commentDownvotes' => collect(),
        ];

        Redis::hmset('user.'.$user->id.'.data', $userData);

        if ($request->expectsJson()) {
            return res(201, 'Registered successfully.');
        }
    }
}
