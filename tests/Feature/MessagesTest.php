<?php

namespace Tests\Feature;

use App\Events\MessageCreated;
use App\Events\MessageRead;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Events\ConversationRead;


class MessagesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
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
            'user_id' => $user1->id,
        ])->assertStatus(422);

        // send message
        $this->json("post", "/api/messages", [
            'body' => 'hey, what up?',
            'user_id' => $user2->id,
        ])->assertStatus(201);

        $this->assertDatabaseHas('messages', [
            "user_id" => Auth::id(),
            "data" => "{\"text\":\"hey, what up?\"}",
            "read_at" => null,
        ]);
        $this->assertDatabaseHas('conversations', [
            'user_id' => $user1->id,
            'contact_id' => $user2->id,
            'message_id' => 1,
        ]);
        $this->assertDatabaseHas('conversations', [
            'user_id' => $user2->id,
            'contact_id' => $user1->id,
            'message_id' => 1,
        ]);
        $this->assertEquals(1, $user1->conversations()->count());
        $this->assertEquals(1, $user2->conversations()->count());

        // assert broadcasted
        Event::assertDispatched(MessageCreated::class, 1);

        // assert can get messages:
        $this->json("get", "/api/messages", [
            'contact_id' => Auth::id(),
        ])->assertStatus(422)->assertJson([
            "message" => "The given data was invalid.",
            "errors" => [
                "contact_id" => [
                    "Your own account's id is not acceptable.",
                ],
            ],
        ]);
        $this->json("get", "/api/messages", [
            'contact_id' => $user2->id,
        ])
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // assert the message is marked as read now;
        $this->assertDatabaseHas('messages', [
            "user_id" => Auth::id(),
            "data" => "{\"text\":\"hey, what up?\"}",
            "read_at" => now()->toDateTimeString(),
        ]);
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
            'user_id' => $user1->id,
        ])->assertStatus(201);
        $this->assertDatabaseMissing('conversations', [
            'user_id' => $user1->id,
            'contact_id' => $user2->id,
            'message_id' => 1,
        ]);
        $this->assertDatabaseHas('conversations', [
            'user_id' => $user2->id,
            'contact_id' => $user1->id,
            'message_id' => 1,
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

    /** @test */
    public function a_user_can_delete_a_message_from_her_side_only()
    {
        // user1 blocks user2 and he cannot send messages to user1 anymore.
        $user1 = create(User::class);
        $user2 = create(User::class);

        $this->signInViaPassport($user1);

        // send message
        $this->json("post", "/api/messages", [
            'body' => 'hey, what up?',
            'user_id' => $user2->id,
        ])->assertStatus(201);

        $this->assertDatabaseHas('messages', [
            "user_id" => Auth::id(),
            "data" => "{\"text\":\"hey, what up?\"}",
            "read_at" => null,
        ]);
        $this->assertDatabaseHas('conversations', [
            'user_id' => $user1->id,
            'contact_id' => $user2->id,
            'message_id' => 1,
        ]);
        $this->assertDatabaseHas('conversations', [
            'user_id' => $user2->id,
            'contact_id' => $user1->id,
            'message_id' => 1,
        ]);
        $this->assertEquals(1, $user1->conversations()->count());
        $this->assertEquals(1, $user2->conversations()->count());

        // delete
        $this->json("delete", "/api/messages/1")
            ->assertStatus(200);

        $this->assertEquals(0, $user1->conversations()->count());
        $this->assertEquals(1, $user2->conversations()->count());
    }

    /** @test */
    public function a_user_can_batch_delete_messages()
    {
        $user1 = create(User::class);
        $user2 = create(User::class);

        $this->signInViaPassport($user1);

        // send 2 messages 
        $this->json("post", "/api/messages", [
            'body' => 'some message',
            'user_id' => $user2->id,
        ])->assertStatus(201);
        $this->json("post", "/api/messages", [
            'body' => 'some OTHER message',
            'user_id' => $user2->id,
        ])->assertStatus(201);

        $this->assertEquals(2, $user1->conversations()->count());
        $this->assertEquals(2, $user2->conversations()->count());

        // delete 2 messages 
        $this->json("delete", "/api/messages", [
            'messages' => [1, 2],
        ])
            ->assertStatus(200)
            ->assertJson([
                'message' => '2 messages were deleted.',
            ]);

        $this->assertEquals(0, $user1->conversations()->count());
        $this->assertEquals(2, $user2->conversations()->count());
    }

    /** @test */
    public function a_user_can_mark_a_message_as_read()
    {
        $user1 = create(User::class);
        $user2 = create(User::class);

        $this->signInViaPassport($user1);

        // send message
        $this->json("post", "/api/messages", [
            'body' => 'hey, what up?',
            'user_id' => $user2->id,
        ])->assertStatus(201);

        // assert message exists and that hasn't been read yet:
        $this->assertDatabaseHas('messages', [
            'id' => 1,
            "read_at" => null,
        ]);

        // assert that must have enough permission: 
        $this->json("post", "/api/messages/1/read")->assertStatus(403);        

        // login as the receiver (who has the permission to mark the message as read):
        $this->signInViaPassport($user2);

        // mark the message as read:
        $this->json("post", "/api/messages/1/read")->assertStatus(200);

        // assert that it's been broadcasted:
        Event::assertDispatched(MessageRead::class, 1);

        $this->assertDatabaseHas('messages', [
            'id' => 1,
            "read_at" => now(),
        ]);
    }

    /** @test */
    public function a_user_can_get_conversations()
    {
        $user1 = create(User::class);
        $user2 = create(User::class);
        $user3 = create(User::class);
        $user4 = create(User::class);

        $this->signInViaPassport();

        // send message
        $this->json("post", "/api/messages", [
            'body' => $this->faker->paragraph(),
            'user_id' => $user1->id,
        ])->assertStatus(201);
        $this->json("post", "/api/messages", [
            'body' => $this->faker->paragraph(),
            'user_id' => $user2->id,
        ])->assertStatus(201);
        $this->json("post", "/api/messages", [
            'body' => $this->faker->paragraph(),
            'user_id' => $user3->id,
        ])->assertStatus(201);
        $this->json("post", "/api/messages", [
            'body' => $this->faker->paragraph(),
            'user_id' => $user4->id,
        ])->assertStatus(201);

        $this->assertEquals(4, Auth::user()->conversations()->count());

        $this->json("get", "/api/conversations")
            ->assertStatus(200)
            ->assertJsonCount(4, 'data');
    }

    /** @test */
    public function a_user_can_broadcast_a_conversation_being_read()
    {
        $this->signInViaPassport();

        $contact = create(User::class);

        $this->json("post", "/api/messages", [
            'body' => $this->faker->paragraph(),
            'user_id' => $contact->id,
        ])->assertStatus(201);
        $this->json("post", "/api/messages", [
            'body' => $this->faker->paragraph(),
            'user_id' => $contact->id,
        ])->assertStatus(201);

        $this->assertEquals(2, Auth::user()->conversations()->count());

        $this->json("post", "/api/conversations/{$contact->id}/read")->assertStatus(200);

        Event::assertDispatched(ConversationRead::class, 1);
    }

    /** @test */
    public function a_user_can_delete_a_conversation()
    {
        $contact = create(User::class);

        $this->signInViaPassport();

        // send message
        $this->json("post", "/api/messages", [
            'body' => $this->faker->paragraph(),
            'user_id' => $contact->id,
        ])->assertStatus(201);

        $this->assertEquals(1, Auth::user()->conversations()->count());

        $this->json("delete", "/api/conversations/{$contact->id}")->assertStatus(200);

        $this->assertEquals(0, Auth::user()->conversations()->count());
    }
}
