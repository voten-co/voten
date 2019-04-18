<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmbedTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->signInViaPassport();
    }

    /** @test */
    public function can_fetch_title_from_external_link()
    {
        $res = $this->json('GET', '/api/links/title', [
            'url' => 'https://github.com/voten-co/voten',
        ])
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'voten-co/voten',
                ],
            ]);
    }

    /** @test */
    public function url_must_be_a_valid_address()
    {
        $res = $this->json('GET', '/api/links/title', [
            'url' => 'voten.co',
        ])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "url" => [
                        "The url format is invalid."
                    ],
                ],
            ]);
    }

    /** @test */
    public function url_must_be_active()
    {
        $res = $this->json('GET', '/api/links/title', [
            'url' => 'https://without-dns-record.voten.co',
        ])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "url" => [
                        "The url is not a valid URL."
                    ],
                ],
            ]);
    }
}
