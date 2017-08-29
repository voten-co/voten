<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Make sure the user can register from the home.
     *
     * @return void
     */
    public function testSuccessfulRegistration()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->press('Sign up/Log in')
                    ->assertSeeLink('Sign up')
                    ->clickLink('Sign up')
                    ->assertSee('By clicking Sign Up, you agree to our')
                    ->keys('.form-register input[name="username"]', 'duskregister')
                    ->type('email', 'dusk@example.com')
                    ->keys('.form-register input[name="password"]', 'absolutelyamazing')
                    ->type('confirm_password', 'absolutelyamazing')
                    ->click('.form-register button.v-button')
                    ->waitForLocation('/find-channels')
                    ->assertSee('Welcome to Voten, dusk');
        });
    }
}
