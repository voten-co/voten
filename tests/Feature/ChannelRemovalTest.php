<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;
use Carbon\Carbon;
use App\Submission;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use App\Mail\ChannelRemovalWarning;
use Illuminate\Support\Facades\Mail;
use App\User;

class ChannelRemovalTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function check_for_inactive_channels_and_send_removal_warning_emails()
    {
        // inactive_channel: a channel that hasn't had any posts for more than 2 months 
        $this->signInViaPassportAsVotenAdministrator();

        $active_channel = create(Channel::class, [
            'created_at' => now()->subMonths(5)
        ]);
        $inactive_channel1 = create(Channel::class, [
            'created_at' => now()->subMonths(5)
        ]);
        $inactive_channel2 = create(Channel::class, [
            'created_at' => now()->subMonths(5)
        ]);

        $moderator = create(User::class, ['username' => 'admin']); 

        // create moderators for channels 
        $this->json('POST', '/api/moderators', [
            'channel_id' => $inactive_channel1->id, 
            'username' => 'admin', 
            'role' => 'administrator'
        ])->assertStatus(201);
        $this->json('POST', '/api/moderators', [
            'channel_id' => $inactive_channel2->id, 
            'username' => 'admin', 
            'role' => 'administrator'
        ])->assertStatus(201);
        $this->json('POST', '/api/moderators', [
            'channel_id' => $active_channel->id, 
            'username' => 'admin', 
            'role' => 'administrator'
        ])->assertStatus(201);
        
        create(Submission::class, [
            'channel_name' => $active_channel->name, 'channel_id' => $active_channel->id, 'created_at' => now()->subMonth()
        ]);
        create(Submission::class, [
            'channel_name' => $inactive_channel1->name, 'channel_id' => $inactive_channel1->id, 'created_at' => now()->subMonths(3)
        ]);
        create(Submission::class, [
            'channel_name' => $inactive_channel2->name, 'channel_id' => $inactive_channel2->id, 'created_at' => now()->subMonths(3)
        ]);

        Mail::fake();

        Artisan::call('send-channel-removal-warning-emails');

        Mail::assertQueued(ChannelRemovalWarning::class, 2);
    }
}
