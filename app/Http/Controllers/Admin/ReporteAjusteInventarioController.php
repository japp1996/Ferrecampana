<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\MovimientoInventario;
use Auth;

class ReporteAjusteInventarioController extends Controller
{
    public function index()
    {
        //$movimientos = MovimientoInventario::where('tipo_movimiento', ['3','4'])->with('producto')->get();
        $productos = Producto::select('productos.id', 'movimiento_inventario.producto_id', 'productos.name')
        ->join('movimiento_inventario', 'productos.id', 'movimiento_inventario.producto_id')
        ->where('tipo_movimiento', '3')
        ->orWhere('tipo_movimiento', '4')
        ->get();
        return view('admin.inventario.reporte_ajuste')->with(['productos' => $productos]);
    }

    public function store(Request $request)
    {
        if ($request->producto_id == "") {
            return response()->json(['error' => 'Por favor, Seleccione un producto'],422);
        }

        $data = MovimientoInventario::where('producto_id', $request->producto_id)
        ->where('tipo_movimiento', '3')
        ->orWhere('tipo_movimiento', '4')
        ->with('producto')
        ->get();
        
        return response()->json(['result'=>true, 'data' => $data]);
    }
}
