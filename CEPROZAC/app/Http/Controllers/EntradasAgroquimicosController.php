<?php

namespace CEPROZAC\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input; 
use CEPROZAC\Http\Requests;
use CEPROZAC\Http\Requests\EntradasAgroquimicosRequest;
use CEPROZAC\Http\Controllers\Controller;
use CEPROZAC\EntradasAgroquimicos;
use CEPROZAC\Empleado;
use CEPROZAC\AlmacenAgroquimicos;
use CEPROZAC\ProvedorMateriales;
use CEPROZAC\empresas_ceprozac;
use CEPROZAC\cantidad_unidades_agro;
use CEPROZAC\Unidades_medida;
use CEPROZAC\DetallesEntradasAgroquimicos;
use CEPROZAC\NombreUnidadesMedida;

use DB;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Worksheet_Drawing;
use Validator; 
use \Milon\Barcode\DNS1D;
use \Milon\Barcode\DNS2D;
use Illuminate\Support\Collection as Collection;

class EntradasAgroquimicosController extends Controller
{ 
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response 
     */
    public function index()
    {
      $entrada= DB::table('entradasagroquimicos')->where('entradasagroquimicos.estado','=','Activo')
      ->join('provedor_materiales','entradasagroquimicos.provedor','=', 'provedor_materiales.id')
      ->join('empresas_ceprozac', 'entradasagroquimicos.comprador','=', 'empresas_ceprozac.id')
      ->join('empleados as empEntrega', 'entradasagroquimicos.entregado','=', 'empEntrega.id')
      ->join('empleados as empRecibe', 'entradasagroquimicos.recibe_alm','=', 'empRecibe.id')
      ->select('entradasagroquimicos.id as idEntradaAgroquimicos', 'entradasagroquimicos.fecha',
        'entradasagroquimicos.factura', 'entradasagroquimicos.moneda', 'entradasagroquimicos.observacionesc',
        'entradasagroquimicos.estado as estadoEntrada','empEntrega.nombre as nombreEmpleadoEntrega',
        'empEntrega.apellidos as apellidosEmpleadoEntrega', 'empRecibe.nombre as nombreEmpleadoRecibe', 
        'empRecibe.apellidos as apellidosEmpleadoRecibe' , 'empresas_ceprozac.nombre as nombreEmpresa')
      ->get();



     /*
     ->join('entradasagroquimicos', 'detalles_entradas_agroquimicos.idEntradaAgroquimicos', '=', 'entradasagroquimicos.id')
     ->select('detalles_entradas_agroquimicos.*', 'entradasagroquimicos.*')
     ->join('provedores_materiales')
     ->join('almacenagroquimicos', 'detalles_entradas_agroquimicos.id_material','=','almacenagroquimicos.id' )

     ->join('provedor_materiales', 'entradasagroquimicos.provedor', '=', 'provedor_materiales.id')
     ->select('detalles_entradas_agroquimicos.*','entradasagroquimicos.*', 'almacenagroquimicos.*'
      , 'provedor_materiales.*')
     ->where('entradasagroquimicos.estado','=','Activo')
     ->get();
        // print_r($salida);

        */
     return view('almacen.agroquimicos.entradas.index', ['entrada' => $entrada]);



        //
   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() 
    {
      $empleado=DB::table('empleados')->where('estado','=' ,'Activo')->get();
      $provedor = DB::table('provedores_tipo_provedor')
      ->join('provedor_materiales as p', 'provedores_tipo_provedor.idProvedorMaterial', '=', 'p.id')
      ->select('p.*','p.nombre as nombre')
      ->where('provedores_tipo_provedor.idTipoProvedor','2')->get();
      $unidades  = DB::table('unidades_medidas')
      ->join('nombre_unidades_medidas', 'unidades_medidas.idUnidadMedida', '=', 'nombre_unidades_medidas.id')
      ->select('unidades_medidas.id as idContenedorUnidadMedida','unidades_medidas.nombre','unidades_medidas.cantidad', 'unidades_medidas.idUnidadMedida', 'nombre_unidades_medidas.*')
      ->where('estado', '=', 'Activo')
      ->get();
      $empresas=DB::table('empresas_ceprozac')->where('estado','=' ,'Activo')->get();
      $material=DB::table('almacenagroquimicos')->where('almacenagroquimicos.estado','=' ,'Activo')->where('almacenagroquimicos.cantidad','>=','0')
      ->join('unidades_medidas as u', 'almacenagroquimicos.idUnidadMedida', '=', 'u.id')
      ->select('u.idUnidadMedida')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('almacenagroquimicos.*','u.nombre as nombreUnidad','n.nombreUnidadMedida as NombreUnidadP','u.cantidad as cantidadMedida')->get();
      

      $cuenta = count($material);
      

      if (empty($material)){
        return redirect('/almacen/entradas/agroquimicos');

         // return view("almacen.materiales.salidas.create")->with('message', 'No Hay Material Registrado, Favor de Dar de Alta Material Para Poder Acceder a Este Modulo');
      }else if (empty($empleado)) {
        return redirect('/almacen/entradas/agroquimicos');


      }else if (empty($provedor)){
        return redirect('/almacen/entradas/agroquimicos');


     }
     else{
      return view("almacen.agroquimicos.entradas.create",["material"=>$material,"provedor"=>$provedor],["empleado"=>$empleado,"empresas"=>$empresas,"unidades"=>$unidades]);

    }
        //
  }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */ 
    public function store(entradasagroquimicosRequest $formulario)
    {

      $validator = Validator::make(
        $formulario->all(), 
        $formulario->rules(),
        $formulario->messages());
      if ($validator->valid()){

        if ($formulario->ajax()){
          return response()->json(["valid" => true], 200);
        }
        else{
  
          $material= new entradasagroquimicos;
          $material->provedor=$formulario->get('prov');
          $material->fecha=$formulario->get('fecha');
          $material->factura=$formulario->get('factura');
          $material->comprador=$formulario->get('recibio');
          $material->moneda=$formulario->get('moneda');
          $material->entregado=$formulario->get('entrega');
          $material->recibe_alm=$formulario->get('recibe');
          $material->observacionesc=$formulario->get('observacionesq');
          $material->estado="Activo";
          $material->save();

          $ultimo = entradasagroquimicos::orderBy('id', 'desc')->first()->id;
          $num = 1;
          $y = 0;
          $limite = $formulario->get('total');

          while ($num <= $limite) {
            $detalle = new DetallesEntradasAgroquimicos;
            $detalle->idEntradaAgroquimicos=$ultimo;
            $producto = $formulario->get('codigo2');
            $first = head($producto);
            $name = explode(",",$first);
            //$first = $name[0];
             //$first = $name[1];

            $detalle->id_material=$first = $name[$y];
            $material=almacenagroquimicos::findOrFail($first = $name[$y]);//id del producto
            //print_r($first = $name[$y]."*ID PRODUCTO*");
            $y = $y + 3;
            //print_r($first = $name[$y]."*CANTIDAD*");
            $unidad_mediaCentral=$first = $name[$y];
            $name2 = explode(" ",$unidad_mediaCentral);
            $detalle->cantidad=$first2 = $name2[0];
            //print_r($first = $first2 = $name2[0]."*CANTIDAD TOTAL*");
            $material->cantidad=$material->cantidad+$detalle->cantidad;
        //$material->cantidad=$first = $name[$y];
            $y = $y + 1;
            $detalle->p_unitario=$first = $name[$y];
            //print_r($first = $name[$y]."*PUNITARIO*");
        /////
            $y = $y + 1;
            $detalle->iva=$first = $name[$y];
            //print_r($first = $name[$y]."*IVA*");
            $y = $y + 1;
            $detalle->ieps=$first = $name[$y];
            //print_r($first = $name[$y]."*IEPS*");
            $y = $y + 2;
            $detalle->save();
            $material->update();


            $num = $num + 1;

          }
        //

        }}
        return redirect('/almacen/entradas/agroquimicos');
      }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($factura)
    {


     $material=DB::table('almacenagroquimicos')->where('almacenagroquimicos.estado','=' ,'Activo')->where('almacenagroquimicos.cantidad','>=','0')
     ->join('unidades_medidas as u', 'almacenagroquimicos.idUnidadMedida', '=', 'u.id')
     ->select('u.idUnidadMedida')
     ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
     ->select('almacenagroquimicos.*','u.nombre as nombreUnidad','n.nombreUnidadMedida as NombreUnidadP','u.cantidad as cantidadMedida')->get();

     $empleado=DB::table('empleados')->where('estado','=' ,'Activo')->get();
     $provedor = DB::table('provedores_tipo_provedor')
     ->join('provedor_materiales as p', 'provedores_tipo_provedor.idProvedorMaterial', '=', 'p.id')
     ->select('p.*','p.nombre as nombre')
     ->where('provedores_tipo_provedor.idTipoProvedor','2')->get();
     $empresas=DB::table('empresas_ceprozac')->where('estado','=' ,'Activo')->get();


     $entradas=DB::table('detalles_entradas_agroquimicos')
     ->join('almacenagroquimicos as a', 'detalles_entradas_agroquimicos.id_material', '=', 'a.id')
     ->select('a.idUnidadMedida')
     ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
     ->select('u.idUnidadMedida')
     ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
     ->join('entradasagroquimicos as e', 'detalles_entradas_agroquimicos.idEntradaAgroquimicos', '=', 'e.id')
     ->select('detalles_entradas_agroquimicos.*','e.*','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','u.nombre as UnidadNombre')
     ->where('e.factura','=',$factura)->get(); 


     $entrada=DB::table('detalles_entradas_agroquimicos')
     ->join('almacenagroquimicos as a', 'detalles_entradas_agroquimicos.id_material', '=', 'a.id')
     ->select('a.idUnidadMedida')
     ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
     ->select('u.idUnidadMedida')
     ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
     ->join('entradasagroquimicos as e', 'detalles_entradas_agroquimicos.idEntradaAgroquimicos', '=', 'e.id')
     ->select('detalles_entradas_agroquimicos.*','e.*','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida')
     ->where('e.factura','=',$factura)->first(); 

        // 
     return view('almacen.agroquimicos.entradas.edit', ['entrada'=>$entrada,'empleado' => $empleado,'entradas'=> $entradas,'material'=>$material,'provedor'=>$provedor,'empresas'=>$empresas]);
   }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

      $material=entradasagroquimicos::findOrFail($id);
      $material->provedor=$request->get('prov');
      $material->fecha=$request->get('fecha');
      $material->factura=$request->get('factura');
      $material->comprador=$request->get('recibio');
      $material->moneda=$request->get('moneda');
      $material->entregado=$request->get('entrega');
      $material->recibe_alm=$request->get('recibe');
      $material->observacionesc=$request->get('observacionesq');
      $material->update();

      $entradas=DB::table('detalles_entradas_agroquimicos')->where('idEntradaAgroquimicos','=',$id)->get();
      $cuenta = count($entradas);

      for ($x=0; $x < $cuenta  ; $x++) {
        $elimina = DetallesEntradasAgroquimicos::findOrFail($entradas[$x]->id);
        $decrementa=almacenagroquimicos::findOrFail($elimina->id_material);
        $decrementa->cantidad=$decrementa->cantidad- $elimina->cantidad;
        $decrementa->update();
        $elimina->delete();


        # code...
      }
      $num = 1;
      $y = 0;
      $limite = $request->get('total');

      while ($num <= $limite) {
        $detalle = new DetallesEntradasAgroquimicos;
        $detalle->idEntradaAgroquimicos=$id;
        $producto = $request->get('codigo2');
        $first = head($producto);
        $name = explode(",",$first);
            //$first = $name[0];
             //$first = $name[1];

        $detalle->id_material=$first = $name[$y];
            $material=almacenagroquimicos::findOrFail($first = $name[$y]);//id del producto
            print_r($first = $name[$y]."*ID PRODUCTO*");
            $y = $y + 3;
            print_r($first = $name[$y]."*CANTIDAD*");
            $unidad_mediaCentral=$first = $name[$y];
            $name2 = explode(" ",$unidad_mediaCentral);
            $detalle->cantidad=$first2 = $name2[0];
            print_r($first = $first2 = $name2[0]."*CANTIDAD TOTAL*");
            $material->cantidad=$material->cantidad+$detalle->cantidad;
        //$material->cantidad=$first = $name[$y];
            $y = $y + 1;
            $detalle->p_unitario=$first = $name[$y];
            print_r($first = $name[$y]."*PUNITARIO*");
        /////
            $y = $y + 1;
            $detalle->iva=$first = $name[$y];
            print_r($first = $name[$y]."*IVA*");
            $y = $y + 1;
            $detalle->ieps=$first = $name[$y];
            print_r($first = $name[$y]."*IEPS*");
            $y = $y + 2;
            $detalle->save();
            $material->update();


            $num = $num + 1;

          }


          return redirect('/almacen/entradas/agroquimicos');
        //
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


     $entradas=DB::table('detalles_entradas_agroquimicos')
     ->where('idEntradaAgroquimicos','=',$id)->get(); 
     $cuenta = count($entradas);
     for ($x=0; $x < $cuenta  ; $x++) {
      $elimina = DetallesEntradasAgroquimicos::findOrFail($entradas[$x]->id);
      $decrementa=almacenagroquimicos::findOrFail($elimina->id_material);
      $decrementa->cantidad=$decrementa->cantidad- $elimina->cantidad;
      $decrementa->update();
        //$elimina->delete();PENDIENTE CHECAR SI SE ELIMINA O SE QUEDA
        # code...
    }
    $entrada = entradasagroquimicos::findOrFail($id);
    $entrada->estado="Inactivo";
    $entrada->update();


    return Redirect::to('/almacen/entradas/agroquimicos');   
        //
  }


