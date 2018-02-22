<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmailAddress;
use App\Rules\CurrentPassword;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * updates user profile info.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function profile(Request $request)
    {
        $this->validate($request, [
            'cover_color' => 'in:Dark Blue,Blue,Red,Dark,Pink,Dark Green,Bright Green,Purple,Gray,Orange',
            'location'    => 'nullable|string|max:25',
            'name'        => 'nullable|string|max:25',
            'bio'         => 'nullable|max:400',
            'website'     => 'nullable|url',
            'twitter'     => 'nullable|string|max:25',
        ]);

        Auth::user()->update([
            'color'    => $request->cover_color,
            'location' => $request->location,
            'name'     => $request->name,
            'bio'      => $request->bio,
            'info'     => [
                'website' => $request->website,
                'twitter' => $request->twitter,
            ],
        ]);

        return res(200, 'Your settings has been updated');
    }

    /**
     * updates the account settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function account(Request $request)
    {
        $this->validate($request, [
            'notify_submissions_replied' => 'required|boolean',
            'notify_comments_replied'    => 'required|boolean',
            'notify_mentions'            => 'required|boolean',
        ]);

        Auth::user()->update([
            'settings' => [
                'notify_submissions_replied' => $request->notify_submissions_replied,
                'notify_comments_replied'    => $request->notify_comments_replied,
                'notify_mentions'            => $request->notify_mentions,
            ],
        ]);

        return res(200, 'Your notifications settings has been updated');
    }

    /**
     * updates email address.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return response
     */
    public function email(Request $request)
    {
        $this->validate($request, [
            'password' => [new CurrentPassword()],
            'email'    => 'email|max:255|unique:users',
        ]);

        Auth::user()->update([
            'email'     => $request->email,
            'confirmed' => 0,
        ]);

        $this->pleaseConfirmEmailAddress(Auth::user());

        return res(200, 'Email has been successfully updated');
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
        $token = str_random(64);

        DB::table('email_verifications')->insert([
            'email'      => $user->email,
            'user_id'    => $user->id,
            'token'      => $token,
            'created_at' => now(),
        ]);

        \Mail::to($user->email)->queue(new VerifyEmailAddress($user->username, $token));
    }

    /**
     * updates user's password.
     *
     * @return
     */
    public function password(Request $request)
    {
        $this->validate($request, [
            'new_password' => ['required', 'min:6', 'confirmed'],
            'password'     => ['required', new CurrentPassword()],
        ]);

        Auth::user()->update([
            'password' => bcrypt($request->new_password),
        ]);

        return res(200, 'Password has been successfully updated.');
    }
}
