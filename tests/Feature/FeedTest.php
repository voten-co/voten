<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Channel;
use App\Submission;
use App\Suggested;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\User;

class FeedTest extends TestCase
{
    use RefreshDatabase; 

    public function setUp(): void
    {
        parent::setUp(); 
        
        // create 3 channels: 
        $channel1 = create(Channel::class, ['id' => 1]);
        $channel2 = create(Channel::class, ['id' => 2]);
        $channel3 = create(Channel::class, ['id' => 3]);
        $channel4 = create(Channel::class, ['id' => 4]);

        // create a total of 66 + 9 submissions: 
        factory(Submission::class, 20)->create(['channel_id' => $channel1->id, 'channel_name' => $channel1->name]);
        factory(Submission::class, 24)->create(['channel_id' => $channel2->id, 'channel_name' => $channel2->name]);
        factory(Submission::class, 22)->create(['channel_id' => $channel3->id, 'channel_name' => $channel3->name]);
        factory(Submission::class, 3)->create(['channel_id' => $channel4->id, 'channel_name' => $channel4->name]);

        // make $channel1 and $channel2 suggested channels:
        Suggested::create([
            'z_index'     => 5,
            'group'       => 'Technology',
            'channel_id'  => $channel1->id,
        ]);
        Suggested::create([
            'z_index'     => 5,
            'group'       => 'Technology',
            'channel_id'  => $channel2->id,
        ]);
        
        Cache::forget('default-channels-ids');
    }

    /** @test */
    public function a_guest_can_get_feed_submissions()
    {
        // Paginate a total of 44/66 posted submissions: 
        $this->json("get", "/api/guest/feed", [
            'page' => 1,
        ])->assertStatus(200)->assertJsonCount(15, 'data');
        
        $this->json("get", "/api/guest/feed", [
            'page' => 2,
        ])->assertStatus(200)->assertJsonCount(15, 'data');

        $this->json("get", "/api/guest/feed", [
            'page' => 3,
        ])->assertStatus(200)->assertJsonCount(14, 'data');
        
        $this->json("get", "/api/guest/feed", [
            'page' => 4,
        ])->assertStatus(200)->assertJsonCount(0, 'data');
    }

    /** @test */
    public function a_user_can_get_submissions_from_subscribed_channels()
    {
        $this->signInViaPassport();

        // subscribe to $channel1 and $channel2
        $this->json("post", "/api/channels/2/subscribe")->assertStatus(201);
        $this->json("post", "/api/channels/3/subscribe")->assertStatus(201);
        $this->assertEquals(2, Auth::user()->subscriptions()->count());
        
        // // Paginate a total of 46/66 posted submissions: 
        $this->json("get", "/api/feed", [
            'page' => 1,
            'filter' => 'subscribed',
        ])->assertStatus(200)->assertJsonCount(15, 'data');
        
        $this->json("get", "/api/feed", [
            'page' => 4,
            'filter' => 'subscribed',
        ])->assertStatus(200)->assertJsonCount(1, 'data');
    }

    /** @test */
    public function a_user_can_get_submissions_from_bookmarked_channels()
    {
        $this->signInViaPassport();
        
        $this->json("post", "/api/channels/2/bookmark")->assertStatus(201);
        
        $this->json("get", "/api/feed", [
            'page' => 1,
            'filter' => 'bookmarked',
        ])->assertStatus(200)->assertJsonCount(15, 'data');
        $this->json("get", "/api/feed", [
            'page' => 2,
            'filter' => 'bookmarked',
        ])->assertStatus(200)->assertJsonCount(24 - 15, 'data');
    }
    
    /** @test */
    public function a_user_can_get_submissions_from_all_channels()
    {
        $this->signInViaPassport();

        $this->json("get", "/api/feed", [
            'page' => 5,
            'filter' => 'all',
        ])->assertStatus(200)->assertJsonCount(9, 'data');
    }
    
