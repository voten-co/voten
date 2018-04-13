<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;
use Illuminate\Support\Facades\Auth;

class BlockDomainsForChannelsTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function a_channel_moderator_can_block_and_unblock_a_domain()
    {
        $channel = create(Channel::class);

        $this->signInViaPassport();

        // assert a non-moderator gets 403 
        $this->json("post", "/api/channels/{$channel->id}/blocked-domains")->assertStatus(403);
        
        $channel->moderators()->attach(Auth::id(), ['role' => 'moderator']); 

        $this->json("post", "/api/channels/{$channel->id}/blocked-domains", [
            'domain' => 'https://laravel.com/docs/5.6/validation/',
        ])->assertStatus(201);
        $this->assertDatabaseHas('blocked_domains', [
            'domain' => 'laravel.com',
            'channel' => $channel->name,
        ]);

        // assert that the link cannot be posted 
        $this->json("post", "/api/submissions", [
            'type' => 'link',
            'channel_name' => $channel->name,
            'title' => 'an awesome website',            
            'url' => 'https://laravel.com/docs/5.6/testing',
        ])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "url" => [
                        "The submitted website is in the channel's blacklist. Please find another source.",
                    ],
                ],
            ]);
        
        // unblock 
        $this->json("delete", "/api/channels/{$channel->id}/blocked-domains/laravel.com")->assertStatus(200);
        $this->assertDatabaseMissing('blocked_domains', [
            'domain' => 'laravel.com',
            'channel' => $channel->name,
        ]);
    }

    /** @test */
    public function a_channel_moderator_can_get_blocked_domains()
    {
        $channel = create(Channel::class);
        
        $this->signInViaPassport();

        $this->json("get", "/api/channels/{$channel->id}/blocked-domains")->assertStatus(403);

        $channel->moderators()->attach(Auth::id(), ['role' => 'moderator']);         
        
        $this->json("get", "/api/channels/{$channel->id}/blocked-domains")->assertStatus(200);
    }

    /** @test */
    public function a_voten_administrator_can_block_a_domain_for_all_channels_get_the_list_and_unblock()
    {
        $this->signInViaPassport();
        $this->json("post", "/api/admin/blocked-domains", [
            'domain' => 'http://laravel.com',
        ])->assertStatus(403);

        $this->signInViaPassportAsVotenAdministrator();
        
        // assert domain is a valid URL 
        $this->json("post", "/api/admin/blocked-domains", [
            'domain' => 'laravel.com',
        ])->assertStatus(422);

        $this->json("post", "/api/admin/blocked-domains", [
            'domain' => 'https://laravel.com/',
        ])->assertStatus(201);
        $this->assertDatabaseHas('blocked_domains', [
            'domain' => 'laravel.com',
            'channel' => 'all',
        ]);

        // assert that the link cannot be posted 
        $channel = create(Channel::class);
        $this->json("post", "/api/submissions", [
            'type' => 'link',
            'channel_name' => $channel->name,
            'title' => 'an awesome website',            
            'url' => 'https://laravel.com/docs/5.6/testing',
        ])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "url" => [
                        "The submitted website is in the channel's blacklist. Please find another source.",
                    ],
                ],
            ]);
        
        // get the list of blocked channels 
        $this->json("get", "/api/admin/blocked-domains")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
        
        $this->json("delete", "/api/admin/blocked-domains/a-non-blocked-domain.com")->assertStatus(404);
        $this->json("delete", "/api/admin/blocked-domains/laravel.com")->assertStatus(200);
        $this->assertDatabaseMissing('blocked_domains', [
            'domain' => 'laravel.com',
            'channel' => 'all',
        ]);
    }
}
