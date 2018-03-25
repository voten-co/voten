<?php

use App\Channel;
use App\Mail\ChannelRemovalWarning;
use Illuminate\Support\Facades\Mail;

// channel removal
Artisan::command('send-channel-removal-warning-emails', function () {
    $inactive_channels = Channel::where('created_at', '<=', now()->subMonths(2))
        ->whereDoesntHave('submissions', function ($query) {
            $query->where('created_at', '>=', now()->subMonths(2));
        })->get();

    foreach ($inactive_channels as $channel) {
        $mods = $channel->moderators;

        foreach ($mods as $user) {
            if ($user->confirmed) {
                Mail::to($user->email)->queue(new ChannelRemovalWarning($user, $channel));
            }
        }
    }

    $this->info($inactive_channels->count() . ' have been warned. ');
})->describe('Send removal warning emails to moderators of channels inactive for more than 2 months.');
