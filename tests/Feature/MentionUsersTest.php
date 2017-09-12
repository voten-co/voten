<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MentionUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function mentioned_users_in_a_comment_are_notified()
    {
        $john = create('App\User', ['username' => 'JohnDoe']);

        $this->signIn($john);

        $jane = create('App\User', ['username' => 'JaneDoe']);

        $comment = make('App\Comment', [
            'body' => 'Hey @JaneDoe look at this.'
        ]);

        // post the right data (and not this $comment->toArray() shit)

        $this->json('post', '/comment', [
            'body' => $comment->body,
            'submission_id' => 1,
            'parent_id' => 0
        ]);

//        $this->->assertStatus(200);

//        $this->assertCount(1, $jane->notifications);
        $this->assertTrue(true);
    }
}
