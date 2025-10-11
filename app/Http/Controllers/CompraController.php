<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Producto;
use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor;

class CompraController extends Controller
{
        public function index()
    {
        $compras = \App\Models\Compra::with('detalles.producto')->orderBy('fecha', 'desc')->get();
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
       $productos = Producto::orderBy('nombre')->get();
    $proveedores = Proveedor::orderBy('nombre')->get();

    return view('compras.create', compact('productos', 'proveedores'));
    }

    public function store(Request $request)
{
    if (!obtenerCajaAbiertaUsuario()) {
    return back()->with('error', 'Debe abrir caja antes de realizar esta operación.');
}
    $caja = obtenerCajaAbiertaUsuario();
$factura = null; // <-- declarar aquí
       

    $request->validate([
        'fecha' => 'required|date',
        'proveedor_id' => 'nullable|exists:proveedores,id',
        'productos' => 'required|array|min:1',
        'productos.*.producto_id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|integer|min:1',
        'productos.*.precio' => 'required|numeric|min:0',
    ]);

    DB::transaction(function () use ($request) {
        $compra = Compra::create([
            'fecha' => $request->fecha,
            'proveedor_id' => $request->proveedor_id,
            'total' => 0,
        ]);

        $total = 0;

        foreach ($request->productos as $item) {
            $producto = Producto::findOrFail($item['producto_id']);
            $cantidad = $item['cantidad'];
            $precio = $item['precio'];
            $subtotal = $cantidad * $precio;

            CompraDetalle::create([
                'compra_id' => $compra->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $subtotal,
            ]);

            // Calcular nuevo stock y precio promedio
            $stock_anterior = $producto->stock ?? 0;
            $precio_anterior = $producto->precio_costo ?? 0;
            $nuevo_stock = $stock_anterior + $cantidad;

            $nuevo_precio_costo = $nuevo_stock > 0
                ? (($stock_anterior * $precio_anterior) + ($cantidad * $precio)) / $nuevo_stock
                : $precio;

            $producto->update([
                'stock' => $nuevo_stock,
                'precio_costo' => $nuevo_precio_costo,
            ]);

            $total += $subtotal;
        }

        $compra->update(['total' => $total]);
    });

     if ($caja && $factura) {
            MovimientoCaja::create([
                'caja_id' => $caja->id,
                'tipo' => 'egreso',
                'monto' => $total,
                'descripcion' => 'Compra registrada - ID: ' . $compra->id,
                'fecha' => now(),
                'referencia_id' => $compra->id,
                'referencia_type' => \App\Models\Compra::class,
                'user_id' => auth()->id(), // ← este campo es obligatorio
            ]);
        } 

    return redirect()->route('compras.index')->with('success', 'Compra registrada correctamente.');
}
}