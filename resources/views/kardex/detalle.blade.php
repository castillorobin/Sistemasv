@extends('layouts.app')

@section('title', 'Detalle Kardex')

@section('content')
<div class="container">
    <h3 class="mb-4">Kardex - Detalle del Producto</h3>

    <div class="mb-3">
        <strong>Producto:</strong> {{ $producto->nombre }} <br>
        <strong>Stock Actual:</strong> {{ $producto->stock }}
    </div>

    <table class="table table-striped table-bordered">
        <thead class="table-light">
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Stock Final</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movimientos as $mov)
                <tr>
                    <td>{{ $mov->fecha->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge {{ $mov->tipo === 'entrada' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($mov->tipo) }}
                        </span>
                    </td>
                    <td>{{ $mov->precio_unitario }}</td>
                    <td>{{ $mov->catidad }}</td>
                    <td>{{ $mov->total }}</td>
                    <td>{{ $mov->stock_final }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No hay movimientos registrados para este producto.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('kardex.index') }}" class="btn btn-secondary mt-3">← Volver al Kardex</a>
</div>
@endsection
