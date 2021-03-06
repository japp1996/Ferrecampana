<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Models\EstadoPedido;
use App\Models\DetallesPedido;
use App\Http\Requests\PedidoRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Auditoria;

class PedidoController extends Controller
{
    public function index() {
        $productos = Producto::select('productos.id','productos.code as idP', 'productos.name', 'productos.stock','productos.id_categoria', 'productos.price', 'productos.unity', 'categorias.code', 'categorias.descripcion_categoria')
            ->join('categorias','productos.id_categoria', 'categorias.code')
            ->where('productos.status', '1')
            ->get();
        $categorias = Categoria::get();
    	return view('admin.pedidos.index')
            ->with(['productos' => $productos, 'categorias' => $categorias, 'current' => 'process']);
    }

    public function create() {

    }

    public function store(Request $request) {
        $pedido = new Pedido;
        $pedido->number = Auth::user()->number;
        $pedido->id_estado = 3;
        $pedido->save();
        foreach ($request->all() as $key) {
            $detalles = new DetallesPedido;
            $detalles->id_pedido = $pedido->id;
            $detalles->id_producto = $key['id_producto'];
            $detalles->cantidad = $key['cantidad'];
            $detalles->precio = $key['precio'];
            $detalles->save();
            $producto = Producto::find($key['id_producto']);
            $producto->stock = $producto->stock - $key['cantidad'];
            $producto->save();
        }

        $auditoria = new Auditoria;
        $auditoria->number = Auth::user()->number;
        $auditoria->operacion = 'REGISTRO';
        $auditoria->rama = 'PEDIDO';
        $auditoria->detalles_operacion = 'Registro de un pedido con el id: '.$pedido->id;
        $auditoria->save();
        return response()->json(['result' => true, 'text' => 'Pedido realizado con éxito']);
    }

    public function show() {
        $pedidos = Pedido::where('id_estado', '!=', '1')->with(['detalles' => function($q){
            $q->with(['productos' => function($query) {
                $query->get();
            }])->get();
        }])->with(['usuario' => function($quer) {
            $quer->where('number', Auth::user()->number)->get();
        }])
        ->get();
    	return view('admin.pedidos.show')->with(['pedidos' => $pedidos, 'current' => 'list']);
    }

    public function edit() {
    	
    }

    public function update() {
    	
    }
    
    public function destroy($id) {
        $destroy = Pedido::find($id);

        $detalles = DetallesPedido::where('id_pedido', $id)->get();
        foreach ($detalles as $key) {
            $producto = Producto::find($key['id_producto']);
            $producto->stock = $producto->stock + $key['cantidad'];
            $producto->save();        
        }
        
        $destroy->id_estado = '1';
        $destroy->save();
        $auditoria = new Auditoria;
        $auditoria->number = Auth::user()->number;
        $auditoria->operacion = 'BORRADO';
        $auditoria->rama = 'PEDIDO';
        $auditoria->detalles_operacion = 'Borrado de un pedido con el id: '.$id;
        $auditoria->save();
        return response()->json(['result' => true, 'text' => 'Genial! Tu Pedido ha sido borrado!']);
    }

    public function get()
    {
        $pedidos = Pedido::where('id_estado', '!=', '1')->with(['detalles' => function($q){
            $q->with(['productos' => function($query) {
                $query->get();
            }])->get();
        }])->with(['usuario' => function($quer) {
            $quer->where('number', Auth::user()->number)->get();
        }])
        ->get();
        return response()->json($pedidos);

    }
}
