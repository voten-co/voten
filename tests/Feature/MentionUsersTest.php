<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mentioned_users_in_a_comment_are_notified()
    {
        $john = create('App\User', ['username' => 'JohnDoe']);

        $this->signInViaPassport($john);

        $jane = create('App\User', ['username' => 'JaneDoe']);

        $submission = create('App\Submission'); 

        $this->json('POST', '/api/comments', [
            'body' => 'Hello @JaneDoe Please take a look at this.', 
            'submission_id' => $submission->id 
        ])->assertStatus(201);

        $this->assertCount(1, $jane->notifications);
    }
}