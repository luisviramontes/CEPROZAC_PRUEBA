@extends('layouts.principal')
@section('contenido')
<div class="pull-left breadcrumb_admin clear_both">
  <div class="pull-left page_title theme_color">
    <h1>Invernaderos</h1>
    <h2 class="">Invernaderos</h2>
  </div>
  <div class="pull-right">
    <ol class="breadcrumb">
      <li ><a style="color: #808080" href="{{url('/invernaderos')}}">Inicio</a></li>
      <li class="active">Invernaderos</a></li>
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
              <h2 class="content-header" style="margin-top: -5px;">&nbsp;&nbsp;<strong>Invernaderos</strong></h2>
            </div>
            <div class="col-md-5">
              <div class="btn-group pull-right">
                <b>

                  <div class="btn-group" style="margin-right: 10px;">
                    <a class="btn btn-sm btn-success tooltips" href="{{ route('invernaderos.create')}}" style="margin-right: 10px;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Registrar nuevo Invernadero"> <i class="fa fa-plus"></i> Registrar </a>

                    <a class="btn btn-sm btn-warning tooltips" href="{{ route('invernaderos.excel')}}" style="margin-right: 10px;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Descargar"> <i class="fa fa-download"></i> Descargar </a>

                  </div>
                  
                </a>
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
                <th>Nombre </th>
                <th>Ubicación </th>
                <th>Número de Módulos </th>


                <td><center><b>Editar</b></center></td>
                <td><center><b>Borrar</b></center></td>
              </tr>
            </thead>
            <tbody>
              @foreach($invernadero  as $invernaderos)
              <tr class="gradeA">
                <td>{{$invernaderos->nombre}} </td>
                <td>{{$invernaderos->ubicacion}} </td>
                <td>{{$invernaderos->num_modulos}} </td>


                <td> 
                  <center>
                    <a href="{{URL::action('invernaderoscontroller@edit',$invernaderos->id)}}" class="btn btn-primary btn-sm" role="button"><i class="fa fa-edit"></i></a>  
                  </center>
                </td>
                <td>
                  <center>
                   <a class="btn btn-danger btn-sm" data-target="#modal-delete-{{$invernaderos->id}}" data-toggle="modal" style="margin-right: 10px;"  role="button"><i class="fa fa-eraser"></i></a></center>
                 </td>
               </td>
             </tr>
             @include('invernaderos.modal')
             @endforeach
           </tbody>
           <tfoot>
            <tr>
              <th>Nombre </th>
              <th>Ubicación </th>
              <th>Número de Módulos </th>
              <th><center><b>Editar</b></center></th>
              <th><center><b>Borrar</b></center></th>
            </tr>
          </tfoot>
        </table>
      </div><!--/table-responsive-->
    </div><!--/porlets-content-->
  </div><!--/block-web-->
</div><!--/col-md-12-->
</div><!--/row-->
</div>
@stop