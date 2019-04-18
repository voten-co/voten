<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificiationsTest extends TestCase
{
    use RefreshDatabase; 

    public function setUp(): void
    {
        parent::setUp(); 
        
        $this->signInViaPassport();        
    }

    /** @test */
    public function users_get_seen_notifications()
    {
        $this->json('get', '/api/notifications', [
            'filter' => 'seen'
        ])->assertStatus(200);
    }
    
    /** @test */
    public function users_get_all_notifications()
    {
        $this->json('get', '/api/notifications')->assertStatus(200);
    }
    
    /** @test */
    public function users_get_unseen_notifications()
    {
        $this->json('get', '/api/notifications', [
            'filter' => 'unseen'
        ])->assertStatus(200);
    }

    /** @test */
    public function user_can_mark_notifications_as_seen()
    {
        $this->json('post', '/api/notifications')->assertStatus(200);
    }
}
