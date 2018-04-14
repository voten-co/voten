<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use App\Events\MessageCreated;

class MessagesTest extends TestCase
{
    use RefreshDatabase; 

    public function setUp()
    {
        parent::setUp(); 
        
        Event::fake();         
    }

    /** @test */
    public function a_user_can_start_a_conversation_with_another_user()
    {
        // let's send a message from user1 to user2. 
        $user1 = create(User::class);
        $user2 = create(User::class);
        
        $this->signInViaPassport($user1);

        // assert cannot send a message to self 
        $this->json("post", "/api/messages", [
            'body' => 'hey, what up?',
            'user_id' => $user1->id
        ])->assertStatus(422);

        // send message
        $this->json("post", "/api/messages", [
            'body' => 'hey, what up?',
            'user_id' => $user2->id
        ])->assertStatus(201);

        $this->assertDatabaseHas('messages', [
            "user_id" => Auth::id(),
            "data" => "{\"text\":\"hey, what up?\"}",
            "read_at" => null, 
        ]);
        $this->assertDatabaseHas('conversations', [
            'user_id' => $user1->id,
            'contact_id' => $user2->id,
            'message_id' => 1
        ]);
        $this->assertDatabaseHas('conversations', [
            'user_id' => $user2->id,
            'contact_id' => $user1->id,
            'message_id' => 1
        ]);
        $this->assertEquals(1, $user1->conversations()->count());
        $this->assertEquals(1, $user2->conversations()->count());
        
        // assert broadcasted 
        Event::assertDispatched(MessageCreated::class, 1);
    }

    /** @test */
    public function a_user_can_block_another_user_and_not_get_messages_ever_since()
    {
        // user1 blocks user2 and he cannot send messages to user1 anymore. 
        $user1 = create(User::class);
        $user2 = create(User::class);

        $this->signInViaPassport($user1);

        // assert that cannot block self 
        $this->json("post", "/api/users/{$user1->id}/block")->assertStatus(400);
        
        $this->json("post", "/api/users/{$user2->id}/block")->assertStatus(201);
        $this->assertDatabaseHas('hidden_users', [
            'user_id' => Auth::id(),
            'blocked_user_id' => $user2->id,
        ]);
        $this->assertEquals(1, Auth::user()->hiddenUsers()->count());
        
        // login as the blocked user:
        $this->signInViaPassport($user2);

        // assert the blocked user can send messages to blocker, but the blocker won't receive it. (shadow-ban kind)
        $this->json("post", "/api/messages", [
            'body' => 'hey, what up?',
            'user_id' => $user1->id
        ])->assertStatus(201);
        $this->assertDatabaseMissing('conversations', [
            'user_id' => $user1->id,
            'contact_id' => $user2->id,
            'message_id' => 1
        ]);
        $this->assertDatabaseHas('conversations', [
            'user_id' => $user2->id,
            'contact_id' => $user1->id,
            'message_id' => 1
        ]);
        $this->assertEquals(0, $user1->conversations()->count());
        $this->assertEquals(1, $user2->conversations()->count());

        // assert not broadcasted:
        Event::assertNotDispatched(MessageCreated::class);

        // unblock: 
        $this->signInViaPassport($user1);
        $this->json("post", "/api/users/{$user2->id}/block")->assertStatus(200);
        $this->assertDatabaseMissing('hidden_users', [
            'user_id' => Auth::id(),
            'blocked_user_id' => $user2->id,
        ]);
        $this->assertEquals(0, Auth::user()->hiddenUsers()->count());
    }
}