  public function excel()
  {         
        /**
         * toma en cuenta que para ver los mismos 
         * datos debemos hacer la misma consulta
        **/
        Excel::create('entradasagroquimicos', function($excel) {
          $excel->sheet('Excel sheet', function($sheet) {
                //otra opción -> $products = Product::select('name')->get();
           $entrada=DetallesEntradasAgroquimicos::
           join('almacenagroquimicos as a', 'detalles_entradas_agroquimicos.id_material', '=', 'a.id')
           ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
           ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
           ->join('entradasagroquimicos as e', 'detalles_entradas_agroquimicos.idEntradaAgroquimicos', '=', 'e.id')
           ->join('provedor_materiales as prov', 'e.provedor', '=', 'prov.id')
           ->join('empresas_ceprozac as emp', 'e.comprador', '=', 'emp.id')
           ->join('empleados as Empleado1', 'e.entregado', '=', 'Empleado1.id')
           ->join('empleados as Empleado2', 'e.entregado', '=', 'Empleado2.id')
           ->select('detalles_entradas_agroquimicos.idEntradaAgroquimicos as IdEntrada','a.nombre as nombreMaterial','detalles_entradas_agroquimicos.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','emp.nombre as empresaNombre','prov.nombre as NombreProvedor','e.factura','detalles_entradas_agroquimicos.p_unitario','detalles_entradas_agroquimicos.iva','detalles_entradas_agroquimicos.ieps','e.moneda','e.fecha as FechaCompra','Empleado1.nombre as NombreEmp1','Empleado1.apellidos as Ape1','Empleado2.nombre','Empleado2.apellidos','e.observacionesc as Observaciónes Observa')->get();       
           $sheet->fromArray($entrada);
           $sheet->row(1,['N°Compra','Material','Cantidad','Medida' ,'Comprador','Proveedor','Numero de Factura','Precio Unitario','IVA','IEPS','Tipo de Moneda','Fecha de Compra',"Entrego","Apellidos","Recibe en Almacén CEPROZAC","Apellidos",'Observaciónes de la Compra']);
           $sheet->setOrientation('landscape');
         });
        })->export('xls');
      }


