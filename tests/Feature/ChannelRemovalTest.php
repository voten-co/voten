<?php

namespace Tests\Feature;

use App\Channel;
use App\Mail\ChannelRemovalWarning;
use App\Submission;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ChannelRemovalTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->signInViaPassportAsVotenAdministrator();

        $active_channel = create(Channel::class, [
            'created_at' => now()->subMonths(5),
        ]);
        $inactive_channel1 = create(Channel::class, [
            'created_at' => now()->subMonths(5),
        ]);
        $inactive_channel2 = create(Channel::class, [
            'created_at' => now()->subMonths(5),
        ]);

        $moderator = create(User::class, ['username' => 'admin']);

        // create moderators for channels
        $this->json('POST', '/api/moderators', [
            'channel_id' => $inactive_channel1->id,
            'username' => 'admin',
            'role' => 'administrator',
        ])->assertStatus(201);
        $this->json('POST', '/api/moderators', [
            'channel_id' => $inactive_channel2->id,
            'username' => 'admin',
            'role' => 'administrator',
        ])->assertStatus(201);
        $this->json('POST', '/api/moderators', [
            'channel_id' => $active_channel->id,
            'username' => 'admin',
            'role' => 'administrator',
        ])->assertStatus(201);

        create(Submission::class, [
            'channel_name' => $active_channel->name, 'channel_id' => $active_channel->id, 'created_at' => now()->subMonth(),
        ]);
        create(Submission::class, [
            'channel_name' => $inactive_channel1->name, 'channel_id' => $inactive_channel1->id, 'created_at' => now()->subMonths(3),
        ]);
        create(Submission::class, [
            'channel_name' => $inactive_channel2->name, 'channel_id' => $inactive_channel2->id, 'created_at' => now()->subMonths(3),
        ]);
    }

    /** @test */
    public function check_for_inactive_channels_and_send_removal_warning_emails()
    {
        Mail::fake();

        Artisan::call('send-channel-removal-warning-emails');

        Mail::assertQueued(ChannelRemovalWarning::class, 2);
    }

    /** @test */
    public function voten_adminstrator_can_fetch_inactive_channels()
    {
        $this->json('GET', '/api/admin/channels/inactive')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
