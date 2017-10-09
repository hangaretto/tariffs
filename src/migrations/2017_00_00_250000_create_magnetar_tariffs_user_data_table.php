<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagnetarTariffsUserDataTable extends Migration
{
    public function up()
    {
        Schema::create('magnetar_tariffs_user_data', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            if(config('database.default') == 'pgsql')
                $table->jsonb('data')->nullable();
            else
                $table->json('data')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('magnetar_tariffs_user_cards');
    }
}