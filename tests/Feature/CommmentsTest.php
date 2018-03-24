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
}
