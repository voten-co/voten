<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmojisTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function can_get_emoji_list()
    {
        $this->json("get", "/api/guest/emojis")
            ->assertStatus(200);
        
        $this->signInViaPassport();
        
        $this->json("get", "/api/emojis")
            ->assertStatus(200);
    }
}
