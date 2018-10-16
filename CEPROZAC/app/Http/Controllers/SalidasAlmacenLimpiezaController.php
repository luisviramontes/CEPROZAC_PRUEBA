<?php

namespace CEPROZAC\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use CEPROZAC\Http\Requests;
use CEPROZAC\Http\Controllers\Controller;
use CEPROZAC\salidasalmacenlimpieza;
use CEPROZAC\Empleado;
use CEPROZAC\AlmacenAgroquimicos;
use CEPROZAC\almacenlimpieza;
use CEPROZAC\ubicaciones_limpieza;
use CEPROZAC\cantidad_unidades_limp;
use CEPROZAC\unidadesmedida;
use CEPROZAC\DetallesSalidasLimpieza;  

use DB;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Worksheet_Drawing;
use Validator; 
use \Milon\Barcode\DNS1D;
use \Milon\Barcode\DNS2D;
use Illuminate\Support\Collection as Collection;
class salidasalmacenlimpiezaController extends Controller
{
    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function index()
    { 

      $salida=DB::table('detalles_salidas_limpieza')
      ->join('almacenlimpieza as a', 'detalles_salidas_limpieza.id_material', '=', 'a.id')
      ->join('salidasalmacenlimpieza as e', 'detalles_salidas_limpieza.idSalidaLimpieza', '=', 'e.id')
      ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
      ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
      ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('detalles_salidas_limpieza.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->where('e.estado','=','Activo')->get(); 
      return view('almacen.limpieza.salidas.index', ['salida' => $salida]);

        // print_r($salida);
        // 

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

     $limpieza=DB::table('ubicaciones_limpieza')->where('estado','=' ,'Activo')->get();
     $empleado=DB::table('empleados')->where('estado','=' ,'Activo')->get();
     $invernadero=DB::table('invernaderos')->where('estado','=' ,'Activo')->get();
     $almacenes=DB::table('almacengeneral')->where('estado','=' ,'Activo')->get(); 
     $provedor = DB::table('provedores_tipo_provedor')
     ->join('provedor_materiales as p', 'provedores_tipo_provedor.idProvedorMaterial', '=', 'p.id')
     ->select('p.*','p.nombre as nombre')
     ->where('provedores_tipo_provedor.idTipoProvedor','3')->get();
     $empresas=DB::table('empresas_ceprozac')->where('estado','=' ,'Activo')->get();
     $unidades  = DB::table('unidades_medidas')
     ->join('nombre_unidades_medidas', 'unidades_medidas.idUnidadMedida', '=', 'nombre_unidades_medidas.id')
     ->select('unidades_medidas.id as idContenedorUnidadMedida','unidades_medidas.nombre','unidades_medidas.cantidad', 'unidades_medidas.idUnidadMedida', 'nombre_unidades_medidas.*')
     ->where('estado', '=', 'Activo')
     ->get();
     $material=DB::table('almacenlimpieza')->where('almacenlimpieza.estado','=' ,'Activo')->where('almacenlimpieza.cantidad','>=','0')
     ->join('unidades_medidas as u', 'almacenlimpieza.idUnidadMedida', '=', 'u.id')
     ->select('u.idUnidadMedida')
     ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
     ->select('almacenlimpieza.*','u.nombre as nombreUnidad','n.nombreUnidadMedida as NombreUnidadP','u.cantidad as cantidadMedida')->get();

     if (empty($material)){
      return redirect('almacen/salidas/limpieza');

         // return view("almacen.materiales.salidas.create")->with('message', 'No Hay Material Registrado, Favor de Dar de Alta Material Para Poder Acceder a Este Modulo');
    }else if (empty($empleado)) {
     return redirect('almacen/salidas/limpieza');

   }else if (empty($provedor)){
     return redirect('almacen/salidas/limpieza');


   }
   else{
    return view("almacen.limpieza.salidas.create",["material"=>$material,"provedor"=>$provedor,"empleado"=>$empleado,"empresas"=>$empresas,"unidades"=>$unidades,"invernadero"=>$invernadero,"almacenes"=>$almacenes,'limpieza'=>$limpieza]);

  }

        //
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     $material= new salidasalmacenlimpieza;

     $material->destino=$request->get('destino');
     $material->entrego=$request->get('entrego');
     $material->recibio=$request->get('recibio');
     $material->tipo_movimiento=$request->get('movimiento');
     $material->fecha=$request->get('fecha');
     $material->estado="Activo";
     $material->save();

     $ultimo = salidasalmacenlimpieza::orderBy('id', 'desc')->first()->id;
     $num = 1;
     $y = 0;
     $limite = $request->get('total');

     while ($num <= $limite) {
      $detalle = new DetallesSalidasLimpieza;
      $detalle->idSalidaLimpieza=$ultimo;
      $producto = $request->get('codigo2');
      $first = head($producto);
      $name = explode(",",$first);
            //$first = $name[0];
             //$first = $name[1];

      $detalle->id_material=$first = $name[$y];
            $material=almacenlimpieza::findOrFail($first = $name[$y]);//id del producto
            //print_r($first = $name[$y]."*ID PRODUCTO*");
            $y = $y + 3;
            //print_r($first = $name[$y]."*CANTIDAD*");
            $unidad_mediaCentral=$first = $name[$y];
            $name2 = explode(" ",$unidad_mediaCentral);
            $detalle->cantidad=$first2 = $name2[0];
            //print_r($first = $first2 = $name2[0]."*CANTIDAD TOTAL*");
            $material->cantidad=$material->cantidad-$detalle->cantidad;
        //$material->cantidad=$first = $name[$y];
            $y = $y + 1;
            $detalle->save();
            $material->update();
            $num = $num + 1;

          }
        //
          return redirect('/almacen/salidas/limpieza');
        }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

      $limpieza=DB::table('ubicaciones_limpieza')->where('estado','=' ,'Activo')->get();


      $materiales=DB::table('almacenlimpieza')->where('almacenlimpieza.estado','=' ,'Activo')->where('almacenlimpieza.cantidad','>=','0')
      ->join('unidades_medidas as u', 'almacenlimpieza.idUnidadMedida', '=', 'u.id')
      ->select('u.idUnidadMedida')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('almacenlimpieza.*','u.nombre as nombreUnidad','n.nombreUnidadMedida as NombreUnidadP','u.cantidad as cantidadMedida')->get();

      $empleado=DB::table('empleados')->where('estado','=' ,'Activo')->get();
      $provedor = DB::table('provedores_tipo_provedor')
      ->join('provedor_materiales as p', 'provedores_tipo_provedor.idProvedorMaterial', '=', 'p.id')
      ->select('p.*','p.nombre as nombre')
      ->where('provedores_tipo_provedor.idTipoProvedor','2')->get();
      $empresas=DB::table('empresas_ceprozac')->where('estado','=' ,'Activo')->get();


      $salida=DB::table('detalles_salidas_limpieza')->where('detalles_salidas_limpieza.idSalidaLimpieza','=',$id)
      ->join('almacenlimpieza as a', 'detalles_salidas_limpieza.id_material', '=', 'a.id')
      ->join('salidasalmacenlimpieza as e', 'detalles_salidas_limpieza.idSalidaLimpieza', '=', 'e.id')
      ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('detalles_salidas_limpieza.*','e.*','e.id as idSalida')->first(); 


      $salidas=DB::table('detalles_salidas_limpieza')->where('detalles_salidas_limpieza.idSalidaLimpieza','=',$id)
      ->join('almacenlimpieza as a', 'detalles_salidas_limpieza.id_material', '=', 'a.id')
      ->join('salidasalmacenlimpieza as e', 'detalles_salidas_limpieza.idSalidaLimpieza', '=', 'e.id')
      ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
      ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
      ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('detalles_salidas_limpieza.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->get(); 

      return view("almacen.limpieza.salidas.edit",['salidas'=>$salidas,"salida"=>$salida,"empleado"=>$empleado,'materiales'=>$materiales,'limpieza'=>$limpieza,'limpieza'=>$limpieza]);
        //
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

      $material=salidasalmacenlimpieza::findOrFail($id);

     $material->destino=$request->get('destino');
     $material->entrego=$request->get('entrego');
     $material->recibio=$request->get('recibio');
     $material->tipo_movimiento=$request->get('movimiento');
     $material->fecha=$request->get('fecha');
     $material->update();

     $salidas=DB::table('detalles_salidas_limpieza')->where('idSalidaLimpieza','=',$id)->get();
     $cuenta = count($salidas);

     for ($x=0; $x < $cuenta  ; $x++) {
      $elimina = DetallesSalidasLimpieza::findOrFail($salidas[$x]->id);
      $decrementa=almacenlimpieza::findOrFail($elimina->id_material);
      $decrementa->cantidad=$decrementa->cantidad + $elimina->cantidad;
      $decrementa->update();
      $elimina->delete();
        # code...
    }
    $num = 1;
    $y = 0;
    $limite = $request->get('total');
    print_r($limite);
    while ($num <= $limite) {
      $detalle = new DetallesSalidasLimpieza;
      $detalle->idSalidaLimpieza=$id;
      $producto = $request->get('codigo2');
      print_r($producto);
      $first = head($producto);
      $name = explode(",",$first);
            //$first = $name[0];
             //$first = $name[1];
      print_r($first = $name[$y]);
      $detalle->id_material=$first = $name[$y];
            $material=almacenlimpieza::findOrFail($first = $name[$y]);//id del producto
            //print_r($first = $name[$y]."*ID PRODUCTO*");
            $y = $y + 3;
            //print_r($first = $name[$y]."*CANTIDAD*");
            $unidad_mediaCentral=$first = $name[$y];
            $name2 = explode(" ",$unidad_mediaCentral);
            $detalle->cantidad=$first2 = $name2[0];
            //print_r($first = $first2 = $name2[0]."*CANTIDAD TOTAL*");
            $material->cantidad=$material->cantidad-$detalle->cantidad;
        //$material->cantidad=$first = $name[$y];
            $y = $y + 1;
            $detalle->save();
            $material->update();


            $num = $num + 1;

          }

          return redirect('almacen/salidas/limpieza');
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
 $entradas=DB::table('detalles_salidas_limpieza')
     ->where('idSalidaLimpieza','=',$id)->get(); 
     $cuenta = count($entradas);
     for ($x=0; $x < $cuenta  ; $x++) {
      $elimina = DetallesSalidasLimpieza::findOrFail($entradas[$x]->id);
      $decrementa=almacenlimpieza::findOrFail($elimina->id_material);
      $decrementa->cantidad=$decrementa->cantidad+ $elimina->cantidad;
      $decrementa->update();
        //$elimina->delete();PENDIENTE CHECAR SI SE ELIMINA O SE QUEDA
        # code...
    }
    $entrada = salidasalmacenlimpieza::findOrFail($id);
    $entrada->estado="Inactivo";
    $entrada->update();
  return Redirect::to('/almacen/salidas/limpieza');   

        //
}
        //


  public function excel()
  {         
        /**
         * toma en cuenta que para ver los mismos 
         * datos debemos hacer la misma consulta
        **/
        Excel::create('salidasalmacenlimpieza', function($excel) {
          $excel->sheet('Excel sheet', function($sheet) {
                //otra opción -> $products = Product::select('name')->get();
           $salida=DetallesSalidasLimpieza::
           join('almacenlimpieza as a', 'detalles_salidas_limpieza.id_material', '=', 'a.id')
           ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
           ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
           ->join('salidasalmacenlimpieza as e', 'detalles_salidas_limpieza.idSalidaLimpieza', '=', 'e.id')
           ->join('empleados as Empleado1', 'e.entrego', '=', 'Empleado1.id')
           ->join('empleados as Empleado2', 'e.recibio', '=', 'Empleado2.id')
           ->select('detalles_salidas_limpieza.idSalidaLimpieza as IdEntrada','a.nombre as nombreMaterial','detalles_salidas_limpieza.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','e.fecha as FechaCompra','Empleado1.nombre as NombreEmp1','Empleado1.apellidos as Ape1','Empleado2.nombre','Empleado2.apellidos','e.tipo_movimiento as Observaciónes')->get();       
           $sheet->fromArray($salida);
           $sheet->row(1,['N°Salida','Material','Cantidad','Medida' ,'Fecha',"Entrego","Apellidos","Recibe en Almacén CEPROZAC","Apellidos",'Observaciónes']);
           $sheet->setOrientation('landscape');
         });
        })->export('xls');
      }

      public function verInformacion($id){
       $salidas=DB::table('detalles_salidas_limpieza')->where('detalles_salidas_limpieza.idSalidaLimpieza','=',$id)
       ->join('almacenlimpieza as a', 'detalles_salidas_limpieza.id_material', '=', 'a.id')
       ->join('salidasalmacenlimpieza as e', 'detalles_salidas_limpieza.idSalidaLimpieza', '=', 'e.id')
       ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
       ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
       ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
       ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
       ->select('detalles_salidas_limpieza.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->first();

       $salida=DB::table('detalles_salidas_limpieza')->where('detalles_salidas_limpieza.idSalidaLimpieza','=',$id)
       ->join('almacenlimpieza as a', 'detalles_salidas_limpieza.id_material', '=', 'a.id')
       ->join('salidasalmacenlimpieza as e', 'detalles_salidas_limpieza.idSalidaLimpieza', '=', 'e.id')
       ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
       ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
       ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
       ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
       ->select('detalles_salidas_limpieza.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','a.descripcion as descripcion','a.codigo as codigo','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->get(); 

       return view("almacen.limpieza.salidas.reporte",["salidas"=>$salidas,'salida'=>$salida]);

     }

     public function invoice($id) {
     $salidas=DB::table('detalles_salidas_limpieza')->where('detalles_salidas_limpieza.idSalidaLimpieza','=',$id)
       ->join('almacenlimpieza as a', 'detalles_salidas_limpieza.id_material', '=', 'a.id')
       ->join('salidasalmacenlimpieza as e', 'detalles_salidas_limpieza.idSalidaLimpieza', '=', 'e.id')
       ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
       ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
       ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
       ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
       ->select('detalles_salidas_limpieza.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->first();

       $salida=DB::table('detalles_salidas_limpieza')->where('detalles_salidas_limpieza.idSalidaLimpieza','=',$id)
       ->join('almacenlimpieza as a', 'detalles_salidas_limpieza.id_material', '=', 'a.id')
       ->join('salidasalmacenlimpieza as e', 'detalles_salidas_limpieza.idSalidaLimpieza', '=', 'e.id')
       ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
       ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
       ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
       ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
       ->select('detalles_salidas_limpieza.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','a.descripcion as descripcion','a.codigo as codigo','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->get(); 
       
       $date = date('Y-m-d');
       $invoice = "2222";
       $view =  \View::make('almacen.limpieza.salidas.invoice', compact('date', 'invoice','salida','salidas'))->render();
       $pdf = \App::make('dompdf.wrapper');
       $pdf->loadHTML($view);
       return $pdf->stream('invoice');

     }
   }

