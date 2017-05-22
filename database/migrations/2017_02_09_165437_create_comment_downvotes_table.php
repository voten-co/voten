<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentDownvotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_downvotes', function (Blueprint $table) {
            $table->primary(['user_id', 'comment_id']);
            $table->integer('user_id')->unsigned();
            $table->ipAddress('ip_address')->index()->nullable();
            $table->integer('comment_id')->unsigned();
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
        Schema::dropIfExists('comment_downvotes');
    }
}
