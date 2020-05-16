<?php

use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('channels')->insert([
            'name' => str_slug('TestChannel', ''),
            'description' => "First channel used for testing",
            'nsfw' => 0,
            'avatar' => '/imgs/channel-avatar.png',
            'color' => 'Blue',
        ]);
    }
}