      public function show(entradasagroquimicosRequest $formulario,$factura)
      {
$material=DB::table('almacenagroquimicos')->where('almacenagroquimicos.estado','=' ,'Activo')->where('almacenagroquimicos.cantidad','>=','0')
     ->join('unidades_medidas as u', 'almacenagroquimicos.idUnidadMedida', '=', 'u.id')
     ->select('u.idUnidadMedida')
     ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
     ->select('almacenagroquimicos.*','u.nombre as nombreUnidad','n.nombreUnidadMedida as NombreUnidadP','u.cantidad as cantidadMedida')->get();

     $empleado=DB::table('empleados')->where('estado','=' ,'Activo')->get();
     $provedor = DB::table('provedores_tipo_provedor')
     ->join('provedor_materiales as p', 'provedores_tipo_provedor.idProvedorMaterial', '=', 'p.id')
     ->select('p.*','p.nombre as nombre')
     ->where('provedores_tipo_provedor.idTipoProvedor','2')->get();
     $empresas=DB::table('empresas_ceprozac')->where('estado','=' ,'Activo')->get();


     $entradas=DB::table('detalles_entradas_agroquimicos')
     ->join('almacenagroquimicos as a', 'detalles_entradas_agroquimicos.id_material', '=', 'a.id')
     ->select('a.idUnidadMedida')
     ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
     ->select('u.idUnidadMedida')
     ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
     ->join('entradasagroquimicos as e', 'detalles_entradas_agroquimicos.idEntradaAgroquimicos', '=', 'e.id')
     ->select('detalles_entradas_agroquimicos.*','e.*','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','u.nombre as UnidadNombre')
     ->where('e.factura','=',$factura)->get(); 


     $entrada=DB::table('detalles_entradas_agroquimicos')
     ->join('almacenagroquimicos as a', 'detalles_entradas_agroquimicos.id_material', '=', 'a.id')
     ->select('a.idUnidadMedida')
     ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
     ->select('u.idUnidadMedida')
     ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
     ->join('entradasagroquimicos as e', 'detalles_entradas_agroquimicos.idEntradaAgroquimicos', '=', 'e.id')
     ->select('detalles_entradas_agroquimicos.*','e.*','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida')
     ->where('e.factura','=',$factura)->first(); 

        // 
     return view('almacen.agroquimicos.entradas.edit', ['entrada'=>$entrada,'empleado' => $empleado,'entradas'=> $entradas,'material'=>$material,'provedor'=>$provedor,'empresas'=>$empresas]);


        // return redirect('/almacen/entradas/agroquimicos');
        //
      }

