<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->primary(['user_id', 'submission_id', 'ip_address']);
            $table->integer('user_id')->unsigned();
            $table->ipAddress('ip_address')->nullable();
            $table->integer('submission_id')->unsigned();
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
        Schema::drop('votes');
    }
}
