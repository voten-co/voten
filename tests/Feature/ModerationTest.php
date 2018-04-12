<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;
use Illuminate\Support\Facades\Auth;
use App\User;

class ModerationTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function a_none_channel_administrator_cannot_add_nor_remove_moderators()
    {
        $channel = create(Channel::class);

        $this->signInViaPassport();

        $this->json('post', "/api/channels/{$channel->id}/moderators")->assertStatus(403);
        $this->json('delete', "/api/channels/{$channel->id}/moderators/1")->assertStatus(403);

        $channel->moderators()->attach(Auth::id(), [
            'role' => 'moderator',
        ]); 

        $this->json('post', "/api/channels/{$channel->id}/moderators")->assertStatus(403);
        $this->json('delete', "/api/channels/{$channel->id}/moderators/1")->assertStatus(403);
    }

    /** @test */
    public function a_channel_administrator_can_add_new_moderators()
    {
        $this->signInViaPassport();

        $to_be_moderator = create(User::class);

        $channel = create(Channel::class);
        $channel->moderators()->attach(Auth::id(), [
            'role' => 'administrator',
        ]); 

        // assert role is either 'moderator' or 'administrator'
        $this->json('post', "/api/channels/{$channel->id}/moderators", [
            'user_id' => $to_be_moderator->id,
            'role' => 'chairman',
        ])  
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "role" => [
                        "The selected role is invalid.",
                    ]
                ],
            ]);

        // assert add 
        $this->json('post', "/api/channels/{$channel->id}/moderators", [
            'user_id' => $to_be_moderator->id,
            'role' => 'moderator',
        ])->assertStatus(201);
        $this->assertDatabaseHas('roles', [
            'role' => 'moderator',
            'user_id' => $to_be_moderator->id,
        ]);

        // assert remove 
        $this->json('delete', "/api/channels/{$channel->id}/moderators/1")->assertStatus(200);
        $this->assertDatabaseMissing('roles', [
            'role' => 'administrator',
            'user_id' => $to_be_moderator->id,
        ]);
    }

    /** @test */
    public function a_channel_administrator_can_get_moderators_list()
    {
        $this->signInViaPassport();

        $to_be_moderator1 = create(User::class);
        $to_be_moderator2 = create(User::class);
        $to_be_moderator3 = create(User::class);

        $channel = create(Channel::class);
        $channel->moderators()->attach(Auth::id(), [
            'role' => 'administrator',
        ]); 
        
        // assert add 
        $this->json('post', "/api/channels/{$channel->id}/moderators", [
            'user_id' => $to_be_moderator1->id,
            'role' => 'moderator',
        ])->assertStatus(201);
        $this->json('post', "/api/channels/{$channel->id}/moderators", [
            'user_id' => $to_be_moderator2->id,
            'role' => 'moderator',
        ])->assertStatus(201);
        $this->json('post', "/api/channels/{$channel->id}/moderators", [
            'user_id' => $to_be_moderator3->id,
            'role' => 'moderator',
        ])->assertStatus(201);

        $this->json('get', "/api/channels/{$channel->id}/moderators")
            ->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }
}
