<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware; 

    public function setUp()
    {
        parent::setUp(); 

        Storage::fake('ftp');
        $this->signInViaPassport();
    }

    /** @test */
    public function user_can_upload_avatar()
    {
        // don't accept non-square images 
        $this->json('POST', '/api/users/avatar', [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 100)
        ])->assertStatus(422);

        // accept squar images 
        $uploaded_file_address = $this->json('POST', '/api/users/avatar', [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 250)
        ])->assertStatus(200);
    }

    /** @test */
    public function user_can_upload_photo()
    {
        $this->disableExceptionHandling();        
        
        $this->json('POST', '/api/photos', [
            'file' => UploadedFile::fake()->image('sample.jpg')
        ])->assertStatus(201);
    }
}
