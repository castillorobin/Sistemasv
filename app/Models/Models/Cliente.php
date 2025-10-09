<?php

namespace App\Models\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
              'nombre',
    'nombre_comercial',
    'nit',
    'dui',
    'nrc',
    'telefono',
    'correo',
    'actividad_economica_id',
    'departamento_id',
    'municipio_id',
    ];

    public function actividadEconomica()
{
    return $this->belongsTo(ActividadEconomica::class);
}

public function departamento()
{
    return $this->belongsTo(Departamento::class);
}

public function municipio()
{
    return $this->belongsTo(Municipio::class);
}
}
