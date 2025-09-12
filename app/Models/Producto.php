<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'unidad',
        'precio_costo',
        'precio_venta',
        'stock',
    ];
}