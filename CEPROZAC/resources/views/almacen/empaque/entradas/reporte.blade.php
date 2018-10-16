@inject('metodo','CEPROZAC\Http\Controllers\entradasempaquescontroller')
@extends('layouts.principal')
@section('contenido')
<div class="pull-left breadcrumb_admin clear_both">
	<div class="pull-left page_title theme_color">
		<h1>Inicio</h1>
		<h2 class="">Entradas Empaque</h2>
	</div>
	<div class="pull-right">
		<ol class="breadcrumb">
			<li ><a style="color: #808080" href="{{url('/almacen/entradas/empaque')}}">Inicio</a></li>
			<li class="active">Reporte de Entrada de Almacén de Empaque</a></li>
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
							<h4 class="content-header " style="margin-top: -5px;">&nbsp;&nbsp;<strong>INFORMACION DE FACTURA N°: {{$entrada->factura}} </strong></h4>
						</div>
						<div class="btn-group pull-right">
							<b>

								<div class="btn-group" style="margin-right: 10px;">
									<a class="btn btn-sm btn-success tooltips" href="{{ route('almacen.entradas.empaque.create')}}" style="margin-right: 10px;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Registrar nueva Salida"> <i class="fa fa-plus"></i> Registrar Entrada de Almacén</a>

									<a class="btn btn-sm btn-danger tooltips" href="/almacen/entradas/empaque" style="margin-right: 10px;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Cancelar"> <i class="fa fa-times"></i> Salir</a>

									<a class="btn btn-sm btn btn-info" href="{{URL::action('entradasempaquescontroller@pdfentradaempaques',$entrada->factura)}}" target="_blank" style="margin-right: 10px;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Registrar nueva Entrada"> <i class="fa fa-print"></i>Imprimir Reporte</a>

								</div> 
							</b>
						</div>

					</div>
				</div>
				<div class="porlets-content container clear_both padding_fix">

					<div class="col-lg-6"> 
						<section class="panel default blue_title h4">
							<div class="panel-heading"><span class="semi-bold">Informacion De Entrada de Almacén</span> 
							</div>
							<div class="panel-body">
								<table class="table table-striped">
									<tbody>
										<tr>
											<th>FACTURA: </th>
											<td>{{$entrada->factura}}</td>
										</tr>
										<tr>
											<th>EMPRESA:</th>
											<td>CEPROZAC</td>
										</tr>
										<tr>
											<th>COMPRADOR:</th>
											<td>{{$entrada->empresaNombre}} </td>
										</tr>
										<tr>
											<th>PROVEEDOR:</th>
											<td>{{$entrada->ProvedorNombre}}</td>
										</tr>

										<tr>
											<th>FECHA:</th>
											<td>{{$entrada->fecha}}</td>
										</tr>
										<tr>
											<th>MONEDA:</th>
											<td>{{$entrada->moneda}}</td>
										</tr>
										<tr>
											<th>RECIBIO COMPRA:</th>
											<td>{{$entrada->nombre1}} {{$entrada->apellido1}}</td>
										</tr>
										<tr>
											<th>RECIBIO EN ALMACÉN:</th>
											<td>{{$entrada->nombre2}} {{$entrada->apellido2}}</td>
										</tr>
										<tr>


										</tr>
									</tbody>
								</table>
							</div>
						</section>
					</div>
					@foreach($data2 as $material)
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
											<th>Cantidad:</th>
											<td><li>{{$metodo->Calcula_Cantidad($material->cantidad , $material->cantidadUnidad , $material->nombreUnidadMedida , $material->UnidadNombre)}}</li>
												<li>{{$metodo->Calcula_Cantidad2($material->cantidad , $material->cantidadUnidad , $material->nombreUnidadMedida , $material->UnidadNombre)}}</li>
												<li>{{$metodo->Calcula_Cantidad3($material->cantidad , $material->cantidadUnidad , $material->nombreUnidadMedida , $material->UnidadNombre)}}</li></td>
											</tr>


											<tr>
												<th>Precio Unitario:</th>
												<td>${{$material->p_unitario}}</td>
											</tr>


											<tr>
												<th>IVA:</th>
												<td>${{$material->iva}}</td>
											</tr>


											<tr>
												<th>Subtotal:</th>
												<td>${{$metodo->CALCULA_SUB($material->cantidad , $material->cantidadUnidad , $material->p_unitario, $material->iva,$material->nombreUnidadMedida)}}</td>
											</tr>
																					                                        <tr>
                      <th> <div style="visibility: hidden"> Dato Nulo</div></th>
                      <td></td>
                    </tr>
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
