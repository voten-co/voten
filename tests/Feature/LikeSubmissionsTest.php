<?php

namespace Tests\Feature;

use App\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class LikeSubmissionsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp(); 
        
        $this->withoutExceptionHandling();
        $this->signInViaPassport();
    }

    /** @test */
    public function a_user_can_like_a_submission()
    {
        $submission = create(Submission::class);

        // like 
        $this->json('post', "/api/submissions/{$submission->id}/like")
            ->assertStatus(201);
        $this->assertDatabaseHas('submission_likes', [
            'user_id' => Auth::id(), 
            'submission_id' => $submission->id,
        ]);
        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id, 
            'likes' => 2,
            'rate' => rate(2, $submission->created_at),
        ]);

        // undo
        $this->json('post', "/api/submissions/{$submission->id}/like")
            ->assertStatus(200);
        $this->assertDatabaseMissing('submission_likes', [
            'user_id' => Auth::id(), 
            'submission_id' => $submission->id,
            'rate' => rate(1, $submission->created_at)
        ]);
        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id, 
            'likes' => 1,
        ]);
    }
}
