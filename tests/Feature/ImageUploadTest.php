<?php

namespace Tests\Feature;

use App\Channel;
use function GuzzleHttp\json_decode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));
        $this->signInViaPassport();
    }

    /** @test */
    public function user_can_upload_avatar()
    {
        // don't accept non-square images
        $this->json('POST', '/api/users/avatar', [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 100),
        ])->assertStatus(422);

        // accept squar images
        $uploaded_file_address = $this->json('POST', '/api/users/avatar', [
            'photo' => UploadedFile::fake()->image('avatar.png', 250, 250),
        ])->assertStatus(200);
    }

    /** @test */
    public function user_can_upload_photo()
    {
        $response = $this->json('POST', '/api/photos', [
            'file' => UploadedFile::fake()->image('sample.jpg'),
        ])->assertStatus(201);
        $data = json_decode($response->getContent())->data;

        $this->assertDatabaseHas('photos', [
            'user_id' => Auth::id(),
            'thumbnail_path' => $data->thumbnail_path,
            'path' => $data->path,
        ]);

        Storage::disk(config('filesystems.default'))->assertExists('submissions/img/' . str_after($data->path, 'submissions/img/'));
        Storage::disk(config('filesystems.default'))->assertExists('submissions/img/thumbs/' . str_after($data->thumbnail_path, 'submissions/img/thumbs/'));
    }

    // /** @test */
    // public function photo_gets_deleted_after_submission_is_deleted()
    // {
    //     $channel = create(Channel::class);

    //     // upload a photo
    //     $response = $this->json('POST', '/api/photos', [
    //         'file' => UploadedFile::fake()->image('sample.jpg'),
    //     ])->assertStatus(201);
    //     $photo = json_decode($response->getContent())->data;

    //     // create post
    //     $submission_response = $this->json('post', '/api/submissions', [
    //         'channel_name' => $channel->name,
    //         'type' => 'img',
    //         'title' => 'test title',
    //         'photos_id' => [$photo->id],
    //     ])->assertStatus(200);

    //     $this->assertDatabaseHas('submissions', [
    //         'title' => 'test title',
    //         'type' => 'img',
    //         'channel_name' => $channel->name,
    //     ]);
    //     $submission = json_decode($submission_response->getContent())->data;

    //     Storage::disk(config('filesystems.default'))->assertExists('submissions/img/' . str_after($photo->path, 'submissions/img/'));
    //     Storage::disk(config('filesystems.default'))->assertExists('submissions/img/thumbs/' . str_after($photo->thumbnail_path, 'submissions/img/thumbs/'));

    //     // delete post
    //     $this->json('delete', "/api/submissions/{$submission->id}")->assertStatus(200);

    //     $this->assertDatabaseMissing('submissions', ['id' => $submission->id]);

    //     $this->assertDatabaseMissing('photos', [
    //         'user_id' => Auth::id(),
    //         'thumbnail_path' => $photo->thumbnail_path,
    //         'path' => $photo->path,
    //     ]);

    //     Storage::disk(config('filesystems.default'))->assertMissing('submissions/img/' . str_after($photo->path, 'submissions/img/'));
    //     Storage::disk(config('filesystems.default'))->assertMissing('submissions/img/thumbs/' . str_after($photo->thumbnail_path, 'submissions/img/thumbs/'));
    // }
}