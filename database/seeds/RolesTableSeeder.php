<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new \App\Role();
        $role->user_id     = 1;
        $role->role        = 'administrator';
        $role->category_id = 1;
        $role->save();
    }
}
