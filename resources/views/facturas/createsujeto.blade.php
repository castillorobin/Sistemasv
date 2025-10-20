@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Factura - Sujeto Excluido</h5>
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
    </div>
    <div class="card-body">
        <form action="{{ route('facturas.sujeto_excluido.generar') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="proveedor_id" class="form-label">Proveedor (Sujeto Excluido)</label>
                <select name="proveedor_id" id="proveedor_id" class="form-control" required>
                    <option value="">Seleccione</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <hr>
            <h5>Productos</h5>
            <table class="table table-bordered" id="tabla-productos">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="row">
                <div class="col-md-4">
                    <label>Descripción</label>
                    <input type="text" id="descripcion" class="form-control">
                </div>
                <div class="col-md-2">
                    <label>Precio</label>
                    <input type="number" id="precio" class="form-control" step="0.01">
                </div>
                <div class="col-md-2">
                    <label>Cantidad</label>
                    <input type="number" id="cantidad" class="form-control" value="1" min="1">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="agregar-producto" class="btn btn-primary">Agregar</button>
                </div>
            </div>

            <input type="hidden" name="productos_json" id="productos_json">

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Generar Factura</button>
                <a href="{{ route('facturas.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
    let productos = [];

    function renderTabla() {
        const tbody = document.querySelector("#tabla-productos tbody");
        tbody.innerHTML = "";

        productos.forEach((prod, index) => {
            const subtotal = (prod.precio * prod.cantidad).toFixed(2);
            const row = `
                <tr>
                    <td>${prod.descripcion}</td>
                    <td>$${parseFloat(prod.precio).toFixed(2)}</td>
                    <td>${prod.cantidad}</td>
                    <td>$${subtotal}</td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})">Eliminar</button></td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        document.getElementById('productos_json').value = JSON.stringify(productos);
    }

    function eliminarProducto(index) {
        productos.splice(index, 1);
        renderTabla();
    }

    document.getElementById('agregar-producto').addEventListener('click', function () {
        const descripcion = document.getElementById('descripcion').value.trim();
        const precio = parseFloat(document.getElementById('precio').value);
        const cantidad = parseInt(document.getElementById('cantidad').value);

        if (!descripcion || isNaN(precio) || isNaN(cantidad) || precio <= 0 || cantidad <= 0) {
            alert("Complete correctamente todos los campos del producto.");
            return;
        }

        productos.push({ descripcion, precio, cantidad });
        renderTabla();

        // Reset campos
        document.getElementById('descripcion').value = '';
        document.getElementById('precio').value = '';
        document.getElementById('cantidad').value = 1;
    });
</script>
@endsection
