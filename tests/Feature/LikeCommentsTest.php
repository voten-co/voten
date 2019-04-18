<?php

namespace Tests\Feature;

use App\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\User;

class LikeCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp(); 

        $this->signInViaPassport();
    }

    /** @test */
    public function a_user_can_like_a_comment()
    {
        $comment = create(Comment::class);

        // like 
        $this->json('post', "/api/comments/{$comment->id}/like")
            ->assertStatus(201);
        $this->assertDatabaseHas('comment_likes', [
            'user_id' => Auth::id(), 
            'comment_id' => $comment->id,
        ]);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id, 
            'likes' => 2,
            'rate' => rate(2, $comment->created_at),
        ]);

        // undo
        $this->json('post', "/api/comments/{$comment->id}/like")
            ->assertStatus(200);
        $this->assertDatabaseMissing('comment_likes', [
            'user_id' => Auth::id(), 
            'comment_id' => $comment->id,
            'rate' => rate(1, $comment->created_at)
        ]);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id, 
            'likes' => 1,
        ]);
    }

    /** 
     * The test has the same IP address, no need to fake it. 
     * 
     * @test
     */
    public function same_ip_address_can_like_more_than_once_but_the_rate_must_stay_the_same()
    {
        $comment = create(Comment::class);

        // first like 
        $this->json('post', "/api/comments/{$comment->id}/like")
            ->assertStatus(201);
        $this->assertDatabaseHas('comment_likes', [
            'user_id' => Auth::id(), 
            'comment_id' => $comment->id,
        ]);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id, 
            'likes' => 2,
            'rate' => rate(2, $comment->created_at),
        ]);

        // login as a second user 
        $this->signInViaPassport(create(User::class));
        
        // like the same comment with ti 
        $this->json('post', "/api/comments/{$comment->id}/like")
            ->assertStatus(201);
        
        // assert that it's been added to users likes 
        $this->assertDatabaseHas('comment_likes', [
            'user_id' => Auth::id(), 
            'comment_id' => $comment->id,
        ]);
        
        // but hasn't affected comment's rate 
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id, 
            'likes' => 2,
            'rate' => rate(2, $comment->created_at),
        ]);
    }
}
