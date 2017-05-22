<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionDownvotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submission_downvotes', function (Blueprint $table) {
            $table->primary(['user_id', 'submission_id']);
            $table->integer('user_id')->unsigned();
            $table->ipAddress('ip_address')->index()->nullable();
            $table->integer('submission_id')->unsigned();
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
        Schema::dropIfExists('submission_downvotes');
    }
}
