@inject('metodo','CEPROZAC\Http\Controllers\entradasempaquescontroller')
@extends('layouts.principal')
@section('contenido')
<div class="pull-left breadcrumb_admin clear_both">
  <div class="pull-left page_title theme_color">

    <h1>Inicio</h1>
    <h2 class="">Almacén de Empaque</h2>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  </div>
  <div class="pull-right">
    <ol class="breadcrumb">
      <li><a style="color: #808080" href="{{url('almacenes/empaque')}}">Inicio</a></li>
      <li><a style="color: #808080" href="{{url('almacen/salidas/empaque')}}">Salidas de Almacén de Empaque</a></li>
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
              <h2 class="content-header" style="margin-top: -5px;"><strong>Editar Salida de Empaque: {{$salida->idSalida}} </strong></h2>
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

         <div class="text-success" id='result'>
          @if(Session::has('message'))
          {{Session::get('message')}}
          @endif
        </div>
        <form action="{{url('/almacen/salidas/empaque', [$salida->idSalida])}}" method="post" class="form-horizontal row-border" parsley-validate novalidate files="true" enctype="multipart/form-data" accept-charset="UTF-8">
          {{csrf_field()}}
          <input type="hidden" name="_method" value="PUT">




          <div class="form-group">
            <label class="col-sm-3 control-label">Entrego : <strog class="theme_color">*</strog></label>
            <div class="col-sm-6">
              <select name="entrego" id="entrego" class="form-control select2" required>  

               @foreach($empleado as $emp)

               @if($emp->id == $salida->entrego)
               <option value="{{$emp->id}}" selected>{{$emp->nombre}} {{$emp->apellidos}} </option>
               @else
               <option value="{{$emp->id}}">
                 {{$emp->nombre}} {{$emp->apellidos}} 
               </option>
               @endif             
               @endforeach               
             </select>
             <span id="errorentrego" style="color:#FF0000;"></span>
             <div class="help-block with-errors"></div>
           </div>
         </div>




         <div class="form-group">
          <label class="col-sm-3 control-label">Recibio : <strog class="theme_color">*</strog></label>
          <div class="col-sm-6">
            <select name="recibio" id="recibio"   class="form-control select2" required>  
             @foreach($empleado as $emp)

             @if($emp->id == $salida->recibio)
             <option value="{{$emp->id}}" selected>{{$emp->nombre}} {{$emp->apellidos}} </option>
             @else
             <option value="{{$emp->id}}">
               {{$emp->nombre}} {{$emp->apellidos}} 
             </option>
             @endif             
             @endforeach              
           </select>
           <div class="help-block with-errors"></div>
         </div>
       </div>



       <div class="form-group">
        <label class="col-sm-3 control-label">Observaciónes: <strog class="theme_color"></strog></label>
        <div class="col-sm-6">
          <input name="movimiento" id="movimiento" value="{{$salida->tipo_movimiento}}"  type="text"  maxlength="35" onchange="mayus(this);"  class="form-control" onkeypress=" return soloLetras(event);"  placeholder="Ingrese el Tipo de Movimiento Realizado"/>
        </div>
      </div>

      <div class="form-group">
        <label class="col-sm-3 control-label">Destino : <strog class="theme_color">*</strog></label>
        <div class="col-sm-6">
          <select name="destino" id="destino"   class="form-control select2" >  
            @foreach($almacenes as $limp)
            @if ($limp->nombre == $salida->destino)
            <option value="{{$limp->nombre}}" selected>{{$limp->nombre}} </option>
            @else
            <option value="{{$limp->nombre}}">{{$limp->nombre}} </option>
            @endif
            @endforeach              
          </select>
          <div class="help-block with-errors"></div>
        </div>
      </div> 

      <div class="form-group">
        <label class="col-sm-3 control-label">Fecha de Salida: <strog class="theme_color">*</strog></label>
        <div class="col-sm-6">

         <input type="date" name="fecha" id="fecha" value="{{$salida->fecha}}" class="form-control mask" >
       </div>
     </div>

     <div class="col-lg-4 col-lg-offset-4">
       <div class="form-group">
        <label class="col-sm-6 control-label">Buscar Codigo de Barras: <strog class="theme_color">*</strog></label>
        <div class="col-sm-6">
          <input  id="codigo" value="" onkeypress="return teclas(event);" name="codigo" type="text"  maxlength="35"  class="form-control"  placeholder="Ingrese el Codigo de Barras"/>
        </div>
      </div>
    </div>


    <div class="container clear_both padding_fix">
      <div class="block-web">
       <div class="row">
        <div class="panel panel-primary"> 

          <div class="panel-body">


      <div class="col-sm-3">
        <div class="form-group"> 
          <label for="material">Material </label>
          <select name="id_materialk"   class="form-control select"  value="id_materialk" data-live-search="true"   id="id_materialk" >  
            @foreach($materiales as $mat)
            <option value="{{$mat->cantidad}}_{{$mat->descripcion}}_{{$mat->codigo}}_{{$mat->id}}_{{$mat->nombre}}_{{$mat->idUnidadMedida}}_{{$mat->nombreUnidad}}_{{$mat->NombreUnidadP}}_{{$mat->cantidadMedida}}">
             {{$mat->nombre}}
           </option>
           @endforeach              
         </select>
         <div class="help-block with-errors"></div>
       </div>
     </div><!--/form-group--> 



     <div class="col-sm-3">
       <div class="form-group"> 
        <label for="descripcion">Descripción </label>
        <input name="descripcion" id="descripcion" disabled class="form-control" />
      </div>    
    </div>  

    <div class="col-sm-3">
      <div class="form-group"> 
        <label for="cantidad">Cantidad Actual en Almacén </label>
        <input name="cantidadp" id="cantidadp" disabled class="form-control" />
      </div>    
    </div>   



  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label">Unidad de Medida <strog class="theme_color">*</strog></label>
    <div class="col-sm-3">
      <div class="input-group" >
        <div class="input-group-addon" >Completas</div>
        <input name="unidadesCompletas" id="unidadesCompletas"  parsley-range="[0,500]" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency" value="" placeholder="3" onkeypress=" return soloNumeros(event);"/>
        <span id="errorUnidad" style="color:#FF0000;"></span>
      </div>
    </div>
    <div class="col-sm-3">
      <input id="unidadAux" name="unidadAux"  value="Medida" class="form-control currency" readonly="" />
    </div>
  </div>
