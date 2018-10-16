<?php

namespace CEPROZAC\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use CEPROZAC\Http\Requests;
use CEPROZAC\Http\Controllers\Controller;
use CEPROZAC\salidasagroquimicos;
use CEPROZAC\Empleado;
use CEPROZAC\AlmacenAgroquimicos;
use CEPROZAC\cantidad_unidades_agro;
use CEPROZAC\unidadesmedida;
use CEPROZAC\DetallesSalidasAgroquimicos;

use DB;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_Worksheet_Drawing;
use Validator; 
use \Milon\Barcode\DNS1D;
use \Milon\Barcode\DNS2D;
use Illuminate\Support\Collection as Collection;  

 
class salidasagroquimicosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()  
    {
     $salida=DB::table('detalles_salidas_agroquimicos')
     ->join('almacenagroquimicos as a', 'detalles_salidas_agroquimicos.id_material', '=', 'a.id')
     ->join('salidasagroquimicos as e', 'detalles_salidas_agroquimicos.idSalidasAgroquimicos', '=', 'e.id')
     ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
     ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
     ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
     ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
     ->select('detalles_salidas_agroquimicos.*','e.id as idSalida','e.destino as DestinoF','e.modulos_aplicados as modulos','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->where('e.estado','=','Activo')->get(); 
     return view('almacen.agroquimicos.salidas.index', ['salida' => $salida]);

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
     $invernadero=DB::table('invernaderos')->where('estado','=' ,'Activo')->get();
     $almacenes=DB::table('almacengeneral')->where('estado','=' ,'Activo')->get(); 
     $provedor = DB::table('provedores_tipo_provedor')
     ->join('provedor_materiales as p', 'provedores_tipo_provedor.idProvedorMaterial', '=', 'p.id')
     ->select('p.*','p.nombre as nombre')
     ->where('provedores_tipo_provedor.idTipoProvedor','2')->get();
     $empresas=DB::table('empresas_ceprozac')->where('estado','=' ,'Activo')->get();
     $unidades  = DB::table('unidades_medidas')
     ->join('nombre_unidades_medidas', 'unidades_medidas.idUnidadMedida', '=', 'nombre_unidades_medidas.id')
     ->select('unidades_medidas.id as idContenedorUnidadMedida','unidades_medidas.nombre','unidades_medidas.cantidad', 'unidades_medidas.idUnidadMedida', 'nombre_unidades_medidas.*')
     ->where('estado', '=', 'Activo')
     ->get();
     $material=DB::table('almacenagroquimicos')->where('almacenagroquimicos.estado','=' ,'Activo')->where('almacenagroquimicos.cantidad','>=','0')
     ->join('unidades_medidas as u', 'almacenagroquimicos.idUnidadMedida', '=', 'u.id')
     ->select('u.idUnidadMedida')
     ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
     ->select('almacenagroquimicos.*','u.nombre as nombreUnidad','n.nombreUnidadMedida as NombreUnidadP','u.cantidad as cantidadMedida')->get();


     $cuenta = count($material);


     if (empty($material)){
       return redirect('almacen/salidas/agroquimicos');

         // return view("almacen.materiales.salidas.create")->with('message', 'No Hay Material Registrado, Favor de Dar de Alta Material Para Poder Acceder a Este Modulo');
     }else if (empty($empleado)) {
       return redirect('almacen/salidas/agroquimicos');

     }else if (empty($provedor)){
       return redirect('almacen/salidas/agroquimicos');


     }
     else{
      return view("almacen.agroquimicos.salidas.create",["material"=>$material,"provedor"=>$provedor,"empleado"=>$empleado,"empresas"=>$empresas,"unidades"=>$unidades,"invernadero"=>$invernadero,"almacenes"=>$almacenes]);

    }

        //
  }
        //

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

     $material= new salidasagroquimicos;
     $destino=$request->get('destino_f');

     $name = explode("_",$destino);
     $material->destino=$name[0];;
     $material->modulos_aplicados=$request->get('num_modulos');
     $material->entrego=$request->get('entrego');
     $material->recibio=$request->get('recibio');
     $material->tipo_movimiento=$request->get('movimiento');
     $material->fecha=$request->get('fecha');
     $material->estado="Activo";
     $material->save();

     $ultimo = salidasagroquimicos::orderBy('id', 'desc')->first()->id;
     $num = 1;
     $y = 0;
     $limite = $request->get('total');

     while ($num <= $limite) {
      $detalle = new DetallesSalidasAgroquimicos;
      $detalle->idSalidasAgroquimicos=$ultimo;
      $producto = $request->get('codigo2');
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
            $material->cantidad=$material->cantidad-$detalle->cantidad;
        //$material->cantidad=$first = $name[$y];
            $y = $y + 1;
            $detalle->save();
            $material->update();


            $num = $num + 1;

          }
        //
          return redirect('/almacen/salidas/agroquimicos');
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

      $invernadero=DB::table('invernaderos')->where('estado','=' ,'Activo')->get();
      $almacenes=DB::table('almacengeneral')->where('estado','=' ,'Activo')->get();

      $materiales=DB::table('almacenagroquimicos')->where('almacenagroquimicos.estado','=' ,'Activo')->where('almacenagroquimicos.cantidad','>=','0')
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


      $salida=DB::table('detalles_salidas_agroquimicos')->where('detalles_salidas_agroquimicos.idSalidasAgroquimicos','=',$id)
      ->join('almacenagroquimicos as a', 'detalles_salidas_agroquimicos.id_material', '=', 'a.id')
      ->join('salidasagroquimicos as e', 'detalles_salidas_agroquimicos.idSalidasAgroquimicos', '=', 'e.id')
      ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('detalles_salidas_agroquimicos.*','e.*','e.id as idSalida')->first(); 


      $salidas=DB::table('detalles_salidas_agroquimicos')->where('detalles_salidas_agroquimicos.idSalidasAgroquimicos','=',$id)
      ->join('almacenagroquimicos as a', 'detalles_salidas_agroquimicos.id_material', '=', 'a.id')
      ->join('salidasagroquimicos as e', 'detalles_salidas_agroquimicos.idSalidasAgroquimicos', '=', 'e.id')
      ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
      ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
      ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('detalles_salidas_agroquimicos.*','e.id as idSalida','e.destino as DestinoF','e.modulos_aplicados as modulos','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->get(); 

        //

      return view("almacen.agroquimicos.salidas.edit",["salidas"=>$salidas,"salida"=>$salida,"empleado"=>$empleado,'materiales'=>$materiales,'almacenes'=>$almacenes,'invernadero'=>$invernadero]);

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
      $material=salidasagroquimicos::findOrFail($id);
      $destino=$request->get('destino_f');

      $name = explode("_",$destino);
      $material->destino=$name[0];
      $material->modulos_aplicados=$request->get('num_modulos');
      $material->entrego=$request->get('entrego');
      $material->recibio=$request->get('recibio');
      $material->tipo_movimiento=$request->get('movimiento');
      $material->fecha=$request->get('fecha');
      $material->update();

      $salidas=DB::table('detalles_salidas_agroquimicos')->where('idSalidasAgroquimicos','=',$id)->get();
      $cuenta = count($salidas);

      for ($x=0; $x < $cuenta  ; $x++) {
        $elimina = DetallesSalidasAgroquimicos::findOrFail($salidas[$x]->id);
        $decrementa=almacenagroquimicos::findOrFail($elimina->id_material);
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
      $detalle = new DetallesSalidasAgroquimicos;
      $detalle->idSalidasAgroquimicos=$id;
      $producto = $request->get('codigo2');
      print_r($producto);
      $first = head($producto);
      $name = explode(",",$first);
            //$first = $name[0];
             //$first = $name[1];
     print_r($first = $name[$y]);
      $detalle->id_material=$first = $name[$y];
            $material=almacenagroquimicos::findOrFail($first = $name[$y]);//id del producto
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

      return redirect('almacen/salidas/agroquimicos');
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

 $entradas=DB::table('detalles_salidas_agroquimicos')
     ->where('idSalidasAgroquimicos','=',$id)->get(); 
     $cuenta = count($entradas);
     for ($x=0; $x < $cuenta  ; $x++) {
      $elimina = DetallesSalidasAgroquimicos::findOrFail($entradas[$x]->id);
      $decrementa=almacenagroquimicos::findOrFail($elimina->id_material);
      $decrementa->cantidad=$decrementa->cantidad+ $elimina->cantidad;
      $decrementa->update();
        //$elimina->delete();PENDIENTE CHECAR SI SE ELIMINA O SE QUEDA
        # code...
    }
    $entrada = salidasagroquimicos::findOrFail($id);
    $entrada->estado="Inactivo";
    $entrada->update();
  return Redirect::to('/almacen/salidas/agroquimicos');   

        //
}



        public function excel()
  {         
        /**
         * toma en cuenta que para ver los mismos 
         * datos debemos hacer la misma consulta
        **/
        Excel::create('salidasagroquimicos', function($excel) {
          $excel->sheet('Excel sheet', function($sheet) {
                //otra opción -> $products = Product::select('name')->get();
           $salida=DetallesSalidasAgroquimicos::
           join('almacenagroquimicos as a', 'detalles_salidas_agroquimicos.id_material', '=', 'a.id')
           ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
           ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
           ->join('salidasagroquimicos as e', 'detalles_salidas_agroquimicos.idSalidasAgroquimicos', '=', 'e.id')
           ->join('empleados as Empleado1', 'e.entrego', '=', 'Empleado1.id')
           ->join('empleados as Empleado2', 'e.recibio', '=', 'Empleado2.id')
           ->select('detalles_salidas_agroquimicos.idSalidasAgroquimicos as IdEntrada','a.nombre as nombreMaterial','detalles_salidas_agroquimicos.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','e.fecha as FechaCompra','Empleado1.nombre as NombreEmp1','Empleado1.apellidos as Ape1','Empleado2.nombre','Empleado2.apellidos','e.tipo_movimiento as Observaciónes','e.destino','e.modulos_aplicados')->get();       
           $sheet->fromArray($salida);
           $sheet->row(1,['N°Salida','Material','Cantidad','Medida' ,'Fecha',"Entrego","Apellidos","Recibe en Almacén CEPROZAC","Apellidos",'Observaciónes','Destino','Módulos']);
           $sheet->setOrientation('landscape');
         });
        })->export('xls');
      }


      public function verSalidaAgroquimicos($id){
         $salidas=DB::table('detalles_salidas_agroquimicos')->where('detalles_salidas_agroquimicos.idSalidasAgroquimicos','=',$id)
      ->join('almacenagroquimicos as a', 'detalles_salidas_agroquimicos.id_material', '=', 'a.id')
      ->join('salidasagroquimicos as e', 'detalles_salidas_agroquimicos.idSalidasAgroquimicos', '=', 'e.id')
      ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
      ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
      ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('detalles_salidas_agroquimicos.*','e.id as idSalida','e.destino as DestinoF','e.modulos_aplicados as modulos','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->first();

       $salida=DB::table('detalles_salidas_agroquimicos')->where('detalles_salidas_agroquimicos.idSalidasAgroquimicos','=',$id)
      ->join('almacenagroquimicos as a', 'detalles_salidas_agroquimicos.id_material', '=', 'a.id')
      ->join('salidasagroquimicos as e', 'detalles_salidas_agroquimicos.idSalidasAgroquimicos', '=', 'e.id')
      ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
      ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
      ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('detalles_salidas_agroquimicos.*','e.id as idSalida','e.destino as DestinoF','e.modulos_aplicados as modulos','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','a.descripcion as descripcion','a.codigo as codigo','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->get(); 

      return view("almacen.agroquimicos.salidas.reporte",["salidas"=>$salidas,'salida'=>$salida]);

      }

       public function pdfsalidaAgroquimicos($id) {
           $salidas=DB::table('detalles_salidas_agroquimicos')->where('detalles_salidas_agroquimicos.idSalidasAgroquimicos','=',$id)
      ->join('almacenagroquimicos as a', 'detalles_salidas_agroquimicos.id_material', '=', 'a.id')
      ->join('salidasagroquimicos as e', 'detalles_salidas_agroquimicos.idSalidasAgroquimicos', '=', 'e.id')
      ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
      ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
      ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('detalles_salidas_agroquimicos.*','e.id as idSalida','e.destino as DestinoF','e.modulos_aplicados as modulos','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->first();

       $salida=DB::table('detalles_salidas_agroquimicos')->where('detalles_salidas_agroquimicos.idSalidasAgroquimicos','=',$id)
      ->join('almacenagroquimicos as a', 'detalles_salidas_agroquimicos.id_material', '=', 'a.id')
      ->join('salidasagroquimicos as e', 'detalles_salidas_agroquimicos.idSalidasAgroquimicos', '=', 'e.id')
      ->join('empleados as empleado', 'e.entrego', '=', 'empleado.id')
      ->join('empleados as empleado2', 'e.recibio', '=', 'empleado2.id')
      ->join('unidades_medidas as u', 'a.idUnidadMedida', '=', 'u.id')
      ->join('nombre_unidades_medidas as n', 'u.idUnidadMedida', '=', 'n.id')
      ->select('detalles_salidas_agroquimicos.*','e.id as idSalida','e.destino as DestinoF','e.modulos_aplicados as modulos','e.tipo_movimiento as TipoMov','e.fecha as Fechasalida','a.nombre as nombreMaterial','a.descripcion as descripcion','a.codigo as codigo','u.cantidad as cantidadUnidad','n.nombreUnidadMedida as nombreUnidadMedida','empleado.nombre as nombre1','empleado.apellidos as apellido1','empleado2.nombre as nombre2','empleado2.apellidos as apellido2','u.nombre as UnidadNombre')->get();
      $date = date('Y-m-d');
   $invoice = "2222";
   $view =  \View::make('almacen.agroquimicos.salidas.invoice', compact('date', 'invoice','salida','salidas'))->render();
   $pdf = \App::make('dompdf.wrapper');
   $pdf->loadHTML($view);
   return $pdf->stream('invoice');

       }
    }
