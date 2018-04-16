<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;
use App\Suggested;

class SuggestedChannelsTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function an_admin_can_create_a_suggested_channel()
    {
        $channel = create(Channel::class);

        $this->signInViaPassport();

        $this->json("post", "/api/admin/suggested-channels", [
            'channel_name' => $channel->name,
            'z_index' => 5,
        ])->assertStatus(403);

        $this->signInViaPassportAsVotenAdministrator();
        
        $this->json("post", "/api/admin/suggested-channels", [
            'channel_name' => $channel->name,
            'z_index' => 5,
            'group' => 'technology'
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'z_index' => 5,
                    'group' => 'technology'
                ]
            ]);
    }
    
    /** @test */
    public function an_admin_can_get_suggested_channels()
    {
        $channel1 = create(Channel::class);
        $channel2 = create(Channel::class);
        $channel3 = create(Channel::class);

        $this->signInViaPassportAsVotenAdministrator();
        
        $this->json("post", "/api/admin/suggested-channels", [
            'channel_name' => $channel1->name,
            'z_index' => 5,
        ])->assertStatus(201);
        $this->json("post", "/api/admin/suggested-channels", [
            'channel_name' => $channel2->name,
            'z_index' => 5,
        ])->assertStatus(201);
        $this->json("post", "/api/admin/suggested-channels", [
            'channel_name' => $channel3->name,
            'z_index' => 5,
        ])->assertStatus(201);

        $this->json("get", "/api/admin/suggested-channels")
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
            
        // assert a normal user cannot get:
        $this->signInViaPassport();
        $this->json("get", "/api/admin/suggested-channels")->assertStatus(403); 
    }

    /** @test */
    public function an_admin_can_delete_a_channel_from_the_suggested_list()
    {
        $channel = create(Channel::class);
        $channel2 = create(Channel::class);
        $this->signInViaPassportAsVotenAdministrator();

        $this->json("post", "/api/admin/suggested-channels", [
            'channel_name' => $channel->name,
            'z_index' => 5,
            'group' => 'technology'
        ])->assertStatus(201);
        $this->json("post", "/api/admin/suggested-channels", [
            'channel_name' => $channel2->name,
            'z_index' => 5,
            'group' => 'technology'
        ])->assertStatus(201);

        $this->assertEquals(2, Suggested::all()->count());

        $this->json("delete", "/api/admin/suggested-channels/1")->assertStatus(200);

        $this->assertEquals(1, Suggested::all()->count());

        $this->signInViaPassport();
        $this->json("delete", "/api/admin/suggested-channels/2")->assertStatus(403);
    }
    
    /** @test */
    public function a_user_can_get_a_suggested_channel()
    {
        // create a suggested channel 
        $channel = create(Channel::class);
        $this->signInViaPassportAsVotenAdministrator();
        $this->json("post", "/api/admin/suggested-channels", [
            'channel_name' => $channel->name,
            'z_index' => 5,
            'group' => 'technology'
        ])->assertStatus(201);

        // now a user can get it: 
        $this->signInViaPassport();

        $this->json("get", "/api/suggested-channel")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'name' => $channel->name, 
                    'id' => $channel->id, 
                ]
            ]);
    }
}
