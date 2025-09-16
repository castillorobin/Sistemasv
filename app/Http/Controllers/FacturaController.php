<?php

namespace App\Http\Controllers;


use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class FacturaController extends Controller
{
public function index()
{
$facturas = Factura::latest()->paginate(10);
return view('facturas.index', compact('facturas'));
}


public function create()
{
$clientes = Cliente::all();
$productos = Producto::all();
return view('facturas.create', compact('clientes', 'productos'));
}


public function store(Request $request)
{
$request->validate([
'cliente_id' => 'required',
'tipo' => 'required',
'productos' => 'required|array',
]);


DB::transaction(function () use ($request) {
$cliente = Cliente::findOrFail($request->cliente_id);


$factura = Factura::create([
'cliente_id' => $cliente->id,
'tipo' => $request->tipo,
'fecha' => now(),
'numero' => 'F'.str_pad(Factura::max('id')+1, 6, '0', STR_PAD_LEFT),
'total_sin_iva' => 0,
'iva' => 0,
'total' => 0,
]);


$total = 0;
$subtotal = 0;


foreach ($request->productos as $item) {
$producto = Producto::findOrFail($item['producto_id']);
$cantidad = $item['cantidad'];
$precio_unitario = $producto->precio_venta;
$subtotal_detalle = $precio_unitario * $cantidad;


FacturaDetalle::create([
'factura_id' => $factura->id,
'producto_id' => $producto->id,
'cantidad' => $cantidad,
'precio_unitario' => $precio_unitario,
'subtotal' => $subtotal_detalle,
]);


// Descontar stock
$producto->stock -= $cantidad;
$producto->save();


$subtotal += $subtotal_detalle;
}


$iva = $subtotal * 0.13;
$total = $subtotal + $iva;


$factura->update([
'total_sin_iva' => $subtotal,
'iva' => $iva,
'total' => $total,
]);
});


return redirect()->route('facturas.index')->with('success', 'Factura registrada');
}
}