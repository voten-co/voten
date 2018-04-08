<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropUnusedVotingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('comment_downvotes');
        Schema::dropIfExists('submission_downvotes');
        Schema::dropIfExists('submission_votes');
        Schema::dropIfExists('comment_votes');
        Schema::dropIfExists('votes');
        Schema::dropIfExists('help_votes');
        Schema::dropIfExists('helps');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
