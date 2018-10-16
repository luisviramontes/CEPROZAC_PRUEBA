<?php

namespace CEPROZAC\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use CEPROZAC\Http\Requests;
use CEPROZAC\Http\Controllers\Controller;
use CEPROZAC\salidasempaques;
use CEPROZAC\Empleado;
use CEPROZAC\almacenempaque;
use CEPROZAC\DetallesSalidasEmpaque; 

use DB;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Worksheet_Drawing;
use Validator; 
use \Milon\Barcode\DNS1D;
use \Milon\Barcode\DNS2D;
use Illuminate\Support\Collection as Collection;


class salidasempaquescontroller extends Controller
{ 
    /**

     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $salida=DB::table('detalles_salidas_empaque')
      ->join('almacenempaque as a', 'detalles_salidas_empaque.id_material', '=', 'a.id')
      ->select('a.idFormaEmpaque')
      ->join('forma_empaques as f','f.id','=','a.idFormaEmpaque')
      ->join('salidasempaques as e', 'detalles_salidas_empaque.idSalidaEmpaque', '=', 'e.id')
      ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
      ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
      ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('detalles_salidas_empaque.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','f.formaEmpaque as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->where('e.estado','=','Activo')->get(); 
      return view('almacen.empaque.salidas.index', ['salida' => $salida]);
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
     ->where('provedores_tipo_provedor.idTipoProvedor','4')->get();
     $empresas=DB::table('empresas_ceprozac')->where('estado','=' ,'Activo')->get();
     $unidades  = DB::table('unidades_medidas')
     ->join('nombre_unidades_medidas', 'unidades_medidas.idUnidadMedida', '=', 'nombre_unidades_medidas.id')
     ->select('unidades_medidas.id as idContenedorUnidadMedida','unidades_medidas.nombre','unidades_medidas.cantidad', 'unidades_medidas.idUnidadMedida', 'nombre_unidades_medidas.*')
     ->where('estado', '=', 'Activo')
     ->get();
     $material=DB::table('almacenempaque')->where('almacenempaque.estado','=' ,'Activo')->where('almacenempaque.cantidad','>=','0')
     ->join('forma_empaques as f','f.id','=','almacenempaque.idFormaEmpaque')
     ->join('unidades_medidas as u', 'almacenempaque.idUnidadMedida', '=', 'u.id')
     ->select('u.idUnidadMedida')
     ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
     ->select('almacenempaque.*','f.formaEmpaque as nombre','u.nombre as nombreUnidad','n.nombreUnidadMedida as NombreUnidadP','u.cantidad as cantidadMedida')->get();

     if (empty($material)){
      return redirect('almacen/salidas/empaque');

         // return view("almacen.materiales.salidas.create")->with('message', 'No Hay Material Registrado, Favor de Dar de Alta Material Para Poder Acceder a Este Modulo');
    }else if (empty($empleado)) {
     return redirect('almacen/salidas/empaque');

   }else if (empty($provedor)){
     return redirect('almacen/salidas/empaque');


   }
   else{
    return view("almacen.empaque.salidas.create",["material"=>$material,"provedor"=>$provedor,"empleado"=>$empleado,"empresas"=>$empresas,"unidades"=>$unidades,"invernadero"=>$invernadero,"almacenes"=>$almacenes,'limpieza'=>$limpieza]);

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
     $material= new salidasempaques;

     $material->destino=$request->get('destino');
     $material->entrego=$request->get('entrego');
     $material->recibio=$request->get('recibio');
     $material->tipo_movimiento=$request->get('movimiento');
     $material->fecha=$request->get('fecha');
     $material->estado="Activo";
     $material->save();

     $ultimo = salidasempaques::orderBy('id', 'desc')->first()->id;
     $num = 1;
     $y = 0;
     $limite = $request->get('total');

     while ($num <= $limite) {
      $detalle = new DetallesSalidasEmpaque;
      $detalle->idSalidaEmpaque=$ultimo;
      $producto = $request->get('codigo2');
      $first = head($producto);
      $name = explode(",",$first);
            //$first = $name[0];
             //$first = $name[1];

      $detalle->id_material=$first = $name[$y];
            $material=almacenempaque::findOrFail($first = $name[$y]);//id del producto
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
          return redirect('/almacen/salidas/empaque');
        //
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
    {     $limpieza=DB::table('ubicaciones_limpieza')->where('estado','=' ,'Activo')->get();


    $materiales=DB::table('almacenempaque')->where('almacenempaque.estado','=' ,'Activo')->where('almacenempaque.cantidad','>=','0')
        ->select('almacenempaque.idFormaEmpaque')
       ->join('forma_empaques as f','f.id','=','almacenempaque.idFormaEmpaque')
    ->join('unidades_medidas as u', 'almacenempaque.idUnidadMedida', '=', 'u.id')
    ->select('u.idUnidadMedida')
    ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
    ->select('almacenempaque.*','u.nombre as nombreUnidad','n.nombreUnidadMedida as NombreUnidadP','u.cantidad as cantidadMedida','f.formaEmpaque as nombre')->get();

    $almacenes=DB::table('almacengeneral')->where('estado','=' ,'Activo')->get(); 
    $empleado=DB::table('empleados')->where('estado','=' ,'Activo')->get();
    $provedor = DB::table('provedores_tipo_provedor')
    ->join('provedor_materiales as p', 'provedores_tipo_provedor.idProvedorMaterial', '=', 'p.id')
    ->select('p.*','p.nombre as nombre')
    ->where('provedores_tipo_provedor.idTipoProvedor','4')->get();
    $empresas=DB::table('empresas_ceprozac')->where('estado','=' ,'Activo')->get();


    $salida=DB::table('detalles_salidas_empaque')->where('detalles_salidas_empaque.idSalidaEmpaque','=',$id)
    ->join('almacenempaque as a', 'detalles_salidas_empaque.id_material', '=', 'a.id')
    ->join('salidasempaques as e', 'detalles_salidas_empaque.idSalidaEmpaque', '=', 'e.id')
    ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
    ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
    ->select('detalles_salidas_empaque.*','e.*','e.id as idSalida')->first(); 


    $salidas=DB::table('detalles_salidas_empaque')->where('detalles_salidas_empaque.idSalidaEmpaque','=',$id)
    ->join('almacenempaque as a', 'detalles_salidas_empaque.id_material', '=', 'a.id')
    ->select('a.idFormaEmpaque')
       ->join('forma_empaques as f','f.id','=','a.idFormaEmpaque')
    ->join('salidasempaques as e', 'detalles_salidas_empaque.idSalidaEmpaque', '=', 'e.id')
    ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
    ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
    ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
    ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
    ->select('detalles_salidas_empaque.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','f.formaEmpaque as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->get(); 

    return view("almacen.empaque.salidas.edit",['salidas'=>$salidas,"salida"=>$salida,"empleado"=>$empleado,'materiales'=>$materiales,'limpieza'=>$limpieza,'almacenes'=>$almacenes]);
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

     $material=salidasempaques::findOrFail($id);

     $material->destino=$request->get('destino');
     $material->entrego=$request->get('entrego');
     $material->recibio=$request->get('recibio');
     $material->tipo_movimiento=$request->get('movimiento');
     $material->fecha=$request->get('fecha');
     $material->update();

     $salidas=DB::table('detalles_salidas_empaque')->where('idSalidaEmpaque','=',$id)->get();
     $cuenta = count($salidas);

     for ($x=0; $x < $cuenta  ; $x++) {
      $elimina = DetallesSalidasEmpaque::findOrFail($salidas[$x]->id);
      $decrementa=almacenempaque::findOrFail($elimina->id_material);
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
      $detalle = new DetallesSalidasEmpaque;
      $detalle->idSalidaEmpaque=$id;
      $producto = $request->get('codigo2');
      print_r($producto);
      $first = head($producto);
      $name = explode(",",$first);
            //$first = $name[0];
             //$first = $name[1];
      print_r($first = $name[$y]);
      $detalle->id_material=$first = $name[$y];
            $material=almacenempaque::findOrFail($first = $name[$y]);//id del producto
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

          return redirect('almacen/salidas/empaque');
        }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
     $entradas=DB::table('detalles_salidas_empaque')
     ->where('idSalidaEmpaque','=',$id)->get(); 
     $cuenta = count($entradas);
     for ($x=0; $x < $cuenta  ; $x++) {
      $elimina = DetallesSalidasEmpaque::findOrFail($entradas[$x]->id);
      $decrementa=almacenempaque::findOrFail($elimina->id_material);
      $decrementa->cantidad=$decrementa->cantidad+ $elimina->cantidad;
      $decrementa->update();
        //$elimina->delete();PENDIENTE CHECAR SI SE ELIMINA O SE QUEDA
        # code...
    }
    $entrada = salidasempaques::findOrFail($id);
    $entrada->estado="Inactivo";
    $entrada->update();
    return Redirect::to('/almacen/salidas/empaque');   

        //
  }

  public function excel()
  {         
        /**
         * toma en cuenta que para ver los mismos 
         * datos debemos hacer la misma consulta
        **/
        Excel::create('salidasempaques', function($excel) {
          $excel->sheet('Excel sheet', function($sheet) {
                //otra opción -> $products = Product::select('name')->get();
           $salida=DetallesSalidasEmpaque::
           join('almacenempaque as a', 'detalles_salidas_empaque.id_material', '=', 'a.id')
           ->select('a.idFormaEmpaque')
       ->join('forma_empaques as f','f.id','=','a.idFormaEmpaque')
           ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
           ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
           ->join('salidasempaques as e', 'detalles_salidas_empaque.idSalidaEmpaque', '=', 'e.id')
           ->join('empleados as Empleado1', 'e.entrego', '=', 'Empleado1.id')
           ->join('empleados as Empleado2', 'e.recibio', '=', 'Empleado2.id')
           ->select('detalles_salidas_empaque.idSalidaEmpaque as IdEntrada','f.formaEmpaque as nombreMaterial','detalles_salidas_empaque.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','e.fecha as FechaCompra','Empleado1.nombre as NombreEmp1','Empleado1.apellidos as Ape1','Empleado2.nombre','Empleado2.apellidos','e.tipo_movimiento as Observaciónes')->get();       
           $sheet->fromArray($salida);
           $sheet->row(1,['N°Salida','Material','Cantidad','Medida' ,'Fecha',"Entrego","Apellidos","Recibe en Almacén CEPROZAC","Apellidos",'Observaciónes']);
           $sheet->setOrientation('landscape');
         });
        })->export('xls');
      }

      public function versalidaempaques($id){
       $salidas=DB::table('detalles_salidas_empaque')->where('detalles_salidas_empaque.idSalidaEmpaque','=',$id)
       ->join('almacenempaque as a', 'detalles_salidas_empaque.id_material', '=', 'a.id')
       ->select('a.idFormaEmpaque')
       ->join('forma_empaques as f','f.id','=','a.idFormaEmpaque')
       ->join('salidasempaques as e', 'detalles_salidas_empaque.idSalidaEmpaque', '=', 'e.id')
       ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
       ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
       ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
       ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
       ->select('detalles_salidas_empaque.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','f.formaEmpaque as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->first();

       $salida=DB::table('detalles_salidas_empaque')->where('detalles_salidas_empaque.idSalidaEmpaque','=',$id)
       ->join('almacenempaque as a', 'detalles_salidas_empaque.id_material', '=', 'a.id')
       ->select('a.idFormaEmpaque')
       ->join('forma_empaques as f','f.id','=','a.idFormaEmpaque')
       ->join('salidasempaques as e', 'detalles_salidas_empaque.idSalidaEmpaque', '=', 'e.id')
       ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
       ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
       ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
       ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
       ->select('detalles_salidas_empaque.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','f.formaEmpaque as nombreMaterial','a.descripcion as descripcion','a.codigo as codigo','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->get(); 

       return view("almacen.empaque.salidas.reporte",["salidas"=>$salidas,'salida'=>$salida]);

     }

     public function pdfsalidaempaques($id) {
       $salidas=DB::table('detalles_salidas_empaque')->where('detalles_salidas_empaque.idSalidaEmpaque','=',$id)
       ->join('almacenempaque as a', 'detalles_salidas_empaque.id_material', '=', 'a.id')
       ->select('a.idFormaEmpaque')
       ->join('forma_empaques as f','f.id','=','a.idFormaEmpaque')
       ->join('salidasempaques as e', 'detalles_salidas_empaque.idSalidaEmpaque', '=', 'e.id')
       ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
       ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
       ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
       ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
       ->select('detalles_salidas_empaque.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','f.formaEmpaque as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->first();

       $salida=DB::table('detalles_salidas_empaque')->where('detalles_salidas_empaque.idSalidaEmpaque','=',$id)
       ->join('almacenempaque as a', 'detalles_salidas_empaque.id_material', '=', 'a.id')
       ->select('a.idFormaEmpaque')
       ->join('forma_empaques as f','f.id','=','a.idFormaEmpaque')
       ->join('salidasempaques as e', 'detalles_salidas_empaque.idSalidaEmpaque', '=', 'e.id')
       ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
       ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
       ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
       ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
       ->select('detalles_salidas_empaque.*','e.id as idSalida','e.destino as DestinoF','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','f.formaEmpaque as nombreMaterial','a.descripcion as descripcion','a.codigo as codigo','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->get(); 
       
       $date = date('Y-m-d');
       $invoice = "2222";
       $view =  \View::make('almacen.empaque.salidas.invoice', compact('date', 'invoice','salida','salidas'))->render();
       $pdf = \App::make('dompdf.wrapper');
       $pdf->loadHTML($view);
       return $pdf->stream('invoice');

     }



   }
