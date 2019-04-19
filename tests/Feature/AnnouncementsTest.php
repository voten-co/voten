<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnnouncementsTest extends TestCase
{
    use RefreshDatabase, WithFaker; 

    /** @test */
    public function a_voten_administrator_can_create_a_new_announcement()
    {
        // assert permission:
        $this->signInViaPassport();
        $this->json("post", "api/announcements")->assertStatus(403);

        $this->signInViaPassportAsVotenAdministrator();
         
        $this->json("post", "api/announcements", [
            'body'    => 'her dear users pay attentions please',
            'title'   => 'first announcement',
            'duration' => 3
        ])->assertStatus(201);

        $this->assertDatabaseHas('announcements', [
            'body'    => 'her dear users pay attentions please',
            'title'   => 'first announcement',
            'active_until' => now()->addDays(3)->toDateTimeString()
        ]);
    }

    /** @test */
    public function a_user_can_get_list_of_announcements()
    {
        // create 2 announcements  
        $this->signInViaPassportAsVotenAdministrator();
        $this->json("post", "api/announcements", [
            'body'    => $this->faker->paragraph(),
            'title'   => $this->faker->name(),
            'duration' => 3
        ])->assertStatus(201);
        $this->json("post", "api/announcements", [
            'body'    => $this->faker->paragraph(),
            'title'   => $this->faker->name(),
            'duration' => 3
        ])->assertStatus(201);

        $this->signInViaPassport();

        $this->json("get", "api/announcements")
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function a_user_can_mark_a_announcement_as_seen_and_not_get_it_anymore()
    {
        // create 2 announcements  
        $this->signInViaPassportAsVotenAdministrator();
        $this->json("post", "api/announcements", [
            'body'    => $this->faker->paragraph(),
            'title'   => $this->faker->name(),
            'duration' => 3
        ])->assertStatus(201);
        $this->json("post", "api/announcements", [
            'body'    => $this->faker->paragraph(),
            'title'   => $this->faker->name(),
            'duration' => 3
        ])->assertStatus(201);

        $this->signInViaPassport();
        
        $this->json("get", "api/announcements")
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // mark is as seen: 
        $this->json("post", "api/announcements/1/seen")
            ->assertStatus(200);

        // Now gets only one: 
        $this->json("get", "api/announcements")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }
    
    /** @test */
    public function a_voten_administrator_can_delete_a_announcement()
    {
        // create 2 announcements  
        $this->signInViaPassportAsVotenAdministrator();
        $this->json("post", "api/announcements", [
            'body'    => $this->faker->paragraph(),
            'title'   => $this->faker->name(),
            'duration' => 3
        ])->assertStatus(201);
        $this->json("post", "api/announcements", [
            'body'    => $this->faker->paragraph(),
            'title'   => $this->faker->name(),
            'duration' => 3
        ])->assertStatus(201);
        
        $this->json("get", "api/announcements")
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');

        // mark is as seen: 
        $this->json("delete", "/api/announcements/1")
            ->assertStatus(200);

        // Now gets only one: 
        $this->json("get", "api/announcements")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // assert permission: 
        $this->signInViaPassport();
        $this->json("delete", "/api/announcements/2")->assertStatus(403);
    }
}
