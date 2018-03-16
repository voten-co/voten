<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateChannelsTable extends Migration
{
    public function up()
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique()->index();
            $table->string('language')->default('en');
            $table->text('description');
            $table->boolean('nsfw')->default(0);
            $table->string('color')->default('Dark');
            $table->string('avatar')->default('/imgs/channel-avatar.png');
            $table->boolean('public')->default(1);
            $table->boolean('active')->default(1);
            $table->integer('subscribers')->default(1);
            $table->json('settings')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('channels');
    }
}
