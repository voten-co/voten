<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_votes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('help_id')->unsigned()->index();
            $table->ipAddress('ip_address')->index();
            $table->string('type');

            $table->unique(['help_id', 'ip_address', 'type']);
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
        Schema::dropIfExists('help_votes');
    }
}
