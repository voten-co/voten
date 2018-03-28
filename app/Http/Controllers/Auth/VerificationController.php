<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailAddress;
use App\Mail\WelcomeToVoten;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerificationController extends Controller
{
    /**
     * Verify user's email address based on the sent token.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function verifyEmailAddress(Request $request)
    {
        if (!$email_verification = DB::table('email_verifications')->where('token', $request->token)->first()) {
            return redirect('/');
        }

        if ($email_verification->verified_at === null) {
            DB::table('email_verifications')
                ->where(['token' => $request->token])
                ->update([
                    'verified_at' => now(),
                ]);

            $user = User::where('email', $email_verification->email)->firstOrFail();

            $user->update(['confirmed' => 1]);

            // In case user has changed his email address (and already recieved "WelcomeToVoten" before), there's no need to send "WelcomeToVoten" again.
            if (DB::table('email_verifications')->where(['user_id' => $user->id])->count() === 1) {
                \Mail::to($user->email)->queue(new WelcomeToVoten($user->username));
            }
        }

        return view('auth.verify-email', compact('email_verification'));
    }

    /**
     * resends the Verification email.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendVerifyEmailAddress()
    {
        $email_verification = DB::table('email_verifications')
            ->where('email', Auth::user()->email)
            ->first();

        \Mail::to(Auth::user()->email)->queue(
            new VerifyEmailAddress(Auth::user()->username, $email_verification->token)
        );

        return res(200, 'Verification email re-sent. ');
    }
}
