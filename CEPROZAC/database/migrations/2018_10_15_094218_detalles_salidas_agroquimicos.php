<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DetallesSalidasAgroquimicos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_salidas_agroquimicos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idSalidasAgroquimicos')->unsigned();
            $table->foreign('idSalidasAgroquimicos')->references('id')->on('salidasagroquimicos');
            $table->integer('id_material')->unsigned();
            $table->foreign('id_material')->references('id')->on('almacenagroquimicos');
            $table->integer('cantidad');
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
        Schema::drop('detalles_salidas_agroquimicos');
    }
}
