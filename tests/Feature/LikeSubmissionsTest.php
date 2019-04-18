<?php

namespace Tests\Feature;

use App\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\User;

class LikeSubmissionsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp(); 

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

    /** 
     * The test has the same IP address, no need to fake it. 
     * 
     * @test
     */
    public function same_ip_address_can_like_more_than_once_but_the_rate_must_stay_the_same()
    {
        $submission = create(Submission::class);

        // first like 
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

        // login as a second user 
        $this->signInViaPassport(create(User::class));
        
        // like the same submission with ti 
        $this->json('post', "/api/submissions/{$submission->id}/like")
            ->assertStatus(201);
        
        // assert that it's been added to users likes 
        $this->assertDatabaseHas('submission_likes', [
            'user_id' => Auth::id(), 
            'submission_id' => $submission->id,
        ]);
        
        // but hasn't affected submission's rate 
        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id, 
            'likes' => 2,
            'rate' => rate(2, $submission->created_at),
        ]);
    }
}
