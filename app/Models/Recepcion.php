<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DetallesRecepcion;
use App\Models\Usuario;
use App\Models\EstadoPedido;

class Recepcion extends Model
{
    protected $table = "recepcion";
    public $timestamps = false;
    
    public function estado() {
    	return $this->hasOne(EstadoPedido::class, 'id');
    }

    public function detalles() {
    	return $this->hasMany(DetallesRecepcion::class, 'id_recepcion');
    }

    public function usuario()
    {
		return $this->belongsTo(Usuario::class, 'number');
    }
}
