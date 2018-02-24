<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackendTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function horizon_must_not_be_accessible_to_guests()
    {
        $this->signIn();

        $this->get('/horizon')->assertStatus(403);
    }

    /** @test */
    public function backend_panel_must_not_be_accessible_to_guests()
    {
        $this->get('/backend')->assertStatus(403);
    }

    /** @test */
    public function a_none_administrator_authinticated_user_cant_access_backend_panel()
    {
        $this->signIn();

        $this->get('/backend')->assertStatus(403);
    }
}
