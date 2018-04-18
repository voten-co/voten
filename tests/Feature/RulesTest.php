<?php

namespace Tests\Feature;

use App\Channel;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Rule;
use Illuminate\Foundation\Testing\WithFaker;

class RulesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_moderator_can_create_rules_edit_and_delete_them()
    {
        $channel = create(Channel::class);
        $user = create(User::class);

        $channel->moderators()->attach($user->id, [
            'role' => 'administrator',
        ]);
        $this->signInViaPassport($user);

        // create
        $this->json("POST", "/api/channels/{$channel->id}/rules", [
            'body' => 'some reasonable rule',
        ])->assertStatus(201);
        $this->assertDatabaseHas('rules', [
            'title' => 'some reasonable rule',
            'channel_id' => $channel->id,
        ]);

        // edit
        $this->json('PATCH', "/api/channels/{$channel->id}/rules/1", [
            'body' => 'edited text'
        ]);
        $this->assertDatabaseHas('rules', [
            'title' => 'edited text',
            'channel_id' => $channel->id 
        ]);
        
        // delete 
        $this->json('delete', "/api/channels/{$channel->id}/rules/1");
        $this->assertDatabaseMissing('rules', [
            'title' => 'edited text',
            'channel_id' => $channel->id 
        ]);
    }

    /** @test */
    public function a_user_can_get_rules_of_the_channel()
    {
        $this->signInViaPassport();
        
        $channel = create(Channel::class);
        
        Rule::create([
            'title'      => $this->faker->paragraph(),
            'channel_id' => $channel->id,
        ]); 
        Rule::create([
            'title'      => $this->faker->paragraph(),
            'channel_id' => $channel->id,
        ]); 
        
        $this->json("get", "/api/channels/{$channel->id}/rules")
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function a_guest_can_get_rules_of_the_channel()
    {
        $channel = create(Channel::class);
        
        Rule::create([
            'title'      => $this->faker->paragraph(),
            'channel_id' => $channel->id,
        ]); 
        Rule::create([
            'title'      => $this->faker->paragraph(),
            'channel_id' => $channel->id,
        ]); 
        
        $this->json("get", "/api/guest/channels/{$channel->id}/rules")
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