</div>

<div class="form-group">    
  <label class="col-sm-3 control-label">Unidades Incompletas: <strog class="theme_color">*</strog></label>

  <div class="col-sm-3">
    <div class="input-group" >
      <div class="input-group-addon" id="unidadCentral">KILOGRAMOS</div>
      <input id="Medida" name="unidadCentral"  parsley-range="[0,500]" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency" value="" placeholder="3" onkeypress=" return soloNumeros(event);"/>
    </div>
  </div>
  <div class="col-sm-3">
    <div class="input-group" >
      <div class="input-group-addon" id="unidadDeMedida">Gramos</div>
      <input  name="unidadDeMedida"  parsley-range="[0,500]" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency"   id="unidadMinima" placeholder="3" onkeypress=" return soloNumeros(event);"/>
    </div>
  </div>
</div>



</div>
<div class="col-sm-3">
  <div class="form-group"> 
    <button type="button" id="btn_add" onclick="agregar();" class="btn btn-primary">Agregar</button>
  </div>
</div>




<div class="form-group"  class="table-responsive"> 
  <table id="detalles" name="detalles[]" value="" class="table table-responsive-xl table-bordered">
    <thead style="background-color:#A9D0F5">
      <th>Opciones</th>
      <th>N°Articulo</th>
      <th>Articulo</th>
      <th>Cantidad</th>
      <th>Total</th>


    </thead>
    <tfoot>
      @foreach($salidas  as $salidas2)
      <tr class="gradeA">
        <td><input type="button" value="Eliminar"  onClick="eliminarFila(this.parentNode.parentNode.rowIndex);"> </td>
        <td>{{$salidas2->id_material}}</td>
        <td>{{$salidas2->nombreMaterial}}</td>
        <td><li>{{$metodo->Calcula_Cantidad($salidas2->cantidad , $salidas2->cantidadUnidad , $salidas2->nombreUnidadMedida , $salidas2->UnidadNombre)}}</li>
         <li>{{$metodo->Calcula_Cantidad2($salidas2->cantidad , $salidas2->cantidadUnidad , $salidas2->nombreUnidadMedida , $salidas2->UnidadNombre)}}</li>
         <li>{{$metodo->Calcula_Cantidad3($salidas2->cantidad , $salidas2->cantidadUnidad , $salidas2->nombreUnidadMedida , $salidas2->UnidadNombre)}}</li></td>
         @if($salidas2->nombreUnidadMedida == "KILOGRAMOS")
         <td>{{$salidas2->cantidad}} GRAMOS</td>

         @elseif($salidas2->nombreUnidadMedida == "LITROS")
         <td>{{$salidas2->cantidad}} MILILITROS</td>


         @elseif($salidas2->nombreUnidadMedida == "METROS")
         <td>{{$salidas2->cantidad}} CENTIMETROS</td>

         @else
         <td>{{$salidas2->cantidad}} UNIDADES</td>

         @endif

       </td>
     </td>
   </tr>    

   @endforeach
 </tfoot>
