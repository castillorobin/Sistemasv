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
        'productos_json' => 'required|string',
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

        $items = json_decode($request->input('productos_json'), true);
        if (!is_array($items) || empty($items)) {
            throw new \Exception('No se proporcionaron productos válidos.');
        }

        $subtotal = 0;

        foreach ($items as $item) {
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

public function show(Factura $factura)
{
    // Cargar detalles relacionados si no están con lazy loading
    $factura->load('cliente', 'detalles.producto');

    return view('facturas.show', compact('factura'));
}

public function destroy(Factura $factura)
{
    DB::transaction(function () use ($factura) {
        // Cargar los detalles con sus productos
        $factura->load('detalles.producto');

        // Restaurar el stock de cada producto
        foreach ($factura->detalles as $detalle) {
            $producto = $detalle->producto;
            $producto->stock += $detalle->cantidad;
            $producto->save();
        }

        // Eliminar los detalles (si tienes restricción en DB puede que se eliminen en cascada)
        $factura->detalles()->delete();

        // Eliminar la factura
        $factura->delete();
    });

    return redirect()->route('facturas.index')->with('success', 'Factura eliminada y stock restaurado.');
}

}