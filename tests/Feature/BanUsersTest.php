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

        $this->json('POST', "/api/channels/{$channel->id}/banned-users", [
            'user_id' => $user->id,
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

        $this->json('POST', "/api/channels/{$channel->id}/banned-users", [
            'user_id' => $to_ban_user->id, 
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
        $this->json('POST', "/api/channels/{$channel->id}/banned-users", [
            'user_id' => $to_ban_user->id,
            'duration' => 0,
            'description' => 'He did something very bad :).'
        ])->assertStatus(201);

        // unban 
        $this->json('DELETE', "/api/channels/{$channel->id}/banned-users/{$to_ban_user->id}")->assertStatus(200);
        // user must be already banned to begin with:
        $this->json('DELETE', "/api/channels/{$channel->id}/banned-users/100")->assertStatus(404);
    }

    /** @test */
    public function a_voten_administrator_can_ban_users_and_delete_their_posts_and_comments_and_unban_them()
    {
        $user = create(User::class);
        create(Submission::class, ['user_id' => $user->id]);
        create(Comment::class, ['user_id' => $user->id]);

        $this->signInViaPassport();
        $this->json('POST', '/api/admin/banned-users', [
            'user_id' => $user->id, 
            'duration' => 0,
            'description' => 'He did something very bad :).', 
            'delete_posts' => 1
        ])->assertStatus(403);

        $this->signInViaPassportAsVotenAdministrator();

        $this->json('POST', '/api/admin/banned-users', [
            'user_id' => $user->id, 
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

        $this->json("delete", "/api/admin/banned-users/{$user->id}")->assertStatus(200);
        $this->assertDatabaseMissing('bans', [
            'user_id' => $user->id,
            'channel' => 'all'
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'active' => true
        ]);
    }
}
