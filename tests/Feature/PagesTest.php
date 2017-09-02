<?php

namespace Tests\Feature;

use Tests\TestCase;

class PagesTest extends TestCase
{
    /** @test */
    public function about_page()
    {
        $response = $this->get('/about');
        $response->assertSee('Social Bookmarking For The 21st Century');
    }

    /** @test */
    public function credits_page()
    {
        $response = $this->get('/credits');
        $response->assertSee('Credits');
    }

    /** @test */
    public function privacy_policy_page()
    {
        $response = $this->get('/privacy-policy');
        $response->assertSee('Privacy Policy');
    }

    /** @test */
    public function terms_of_service_page()
    {
        $response = $this->get('/tos');
        $response->assertSee('Terms Of Service');
    }
}