    /** @test */
    public function a_user_can_get_submissions_from_bookmarked_users()
    {
        $this->signInViaPassport();

        $user = create(User::class);
        $submission = create(Submission::class, ['user_id' => $user->id]);
        $submission = create(Submission::class, ['user_id' => $user->id]);
        
        // bookmark user: 
        $this->json("post", "/api/users/{$user->id}/bookmark")->assertStatus(201);

        $this->json("get", "/api/feed", [
            'page' => 1,
            'filter' => 'by-bookmarked-users',
        ])->assertStatus(200)->assertJsonCount(2, 'data');
    }
    
    /** @test */
    public function a_user_can_get_submissions_from_his_moderating_channels()
    {
        $this->signInViaPassport();
        
        $channel2 = Channel::find(2); // which has 24 submissions 
        $channel2->moderators()->attach(Auth::user(), [
            'role' => 'moderator',
        ]); 

        $this->json("get", "/api/feed", [
            'page' => 2,
            'filter' => 'moderating',
        ])->assertStatus(200)->assertJsonCount(24 - 15, 'data');
    }
    
    /** @test */
    public function a_user_can_limit_submissions_to_a_single_type_only()
    {
        $this->signInViaPassport();
        
        createFromState(Submission::class, 'link'); 
        createFromState(Submission::class, 'link'); 

        $this->json("get", "/api/feed", [
            'page' => 1,
            'filter' => 'all',
            'submissions_type' => 'link'
        ])->assertStatus(200)->assertJsonCount(2, 'data');
    }
    
    /** @test */
    public function a_user_can_include_nsfw_submissions()
    {
        $this->signInViaPassport();
        
        createFromState(Submission::class, 'nsfw'); 

        $this->json("get", "/api/feed", [
            'page' => 5,
            'filter' => 'all',
            'include_nsfw_submissions' => 0 // the default 
        ])->assertStatus(200)->assertJsonCount(9, 'data');
        
        $this->json("get", "/api/feed", [
            'page' => 5,
            'filter' => 'all',
            'include_nsfw_submissions' => 1
        ])->assertStatus(200)->assertJsonCount(10, 'data');
    }
    
    /** @test */
    public function a_user_can_exclude_liked_submissions()
    {
        $this->signInViaPassport();

        $this->json("get", "/api/feed", [
            'page' => 5,
            'filter' => 'all', 
        ])->assertStatus(200)->assertJsonCount(9, 'data');

        $this->json("post", "/api/submissions/1/like")->assertStatus(201);
        $this->json("post", "/api/submissions/2/like")->assertStatus(201);

        $this->json("get", "/api/feed", [
            'page' => 5,
            'filter' => 'all',
            'exclude_liked_submissions' => 1
        ])->assertStatus(200)->assertJsonCount(7, 'data');
    }
    
    /** @test */
    public function a_user_can_exclude_bookmarked_submissions()
    {
        $this->signInViaPassport();

        $this->json("get", "/api/feed", [
            'page' => 5,
            'filter' => 'all', 
        ])->assertStatus(200)->assertJsonCount(9, 'data');

        $this->json("post", "/api/submissions/1/bookmark")->assertStatus(201);
        $this->json("post", "/api/submissions/2/bookmark")->assertStatus(201);

        $this->json("get", "/api/feed", [
            'page' => 5,
            'filter' => 'all',
            'exclude_liked_submissions' => 1
        ])->assertStatus(200)->assertJsonCount(7, 'data');
    }

    /** @test */
    public function a_user_can_get_submissions_of_a_channel()
    {
        $this->signInViaPassport();

        $this->json("get", "/api/channels/4/submissions", [
            'sort' => 'hot', 
            'page' => 1
        ])
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    // /** @test */
    // public function a_user_can_sort_his_feed_by_hot()
    // {
    //     // todo
    //     $this->assertTrue(true);
    // }

    // /** @test */
    // public function a_user_can_sort_his_feed_by_new()
    // {
    //     // todo
    //     $this->assertTrue(true);
    // }
    
    // /** @test */
    // public function a_user_can_sort_his_feed_by_rising()
    // {
    //     // todo
    //     $this->assertTrue(true);
    // }
    
    // /** @test */
    // public function a_user_wont_get_duplicate_submissions_about_the_same_url()
    // {
    //     // todo
    //     $this->assertTrue(true);
    // }
}