      public function Calcula_Cantidad($Cantidad,$Valor,$Nombre,$Unombre){
        if($Nombre == "KILOGRAMOS" || $Nombre == "LITROS"||$Nombre == "METROS"){
        $x=$Valor * 1000; //se obtiene el valor de la unidad
        $y= $Cantidad / $x; //se obtiene la U principal KILOS, LITROS, METROS
        $aux=floor($y); //SE REDONDE AL ENTERO
        $subdiv=$aux * $x;//SE OBTIENEN LOS GRAMOS DE LA U PRINCIPAL
        $sub=$Cantidad-$subdiv;
        $CantidadInf=$sub / 1000;
        $CantidadInferior=floor($CantidadInf);
        $auxCantidadInf= $CantidadInferior * 1000;
        $sub2=$sub-$auxCantidadInf;//se obtienen los gramos,centimetos o litros
        $cantidad=$aux." ".$Unombre;
        }elseif ($Nombre == "UNIDADES") {
        $cantidad=$Cantidad." ".$Unombre;
          # code...
        }
        return $cantidad ;

      }



      public function Calcula_Cantidad2($Cantidad,$Valor,$Nombre,$Unombre){
        $x=$Valor * 1000; //se obtiene el valor de la unidad
        $y= $Cantidad / $x; //se obtiene la U principal KILOS, LITROS, METROS
        $aux=floor($y); //SE REDONDE AL ENTERO
        $subdiv=$aux * $x;//SE OBTIENEN LOS GRAMOS DE LA U PRINCIPAL
        $sub=$Cantidad-$subdiv;
        $CantidadInf=$sub / 1000;
        $CantidadInferior=floor($CantidadInf);
        $auxCantidadInf= $CantidadInferior * 1000;
        $sub2=$sub-$auxCantidadInf;//se obtienen los gramos,centimetos o litros
        $cantidad=0;
        if ($Nombre == "KILOGRAMOS"){
          $cantidad=$CantidadInferior." KILOGRAMOS ";
        }elseif ($Nombre == "LITROS") {
          $cantidad=$CantidadInferior." LITROS ";
          # code...
        }elseif ($Nombre == "METROS") {
          $cantidad=$CantidadInferior." METROS ";
          # code...
        }elseif ($Nombre == "UNIDADES") {
          $cantidad=$CantidadInferior." UNIDADES ";
          # code...
        }
        return $cantidad ;

      }

