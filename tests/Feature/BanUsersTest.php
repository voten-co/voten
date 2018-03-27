<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;
use App\User;
use App\Submission;
use App\Comment;

class BanUsersTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function a_non_moderator_cannot_ban_users()
    {
        $user = create(User::class);
        $channel = create(Channel::class);

        $this->signInViaPassport();

        $this->json('POST', '/api/channels/users/bans', [
            'username' => $user->username, 
            'channel_id' => $channel->id,
            'duration' => 1,
            'description' => 'he did something very bad :)'
        ])->assertStatus(403);
    }

    /** @test */
    public function a_channel_moderator_can_ban_a_user()
    {
        $auth_user = create(User::class);
        $to_ban_user = create(User::class);
        $channel = create(Channel::class);

        $this->signInViaPassport($auth_user);

        $channel->moderators()->attach($auth_user->id, [
            'role' => 'moderator',
        ]);

        $this->json('POST', '/api/channels/users/bans', [
            'username' => $to_ban_user->username, 
            'channel_id' => $channel->id,
            'duration' => 1,
            'description' => 'he did something very bad :)'
        ])->assertStatus(201);

        $this->assertDatabaseHas('bans', [
            'user_id' => $to_ban_user->id,
            'channel' => $channel->name
        ]);
    }
    
    /** @test */
    public function a_channel_moderator_can_unban_a_user()
    {
        $auth_user = create(User::class);
        $to_ban_user = create(User::class);
        $channel = create(Channel::class);

        $this->signInViaPassport($auth_user);

        $channel->moderators()->attach($auth_user->id, [
            'role' => 'moderator',
        ]);

        // ban 
        $this->json('POST', '/api/channels/users/bans', [
            'username' => $to_ban_user->username, 
            'channel_id' => $channel->id,
            'duration' => 0,
            'description' => 'He did something very bad :).'
        ])->assertStatus(201);

        // unban 
        $this->json('DELETE', '/api/channels/users/bans', [
            'user_id' => $to_ban_user->id, 
            'channel_id' => $channel->id
        ])->assertStatus(200);
    }

    /** @test */
    public function a_voten_administrator_can_ban_users_and_delete_their_posts_and_comments()
    {
        $this->signInViaPassportAsVotenAdministrator();

        $user = create(User::class);
        create(Submission::class, ['user_id' => $user->id]);
        create(Comment::class, ['user_id' => $user->id]);

        $this->json('POST', '/api/admin/users/bans', [
            'username' => $user->username, 
            'duration' => 0,
            'description' => 'He did something very bad :).', 
            'delete_posts' => 1
        ])->assertStatus(201);

        $this->assertDatabaseHas('bans', [
            'user_id' => $user->id,
            'channel' => 'all'
        ]);
        
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'active' => false
        ]);

        $this->assertDatabaseMissing('submissions', [
            'user_id' => $user->id
        ]);
        
        $this->assertDatabaseMissing('comments', [
            'user_id' => $user->id
        ]);
    }
}
