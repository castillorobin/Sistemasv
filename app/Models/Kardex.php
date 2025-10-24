<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    
    protected $table = 'kardexes';
    protected $fillable = [
    'producto_id',
    'fecha',
    'tipo_movimiento',
    'documento_referencia',
    'entrada',
    'cantidad',
    'stock_final',
    'salida',
    'saldo',
    'precio_unitario',
    'total',
    ];
 protected $casts = [
        'fecha' => 'datetime',
    ];

    public function producto()
    {
    return $this->belongsTo(Producto::class);
    }
}
