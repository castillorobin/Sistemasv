@extends('layouts.app')
@section('content')
<div class="card">
<div class="card-header">
<h5>Emitir Factura</h5>
</div>
<div class="card-body">
<form action="{{ route('facturas.store') }}" method="POST">
@csrf
<div class="row mb-3">
<div class="col-md-6">
<label class="form-label">Cliente</label>
<select name="cliente_id" class="form-select" required>
<option value="">-- Seleccione --</option>
@foreach($clientes as $cliente)
<option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
@endforeach
</select>
</div>
<div class="col-md-3">
<label class="form-label">Tipo</label>
<select name="tipo" class="form-select" required>
<option value="consumidor">Consumidor Final</option>
<option value="ccf">Crédito Fiscal</option>
</select>
</div>
</div>


<hr>
<h6>Detalle de Productos</h6>
<div id="productos-wrapper">
<div class="row mb-2 producto-item">
<div class="col-md-6">
<select name="productos[0][producto_id]" class="form-select" required>
<option value="">-- Seleccione producto --</option>
@foreach($productos as $prod)
<option value="{{ $prod->id }}">{{ $prod->nombre }}</option>
@endforeach
</select>
</div>
<div class="col-md-3">
<input type="number" name="productos[0][cantidad]" class="form-control" placeholder="Cantidad" min="1" required>
</div>
<div class="col-md-3">
<button type="button" class="btn btn-danger remove-item">X</button>
</div>
</div>
</div>
<button type="button" id="add-producto" class="btn btn-sm btn-secondary mb-3">+ Agregar producto</button>


<div>
<button class="btn btn-success">Guardar Factura</button>
<a href="{{ route('facturas.index') }}" class="btn btn-secondary">Cancelar</a>
</div>
</form>
</div>
</div>


<script>
let index = 1;
document.getElementById('add-producto').addEventListener('click', function () {
const wrapper = document.getElementById('productos-wrapper');
const item = document.querySelector('.producto-item').cloneNode(true);


// Resetear inputs
item.querySelectorAll('input').forEach(input => input.value = '');
item.querySelectorAll('select').forEach(select => select.selectedIndex = 0);


// Renombrar los name[]
item.querySelectorAll('select, input').forEach(field => {
field.name = field.name.replace(/\[\d+\]/, `[${index}]`);
});


index++;
wrapper.appendChild(item);
});


document.addEventListener('click', function (e) {
if (e.target.classList.contains('remove-item')) {
const items = document.querySelectorAll('.producto-item');
if (items.length > 1) e.target.closest('.producto-item').remove();
}
});
</script>
@endsection