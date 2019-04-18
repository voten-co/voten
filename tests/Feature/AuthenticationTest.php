<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmailAddress;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;


class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    public $mockConsoleOutput = false;

    public function setUp(): void
    {
        parent::setUp(); 
        
        Artisan::call('passport:install');
    }

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
    public function vertification_email_is_queued_after_registeration_with_email_and_can_be_used_to_verify_email()
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

        $this->assertDatabaseHas('users', [
            'email' => 'test@test.com', 
            'username' => 'test_username', 
            'confirmed' => false 
        ]);

        Mail::assertQueued(VerifyEmailAddress::class, 1); 
        
        $this->assertDatabaseHas('email_verifications', [
            'email' => 'test@test.com',
            'verified_at' => null 
        ]);

        $verification_token = DB::table('email_verifications')->where('email', 'test@test.com')->first()->token;

        $this->get("/email/verify?token={$verification_token}")
            ->assertStatus(200)
            ->assertSee("Email Verified"); 
            
        $this->assertDatabaseMissing('email_verifications', [
            'email' => 'test@test.com',
            'verified_at' => null 
        ]);
    }

    /** @test */
    public function login_and_get_access_token()
    {
        $user = create('App\User', [
            'username' => 'username', 
            'password' => bcrypt('password')
        ]); 
        
        $this->json('POST', '/api/token/login', [
            'username' => 'username', 
            'password' => 'password', 
        ])->assertStatus(200)
            ->assertJson(['token_type' => 'Bearer']);
    }
    
    /** @test */
    public function register_and_get_access_token()
    {
        Mail::fake(); 

        $this->json('POST', '/api/token/register', [
            'username' => 'username', 
            'password' => 'password',
            'email' => 'test@test.com',
            'password_confirmation' => 'password',
            'g-recaptcha-response' => 'master_ozzy'
        ])->assertStatus(200)
            ->assertJson(['token_type' => 'Bearer']);

        Mail::assertQueued(VerifyEmailAddress::class, 1);
    }
}
