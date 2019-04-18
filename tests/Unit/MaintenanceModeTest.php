<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase; 
    public $mockConsoleOutput = false;

    public function setUp(): void
    {
        parent::setUp(); 

        Artisan::call('down');
    }

    /** @test */
    public function visitors_get_503_page_when_maintenance_mode_is_on()
    {
        $this->get('/')
            ->assertStatus(503)
            ->assertSee("Voten is under maintenance. We'll probably be back in a few minutes.");
        
        Artisan::call('up');        
    }
    
    /** @test */
    public function api_users_get_503_status_when_maintenance_mode_is_on()
    {
        $this->json('GET', '/api/guest/feed', ['page' => 1])
            ->assertStatus(503);
        
        Artisan::call('up');
    }
}
