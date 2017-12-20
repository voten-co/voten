<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuggestedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suggesteds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_id')->unsigned()->index();
            $table->string('group')->nullable()->index(); // 'technology', 'lifestyle', etc.
            $table->string('language')->default('en')->index();
            $table->integer('z_index')->default(0);
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
        Schema::dropIfExists('suggesteds');
    }
}
