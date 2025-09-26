<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $fillable = ['fecha', 'total'];
    
    public function detalles()
{
    return $this->hasMany(CompraDetalle::class);
}
}
