<?php

namespace Tests\Feature;

use App\Submission;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Channel;

class SubmissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cannot_edit_non_text_submissions()
    {
        $author = create(User::class);
        $submission = createFromState(Submission::class, 'link', ['user_id' => $author->id]);

        $this->signInViaPassport($author);

        $this->json('PATCH', "/api/submissions/{$submission->id}", [
            'text' => 'the new text',
        ])->assertStatus(400);
    }

    /** @test */
    public function author_can_edit_submission()
    {
        $author = create(User::class);
        $submission = create(Submission::class, ['user_id' => $author->id]);

        $this->signInViaPassport($author);

        $this->json('PATCH', "/api/submissions/{$submission->id}", [
            'text' => 'the new text',
        ])->assertStatus(200);
    }

    /** @test */
    public function author_can_delete_submission()
    {
        $author = create(User::class);
        $submission = create(Submission::class, ['user_id' => $author->id]);

        $this->signInViaPassport($author);

        $this->json('DELETE', "/api/submissions/{$submission->id}")->assertStatus(200);

        $this->assertDatabaseMissing('submissions', [
            'id' => $submission->id,
        ]);
    }

    /** @test */
    public function even_voten_administrators_cannot_edit_or_delete_submissions()
    {
        $this->signInViaPassportAsVotenAdministrator();

        $submission = create(Submission::class);

        $this->json('PATCH', "/api/submissions/{$submission->id}", [
            'text' => 'the new text',
        ])->assertStatus(403);

        $this->json('DELETE', "/api/submissions/{$submission->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_hide_a_submission()
    {
        $author = create(User::class);
        $submission = create(Submission::class, ['user_id' => $author->id]);

        $auth = create(User::class);
        $this->signInViaPassport($auth);
        $this->json('POST', "/api/submissions/{$submission->id}/hide")->assertStatus(201);

        $this->assertDatabaseHas('hides', ['submission_id' => $submission->id, 'user_id' => $auth->id]);
    }

    /** @test */
    public function a_user_can_unhide_a_submission()
    {
        $author = create(User::class);
        $submission = create(Submission::class, ['user_id' => $author->id]);

        $auth = create(User::class);
        $this->signInViaPassport($auth);

        // store it
        $this->json('post', "/api/submissions/{$submission->id}/hide")
            ->assertStatus(201);

        $this->assertDatabaseHas('hides', ['submission_id' => $submission->id, 'user_id' => $auth->id]);

        // destroy it
        $this->json('delete', "/api/submissions/{$submission->id}/hide")
            ->assertStatus(200);

        $this->assertDatabaseMissing('hides', ['submission_id' => $submission->id, 'user_id' => $auth->id]);
    }

    /** @test */
    public function author_can_mark_submission_as_nsfw_and_then_as_sfw()
    {
        $user = create(User::class);
        $this->signInViaPassport($user);
        $submission = create(Submission::class, ['user_id' => $user->id]);

        // mark as nsfw 
        $this->json('post', "/api/submissions/{$submission->id}/nsfw")
            ->assertStatus(200);
        
        $this->assertDatabaseHas('submissions', ['id' => $submission->id, 'nsfw' => 1]);

        // mark as safe for work 
        $this->json('delete', "/api/submissions/{$submission->id}/nsfw")
            ->assertStatus(200);

        $this->assertDatabaseHas('submissions', ['id' => $submission->id, 'nsfw' => 0]);
    }

    /** @test */
    public function a_channel_moderator_can_approve_a_submission()
    {
        $submission_author = create(User::class);
        $submission = create(Submission::class, [
            'user_id' => $submission_author->id,
        ]); 

        $this->json("post", "/api/submissions/{$submission->id}/approve")->assertStatus(401);

        $this->signInViaPassport($submission_author);

        $this->json("post", "/api/submissions/{$submission->id}/approve")->assertStatus(403);

        $channel = Channel::find($submission->channel_id);
        $channel->moderators()->attach($moderator = create(User::class), ['role' => 'moderator']);

        $this->signInViaPassport($moderator);

        $this->json("post", "/api/submissions/{$submission->id}/approve")->assertStatus(200);        

        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'approved_at' => now(),
        ]);
    }

    /** @test */
    public function a_channel_moderator_can_disapprove_a_submission()
    {
        $submission_author = create(User::class);
        $submission = create(Submission::class, [
            'user_id' => $submission_author->id,
        ]); 

        $this->json("post", "/api/submissions/{$submission->id}/disapprove")->assertStatus(401);

        $this->signInViaPassport($submission_author);

        $this->json("post", "/api/submissions/{$submission->id}/disapprove")->assertStatus(403);

        $channel = Channel::find($submission->channel_id);
        $channel->moderators()->attach($moderator = create(User::class), ['role' => 'moderator']);

        $this->signInViaPassport($moderator);

        $this->json("post", "/api/submissions/{$submission->id}/disapprove")->assertStatus(200);        

        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'approved_at' => null,
            'deleted_at' => now()->toDateTimeString(),
        ]);
    }
}