</table>

    <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
     <div class="form-group"> 
      <label for="total">Total de Elementos </label>
      <input name="total" id="total" type="number"  class="form-control"  readonly/>
    </div>    
  </div>  

  <div class="form-group">
    <div class="col-sm-6">
      <input  id="codigo2" value="" name="codigo2[]" type="hidden"  maxlength="50"  class="form-control"  placeholder="Ingrese el Codigo de Barras"/>
    </div>
  </div>

</div>

</div>


</div>




<div class="form-group">
  <div class="col-sm-offset-7 col-sm-5">
    <button type="submit" onclick="return save();" class="btn btn-primary">Guardar</button>
    <a href="{{url('/almacen/salidas/empaque')}}" class="btn btn-default"> Cancelar</a>
  </div>
</div><!--/form-group-->
</form>
</div><!--/porlets-content-->
</div><!--/block-web-->
</div><!--/col-md-12-->
</div><!--/row-->
</div><!--/container clear_both padding_fix-->
</html> 
<script type="text/javascript">

  function teclas(event) {
    tecla=(document.all) ? event.keyCode : event.which;
   // alert(tecla);

   var cuenta = document.getElementById('codigo');
   var x = cuenta.value;
   var z = x.length
   if (tecla == 13  ) {
    var busca = z;
    //  alert ("12 entro");
    var y = document.getElementById("id_materialk").length;
    //  alert(y);
    var i= 0;
    while(i <= y){


      if (i == y){
        swal("Producto No Encontrado!", "Verifique el Codigo de Barras!", "error");
        break;
      }

      var e = document.getElementById("id_materialk");
      var value = e.options[e.selectedIndex=i].value;
      var text = e.options[e.selectedIndex=i].text;
      var cantidadtotal = value;
      limite = "8",
      separador = "_",
      arregloDeSubCadenas = cantidadtotal.split(separador, limite);
      stock=arregloDeSubCadenas[0];
      descripcion=arregloDeSubCadenas[1];
      medida=arregloDeSubCadenas[5];
      nombreUnidad=arregloDeSubCadenas[6];
      UnidadP=arregloDeSubCadenas[7];
      tecla=(document.all) ? event.keyCode : event.which;
      if (codigo == x){
        swal("Producto Encontrado:"+nombre +"!", "Stock de Entrada!", "success",{content: "input", inputType:"number",}).then((value) => {
          var aux =`${value}`;

          document.getElementById("unidadesCompletas").value = aux;
  //swal(aux);
});
        document.getElementById("cantidadp").value=stock;
        document.getElementById('id_materialk').selectedIndex = i;
        document.getElementById("descripcion").value=descripcion;
        document.getElementById("unidadAux").value=nombreUnidad;
        obtenerSelect(UnidadP);
        break;
      }

      i++;
    }


    return false;
  }


}

