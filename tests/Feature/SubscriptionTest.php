<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;
use Illuminate\Support\Facades\Auth;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase; 

    public function setUp(): void
    {
        parent::setUp(); 
        
        $this->signInViaPassport();        
    }

    /** @test */
    public function a_user_can_toggle_subscription_to_a_channel()
    {        
        $channel = create(Channel::class);

        // subscribe 
        $this->json("post", "/api/channels/{$channel->id}/subscribe")
            ->assertStatus(201); 
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => Auth::id(),
            'channel_id' => $channel->id,
        ]);

        // unsubscribe 
        $this->json("post", "/api/channels/{$channel->id}/subscribe")
            ->assertStatus(200); 
        $this->assertDatabaseMissing('subscriptions', [
            'user_id' => Auth::id(),
            'channel_id' => $channel->id,
        ]);
    }

    /** @test */
    public function a_user_can_get_his_subscribed_channels()
    {
        $channel1 = create(Channel::class);
        $channel2 = create(Channel::class);
        $channel3 = create(Channel::class);

        $this->json("post", "/api/channels/{$channel1->id}/subscribe")->assertStatus(201); 
        $this->json("post", "/api/channels/{$channel2->id}/subscribe")->assertStatus(201); 
        $this->json("post", "/api/channels/{$channel3->id}/subscribe")->assertStatus(201); 

        $this->json('get', '/api/channels/subscribed')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }
}
