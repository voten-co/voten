<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new \App\User();
        $admin->username  = 'admin';
        $admin->name      = 'Admin';
        $admin->password  = bcrypt('password');
        $admin->confirmed = 1;
        $admin->verified  = 1;
        $admin->email     = 'admin@admin.com';
        $admin->info      = '{"twitter": null, "website": null}';
        $admin->settings  = '{"font": "Lato", "nsfw": false, "nsfw_media": false, "sidebar_color": "Gray", "notify_comments_replied": true, "notify_submissions_replied": true, "submission_small_thumbnail": true, "exclude_upvoted_submissions": false, "exclude_downvoted_submissions": true}';
        $admin->save();
    }
}