window.onload=function() {
     //stock agroquimicos
     var select2 = document.getElementById('id_materialk');
     var selectedOption2 = select2.selectedIndex;
     var cantidadtotal = select2.value;
     limite = "8",
     separador = "_",
     arregloDeSubCadenas = cantidadtotal.split(separador, limite);
     stock=arregloDeSubCadenas[0];
     descripcion=arregloDeSubCadenas[1];
     codigo=arregloDeSubCadenas[2];
     nombre=arregloDeSubCadenas[4];
     medida=arregloDeSubCadenas[5];
     nombreUnidad=arregloDeSubCadenas[6];
     UnidadP=arregloDeSubCadenas[7];
     document.getElementById("cantidadp").value=stock;
     document.getElementById("descripcion").value=descripcion;
     document.getElementById("unidadAux").value=nombreUnidad;
     document.getElementById("codigo").select();
  // <option value="{{$mat->cantidad}}_{{$mat->descripcion}}_{{$mat->codigo}}_{{$mat->id}}_{{$mat->nombre}}_{{$mat->idUnidadMedida}}">
  obtenerSelect(UnidadP);

  var menos =document.getElementById("detalles").rows
  var r = menos.length;
  document.getElementById("total").value= r - 1;
  

}
var select2 = document.getElementById('id_materialk');
  //alert(select);
  select2.addEventListener('change',
    function(){
      var selectedOption = this.options[select2.selectedIndex];
   //   console.log(selectedOption.value + ': ' + selectedOption.text);
   var cantidadtotal = selectedOption.value;
   limite = "8",
   separador = "_",
   arregloDeSubCadenas = cantidadtotal.split(separador, limite);
   stock=arregloDeSubCadenas[0];
   descripcion=arregloDeSubCadenas[1];
   medida=arregloDeSubCadenas[5];
   nombreUnidad=arregloDeSubCadenas[6];
   UnidadP=arregloDeSubCadenas[7];
   // id_materiales=arregloDeSubCadenas[3];

  // console.log(arregloDeSubCadenas); 
 // document.getElementById("pcantidad").value=stock;
 document.getElementById("cantidadp").value=stock;
 document.getElementById("descripcion").value=descripcion;
 document.getElementById("unidadAux").value=nombreUnidad;
 obtenerSelect(UnidadP);


});
  function limpiar(){
   // document.getElementById("scantidad").value="1";
 }

 function eliminarFila(value) {

  document.getElementById("detalles").deleteRow(value);
   // var id2= uno2--;
   var menos =document.getElementById("detalles").rows
   var r = menos.length;
   document.getElementById("total").value= r - 1;
   limpiar();
 } 


 function agregar(){
  var x = document.getElementById('unidadesCompletas').value;
  var y =document.getElementById('Medida').value;
  var z =document.getElementById('unidadMinima').value;

  if(x == "" && y == ""    && z == ""){
   document.getElementById("errorUnidad").innerHTML = "La Cantidad de Entrada debe ser Mayor de 0";
   return false;

 }
 document.getElementById("errorUnidad").innerHTML = "";
 var fechav = document.getElementById('fecha').value;
 var recibiov =  document.getElementById('recibio').value;
 var entregadov = document.getElementById('entrego').value;
 var destinov = document.getElementById('destino').value;
 var materialv = document.getElementById('id_materialk').value;

 if(fechav !== "" && recibiov !== "" &&entregadov !=="" && destinov!=="" && materialv!==""){
   if (parseInt(entregadov) != parseInt(recibiov)){
     document.getElementById("errorentrego").innerHTML = "";


     
     var select=document.getElementById('id_materialk');
     var cantidadtotal = select.value;
     limite = "9",
     separador = "_",
     arregloDeSubCadenas = cantidadtotal.split(separador, limite);
     cantidad=arregloDeSubCadenas[0];
     descripcion=arregloDeSubCadenas[1];
     codigo=arregloDeSubCadenas[2];
     id=arregloDeSubCadenas[3];
     nombre=arregloDeSubCadenas[4];
     UnidadP=arregloDeSubCadenas[7];
     CantidadP=arregloDeSubCadenas[8];

     var comprueba = recorre(id)

     if (comprueba == 1){
      swal("Alerta!", "Este Material Ya se ha Insertado en la Tabla!", "error");
      return false();
    }
    var tabla = document.getElementById("detalles");
    var row = tabla.insertRow(1);
    row.style.backgroundColor = "white";
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);
    if (UnidadP == "KILOGRAMOS" || UnidadP == "LITROS"  || UnidadP == "METROS"){
      var x = document.getElementById('unidadesCompletas').value;
      var y = document.getElementById('Medida').value;
      var z = document.getElementById('unidadMinima').value;
      x= (x * CantidadP)*1000;
      y= y*1000;
      w=parseInt(x)+parseInt(y)+parseInt(z);
      if(x > 0 && y > 0 &&  z > 0){        
        var cantidad_Total="<li>"+document.getElementById('unidadesCompletas').value .concat(" "+document.getElementById('unidadAux').value)+"</li>"+"<br/>"+"<li>"+document.getElementById('Medida').value .concat(" "+document.getElementById('unidadCentral').innerHTML)+"</li>"+"<br/>" +"<li>"+document.getElementById('unidadMinima').value.concat(" "+document.getElementById('unidadDeMedida').innerHTML)+"</li>";
        var cantidad_completa= parseInt(x) + parseInt(y) + parseInt(z);

      }else if(x > 0 && y > 0 && z <= 0){
        var cantidad_Total="<li>"+document.getElementById('unidadesCompletas').value .concat(" "+document.getElementById('unidadAux').value)+"</li>"+"<br/>"+"<li>"+document.getElementById('Medida').value .concat(" "+document.getElementById('unidadCentral').innerHTML)+"</li>";
        var cantidad_completa = parseInt(x) + parseInt(y);
      }else if (x > 0 && y <= 0 && z <= 0){
        var cantidad_Total="<li>"+document.getElementById('unidadesCompletas').value .concat(" "+document.getElementById('unidadAux').value)+"</li>";
        var cantidad_completa= x;

      }


    }else {
      var x = document.getElementById('unidadesCompletas').value;
      var y = document.getElementById('Medida').value;
      w=(x * CantidadP)+parseInt(y);

      var cantidad_Total="<li>"+document.getElementById('unidadesCompletas').value .concat(" "+document.getElementById('unidadAux').value)+"</li>";
      var cantidad_completa= document.getElementById('unidadesCompletas').value;

    }

    if (UnidadP == "KILOGRAMOS"){
      var numbreUnidadAux="GRAMOS"; 
    }else if(UnidadP == "LITROS"){
      var   numbreUnidadAux="MILILITROS"; 
    }else if(UnidadP == "METROS"){
     var numbreUnidadAux="CENTIMETROS"

   }else{
    var  numbreUnidadAux="UNIDADES"
  }
  var cantidades = document.getElementById("cantidadp").value;
  if (w > cantidades) {
    swal("Alerta!", "El Stock de Salida no Puede Ser Mayor que la Cantidad Actual en Almacén!", "error");
    return false;

  }

  cell1.innerHTML =  '<input type="button" value="Eliminar"  onClick="eliminarFila(this.parentNode.parentNode.rowIndex);">';
  cell2.innerHTML = id;
  cell3.innerHTML = nombre;
  cell4.innerHTML = cantidad_Total;;
  cell5.innerHTML = cantidad_completa + " " +numbreUnidadAux;



  var x = document.getElementById("id_materialk");
    //x.remove(x.selectedIndex);
    //limpiar();
    //cargar();
    var menos =document.getElementById("detalles").rows
    var r = menos.length;
    document.getElementById("total").value= r - 1;
    


  }else{
   document.getElementById("errorentrego").innerHTML = "El Empleado que entrega el Material no puede ser el mismo que lo Recibe";

 }}else{

  swal("Alerta!", "Faltan campos Por llenar Favor de Verificar!", "error");
}
} 

