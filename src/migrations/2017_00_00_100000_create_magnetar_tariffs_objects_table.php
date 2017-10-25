<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Magnetar\Tariffs\Models\Object;

class CreateMagnetarTariffsObjectsTable extends Migration
{
    public function up()
    {
        Schema::create('magnetar_tariffs_objects', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name');
            $table->integer('type_id')->nullable()->unsigned();
//            $table->integer('currency_id')->nullable()->unsigned();
//            $table->decimal('price', 7, 2);

            if(config('database.default') == 'pgsql')
                $table->jsonb('periods')->nullable();
            else
                $table->json('periods')->nullable();

            if(config('database.default') == 'pgsql')
                $table->jsonb('data')->nullable();
            else
                $table->json('data')->nullable();

            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('magnetar_tariffs_object_types')->onDelete('set null');
//            $table->foreign('currency_id')->references('id')->on('magnetar_tariffs_currencies')->onDelete('set null');
        });

//        TEST DATA
//        "2": {"price": {"0": {"price": 1}, "500": {"price": 0.7}, "1000": {"price": 0.5}}, "active": true},
//        "2": {"price": 1, "active": "true"},
//        $object = new Object();
//        $object->name = 'TEST';
//        $object->periods = '{"P0Y": {"active": true}, "P0Y1M": {"active": true}, "P0Y2M": {"active": true}}';
//        $object->data = '{
//            "1": {"active": true},
//            "2": {"base_price": 100, "count": 50, "active": true, "refresh_period": "P1M"},
//            "3": {"price": {"P0Y": {"price": 25}, "P0Y1M": {"price": 20}, "P0Y2M": {"price": 15}}, "active": true}
//        }';
//        $object->type_id = 1;
//        $object->save();
//
//        $object = new Object();
//        $object->name = 'TEST1';
//        $object->periods = null;
//        $object->data = '{
//            "1": {"active": true},
//            "2": {"base_price": 100, "count": 50, "active": true, "refresh_period": "P1M"},
//            "3": {"price": {"P0Y": {"price": 25}, "P0Y1M": {"price": 20}, "P0Y2M": {"price": 15}}, "active": true}
//        }';
//        $object->type_id = 1;
//        $object->save();

    }

    public function down()
    {
        Schema::drop('magnetar_tariffs_objects');
    }
}