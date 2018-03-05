<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailAddress;

class AuthinticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_can_register_via_the_form()
    {
        $this->post('/register', [
            'username' => 'test_username',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'g-recaptcha-response' => 'master_ozzy',
        ])->assertRedirect('/discover-channels?newbie=1&sidebar=0');
    }

    /** @test */
    public function a_guest_can_login()
    {
        $this->post('/register', [
            'username' => 'test_username',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'g-recaptcha-response' => 'master_ozzy',
        ]);

        $this->get('/logout');

        $this->post('/login', [
            'username' => 'test_username',
            'password' => 'password',
        ])->assertRedirect('/');
    }

    /** @test */
    public function a_guest_can_register_via_the_api()
    {
        $this->json('POST', '/register', [
            'username' => 'test_username',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'g-recaptcha-response' => 'master_ozzy',
        ])->assertJson([
            'message' => 'Registered successfully.',
        ]);
    }

    /** @test */
    public function a_guest_can_login_via_the_api()
    {
        $this->post('/register', [
            'username' => 'test_username',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'g-recaptcha-response' => 'master_ozzy',
        ]);

        $this->get('/logout');

        $res = $this->json('POST', '/login', [
            'username' => 'test_username',
            'password' => 'password',
        ])->assertJson([
            'message' => 'Logged in successfully.',
        ]);
    }

    /** @test */
    public function vertification_email_is_queued_after_registeration_with_email()
    {
        Mail::fake();

        $this->json('POST', '/register', [
            'username' => 'test_username',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'g-recaptcha-response' => 'master_ozzy',
        ])->assertJson([
            'message' => 'Registered successfully.',
        ]);

        Mail::assertQueued(VerifyEmailAddress::class, 1); 
    }
}
