<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CantidadUnidadesEmp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cantidad_unidades_emp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idProducto')->unsigned();
            $table->foreign('idProducto')->references('id')->on('almacenempaque');
            $table->integer('idMedida')->unsigned();
            $table->foreign('idMedida')->references('id')->on('unidades_medidas');
            $table->double('cantidad')->nullable();
            $table->string('estado');
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
        Schema::drop('cantidad_unidades_emp');
    }
}
