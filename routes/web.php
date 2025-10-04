<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteWebController;
use App\Http\Controllers\ProductoWebController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AjusteInventarioController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DTEController;
use App\Http\Controllers\ProveedorController;
/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

     Route::get('/clientes', [ClienteWebController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/create', [ClienteWebController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClienteWebController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{cliente}/edit', [ClienteWebController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{cliente}', [ClienteWebController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteWebController::class, 'destroy'])->name('clientes.destroy');


    Route::get('/productos', [ProductoWebController::class, 'index'])->name('productos.index');
    Route::get('/productos/create', [ProductoWebController::class, 'create'])->name('productos.create');
    Route::post('/productos', [ProductoWebController::class, 'store'])->name('productos.store');
    Route::get('/productos/{producto}/edit', [ProductoWebController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{producto}', [ProductoWebController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoWebController::class, 'destroy'])->name('productos.destroy');


    Route::resource('categorias', CategoriaController::class)->except('show');
    Route::get('ajustes/create', [AjusteInventarioController::class, 'create'])->name('ajustes.create');
    Route::post('ajustes', [AjusteInventarioController::class, 'store'])->name('ajustes.store');

Route::resource('facturas', FacturaController::class);
//    Route::resource('facturas', FacturaController::class)->only(['index', 'create', 'store']);

Route::resource('compras', CompraController::class)->only(['index', 'create', 'store']);


//Admin DTE's
Route::get('/dtes', [DTEController::class, 'index'])->name('dtes.index');
Route::get('/dtes/{id}/json', [DTEController::class, 'descargarJson'])->name('dtes.descargarJson');
Route::get('/dtes/{id}/pdf', [DTEController::class, 'verPdf'])->name('dtes.verPdf');
Route::get('/dtes/descargar-json', [\App\Http\Controllers\DTEController::class, 'descargarJsonLote'])
     ->name('dtes.descargarJsonLote');

//Contingencia
Route::get('/dtes/emitirEnContingencia/{id}', [ContingenciaController::class, 'emitirEnContingencia'])->name('dtes.emitirEnContingencia');


//Proveedores
Route::resource('proveedores', ProveedorController::class)->parameters([
    'proveedores' => 'proveedor'
]);

});



require __DIR__.'/auth.php';
