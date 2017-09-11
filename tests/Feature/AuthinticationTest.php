<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthinticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_can_register()
    {
        $this->post('/register', [
            'username'              => 'test_username',
            'email'                 => 'test@test.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect('/find-channels?newbie=1&sidebar=0');
    }

    /** @test */
    public function a_guest_can_login()
    {
        $this->post('/register', [
            'username'              => 'test_username',
            'email'                 => 'test@test.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->get('/logout');

        $this->post('/login', [
            'username' => 'test_username',
            'password' => 'password',
        ])->assertRedirect('/');
    }
}
