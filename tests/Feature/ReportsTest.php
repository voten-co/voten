<?php

namespace Tests\Feature;

use App\Channel;
use App\Events\ReportWasCreated;
use App\Submission;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Comment;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_report_a_submission()
    {
        Event::fake();

        $this->signInViaPassport();

        $moderator1 = create(User::class);
        $moderator2 = create(User::class);
        $channel = create(Channel::class);
        $submission = create(Submission::class, ['channel_id' => $channel->id]);

        $channel->moderators()->attach([$moderator1->id, $moderator2->id], [
            'role' => 'moderator',
        ]);

        $this->json("post", "/api/submissions/{$submission->id}/report", [
            "subject" => "It's harassing me or someone that I know",
        ])
            ->assertStatus(200)
            ->assertSeeText('Report submitted successfully.');

        $this->assertDatabaseHas('reports', [
            'channel_id' => $submission->channel_id,
            'reportable_id' => 1,
            'reportable_type' => Submission::class,
        ]);

        Event::assertDispatched(ReportWasCreated::class, 1);
    }
    
    /** @test */
    public function a_user_can_report_a_comment()
    {
        Event::fake();

        $this->signInViaPassport();

        $moderator1 = create(User::class);
        $moderator2 = create(User::class);
        $channel = create(Channel::class);
        $comment = create(Comment::class, ['channel_id' => $channel->id]);

        $channel->moderators()->attach([$moderator1->id, $moderator2->id], [
            'role' => 'moderator',
        ]);

        $this->json("post", "/api/comments/{$comment->id}/report", [
            "subject" => "It's harassing me or someone that I know",
        ])->assertStatus(200);

        $this->assertDatabaseHas('reports', [
            'channel_id' => $comment->channel_id,
            'reportable_id' => 1,
            'reportable_type' => Comment::class,
        ]);

        Event::assertDispatched(ReportWasCreated::class, 1);
    }

    /** @test */
    public function a_moderator_can_get_reported_comments()
    {
        $moderator = create(User::class);
        $channel = create(Channel::class);
        $channel->moderators()->attach($moderator, ['role' => 'moderator']);
        
        $this->signInViaPassport();

        $this->json("get", "/api/channels/{$channel->id}/comments/reported", ['type' => 'unsolved'])->assertStatus(403);

        $this->signInViaPassport($moderator);

        $this->json("get", "/api/channels/{$channel->id}/comments/reported", ['type' => 'unsolved'])
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
    
    /** @test */
    public function a_moderator_can_get_reported_submissions()
    {
        $moderator = create(User::class);
        $channel = create(Channel::class);
        $channel->moderators()->attach($moderator, ['role' => 'moderator']);
        
        $this->signInViaPassport();

        $this->json("get", "/api/channels/{$channel->id}/submissions/reported", ['type' => 'unsolved'])->assertStatus(403);

        $this->signInViaPassport($moderator);

        $this->json("get", "/api/channels/{$channel->id}/submissions/reported", ['type' => 'unsolved'])
            ->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
