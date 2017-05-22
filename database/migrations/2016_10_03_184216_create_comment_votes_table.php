<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_votes', function (Blueprint $table) {
            $table->primary(['user_id', 'comment_id', 'ip_address']);
            $table->integer('user_id')->unsigned();
            $table->ipAddress('ip_address')->nullable();
            $table->integer('comment_id')->unsigned();
            $table->string('type')->default('upvote'); // 1.upvote 2. downvote
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_votes');
    }
}
