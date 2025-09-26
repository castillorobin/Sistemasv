<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
        public function index()
    {
        $compras = \App\Models\Compra::with('detalles.producto')->orderBy('fecha', 'desc')->get();
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $productos = Producto::all();
        return view('compras.create', compact('productos'));
    }

    public function store(Request $request)
{
    // Validación de campos
    $request->validate([
        'fecha' => 'required|date',
        'productos' => 'required|array|min:1',
        'productos.*.producto_id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|integer|min:1',
        'productos.*.precio' => 'required|numeric|min:0',
    ]);

    DB::transaction(function () use ($request) {
        // Crear la compra base
        $compra = Compra::create([
            'fecha' => $request->fecha,
            'total' => 0, // se actualiza al final
        ]);

        $total = 0;

        // Recorrer productos enviados desde el formulario
        foreach ($request->productos as $item) {
            $producto = Producto::findOrFail($item['producto_id']);
            $cantidad = $item['cantidad'];
            $precio = $item['precio'];
            $subtotal = $cantidad * $precio;

            // Crear detalle de compra
            CompraDetalle::create([
                'compra_id' => $compra->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $subtotal,
            ]);

            // Actualizar stock y precio de costo
            $producto->stock += $cantidad;
            $producto->precio_costo = $precio; // usar el último precio como costo actual
            $producto->save();

            $total += $subtotal;
        }

        // Actualizar total de la compra
        $compra->update([
            'total' => $total,
        ]);
    });

    return redirect()->route('compras.index')->with('success', 'Compra registrada correctamente.');
}
}