      public function Calcula_Cantidad3($Cantidad,$Valor,$Nombre,$Unombre){
        $x=$Valor * 1000; //se obtiene el valor de la unidad
        $y= $Cantidad / $x; //se obtiene la U principal KILOS, LITROS, METROS
        $aux=floor($y); //SE REDONDE AL ENTERO

        $subdiv=$aux * $x;//SE OBTIENEN LOS GRAMOS DE LA U PRINCIPAL

        $sub=$Cantidad-$subdiv;
        $CantidadInf=$sub / 1000;
        $CantidadInferior=floor($CantidadInf);

        $auxCantidadInf= $CantidadInferior * 1000;

        $sub2=$sub-$auxCantidadInf;//se obtienen los gramos,centimetos o litros
              $cantidad=0;

        if ($Nombre == "KILOGRAMOS"){
          $cantidad=$sub2." GRAMOS";
        }elseif ($Nombre == "LITROS") {
          $cantidad=$sub2." MILILITROS";
          # code...
        }elseif ($Nombre == "METROS") {
          $cantidad=$sub2." CENTIMETROS";
          # code...
        }elseif ($Nombre == "UNIDADES") {
          $cantidad=$cantidad." UNIDADES";
          # code...
        }


        return $cantidad ;

      }


      public function CALCULA_SUB($Cantidad,$Valor,$Precio,$Iva,$Ieps,$Nombre){
        if($Nombre == "KILOGRAMOS" || $Nombre == "LITROS" || $Nombre == "METROS"){
        $x=$Valor * 1000; //se obtiene el valor de la unidad
        $y= $Cantidad / $x; //se obtiene la U principal KILOS, LITROS, METROS
        $aux=floor($y); //SE REDONDE AL ENTERO
        $precioTotal=($aux * $Precio)+ $Iva +$Ieps;
      }elseif ($Nombre == "UNIDADES") {
        $precioTotal=($Cantidad * $Precio)+ $Iva +$Ieps;
        # code...
      }
        return $precioTotal ;

      }

