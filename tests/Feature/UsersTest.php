<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Submission;
use App\Comment;

class UsersTest extends TestCase
{
    use RefreshDatabase; 
    
    /** @test */
    public function a_user_can_get_a_user()
    {
        $this->signInViaPassport();

        $user = create(User::class);

        $res = $this->json("get", "/api/users/{$user->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id, 
                    'username' => $user->username, 
                ]
            ]);
    }

    /** @test */
    public function a_user_can_get_user_submissions()
    {
        $this->signInViaPassport();

        $user = create(User::class);
        create(Submission::class, ['user_id' => $user->id]);
        create(Submission::class, ['user_id' => $user->id]);
        create(Submission::class, ['user_id' => $user->id]);

        $this->json("get", "/api/users/{$user->id}/submissions")
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function a_user_can_get_user_comments()
    {
        $this->signInViaPassport();

        $user = create(User::class);
        create(Comment::class, ['user_id' => $user->id]);
        create(Comment::class, ['user_id' => $user->id]);
        create(Comment::class, ['user_id' => $user->id]);

        $this->json("get", "/api/users/{$user->id}/comments")
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function a_user_can_get_his_liked_submissions()
    {
        $this->signInViaPassport();

        // create three submissions: 
        create(Submission::class);
        create(Submission::class);
        create(Submission::class);

        // like two submissions: 
        $this->json("post", "/api/submissions/1/like")->assertStatus(201);
        $this->json("post", "/api/submissions/2/like")->assertStatus(201);

        // expect two: 
        $this->json("get", "/api/auth/submissions/liked")
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    // Temporary for the web client only: 
    // Temporary for the web client only: 
    // Temporary for the web client only: 
    /** @test */
    public function a_user_can_get_user_submissions_with_username()
    {
        $this->signInViaPassport();

        $user = create(User::class);
        create(Submission::class, ['user_id' => $user->id]);
        create(Submission::class, ['user_id' => $user->id]);
        create(Submission::class, ['user_id' => $user->id]);

        $this->json("get", "/api/user-submissions", [
            'username' => $user->username,
        ])
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */    
    public function a_user_can_get_user_comments_with_username()
    {
        $this->signInViaPassport();

        $user = create(User::class);
        create(Comment::class, ['user_id' => $user->id]);
        create(Comment::class, ['user_id' => $user->id]);
        create(Comment::class, ['user_id' => $user->id]);

        $this->json("get", "/api/user-comments", [
            'username' => $user->username,
        ])
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
