<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UsernameMentioned;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp(); 
        
        Notification::fake();        
    }

    /** @test */
    public function mentioned_users_in_a_comment_are_notified()
    {        
        $john = create('App\User', ['username' => 'JohnDoe']);

        $this->signInViaPassport($john);

        $jane = create('App\User', ['username' => 'JaneDoe']);

        $submission = create('App\Submission'); 

        $this->json('POST', "/api/submissions/{$submission->id}/comments", [
            'body' => 'Hello @JaneDoe Please take a look at this.' 
        ])->assertStatus(201);

        Notification::assertSentTo($jane, UsernameMentioned::class);
    }
    
    /** @test */
    public function no_notification_when_mentioning_self()
    {
        $john = create('App\User', ['username' => 'JohnDoe']);

        $this->signInViaPassport($john);

        $submission = create('App\Submission'); 

        $this->json('POST', "/api/submissions/{$submission->id}/comments", [
            'body' => 'This is @JohnDoe. Nice to meet you'
        ])->assertStatus(201);

        Notification::assertNotSentTo($john, UsernameMentioned::class);
    }
}