      public function CALCULA_TOTAL($id){

       $entradas=DB::table('detalles_entradas_agroquimicos')->where('idEntradaAgroquimicos','=',$id)->get();
       $cuenta = count($entradas);
       $aux= 0;

       for ($x=0; $x < $cuenta  ; $x++) {
        $detalle = DetallesEntradasAgroquimicos::findOrFail($entradas[$x]->id);
        $material=almacenagroquimicos::findOrFail($detalle->id_material);
        $medida=Unidades_medida::findOrFail($material->idUnidadMedida);
        $unidadesmedida=NombreUnidadesMedida::findOrFail($medida->idUnidadMedida);


        $nombre=$unidadesmedida->nombreUnidadMedida;
        if($nombre == "KILOGRAMOS" || $nombre == "LITROS" || $nombre == "METROS"){
          $valor=$medida->cantidad;
          $z=$valor * 1000;
           $y= $detalle->cantidad / $z; //se obtiene la U principal KILOS, LITROS, METROS
        $aux2=floor($y); //SE REDONDE AL ENTERO
        $precioTotal=($aux2 * $detalle->p_unitario)+ $detalle->iva +$detalle->ieps;        
        $aux+=$precioTotal;
      }elseif ($nombre == "UNIDADES"){
       $aux+=($detalle->cantidad  * $detalle->p_unitario)+ $detalle->iva +$detalle->ieps;
     }
   }
   return $aux;

 }


