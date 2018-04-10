<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

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
    public function a_user_with_good_karma_can_create_a_channel()
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
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'myNewChannel',
                    'description' => 'describing my new cool channel',
                    'subscribers_count' => 0,
                    'submissions_count' => 0,
                    'comments_count' => 0,
                    'cover_color' => 'Blue'
                ]
            ]);

        $this->assertDatabaseHas('channels', [
            'name' => 'myNewChannel', 
            'description' => 'describing my new cool channel'
        ]);

        // assert that he's the administrator of the created channel 
        $this->assertTrue(Auth::user()->moderatingIds()->contains(1));
    }
}
