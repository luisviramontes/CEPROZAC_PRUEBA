<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntradaAlmacenMateriales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('entradaalmacenmateriales', function (Blueprint $table) {
         $table->increments('id');
         $table->integer('provedor')->unsigned();
         $table->foreign('provedor')->references('id')->on('provedor_materiales');
         $table->date('fecha');
         $table->string('factura');
         $table->integer('comprador')->unsigned();
         $table->foreign('comprador')->references('id')->on('empresas_ceprozac');
         $table->string('moneda');
         $table->integer('entregado')->unsigned();
         $table->foreign('entregado')->references('id')->on('empleados');
         $table->integer('recibe_alm')->unsigned();
         $table->foreign('recibe_alm')->references('id')->on('empleados');
         $table->string('observacionesc')->nullable();
         $table->string('estado')->nullable(); 
         $table->timestamps();
     });
      /* DB::unprepared('

        CREATE TRIGGER inserta_entrada AFTER INSERT ON entradaalmacenmateriales
        FOR EACH ROW BEGIN
        UPDATE almacenmateriales SET cantidad=cantidad+NEW.cantidad
        WHERE almacenmateriales.id=NEW.id_material;

        END

        ');*/
   }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('entradaalmacenmateriales');
    }
}
