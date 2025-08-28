@extends('layouts.app')

@section('title', 'Agregar Cliente')

@section('content')
    <h2>Agregar Cliente</h2>

    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tipo Documento</label>
            <input type="text" name="tipo_documento" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Número Documento</label>
            <input type="text" name="numero_documento" class="form-control">
        </div>

        <button class="btn btn-success">Guardar</button>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection
