<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Magnetar\Tariffs\Models\Module;

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

//            $table->decimal('price')->unsigned()->nullable();

            if(config('database.default') == 'pgsql')
                $table->jsonb('price')->nullable();
            else
                $table->json('price')->nullable();

//            $table->integer('currency_id')->nullable()->unsigned();
            $table->timestamps();

//            $table->foreign('currency_id')->references('id')->on('magnetar_tariffs_currencies')->onDelete('set null');
        });

        //        TEST DATA
        $object = new Module();
        $object->name = 'TEST';
        $object->settings = '{"active": true}';
        $object->price = '{"P0Y": {"price": 20}}';
        $object->save();

        $object = new Module();
        $object->name = 'sms';
        $object->settings = '{"count": 1, "active": true}';
        $object->group = 1;
        $object->grade = 1;
        $object->save();

        $object = new Module();
        $object->name = 'test2';
        $object->settings = '{"active": true}';
        $object->price = '{"P0Y": {"price": 25}, "P0Y1M": {"price": 20}, "P0Y2M": {"price": 15}, "P0Y3M": {"price": 12}}';
        $object->save();

    }

    public function down()
    {
        Schema::drop('magnetar_tariffs_modules');
    }
}