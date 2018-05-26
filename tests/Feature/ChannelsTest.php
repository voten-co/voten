<?php

namespace Tests\Feature;

use App\Channel;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ChannelsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_newbie_cannot_create_a_channel()
    {
        $this->signInViaPassport();

        $this->json('post', '/api/channels', [
            'name' => 'myNewChannel',
            'description' => 'describing my new cool channel',
        ])->assertStatus(403);
    }

    /** @test */
    public function a_user_with_good_karma_can_create_a_channel_and_then_edit_it()
    {
        $user = create(User::class, ['submission_xp' => 10]);

        $this->signInViaPassport($user);

        // assert validation
        $this->json('post', '/api/channels', [
            'name' => 'new_channel',
            'description' => '12',
        ])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "name" => [
                        "The name may only contain letters and numbers.",
                    ],
                    "description" => [
                        "The description must be at least 10 characters.",
                    ],
                ],
            ]);

        // assert creation
        $this->json('post', '/api/channels', [
            'name' => 'myNewChannel',
            'description' => 'describing my new cool channel',
            'nsfw' => 1,
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'myNewChannel',
                    'description' => 'describing my new cool channel',
                    'nsfw' => true,
                    'cover_color' => 'Blue',
                ],
            ]);

        $this->assertDatabaseHas('channels', [
            'name' => 'myNewChannel',
            'description' => 'describing my new cool channel',
            'nsfw' => true,
        ]);

        // assert that he's the administrator of the created channel
        $this->assertTrue(Auth::user()->moderatingIds()->contains(1));

        // assert edit
        $this->json("patch", "/api/channels/1", [
            'description' => 'new description',
            'cover_color' => 'Dark',
            'nsfw' => 0,
        ])  
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Channel has been updated successfully.'
            ]);

        $this->assertDatabaseHas('channels', [
            'name' => 'myNewChannel',
            'description' => 'new description',
            'color' => 'Dark',
            'nsfw' => false,
        ]);
    }

    /** @test */
    public function non_administrator_cannot_edit_channel()
    {
        $channel_creator = create(User::class, ['submission_xp' => 10]);

        $this->signInViaPassport($channel_creator);

        // assert creation
        $this->json('post', '/api/channels', [
            'name' => 'myNewChannel',
            'description' => 'describing my new cool channel',
            'nsfw' => 1,
        ])->assertStatus(201);

        // login as a second user
        $this->signInViaPassport(create(User::class));

        $this->json("patch", "/api/channels/1", [
            'description' => 'new description',
            'cover_color' => 'Dark',
            'nsfw' => 0,
        ])->assertStatus(403);
    }

    /** @test */
    public function a_user_can_get_a_channel()
    {
        $channel = create(Channel::class);

        $this->signInViaPassport();

        $this->json("get", "/api/channels/{$channel->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $channel->id,
                    'name' => $channel->name,
                    'avatar' => '/imgs/channel-avatar.png',
                    'cover_color' => 'Blue',
                ],
            ]);
    }
}
