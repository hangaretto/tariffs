<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagnetarTariffsUserObjectsTable extends Migration
{
    public function up()
    {
        Schema::create('magnetar_tariffs_user_objects', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('object_id')->nullable()->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('module_id')->unsigned();
//            $table->integer('currency_id')->nullable()->unsigned();

            if(config('database.default') == 'pgsql')
                $table->jsonb('data')->nullable();
            else
                $table->json('data')->nullable();

            $table->decimal('price')->unsigned()->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->dateTime('paid_at')->nullable();

//            $table->integer('period')->nullable()->unsigned();
//            $table->enum('period_type', ['day', 'week', 'month', 'year'])->nullable();

            $table->timestamps();

            $table->foreign('object_id')->references('id')->on('magnetar_tariffs_objects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('module_id')->references('id')->on('magnetar_tariffs_modules')->onDelete('cascade');
//            $table->foreign('currency_id')->references('id')->on('magnetar_tariffs_currencies')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::drop('magnetar_tariffs_user_objects');
    }
}