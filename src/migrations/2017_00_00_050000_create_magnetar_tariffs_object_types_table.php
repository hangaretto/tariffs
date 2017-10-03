<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Magnetar\Tariffs\Models\ObjectType;

class CreateMagnetarTariffsObjectTypesTable extends Migration
{
    public function up()
    {
        Schema::create('magnetar_tariffs_object_types', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        ObjectType::create(['name' => 'Тариф']);
        ObjectType::create(['name' => 'Пакет']);
    }

    public function down()
    {
        Schema::drop('magnetar_tariffs_object_types');
    }
}