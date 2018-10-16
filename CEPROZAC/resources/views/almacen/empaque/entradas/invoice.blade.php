@inject('metodo','CEPROZAC\Http\Controllers\entradasempaquescontroller')
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>FACTURA N° {{$entrada->factura}}</title>
  <link rel="stylesheet" href="css/plantilla.css" media="all" />
</head>
<body>
  <header class="clearfix">
    <div id="logo">
      <img src="images/logoCeprozac.png"  width="100" height="100"/>
    </div>
    <h1>FACTURA N° {{$entrada->factura}}</h1>
    <div id="project" >
      <div><span>EMPRESA: </span> CEPROZAC</div>
      <div><span>COMPRADOR: </span> {{$entrada->empresaNombre}}</div> 
      <div><span>PROVEEDOR: </span> {{$entrada->ProvedorNombre}}</div> 
      <div><span>DOMICILIO: </span>{{$entrada->ProvedorDireccion}}</div> 
      <div><span>EMAIL: </span> <a href="{{$entrada->ProvedorEmail}}">{{$entrada->ProvedorEmail}}</a></div>
      <div><span>TEL: </span>{{$entrada->ProvedorTelefono}}</div>
    </div>
 
    <div id="project2" align="right" >
      <div><span>FACTURA: </span> {{$entrada->factura}}</div>
      <div><span>FECHA: </span> {{$entrada->fecha}}</div>
      <div><span>MONEDA: </span>{{$entrada->moneda}}</div>
      <div><span>RECIBIO COMPRA: </span>{{$entrada->nombre1}} {{$entrada->apellido1}}</div>
      <div><span>RECIBIO EN ALMACÉN:</span>{{$entrada->nombre2}} {{$entrada->apellido2}}</div>
    </div>
  </header>


  <main>

    <table name="table_producto" id="table_producto" border="0" cellspacing="0" cellpadding="0">
      <thead>
        <tr>
          <th class="no">PRODUCTO</th>
          <th class="desc">CANTIDAD</th>
          <th class="unit">PRECIO UNITARIO</th>
          <th class="unit">IVA</th>
          <th class="total">SUBTOTAL</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data2 as $datos)
        <tr>
          <td class="no">{{$datos->nombreMaterial}}</td>
            <td><li>{{$metodo->Calcula_Cantidad($datos->cantidad , $datos->cantidadUnidad , $datos->nombreUnidadMedida , $datos->UnidadNombre)}}</li>
         <li>{{$metodo->Calcula_Cantidad2($datos->cantidad , $datos->cantidadUnidad , $datos->nombreUnidadMedida , $datos->UnidadNombre)}}</li>
         <li>{{$metodo->Calcula_Cantidad3($datos->cantidad , $datos->cantidadUnidad , $datos->nombreUnidadMedida , $datos->UnidadNombre)}}</li></td>
          <td class="unit">${{$datos->p_unitario}}</td>
          <td class="total">${{$datos->iva}} </td>
          <td class="total">${{$metodo->CALCULA_SUB($datos->cantidad , $datos->cantidadUnidad , $datos->p_unitario, $datos->iva,$datos->nombreUnidadMedida)}} </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
<hr/><hr/>
          <td colspan="3"></td>
          <td>TOTAL</td>
          <td>${{$metodo->CALCULA_TOTAL($datos->idEntradaMaterial)}} </td>
        </tr>
      </tfoot>
    </table>
        <div><?php echo DNS2D::getBarcodeHTML('{$entrada->factura}', "QRCODE",3,3);?></div>
<br/>
        <div><?php echo DNS1D::getBarcodeHTML('{$entrada->factura}', "C128",1,40);?></div>
  </body>
  </html>
