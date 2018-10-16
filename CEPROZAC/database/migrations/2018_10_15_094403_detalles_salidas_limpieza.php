<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DetallesSalidasLimpieza extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_salidas_limpieza', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idSalidaLimpieza')->unsigned();
            $table->foreign('idSalidaLimpieza')->references('id')->on('salidasalmacenlimpieza');
            $table->integer('id_material')->unsigned();
            $table->foreign('id_material')->references('id')->on('almacenlimpieza');
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
        Schema::drop('detalles_salidas_limpieza');
    }
}
