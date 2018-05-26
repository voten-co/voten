<?php

namespace Tests\Feature;

use App\Channel;
use App\Comment;
use App\Events\CommentWasCreated;
use App\Submission;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CommmentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_post_comment_to_submission()
    {
        Event::fake();

        $this->signInViaPassport();

        $submission = create(Submission::class);

        $this->json("post", "/api/submissions/{$submission->id}/comments", [
            'submission_id' => $submission->id,
            'body' => 'some cool comment which BTW supports **markdown**',
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'submission_id' => $submission->id,
                    'content' => [
                        'text' => 'some cool comment which BTW supports **markdown**'
                    ],
                ]
            ]);

        $this->assertDatabaseHas('comments', [
            'submission_id' => $submission->id,
            'body' => 'some cool comment which BTW supports **markdown**',
        ]);

        Event::assertDispatched(CommentWasCreated::class, 1);
    }

    /** @test */
    public function can_get_a_posted_comment()
    {
        $this->signInViaPassport();

        $comment = create(Comment::class);

        $this->json("get", "/api/comments/{$comment->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $comment->id,
                    'user_id' => $comment->user_id,
                    'submission_id' => $comment->submission_id,
                    'author' => [
                        'id' => $comment->user_id
                    ],
                ]
            ]);
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

    /** @test */
    public function a_user_can_get_comments_of_a_submissions()
    {
        $submission = create(Submission::class);

        factory(Comment::class, 10)->create(['submission_id' => $submission->id]);

        $this->signInViaPassport();    

        $this->json("get", "/api/submissions/{$submission->id}/comments")
            ->assertStatus(200)
            ->assertJsonCount(10, 'data');
        
        // get it as a guest: 
        $this->json("get", "/api/guest/submissions/{$submission->id}/comments")
            ->assertStatus(200)
            ->assertJsonCount(10, 'data');
    }
}
