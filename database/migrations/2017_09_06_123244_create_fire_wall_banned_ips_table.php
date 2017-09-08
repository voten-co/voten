<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFireWallBannedIpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fire_wall_banned_ips', function (Blueprint $table) {
            $table->increments('id');
            $table->ipAddress('ip_address')->unique()->index();
            $table->text('description')->nullable();
            $table->dateTimeTz('unban_at')->nullable();
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
        Schema::dropIfExists('fire_wall_banned_ips');
    }
}
