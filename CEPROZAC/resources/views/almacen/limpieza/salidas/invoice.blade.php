@inject('metodo','CEPROZAC\Http\Controllers\EntradasAlmacenLimpiezaController')
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>SALIDA N°: {{$salidas->idSalida}}</title>
	<link rel="stylesheet" href="css/plantilla.css" media="all" />
</head>
<body>
	<header class="clearfix">
		<div id="logo">
			<img src="images/logoCeprozac.png"  width="100" height="100"/>
		</div>
		<h1>INFORMACION DE SALIDA N°: {{$salidas->idSalida}}</h1>
		<div id="project" >
			<div><span>Número de Salida: </span> {{$salidas->idSalida}}</div>
			<div><span>Destino: </span> {{$salidas->DestinoF}}</div> 
				<div><span>Fecha: </span>{{$salidas->Fechasalida}}</div> 
				<div><span>Observaciónes: </span> {{$salidas->TipoMov}}</div>
				<div><span>Entrego Producto: </span>{{$salidas->nombre1}} {{$salidas->apellido1}}</div>
				<div><span>Recibio Producto: </span>{{$salidas->nombre2}} {{$salidas->apellido2}}</div>
			</div>

		</header>


		<main>

			<table name="table_producto" id="table_producto" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="no">PRODUCTO</th>
						<th class="desc">CANTIDAD</th>
						<th class="unit">CANTIDAD TOTAL</th>
					</tr>
				</thead>
				<tbody>
					@foreach($salida as $datos)
					<tr>
						<td class="no">{{$datos->nombreMaterial}}</td>
						<td><li>{{$metodo->Calcula_Cantidad($datos->cantidad , $datos->cantidadUnidad , $datos->nombreUnidadMedida , $datos->UnidadNombre)}}</li>
							<li>{{$metodo->Calcula_Cantidad2($datos->cantidad , $datos->cantidadUnidad , $datos->nombreUnidadMedida , $datos->UnidadNombre)}}</li>
							<li>{{$metodo->Calcula_Cantidad3($datos->cantidad , $datos->cantidadUnidad , $datos->nombreUnidadMedida , $datos->UnidadNombre)}}</li></td>

							@if($datos->nombreUnidadMedida == "KILOGRAMOS")
							<td>{{$datos->cantidad}} GRAMOS</td>
							@elseif($datos->nombreUnidadMedida == "LITROS")
							<td>{{$datos->cantidad}} MILILITROS</td>


							@elseif($datos->nombreUnidadMedida == "METROS")
							<td>{{$datos->cantidad}} CENTIMETROS</td>

							@else
							<td>{{$datos->cantidad}} UNIDADES</td>							


						</tr>
						@endif
						@endforeach
					</tbody>
					<tfoot>
			
					</tfoot>
				</table>
				<div><?php echo DNS2D::getBarcodeHTML('{$salidas->idSalida}', "QRCODE",3,3);?></div>
				<br/>
				<div><?php echo DNS1D::getBarcodeHTML('{$salidas->idSalida}', "C128",1,40);?></div>
			</body>
			</html>
