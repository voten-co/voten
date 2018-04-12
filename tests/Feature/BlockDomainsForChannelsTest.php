<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;

class BlockDomainsForChannelsTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function a_channel_moderator_can_block_a_domain()
    {
        $channel = create(Channel::class);

        $this->signInViaPassport();

        $this->assertTrue(true);

        // assert a non-moderator gets 403 
        // $this->json('post', '/api/channels/')
    }
}
