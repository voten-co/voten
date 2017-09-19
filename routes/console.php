<?php

// This is a one-time action.
Artisan::command('send-verification-email', function () {
    $users = \App\User::whereNotNull('email')->where('confirmed', 0)->get();

    foreach ($users as $user) {
        $token = str_random(60);

        DB::table('email_verifications')->insert([
            'email'      => $user->email,
            'user_id'    => $user->id,
            'token'      => $token,
            'created_at' => now(),
        ]);

        Mail::to($user->email)->queue(new \App\Mail\VerifyEmailAddress($user->username, $token));
    }

    $this->info($users->count().' Emails have been queued for sending. ');
})->describe('Send verification emails to those who have filled an email address but have not verified it.');
