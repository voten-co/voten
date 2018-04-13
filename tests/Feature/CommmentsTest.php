<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;
use App\Submission;
use Illuminate\Support\Facades\Event;
use App\Events\CommentWasCreated;
use Illuminate\Support\Facades\Queue;
use App\Comment;
use App\User;

class CommmentsTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function can_post_comment_to_submission()
    {
        Event::fake();

        $this->signInViaPassport();

        $submission = create(Submission::class);

        $this->json('POST', '/api/comments', [
            'submission_id' => $submission->id, 
            'body' => 'some cool comment which BTW supports **markdown**'
        ])->assertStatus(201);

        $this->assertDatabaseHas('comments', [
            'submission_id' => $submission->id, 
            'body' => 'some cool comment which BTW supports **markdown**'
        ]);

        Event::assertDispatched(CommentWasCreated::class, 1);
    }

    /** @test */
    public function a_channel_moderator_can_approve_a_comment()
    {
        $comment_author = create(User::class);
        $comment = create(Comment::class, [
            'user_id' => $comment_author->id,
        ]); 
        
        $this->json("post", "/api/comments/{$comment->id}/approve")->assertStatus(401);

        $this->signInViaPassport($comment_author);

        $this->json("post", "/api/comments/{$comment->id}/approve")->assertStatus(403);

        $channel = Channel::find($comment->channel_id);
        $channel->moderators()->attach($moderator = create(User::class), ['role' => 'moderator']);

        $this->signInViaPassport($moderator);

        $this->json("post", "/api/comments/{$comment->id}/approve")->assertStatus(200);        

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'approved_at' => now(),
        ]);
    }
    
    /** @test */
    public function a_channel_moderator_can_disapprove_a_comment()
    {
        $comment_author = create(User::class);
        $comment = create(Comment::class, [
            'user_id' => $comment_author->id,
        ]); 
        
        $this->json("post", "/api/comments/{$comment->id}/disapprove")->assertStatus(401);

        $this->signInViaPassport($comment_author);

        $this->json("post", "/api/comments/{$comment->id}/disapprove")->assertStatus(403);

        $channel = Channel::find($comment->channel_id);
        $channel->moderators()->attach($moderator = create(User::class), ['role' => 'moderator']);

        $this->signInViaPassport($moderator);

        $this->json("post", "/api/comments/{$comment->id}/disapprove")->assertStatus(200);        

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'approved_at' => null,
            'deleted_at' => now(),
        ]);
    }
}
