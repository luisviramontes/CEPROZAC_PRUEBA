<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DetallesSalidasMateriales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_salidas_materiales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idSalidaMaterial')->unsigned();
            $table->foreign('idSalidaMaterial')->references('id')->on('salidasalmacenmaterial');
            $table->integer('id_material')->unsigned();
            $table->foreign('id_material')->references('id')->on('almacenmateriales');
            $table->integer('cantidad');
            $table->double('p_unitario'); 
            $table->double('iva');
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
        Schema::drop('detalles_salidas_materiales');
    }
}
