<style type="text/css">
  textarea {
    resize: none;
  }
</style>
@extends('layouts.principal')
@section('contenido')
<div class="pull-left breadcrumb_admin clear_both">
  <div class="pull-left page_title theme_color">
    <h1>Inicio</h1>
    <h2 class="">Bancos</h2>
  </div>
  <div class="pull-right">
    <ol class="breadcrumb">
      <li><a style="color: #808080" href="{{url('bancos')}}">Inicio</a></li>
      <li><a style="color: #808080" href="{{url('bancos')}}">Precio Basculas</a></li>
    </ol>
  </div>
</div>
<div class="container clear_both padding_fix">
  <div class="row">
    <div class="col-md-12">
      <div class="block-web">
        <div class="header">
          <div class="row" style="margin-top: 15px; margin-bottom: 12px;">
            <div class="col-sm-8">
              <div class="actions"> </div>
              <h2 class="content-header" style="margin-top: -5px;"><strong>Registrar Precio Bascula</strong></h2> 
            </div>
            <div class="col-md-4">
              <div class="btn-group pull-right">
                <div class="actions"> 
                </div>
              </div>
            </div>    
          </div>
        </div>
        <div class="porlets-content">
        <form action="{{url('precioBasculas',[$precioBascula->id])}}" method="post" class="form-horizontal row-border" parsley-validate novalidate>
            {{csrf_field()}}
            <input type="hidden" name="_method" value="PUT">
            <div class="form-group">
              <label class="col-sm-3 control-label">Tipo Vehiculo: <strog class="theme_color">*</strog></label>
              <div class="col-sm-6"> 
                <input name="tipoVehiculo" type="text"  onchange="mayus(this);"  class="form-control" maxlength="30" parsley-rangelength="[1,50]"  required value="{{$precioBascula->tipoVehiculo}}" placeholder="Ingrese nombre de el Banco"/>
              </div>
            </div>


            <div class="form-row">    
              <label class="col-sm-3 control-label">Precio: <strog class="theme_color">*</strog></label>
              <div class="col-sm-4">
                <div class="input-group">
                 <div class="input-group-addon">$</div>

                 <input name="precioBascula" maxlength="9" type="text"   step="100" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency" required value="{{$precioBascula->precioBascula}}" placeholder="Ingrese costo de Pesaje" onkeypress=" return soloNumeros(event);"/>
               </div>
             </div>
           </div>



           <div class="form-group">
            <div class="col-sm-offset-7 col-sm-5">
              <button type="submit" class="btn btn-primary">Guardar</button>
              <a href="{{url('/precioBasculas')}}" class="btn btn-default"> Cancelar</a>
            </div>
          </div><!--/form-group-->
        </form>
      </div><!--/porlets-content-->
    </div><!--/block-web-->
  </div><!--/col-md-12-->
</div><!--/row-->
</div><!--/container clear_both padding_fix-->

@endsection
