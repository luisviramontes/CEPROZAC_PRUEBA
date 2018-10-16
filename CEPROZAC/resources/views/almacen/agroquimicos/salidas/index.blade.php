@inject('metodo','CEPROZAC\Http\Controllers\EntradasAgroquimicosController')
@extends('layouts.principal')
@section('contenido')
<div class="pull-left breadcrumb_admin clear_both">
  <div class="pull-left page_title theme_color">
    <h1>Almacén de Agroquímicos</h1>
    <h2 class="">Salidas</h2>
  </div>
  <div class="pull-right">
    <ol class="breadcrumb">
      <li ><a style="color: #808080" href="{{url('almacenes/agroquimicos')}}">Inicio</a></li>
      <li class="active">Salidas de Almacén de Agroquímicos</a></li>
    </ol>
  </div>
</div>
<div class="container clear_both padding_fix">
  <div class="row">
    <div class="col-md-12">
      <div class="block-web">
        <div class="header">
          <div class="row" style="margin-top: 15px; margin-bottom: 12px;">
            <div class="col-sm-7">
              <div class="actions"> </div>
              <h2 class="content-header " style="margin-top: -5px;">&nbsp;&nbsp;<strong>Salidas de Almacén de Agroquímicos </strong></h2>
            </div>
            <div class="col-md-5"> 
              <div class="btn-group pull-right">
                <b>

                  <div class="btn-group" style="margin-right: 10px;">
                   <a class="btn btn-sm btn-success tooltips" href="{{ route('almacen.salidas.agroquimicos.create')}}" style="margin-right: 10px;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Registrar nueva Salida"> <i class="fa fa-plus"></i> Registrar Salida de Almacén</a>

                   <a class="btn btn-sm btn-warning tooltips" href="{{ route('almacen.agroquimicos.salidas.excel')}}" style="margin-right: 10px;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Descargar"> <i class="fa fa-download"></i> Descargar </a>



                 </div>
                 <div class="text-success" id='result'>
                  @if(Session::has('message'))
                  {{Session::get('message')}}
                  @endif
                </div>

              </b>
            </div>
          </div>
        </div>
      </div>

      <div class="porlets-content">
        <div class="table-responsive">
          <table  class="display table table-bordered table-striped" id="dynamic-table">
            <thead>
              <tr>
                <th>N°Salida </th>
                <th>Nombre de Material</th>
                <th> Cantidad</th>
                <th>Destino </th>
                <th>Módulos Aplicados </th>  
                <th>Fecha de Salida</th> 
                <td><center><b>Ver</b></center></td> 
                <td><center><b>Editar</b></center></td>   
                <td><center><b>Borrar</b></center></td>                            
              </tr>
            </thead>
            <tbody>
              @foreach($salida  as $salidas)
              <tr class="gradeA">
              <td>{{$salidas->idSalida}} </td>
                <td>{{$salidas->nombreMaterial}} </td>
                <td><li>{{$metodo->Calcula_Cantidad($salidas->cantidad , $salidas->cantidadUnidad , $salidas->nombreUnidadMedida , $salidas->UnidadNombre)}}</li>
                 <li>{{$metodo->Calcula_Cantidad2($salidas->cantidad , $salidas->cantidadUnidad , $salidas->nombreUnidadMedida , $salidas->UnidadNombre)}}</li>
                 <li>{{$metodo->Calcula_Cantidad3($salidas->cantidad , $salidas->cantidadUnidad , $salidas->nombreUnidadMedida , $salidas->UnidadNombre)}}</li></td>
                 <td>{{$salidas->DestinoF}} </td>
                 @if($salidas->modulos == "")
                 <td>No hay Módulos Seleccionados </td>
                 @else
                 <td>{{$salidas->modulos}} </td>
                 @endif
                 <td>{{$salidas->Fechasalida}} </td>
                 <td >
       <a href="{{URL::action('SalidasAgroquimicosController@verSalidaAgroquimicos',$salidas->idSalida)}}" class="btn btn-primary btn-sm" role="button"><i class="fa fa-eye"></i></a>    </td>
                </td>
                <td> 
                  <center>
                    <a href="{{URL::action('SalidasAgroquimicosController@edit',$salidas->idSalida)}}" class="btn btn-primary btn-sm" role="button"><i class="fa fa-edit"></i></a>  
                  </center>
                </td>
                <td> <a class="btn btn-danger btn-sm" data-target="#modal-delete-{{$salidas->idSalida}}" data-toggle="modal" style="margin-right: 10px;"  role="button"><i class="fa fa-eraser"></i></a>
                </td>
              </td>
            </td>

          </tr>
          @include('almacen.agroquimicos.salidas.modal')


          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th>N°Salida </th>
            <th>Nombre de Material</th>
            <th> Cantidad</th>
            <th>Destino </th>
            <th>Módulos Aplicados </th>
            <th>Fecha de Salida</th>  
            <td><center><b>Ver</b></center></td> 
            <td><center><b>Editar</b></center></td>   
            <td><center><b>Borrar</b></center></td>  
          </tr>
        </tfoot>
      </table>
    </div><!--/table-responsive-->
  </div><!--/porlets-content-->
</div><!--/block-web-->
</div><!--/col-md-12-->
</div><!--/row-->
</div>


@endsection