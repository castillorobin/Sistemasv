<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Models\Cliente;

class Factura extends Model
{
    protected $fillable = [
'cliente_id', 'tipo', 'fecha', 'total', 'total_sin_iva', 'iva', 'numero'
];


public function cliente()
{
return $this->belongsTo(Cliente::class);
}


public function detalles()
{
return $this->hasMany(FacturaDetalle::class);
}
}
