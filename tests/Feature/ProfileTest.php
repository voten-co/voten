<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function profile_page_displays_correct_info()
    {
        create('App\User', [
            'username' => 'JohnDoe', 
            'name' => 'John Doe', 
            'location' => 'Earth', 
            'info' => [
                'twitter' => 'john_on_twitter', 
                'website' => 'https://voten.co'
            ]
        ]);

        $this->get('/@' . 'JohnDoe')
            ->assertSee('@' . 'JohnDoe')
            ->assertSeeText('Earth')
            ->assertSee('john_on_twitter')
            ->assertSee('voten.co')
            ->assertSee('John Doe');
    }
}
