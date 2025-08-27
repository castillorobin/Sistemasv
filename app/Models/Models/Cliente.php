<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'tipo_documento',
        'numero_documento',
        'nrc',
        'giro',
        'telefono',
        'correo',
        'direccion',
        'departamento',
        'municipio',
    ];
}
