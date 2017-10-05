<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Magnetar\Tariffs\Models\Currency;

class CreateMagnetarTariffsCurrenciesTable extends Migration
{
    public function up()
    {
        Schema::create('magnetar_tariffs_currencies', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Currency::create(['name' => 'Рубль']);
    }

    public function down()
    {
        Schema::drop('magnetar_tariffs_currencies');
    }
}