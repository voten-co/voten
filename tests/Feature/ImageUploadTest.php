<?php

namespace Tests\Feature;

use App\Channel;
use function GuzzleHttp\json_decode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));

        $this->signInViaPassport();
    }

    /** @test */
    public function user_can_upload_new_avatar()
    {
        // don't accept non-square images
        $this->json('POST', '/api/auth/avatar', [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 100),
        ])->assertStatus(422);

        // accept square images
        $first_uploaded_avatar = $this->json('POST', '/api/auth/avatar', [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 250),
        ])
            ->assertStatus(200);

        Storage::disk(config('filesystems.default'))->assertExists('users/avatars/' . str_after($first_uploaded_avatar->getContent(), 'users/avatars/'));

        // assert that the previous avatar has been removed
        $second_uploaded_avatar = $this->json('POST', '/api/auth/avatar', [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 250),
        ])->assertStatus(200);

        Storage::disk(config('filesystems.default'))->assertExists('users/avatars/' . str_after($second_uploaded_avatar->getContent(), 'users/avatars/'));

        // assert that the previous avatar has been removed 
        Storage::disk(config('filesystems.default'))->assertMissing('users/avatars/' . str_after($first_uploaded_avatar->getContent(), 'users/avatars/'));
    }

    /** @test */
    public function channel_administrator_can_upload_new_channel_avatar()
    {
        $this->signInViaPassportAsVotenAdministrator();

        $channel = create(Channel::class);

        // accept square images
        $first_uploaded_avatar = $this->json('POST', "/api/channels/{$channel->id}/avatar", [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 250),
        ]);
        
        // assert response is the documented format:
        $first_uploaded_avatar
            ->assertStatus(200)
            ->assertSeeText($first_uploaded_avatar->getContent());
        
        Storage::disk(config('filesystems.default'))->assertExists('channels/avatars/' . str_after($first_uploaded_avatar->getContent(), 'channels/avatars/'));

        // upload another one
        $second_uploaded_avatar = $this->json('POST', "/api/channels/{$channel->id}/avatar", [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 250),
        ]);

        Storage::disk(config('filesystems.default'))->assertExists('channels/avatars/' . str_after($second_uploaded_avatar->getContent(), 'channels/avatars/'));

        // assert that the previous avatar has been removed 
        Storage::disk(config('filesystems.default'))->assertMissing('channels/avatars/' . str_after($first_uploaded_avatar->getContent(), 'channels/avatars/'));
    }

    /** @test */
    public function user_can_upload_photo()
    {
        $response = $this->json('POST', '/api/photos', [
            'file' => UploadedFile::fake()->image('sample.jpg'),
        ])
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'submission_id' => null,
                    'created_at' => now()->toDateTimeString(),
                    'expires_after_secs' => (3600 * 24) * 1, // 24 hours 
                ]
            ]);
        $data = json_decode($response->getContent())->data;

        $this->assertDatabaseHas('photos', [
            'user_id' => Auth::id(),
            'thumbnail_path' => $data->thumbnail_path,
            'path' => $data->path,
        ]);

        Storage::disk(config('filesystems.default'))->assertExists('submissions/img/' . str_after($data->path, 'submissions/img/'));
        Storage::disk(config('filesystems.default'))->assertExists('submissions/img/thumbs/' . str_after($data->thumbnail_path, 'submissions/img/thumbs/'));

        // get it: 
        $this->json("get", "/api/photos/{$data->id}")
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'submission_id' => null,
                    'created_at' => now()->toDateTimeString(),
                    'expires_after_secs' => (3600 * 24) * 1, // 24 hours 
                ]
            ]);
    }

    /** @test */
    public function photo_gets_deleted_after_submission_is_deleted()
    {
        $channel = create(Channel::class);

        // upload a photo
        $response = $this->json('POST', '/api/photos', [
            'file' => UploadedFile::fake()->image('sample.jpg'),
        ])->assertStatus(201);
        $photo = json_decode($response->getContent())->data;

        // create post
        $submission_response = $this->json('post', '/api/submissions', [
            'channel_name' => $channel->name,
            'type' => 'img',
            'title' => 'test title',
            'photos_id' => [$photo->id],
        ])->assertStatus(200);

        $this->assertDatabaseHas('submissions', [
            'title' => 'test title',
            'type' => 'img',
            'channel_name' => $channel->name,
        ]);
        $submission = json_decode($submission_response->getContent())->data;

        Storage::disk(config('filesystems.default'))->assertExists('submissions/img/' . str_after($photo->path, 'submissions/img/'));
        Storage::disk(config('filesystems.default'))->assertExists('submissions/img/thumbs/' . str_after($photo->thumbnail_path, 'submissions/img/thumbs/'));

        // delete post
        $this->json('delete', "/api/submissions/{$submission->id}")->assertStatus(200);

        $this->assertDatabaseMissing('submissions', ['id' => $submission->id]);

        $this->assertDatabaseMissing('photos', [
            'user_id' => Auth::id(),
            'thumbnail_path' => $photo->thumbnail_path,
            'path' => $photo->path,
        ]);

        Storage::disk(config('filesystems.default'))->assertMissing('submissions/img/' . str_after($photo->path, 'submissions/img/'));
        Storage::disk(config('filesystems.default'))->assertMissing('submissions/img/thumbs/' . str_after($photo->thumbnail_path, 'submissions/img/thumbs/'));
    }
}