 public function pdfentradaAgroquimicos($id) 
 {

   $data2 =DB::table('detalles_entradas_agroquimicos')
   ->join('almacenagroquimicos as a', 'detalles_entradas_agroquimicos.id_material', '=', 'a.id')
   ->select('a.idUnidadMedida')
   ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
   ->select('u.idUnidadMedida')
   ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
   ->join('entradasagroquimicos as e', 'detalles_entradas_agroquimicos.idEntradaAgroquimicos', '=', 'e.id')
   ->select('detalles_entradas_agroquimicos.*','e.*','a.nombre as nombreMaterial','a.id as idMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','u.nombre as UnidadNombre')
   ->where('e.factura','=',$id)->get();

   $entrada=DB::table('detalles_entradas_agroquimicos')
   ->join('almacenagroquimicos as a', 'detalles_entradas_agroquimicos.id_material', '=', 'a.id')
   ->select('a.idUnidadMedida')
   ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
   ->select('u.idUnidadMedida')
   ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
   ->join('entradasagroquimicos as e', 'detalles_entradas_agroquimicos.idEntradaAgroquimicos', '=', 'e.id')
   ->select('e.comprador', 'e.entregado','e.recibe_alm','e.provedor')
   ->join('empresas_ceprozac as emp', 'e.comprador', '=', 'emp.id')
   ->join('empleados as empleado', 'e.entregado', '=', 'empleado.id')
   ->join('empleados as empleado2', 'e.recibe_alm', '=', 'empleado2.id')
   ->join('provedor_materiales as prov', 'e.provedor', '=', 'prov.id')
   ->select('detalles_entradas_agroquimicos.*','e.*','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','emp.nombre as empresaNombre','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','prov.nombre as ProvedorNombre','prov.direccion as ProvedorDireccion','prov.telefono as ProvedorTelefono','prov.email as ProvedorEmail')
   ->where('e.factura','=',$id)->first(); 

 
   $date = date('Y-m-d');
   $invoice = "2222";
   $view =  \View::make('almacen.agroquimicos.entradas.invoice', compact('date', 'invoice','entrada','data2'))->render();
   $pdf = \App::make('dompdf.wrapper');
   $pdf->loadHTML($view);
   return $pdf->stream('invoice');
 }

public function verEntradaAgroquimicos($id){
  $data2 =DB::table('detalles_entradas_agroquimicos')
   ->join('almacenagroquimicos as a', 'detalles_entradas_agroquimicos.id_material', '=', 'a.id')
   ->select('a.idUnidadMedida')
   ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
   ->select('u.idUnidadMedida')
   ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
   ->join('entradasagroquimicos as e', 'detalles_entradas_agroquimicos.idEntradaAgroquimicos', '=', 'e.id')
   ->select('detalles_entradas_agroquimicos.*','e.*','a.nombre as nombreMaterial','a.id as idMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','u.nombre as UnidadNombre')
   ->where('e.factura','=',$id)->get();

   $entrada=DB::table('detalles_entradas_agroquimicos')
   ->join('almacenagroquimicos as a', 'detalles_entradas_agroquimicos.id_material', '=', 'a.id')
   ->select('a.idUnidadMedida')
   ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
   ->select('u.idUnidadMedida')
   ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
   ->join('entradasagroquimicos as e', 'detalles_entradas_agroquimicos.idEntradaAgroquimicos', '=', 'e.id')
   ->select('e.comprador', 'e.entregado','e.recibe_alm','e.provedor')
   ->join('empresas_ceprozac as emp', 'e.comprador', '=', 'emp.id')
   ->join('empleados as empleado', 'e.entregado', '=', 'empleado.id')
   ->join('empleados as empleado2', 'e.recibe_alm', '=', 'empleado2.id')
   ->join('provedor_materiales as prov', 'e.provedor', '=', 'prov.id')
   ->select('detalles_entradas_agroquimicos.*','e.*','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','emp.nombre as empresaNombre','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','prov.nombre as ProvedorNombre','prov.direccion as ProvedorDireccion','prov.telefono as ProvedorTelefono','prov.email as ProvedorEmail')
   ->where('e.factura','=',$id)->first(); 


      return view("almacen.agroquimicos.entradas.reporte",["data2"=>$data2,'entrada'=>$entrada]);


}




}

