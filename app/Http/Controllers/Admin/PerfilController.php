<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\Usuario;
use App\Models\Auditoria;
use Auth;

class PerfilController extends Controller
{
    public function index() {
        $usuario = Usuario::select('id','name','number','address','phone','email')
            ->where('number', Auth::user()->number)
            ->first();
    	return view('admin.perfil.index')->with(['usuario' => $usuario, 'current' => 'yo']);
    }

    public function store() {

    }

    public function update(ProfileRequest $request) {
    	$usuario = Usuario::find($request->number);
        $usuario->name = $request->name;
        $usuario->email = $request->email;
        $usuario->phone = $request->phone;
        $usuario->address = $request->address;
        $usuario->save();

        $auditoria = new Auditoria;
        $auditoria->number = Auth::user()->number;
        $auditoria->operacion = 'ACTUALIZACION';
        $auditoria->rama = 'USUARIO';
        $auditoria->detalles_operacion = 'Actualizacion de los datos del usuario C.I: '.$usuario->number.' Nombre:'.$usuario->name;
        $auditoria->save();
        return response()->json(['result' => true, 'text' => 'Genial! Tus datos han sido actualizados!']);
    }
    
    public function destroy() {
    	$destroy = Usuario::find($id);
        $destroy->status = '3';
        $destroy->save();
        
        $auditoria = new Auditoria;
        $auditoria->number = Auth::user()->number;
        $auditoria->operacion = 'BORRADO';
        $auditoria->rama = 'USUARIO';
        $auditoria->detalles_operacion = 'Borrado de los datos y cuenta del usuario C.I: '.$usuario->number.' Nombre:'.$usuario->name;
        $auditoria->save();
        Auth::logout();
        return response()->json(['result' => true, 'text' => 'Que pena! Sin embargo Tu cuenta ha sido borrada, gracias por haber estado con nostros!', 'location' => url('/')]);
    }
}
