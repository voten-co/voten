<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('title');
            $table->string('type');
            $table->json('data');
            $table->string('channel_name')->index();
            $table->float('rate')->index()->nullable();

            // Used for resubmit feature.
            $table->integer('resubmit_id')->unsigned()->index()->nullable();

            $table->integer('user_id')->unsigned()->index();
            $table->boolean('nsfw')->default(0);
            $table->integer('channel_id')->unsigned();

            $table->integer('upvotes')->default(1);
            $table->integer('downvotes')->default(0);
            $table->integer('comments_number')->default(0);

            // approved by moderators so it can't be reported
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('submissions');
    }
}
