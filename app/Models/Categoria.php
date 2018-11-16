<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;

class Categoria extends Model
{
    protected $table = "categorias";
	public $timestamps = false;

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'id_categoria');
    }
}