function recorre(valor) {
 var z = 1
 var arreglo = [];
 var table = document.getElementById('detalles');
 for (var r = 1, n = table.rows.length; r < n; r++) {
  for (var c = 1, m = table.rows[r].cells.length; c < m; c++) {
   if (z == 1){
    var j = table.rows[r].cells[c].innerHTML
    if (valor == j ){
      var r = 1;
      return(r);
      z ++;
    }
  }

  else if(z == 2){
   z ++;


 }else if(z == 3){
  z ++;
}else if(z == 4){
 z ++;
}else{
 z = 1;

}

}
}
}   

function save() {
 if (document.getElementById('total').value > 0){
   var z = 1
   var arreglo = [];
   var table = document.getElementById('detalles');
   for (var r = 1, n = table.rows.length; r < n; r++) {
    for (var c = 1, m = table.rows[r].cells.length; c < m; c++) {
     if (z == 1){
        //alert(z)
       // document.getElementById("id_materialk").id=z;
      // document.getElementById("id_materialk").value=table.rows[r].cells[c].innerHTML;
      arreglo.push(table.rows[r].cells[c].innerHTML);
       // alert(table.rows[r].cells[c].innerHTML);
       z ++;
     }

     else if(z == 2){
         //alert(z)
       //  document.getElementById("id_materialk").value=table.rows[r].cells[c].innerHTML;
       arreglo.push(table.rows[r].cells[c].innerHTML);
       // alert(table.rows[r].cells[c].innerHTML);
       z ++;
       

     }else if(z == 3){
         //alert(z)
       //  document.getElementById("scantidad").value=table.rows[r].cells[c].innerHTML;
       arreglo.push(table.rows[r].cells[c].innerHTML);
        //alert(table.rows[r].cells[c].innerHTML);
        z ++;
      }
      else{
       // document.getElementById("fecha").value=table.rows[r].cells[c].innerHTML;
       arreglo.push(table.rows[r].cells[c].innerHTML);
       document.getElementById("codigo2").value=arreglo;
       z = 1;

     }

   }
 }
 var menos =document.getElementById("detalles").rows
 var r = menos.length;
 document.getElementById("total").value= r - 1;
}else{

  swal("Alerta!", "No hay Elementos Agregados a la Tabla, Para Poder Guardar!", "error");
  return false;

}
}

