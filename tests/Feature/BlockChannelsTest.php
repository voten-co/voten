<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;
use Illuminate\Support\Facades\Auth;

class BlockChannelsTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function a_user_can_block_a_channel_and_then_unblock_it()
    {
        $this->signInViaPassport();

        $channel1 = create(Channel::class);

        // block 
        $this->json('post', "/api/channels/{$channel1->id}/block")->assertStatus(201);
        $this->assertDatabaseHas('blocked_channels', [
            'channel_id' => $channel1->id,
            'user_id' => Auth::id(),
        ]);
        
        // unblock 
        $this->json('post', "/api/channels/{$channel1->id}/block")->assertStatus(200);
        $this->assertDatabaseMissing('blocked_channels', [
            'channel_id' => $channel1->id,
            'user_id' => Auth::id(),
        ]);
    }
}
