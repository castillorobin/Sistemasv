@extends('layouts.app')

@section('title', 'Kardex - Historial de Movimientos')

@section('content')
<div class="container">
    <h3 class="mb-4">Kardex</h3>

    {{-- Filtros --}}
    <form action="{{ route('kardex.index') }}" method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>Producto</label>
            <select name="producto_id" class="form-select">
                <option value="">Todos</option>
                @foreach ($productos as $producto)
                    <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                        {{ $producto->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>Desde</label>
            <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
        </div>

        <div class="col-md-3">
            <label>Hasta</label>
            <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Filtrar</button>
        </div>
    </form>

    {{-- Tabla --}}
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Fecha</th>
                <th>Producto</th>
                <th>Tipo</th>
                <th>Cantidad</th>
                
                <th>Stock Final</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kardex as $mov)
                <tr>
                    <td>{{ $mov->fecha->format('d/m/Y H:i') }}</td>
                    <td>{{ $mov->producto->nombre }}</td>
                    <td>
                        <span class="badge {{ $mov->tipo === 'entrada' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($mov->tipo_movimiento) }}
                        </span>
                    </td>
                    <td>{{ $mov->cantidad }}</td>
                    
                    <td>{{ $mov->stock_final }}</td>
                    <td>
                        <a href="{{ route('kardex.detalle', $mov->producto_id) }}" class="btn btn-sm btn-primary">
                            Ver Historial
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No hay movimientos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