function obtenerSelect(valor) {
 //MILILITROS

//myArr.includes( 'donna' ) 
 if(valor == "MILILITROS"){  //MILILITROS

  $("#unidadDeMedida").hide();
  $("#unidadMinima").hide();
  document.getElementById('unidadCentral').innerHTML='MILILITROS';  
  $("#Medida").show();


} else if(valor =="GRAMOS"){  //GRAMOS

  $("#unidadDeMedida").hide();
  $("#unidadMinima").hide();
  document.getElementById('unidadCentral').innerHTML='GRAMOS';  
  $("#Medida").show();

} else if(valor == "CENTIMETROS" ) {  //CENTIMETROS

  $("#unidadDeMedida").hide();
  $("#unidadMinima").hide();
  document.getElementById('unidadCentral').innerHTML='CENTIMETROS';  
  $("#Medida").show();


} else if(valor == "LITROS" ){  //LITROS

 $("#unidadDeMedida").show();
 $("#unidadMinima").show();


 document.getElementById('unidadCentral').innerHTML='LITROS';  
 document.getElementById('unidadDeMedida').innerHTML='MILILITROS';  

 $("#unidadCentral").show();
 $("#Medida").show();
} else if(valor == "METROS"){  //METROS
 $("#unidadDeMedida").show();
 $("#unidadMinima").show();
 document.getElementById('unidadCentral').innerHTML='METROS';  
 document.getElementById('unidadDeMedida').innerHTML='CENTIMETROS';  


 $("#Medida").show();

}  else if(valor == "KILOGRAMOS") {  //KILOGRAMOS

 $("#unidadDeMedida").show();
 $("#unidadMinima").show();

 document.getElementById('unidadCentral').innerHTML='KILOGRAMOS';  
 document.getElementById('unidadDeMedida').innerHTML='GRAMOS';  

 $("#unidadCentral").show();
 $("#Medida").show();

} else if (valor == "UNIDADES") {  //UNIDADES

  $("#unidadDeMedida").hide();
  $("#unidadMinima").hide();
  document.getElementById('unidadCentral').innerHTML='UNIDADES';  
  $("#Medida").show();

} 

}

</script>

@endsection