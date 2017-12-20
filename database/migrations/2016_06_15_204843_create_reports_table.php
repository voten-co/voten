<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportsTable extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('channel_id')->nullable()->unsigned()->index();
            $table->integer('reportable_id')->unsigned()->index();
            $table->string('reportable_type')->index();
            $table->string('subject')->index();
            $table->integer('user_id')->unsigned();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::drop('reports');
    }
}
