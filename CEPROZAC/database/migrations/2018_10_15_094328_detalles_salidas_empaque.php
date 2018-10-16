<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DetallesSalidasEmpaque extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_salidas_empaque', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idSalidaEmpaque')->unsigned();
            $table->foreign('idSalidaEmpaque')->references('id')->on('salidasempaques');
            $table->integer('id_material')->unsigned();
            $table->foreign('id_material')->references('id')->on('almacenempaque');
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
        Schema::drop('detalles_salidas_empaque');
    }
}
