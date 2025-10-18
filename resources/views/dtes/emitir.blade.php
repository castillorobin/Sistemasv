@extends('layouts.app')

@section('title', 'Emitir Nota de Crédito')

@section('content')
<div class="container">
    <h3>Emitir Nota de Crédito</h3>

    <div class="alert alert-warning">
        <strong>Importante:</strong> Esta nota de crédito será emitida en referencia al DTE número <strong>{{ $dte->numero_control }}</strong> emitido el <strong>{{ $dte->created_at->format('d/m/Y H:i') }}</strong>.
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('notas-credito.emitirDesdeDTE', $dte->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="motivo">Motivo de la Nota de Crédito</label>
            <textarea name="motivo" id="motivo" class="form-control" rows="3" required>Devolución de productos</textarea>
        </div>

        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Emitir Nota de Crédito</button>
            <a href="{{ route('dtes.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection