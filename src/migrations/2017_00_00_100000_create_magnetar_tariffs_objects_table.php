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
            $table->integer('currency_id')->nullable()->unsigned();
            $table->decimal('price', 7, 2);

            if(config('database.default') == 'pgsql')
                $table->jsonb('data')->nullable();
            else
                $table->json('data')->nullable();

            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('magnetar_tariffs_object_types')->onDelete('set null');
            $table->foreign('currency_id')->references('id')->on('magnetar_tariffs_currencies')->onDelete('set null');
        });

//        TEST DATA
//        $object = new Object();
//        $object->name = 'TEST';
//        $object->data = '{"1": {"active": "true"}, "2": {"count": 50, "active": "true"}, "3": {"active": "true", "period": 10, "period_type": "day"}}';
//        $object->price = 200;
//        $object->type_id = 1;
//        $object->currency_id = 1;
//        $object->save();

    }

    public function down()
    {
        Schema::drop('magnetar_tariffs_objects');
    }
}