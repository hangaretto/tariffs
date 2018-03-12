<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMagnetarTariffsUserBalancesTable extends Migration
{
    public function up()
    {
        Schema::create('magnetar_tariffs_user_balances', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->decimal('amount', 8, 2);
            $table->text('info');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::drop('magnetar_tariffs_user_balances');
    }
}