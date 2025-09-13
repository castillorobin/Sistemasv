@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="card">
    <div class="card-header">
    <h2>Editar Cliente</h2>
    </div>
 <div class="card-body">
    <form action="{{ route('clientes.update', $cliente) }}" method="POST">
        @csrf
        @method('PUT')
        <label class="form-label">Nombre:</label>
        <input class="form-control" type="text" name="nombre" value="{{ $cliente->nombre }}" required>
        <br>
        <label class="form-label">Tipo Documento:</label>
        <input class="form-control" type="text" name="tipo_documento" value="{{ $cliente->tipo_documento }}">
        <br>
        <label class="form-label">Número Documento:</label>
        <input class="form-control" type="text" name="numero_documento" value="{{ $cliente->numero_documento }}">
        <br>
        <button class="btn btn-primary" type="submit">Actualizar</button>
    </form>
    </div>
</div>
@endsection