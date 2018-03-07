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
use Validator;
use App\Traits\ApiAuthentication;

class RegisterController extends Controller
{
    use RegistersUsers, ApiAuthentication;

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
                'notify_submissions_replied'    => true,
                'notify_comments_replied'       => true,
                'notify_mentions'               => true,
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
        if (! $user->email) {
            return; 
        }

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
     * Show the application registration form.
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
        $this->pleaseConfirmEmailAddress($user);

        $this->storeInRedis($user);

        if ($request->expectsJson()) {
            return res(201, 'Registered successfully.');
        }
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
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->pleaseConfirmEmailAddress($user);

        $this->storeInRedis($user);

        return $this->generateAccessToken($user);
    }
}
