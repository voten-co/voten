<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase; 

    /** @test */
    public function a_user_may_upload_new_avatar_image_via_api()
    {
        Storage::fake('fake-disk');

        $this->signInViaPassport();

        // don't accept non-square images 
        $this->json('POST', '/api/users/avatar', [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 100)
        ])->assertStatus(422);

        // accept squar images 
        $uploaded_file_address = $this->json('POST', '/api/users/avatar', [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 250)
        ])->assertStatus(200);
    }
}
