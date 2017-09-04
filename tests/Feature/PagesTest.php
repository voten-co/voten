<?php

namespace Tests\Feature;

use Tests\TestCase;

class PagesTest extends TestCase
{
    /** @test */
    public function guest_home_page()
    {
        $this->get('/')->assertStatus(200);
    }

    /** @test */
    public function about_page()
    {
        $this->get('/about')->assertSee('Social Bookmarking For The 21st Century');
    }

    /** @test */
    public function credits_page()
    {
        $this->get('/credits')->assertSee('Credits');
    }

    /** @test */
    public function privacy_policy_page()
    {
        $this->get('/privacy-policy')->assertSee('Privacy Policy');
    }

    /** @test */
    public function terms_of_service_page()
    {
        $this->get('/tos')->assertSee('Terms Of Service');
    }

    /** @test */
    public function horizon_must_not_be_visible_guests()
    {
        $this->get('/horizon')->assertStatus(403);
    }
}
