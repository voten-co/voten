<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 25)->unique()->index();
            $table->string('name')->nullable();
            $table->string('website')->nullable();
            $table->string('location')->nullable();
            $table->string('avatar')->default('/imgs/default-avatar.png');
            $table->string('color')->default('Dark');
            $table->string('bio')->nullable();
            $table->boolean('active')->default(1); // used for banning?
            $table->boolean('confirmed')->default(0); // Email confirmed
            $table->string('email')->unique()->nullable();
            $table->json('settings')->nullable();
            $table->json('info')->nullable();
            $table->boolean('verified')->default(0);
            $table->integer('submission_xp')->default(0); // used for backup (in case redis get's flushed)
            $table->integer('comment_xp')->default(0); // used for backup (in case redis get's flushed)
            $table->string('password');
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('users');
    }
}
