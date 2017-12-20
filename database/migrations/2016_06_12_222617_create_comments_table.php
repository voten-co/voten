<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentsTable extends Migration
{
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('submission_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->integer('parent_id')->unsigned()->index()->default(0);
            $table->integer('channel_id')->unsigned()->index();
            $table->integer('level')->default(0);

            $table->float('rate')->index();
            $table->integer('upvotes')->default(1);
            $table->integer('downvotes')->default(0);
            $table->text('body');

            // approved by moderators so it can't be reported
            $table->timestamp('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('comments');
    }
}
