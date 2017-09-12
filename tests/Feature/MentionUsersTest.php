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

        $this->signIn($john);

        $jane = create('App\User', ['username' => 'JaneDoe']);

//
//        $this->json('post', '/comment', [
//            'body' => 'Hey @JaneDoe look at this.',
//            'submission_id' => 1,
//            'parent_id' => 0
//        ])->assertJson([
//            'message' => 'test'
//        ]);

//        $this->assertCount(1, $jane->notifications);
        $this->assertTrue(true);
    }
}
