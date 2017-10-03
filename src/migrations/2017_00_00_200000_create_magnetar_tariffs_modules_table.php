<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagnetarTariffsModulesTable extends Migration
{
    public function up()
    {
        Schema::create('magnetar_tariffs_modules', function(Blueprint $table)
        {

            $table->increments('id');
            $table->integer('group')->unsigned()->nullable();
            $table->integer('grade')->unsigned()->nullable();
            $table->string('name');

            if(config('database.default') == 'pgsql')
                $table->jsonb('settings')->nullable();
            else
                $table->json('settings')->nullable();

            $table->decimal('price')->unsigned()->nullable();
            $table->integer('currency_id')->nullable()->unsigned();
            $table->timestamps();

            $table->foreign('currency_id')->references('id')->on('magnetar_tariffs_currencies')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::drop('magnetar_tariffs_modules');
    }
}