@inject('metodo','CEPROZAC\Http\Controllers\EntradasAgroquimicosController')
@extends('layouts.principal')
@section('contenido')
<div class="pull-left breadcrumb_admin clear_both">
	<div class="pull-left page_title theme_color">
		<h1>Inicio</h1>
		<h2 class="">Salidas Agroquimicos</h2>
	</div>
	<div class="pull-right">
		<ol class="breadcrumb">
			<li ><a style="color: #808080" href="{{url('/almacen/salidas/agroquimicos')}}">Inicio</a></li>
			<li class="active">Reporte de Salida de Almacén de Agroquimicos</a></li>
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
							<h4 class="content-header " style="margin-top: -5px;">&nbsp;&nbsp;<strong>INFORMACION DE SALIDA N°: {{$salidas->idSalida}} </strong></h4>
						</div>
						<div class="btn-group pull-right">
							<b>

								<div class="btn-group" style="margin-right: 10px;">
									 <a class="btn btn-sm btn-success tooltips" href="{{ route('almacen.salidas.agroquimicos.create')}}" style="margin-right: 10px;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Registrar nueva Salida"> <i class="fa fa-plus"></i> Registrar Salida de Almacén</a>

									<a class="btn btn-sm btn-danger tooltips" href="/almacen/salidas/agroquimicos" style="margin-right: 10px;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Cancelar"> <i class="fa fa-times"></i> Salir</a> 
									<a class="btn btn-sm btn btn-info" href="{{URL::action('SalidasAgroquimicosController@pdfsalidaAgroquimicos',$salidas->idSalida)}}" target="_blank" style="margin-right: 10px;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Registrar nueva Entrada"> <i class="fa fa-print"></i>Imprimir Reporte</a>

								</div> 
							</b>
						</div>

					</div>
				</div>
				<div class="porlets-content container clear_both padding_fix">

					<div class="col-lg-6"> 
						<section class="panel default blue_title h4">
							<div class="panel-heading"><span class="semi-bold">Informacion De Salida de Almacén</span> 
							</div>
							<div class="panel-body">
								<table class="table table-striped">
									<tbody>
										<tr>
											<th>Número de Salida: </th>
											<td>{{$salidas->idSalida}}</td>
										</tr>
										<tr>
											<th>Destino:</th>
											<td>{{$salidas->DestinoF}}</td>
										</tr>
										<tr>
											<th>Módulos Aplicados:</th>
											@if($salidas->modulos == "")
											<td>No hay Módulos Seleccionados </td>
											@else
											<td>{{$salidas->modulos}} </td>
											@endif
										</tr>
										<tr>
											<th>Fecha:</th>
											<td>{{$salidas->Fechasalida}}</td>
										</tr>

										<tr>
											<th>Observaciónes:</th>
											<td>{{$salidas->TipoMov}}</td>
										</tr>
										<tr>
											<th>Entrego Producto:</th>
											<td>{{$salidas->nombre1}} {{$salidas->apellido1}}</td>
										</tr>
										<tr>
											<th>Recibio Producto:</th>
											<td>{{$salidas->nombre2}} {{$salidas->apellido2}}</td>
										</tr>
										<tr>


										</tr>
									</tbody>
								</table>
							</div>
						</section>
					</div>
											@foreach($salida as $material)
					<div class="col-lg-6"> 
						<section class="panel default blue_title h4">
							<div class="panel-heading"><span class="semi-bold">Informacion Del Producto</span> 
							</div>
							<div class="panel-body">
								<table class="table table-striped">
									<tbody>
										<tr>
											<th>Producto: </th>
											<td>{{$material->nombreMaterial}}</td>
										</tr>
										<tr>
											<th>Descripción:</th>
											<td>{{$material->descripcion}}</td>
										</tr>
																				<tr>
											<th>Codigo:</th>
											<td>{{$material->codigo}}</td>
										</tr>
										<tr>
											<th>Cantidad de Salida:</th>
											<td><li>{{$metodo->Calcula_Cantidad($material->cantidad , $material->cantidadUnidad , $material->nombreUnidadMedida , $material->UnidadNombre)}}</li>
												<li>{{$metodo->Calcula_Cantidad2($material->cantidad , $material->cantidadUnidad , $material->nombreUnidadMedida , $material->UnidadNombre)}}</li>
												<li>{{$metodo->Calcula_Cantidad3($material->cantidad , $material->cantidadUnidad , $material->nombreUnidadMedida , $material->UnidadNombre)}}</li></td>
											</tr>

											<th>Cantidad Total:</th>
											@if($material->nombreUnidadMedida == "KILOGRAMOS")
											<td>{{$material->cantidad}} GRAMOS</td>

											@elseif($material->nombreUnidadMedida == "LITROS")
											<td>{{$material->cantidad}} MILILITROS</td>


											@elseif($material->nombreUnidadMedida == "METROS")
											<td>{{$material->cantidad}} CENTIMETROS</td>

											@else
											<td>{{$material->cantidad}} UNIDADES</td>


											@endif
										                                        <tr>
                      <th> <div style="visibility: hidden"> Dato Nulo</div></th>
                      <td></td>
                    </tr>



										</tbody>
									</table>
								</div>
							</section>
						</div>
							@endforeach


					</div><!--/porlets-content-->
				</div><!--/block-web-->
			</div><!--/col-md-12-->
		</div><!--/row-->
	</div>


	@endsection
