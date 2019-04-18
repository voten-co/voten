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
use Illuminate\Support\Facades\Auth;

class ChannelRemovalTest extends TestCase
{
    use RefreshDatabase;
    public $mockConsoleOutput = false;

    public function setUp(): void
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
        $inactive_channel1->moderators()->attach(Auth::id(), ['role' => 'administrator']);
        $inactive_channel2->moderators()->attach(Auth::id(), ['role' => 'administrator']);
        $active_channel->moderators()->attach(Auth::id(), ['role' => 'administrator']); 

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
    public function voten_administrator_can_fetch_inactive_channels()
    {
        $this->json('GET', '/api/admin/channels/inactive')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function a_voten_administrator_can_delete_a_channel()
    {
        $channel = create(Channel::class);
        $submission = create(Submission::class, ['channel_name' => $channel->name, 'channel_id' => $channel->id]);
        
        $this->assertDatabaseHas('channels', [
            'id' => $channel->id,
            'name' => $channel->name,
        ]);
        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'slug' => $submission->slug,
        ]);

        // assert current password is sent correctly 
        $this->json("post", "/api/channels/{$channel->id}/destroy", [
            'password' => 'not_correct_password'
        ])  
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "password" => [
                        "Password is incorrect.",
                    ]
                ],
            ]);

        // assert destroy 
        $this->json("post", "/api/channels/{$channel->id}/destroy", [
            'password' => 'password'
        ])->assertStatus(200);

        $this->assertDatabaseMissing('channels', [
            'id' => $channel->id,
            'name' => $channel->name,
        ]);
        $this->assertDatabaseMissing('submissions', [
            'id' => $submission->id,
            'slug' => $submission->slug,
        ]);
    }
}
