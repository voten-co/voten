<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Submission;
use Illuminate\Support\Facades\Auth;
use App\Comment;
use App\Channel;
use App\User;

class BookmarksTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signInViaPassport();
    }

    /** @test */
    public function a_user_can_bookmark_a_submission()
    {
        $submission = create(Submission::class);
        
        // bookmark  
        $this->json('post', "/api/submissions/{$submission->id}/bookmark")
            ->assertStatus(201);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => Auth::id(),
            'bookmarkable_id' => $submission->id,
            'bookmarkable_type' => Submission::class,
        ]);

        // unbookmark 
        $this->json('post', "/api/submissions/{$submission->id}/bookmark")
            ->assertStatus(200);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => Auth::id(),
            'bookmarkable_id' => $submission->id,
            'bookmarkable_type' => Submission::class,
        ]);
    }
    
    /** @test */
    public function a_user_can_bookmark_a_comment()
    {
        $comment = create(Comment::class);
        
        // bookmark  
        $this->json('post', "/api/comments/{$comment->id}/bookmark")
            ->assertStatus(201);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => Auth::id(),
            'bookmarkable_id' => $comment->id,
            'bookmarkable_type' => Comment::class,
        ]);

        // unbookmark 
        $this->json('post', "/api/comments/{$comment->id}/bookmark")
            ->assertStatus(200);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => Auth::id(),
            'bookmarkable_id' => $comment->id,
            'bookmarkable_type' => Comment::class,
        ]);
    }
    
    /** @test */
    public function a_user_can_bookmark_a_channel()
    {
        $channel = create(Channel::class);
        
        // bookmark  
        $this->json('post', "/api/channels/{$channel->id}/bookmark")
            ->assertStatus(201);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => Auth::id(),
            'bookmarkable_id' => $channel->id,
            'bookmarkable_type' => Channel::class,
        ]);

        // unbookmark 
        $this->json('post', "/api/channels/{$channel->id}/bookmark")
            ->assertStatus(200);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => Auth::id(),
            'bookmarkable_id' => $channel->id,
            'bookmarkable_type' => Channel::class,
        ]);
    }
    
    /** @test */
    public function a_user_can_bookmark_a_user()
    {
        $user = create(User::class);
        
        // bookmark  
        $this->json('post', "/api/users/{$user->id}/bookmark")
            ->assertStatus(201);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => Auth::id(),
            'bookmarkable_id' => $user->id,
            'bookmarkable_type' => User::class,
        ]);

        // unbookmark 
        $this->json('post', "/api/users/{$user->id}/bookmark")
            ->assertStatus(200);

        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => Auth::id(),
            'bookmarkable_id' => $user->id,
            'bookmarkable_type' => User::class,
        ]);
    }

    /** @test */
    public function a_user_can_get_his_bookmarked_submissions()
    {
        $submission1 = create(Submission::class);
        $submission2 = create(Submission::class);
        $submission3 = create(Submission::class);

        $this->json('post', "/api/submissions/{$submission1->id}/bookmark")->assertStatus(201);
        $this->json('post', "/api/submissions/{$submission2->id}/bookmark")->assertStatus(201);
        $this->json('post', "/api/submissions/{$submission3->id}/bookmark")->assertStatus(201);

        $this->json('get', '/api/submissions/bookmarked')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
    
    /** @test */
    public function a_user_can_get_his_bookmarked_comments()
    {
        $comment1 = create(Comment::class);
        $comment2 = create(Comment::class);
        $comment3 = create(Comment::class);

        $this->json('post', "/api/comments/{$comment1->id}/bookmark")->assertStatus(201);
        $this->json('post', "/api/comments/{$comment2->id}/bookmark")->assertStatus(201);
        $this->json('post', "/api/comments/{$comment3->id}/bookmark")->assertStatus(201);

        $this->json('get', '/api/comments/bookmarked')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
    
    /** @test */
    public function a_user_can_get_his_bookmarked_users()
    {
        $user1 = create(User::class);
        $user2 = create(User::class);
        $user3 = create(User::class);

        $this->json('post', "/api/users/{$user1->id}/bookmark")->assertStatus(201);
        $this->json('post', "/api/users/{$user2->id}/bookmark")->assertStatus(201);
        $this->json('post', "/api/users/{$user3->id}/bookmark")->assertStatus(201);

        $this->json('get', '/api/users/bookmarked')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
    
    /** @test */
    public function a_user_can_get_his_bookmarked_channels()
    {
        $channel1 = create(Channel::class);
        $channel2 = create(Channel::class);
        $channel3 = create(Channel::class);

        $this->json('post', "/api/channels/{$channel1->id}/bookmark")->assertStatus(201);
        $this->json('post', "/api/channels/{$channel2->id}/bookmark")->assertStatus(201);
        $this->json('post', "/api/channels/{$channel3->id}/bookmark")->assertStatus(201);

        $this->json('get', '/api/channels/bookmarked')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
