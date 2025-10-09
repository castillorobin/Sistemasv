<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class DocumentoDTE extends Model
{
protected $fillable = [
'sello_recibido','codigo_generacion','numero_control','factura','fecha_generacion','tipo_dte',
'json_original_path','json_legible_path','json_firmado_path','pdf_path',
// Contingencia
'es_contingencia','tipo_contingencia','motivo_contingencia','fecha_contingencia',
'regularizado','fecha_regularizacion','error_regularizacion',
];


protected $casts = [
'es_contingencia' => 'boolean',
'regularizado' => 'boolean',
'fecha_contingencia' => 'datetime',
'fecha_regularizacion' => 'datetime',
];
}