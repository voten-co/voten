<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class DemoContentSeeder extends Seeder
{
    /**
     * Populate a fresh Voten installation with coherent, deterministic demo data.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('users')->where('username', 'admin')->exists()) {
            $this->ensureSuggestedChannels();
            $this->command->info('Existing Voten demo content repaired and refreshed.');

            return;
        }

        DB::transaction(function () {
            $now = Carbon::now();
            $settings = json_encode([
                'font' => 'Lato',
                'sidebar_color' => 'Gray',
                'notify_submissions_replied' => true,
                'notify_comments_replied' => true,
                'notify_mentions' => true,
            ]);
            $info = json_encode(['website' => null, 'twitter' => null]);

            $people = [
                ['admin', 'Demo Administrator', 'admin@voten.special.lu', 'Lisbon, Portugal', 'Keeping this demo tidy and welcoming.'],
                ['maya', 'Maya Chen', 'maya@example.test', 'Taipei, Taiwan', 'Designer, reader, and incurable café explorer.'],
                ['omar', 'Omar Haddad', 'omar@example.test', 'Amman, Jordan', 'Building useful things for the open web.'],
                ['lena', 'Lena Fischer', 'lena@example.test', 'Berlin, Germany', 'Photography, urban gardens, and good typography.'],
                ['noah', 'Noah Williams', 'noah@example.test', 'Vancouver, Canada', 'Developer by day, amateur astronomer by night.'],
                ['sofia', 'Sofia Rossi', 'sofia@example.test', 'Milan, Italy', 'Food history and slow travel.'],
                ['amir', 'Amir Rahimi', 'amir@example.test', 'Tehran, Iran', 'Open-source enthusiast and systems tinkerer.'],
                ['ines', 'Ines Martins', 'ines@example.test', 'Porto, Portugal', 'Writing about small communities doing big things.'],
            ];

            $users = [];
            foreach ($people as $index => $person) {
                $users[] = DB::table('users')->insertGetId([
                    'username' => $person[0],
                    'name' => $person[1],
                    'email' => $person[2],
                    'location' => $person[3],
                    'bio' => $person[4],
                    'password' => Hash::make(
                        $index === 0
                            ? env('DEMO_ADMIN_PASSWORD')
                            : env('DEMO_USER_PASSWORD', env('DEMO_ADMIN_PASSWORD'))
                    ),
                    'confirmed' => 1,
                    'verified' => $index < 3 ? 1 : 0,
                    'active' => 1,
                    'settings' => $settings,
                    'info' => $info,
                    'submission_xp' => 120 - ($index * 7),
                    'comment_xp' => 80 - ($index * 5),
                    'created_at' => $now->copy()->subDays(90 - ($index * 4)),
                    'updated_at' => $now,
                ]);
            }

            DB::table('appointedd_users')->insert([
                'user_id' => $users[0],
                'appointed_as' => 'administrator',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $channelDefinitions = [
                ['voten', 'News, questions, and conversations about the Voten community.', 'Blue'],
                ['technology', 'Thoughtful links and discussions about technology and the open web.', 'Purple'],
                ['design', 'Product design, typography, architecture, and visual craft.', 'Red'],
                ['science', 'Curious discoveries from space, biology, climate, and everything between.', 'Green'],
                ['books', 'What we are reading, what stayed with us, and what should be read next.', 'Orange'],
                ['travel', 'Local knowledge, memorable journeys, and responsible ways to see the world.', 'Teal'],
            ];

            $channels = [];
            foreach ($channelDefinitions as $index => $channel) {
                $channels[] = DB::table('channels')->insertGetId([
                    'name' => $channel[0],
                    'description' => $channel[1],
                    'color' => $channel[2],
                    'avatar' => '/imgs/channel-avatar.png',
                    'subscribers' => count($users),
                    'settings' => json_encode([]),
                    'created_at' => $now->copy()->subDays(75 - ($index * 3)),
                    'updated_at' => $now,
                ]);
            }

            foreach ($channels as $channelId) {
                DB::table('roles')->insert([
                    'user_id' => $users[0],
                    'role' => 'administrator',
                    'channel_id' => $channelId,
                ]);

                foreach ($users as $userId) {
                    DB::table('subscriptions')->insert([
                        'user_id' => $userId,
                        'channel_id' => $channelId,
                        'created_at' => $now->copy()->subDays(30),
                        'updated_at' => $now->copy()->subDays(30),
                    ]);
                }
            }

            $posts = [
                ['voten', 'Welcome to the Voten demo community', 'This restored Voten instance is filled with sample conversations so you can explore feeds, channels, voting, profiles, and moderation without starting from an empty database.'],
                ['technology', 'What makes a small online community feel healthy?', 'Clear norms help, but the strongest signal is whether thoughtful participation is noticed. What patterns have worked in communities you enjoy?'],
                ['design', 'A tiny collection of interfaces that aged gracefully', 'The most durable interfaces tend to have strong hierarchy, restrained motion, and language that respects the person using them.'],
                ['science', 'The night sky is a time machine', 'Looking into space also means looking into the past. Even the sunlight reaching us right now began its trip roughly eight minutes ago.'],
                ['books', 'Which book changed how you approach your work?', 'Not necessarily a business book. Fiction, history, essays, and biographies often give us the most useful new lenses.'],
                ['travel', 'How to explore a city without rushing through it', 'Choose one neighborhood, walk without a checklist, use public transit, and leave enough room for a place to surprise you.'],
                ['technology', 'The quiet value of software that does one thing well', 'Small dependable tools often outlive ambitious platforms because their purpose stays legible to both users and maintainers.'],
                ['design', 'Why empty space is an active design decision', 'Whitespace is not unused space. It groups related information, establishes rhythm, and gives important elements room to speak.'],
                ['science', 'Community science projects worth joining this year', 'From classifying galaxies to recording local bird calls, public research projects can turn ordinary observations into useful datasets.'],
                ['books', 'A weekend reading list for curious builders', 'A mix of essays on systems thinking, biographies of patient inventors, and one excellent novel for resetting the brain.'],
                ['travel', 'Share one overlooked place in your hometown', 'Skip the headline attraction. Tell us about the park, market, library, view, or quiet street you would show a friend.'],
                ['voten', 'What should we demonstrate next?', 'Try creating a post, replying to a discussion, voting, changing your profile, or opening the administrator dashboard.'],
                ['technology', 'When is boring technology the right choice?', 'Usually when reliability, hiring, and long-term maintenance matter more than novelty. The interesting work can live in the product itself.'],
                ['design', 'Good defaults are a form of hospitality', 'A good default reduces anxiety while leaving room for experienced users to make a different choice.'],
                ['science', 'Five minutes of wonder: watch clouds organize themselves', 'Cloud streets, wave patterns, and towering convection cells make atmospheric physics visible from the ground.'],
                ['books', 'The pleasure of rereading at a different age', 'The words stay fixed while the reader changes. A familiar book can become a surprisingly accurate measure of that distance.'],
                ['travel', 'What makes a neighborhood walk memorable?', 'Human scale, changing textures, places to pause, and enough unpredictability to reward paying attention.'],
                ['voten', 'A short guide to participating in this demo', 'Browse the hot and new feeds, open a channel, sign in with the demo account, and use the backend link to explore moderation tools.'],
            ];

            $submissions = [];
            foreach ($posts as $index => $post) {
                $channelIndex = array_search($post[0], array_column($channelDefinitions, 0));
                $createdAt = $now->copy()->subHours(($index * 5) + 1);
                $slug = str_slug($post[1]);
                $likes = max(3, 28 - $index);
                $submissions[] = DB::table('submissions')->insertGetId([
                    'slug' => $slug,
                    'title' => $post[1],
                    'type' => 'text',
                    'data' => json_encode(['text' => $post[2]]),
                    'channel_name' => $post[0],
                    'rate' => 500 - ($index * 17),
                    'user_id' => $users[$index % count($users)],
                    'channel_id' => $channels[$channelIndex],
                    'likes' => $likes,
                    'comments_number' => 3,
                    'url' => config('app.url').'/c/'.$post[0].'/'.$slug,
                    'domain' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            $this->storeSuggestedChannels($channels, $channelDefinitions, $now);

            $commentBodies = [
                'This is exactly the kind of conversation I hoped to find here.',
                'A useful way to frame it. The small details make a bigger difference than people expect.',
                'I would add that consistency matters just as much as the initial idea.',
                'Thanks for sharing this. I am going to try it and report back.',
                'There is a lot packed into this point, especially for smaller communities.',
                'I had a similar experience and arrived at almost the same conclusion.',
            ];

            foreach ($submissions as $index => $submissionId) {
                $channelId = $channels[array_search($posts[$index][0], array_column($channelDefinitions, 0))];
                for ($offset = 0; $offset < 3; $offset++) {
                    $commentId = DB::table('comments')->insertGetId([
                        'submission_id' => $submissionId,
                        'user_id' => $users[($index + $offset + 1) % count($users)],
                        'parent_id' => 0,
                        'channel_id' => $channelId,
                        'level' => 0,
                        'rate' => 120 - ($offset * 11),
                        'likes' => 5 - $offset,
                        'body' => $commentBodies[($index + $offset) % count($commentBodies)],
                        'created_at' => $now->copy()->subHours(($index * 5) + 1)->addMinutes(($offset + 1) * 8),
                        'updated_at' => $now,
                    ]);

                    foreach (array_slice($users, 0, 5 - $offset) as $userId) {
                        DB::table('comment_likes')->insert([
                            'user_id' => $userId,
                            'comment_id' => $commentId,
                            'ip_address' => '127.0.0.1',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }
                }

                foreach (array_slice($users, 0, min(count($users), max(3, 8 - ($index % 5)))) as $userId) {
                    DB::table('submission_likes')->insert([
                        'user_id' => $userId,
                        'submission_id' => $submissionId,
                        'ip_address' => '127.0.0.1',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            foreach ($channels as $channelId) {
                DB::table('activities')->insert([
                    'subject_id' => $channelId,
                    'subject_type' => 'App\\Channel',
                    'name' => 'created_channel',
                    'user_id' => $users[0],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Voten demo seeder',
                    'created_at' => $now->copy()->subDays(75),
                    'updated_at' => $now->copy()->subDays(75),
                ]);
            }
        });

        Cache::forget('default-channels-ids');
        $this->command->info('Voten demo content created.');
    }

    /**
     * Restore the guest-facing channel list on an existing demo database.
     *
     * @return void
     */
    private function ensureSuggestedChannels()
    {
        $now = Carbon::now();
        $channelNames = ['voten', 'technology', 'design', 'science', 'books', 'travel'];
        $channels = DB::table('channels')
            ->whereIn('name', $channelNames)
            ->get(['id', 'name'])
            ->keyBy('name');

        foreach ($channelNames as $index => $channelName) {
            if (! isset($channels[$channelName])) {
                continue;
            }

            DB::table('suggesteds')->updateOrInsert(
                ['channel_id' => $channels[$channelName]->id],
                [
                    'group' => $channelName === 'voten' ? 'community' : 'interests',
                    'language' => 'en',
                    'z_index' => count($channelNames) - $index,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        Cache::forget('default-channels-ids');
    }

    /**
     * Add every seeded channel to the default guest feed.
     *
     * @param array          $channels
     * @param array          $channelDefinitions
     * @param \Carbon\Carbon $now
     *
     * @return void
     */
    private function storeSuggestedChannels(array $channels, array $channelDefinitions, Carbon $now)
    {
        foreach ($channels as $index => $channelId) {
            DB::table('suggesteds')->insert([
                'channel_id' => $channelId,
                'group' => $channelDefinitions[$index][0] === 'voten' ? 'community' : 'interests',
                'language' => 'en',
                'z_index' => count($channels) - $index,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
