<?php

namespace Tests\Browser;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Make sure the user can login from the home.
     *
     * @return void
     */
    public function testSuccessfulLogin()
    {
        User::create([
            'username'  => 'dusklogin',
            'password'  => Hash::make('supercool'),
            'confirmed' => 1,
            'settings'  => [],
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->press('Sign up/Log in')
                ->assertSeeLink('Login')
                ->assertSee('Remember Me')
                ->keys('.form-login input[name="username"]', 'dusklogin')
                ->keys('.form-login input[name="password"]', 'supercool')
                ->click('.form-login button.v-button')
                ->waitForText('dusklogin');
        });
    }
}
