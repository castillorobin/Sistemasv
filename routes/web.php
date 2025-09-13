<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteWebController;
use App\Http\Controllers\ProductoWebController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\AjusteInventarioController;
use App\Http\Controllers\FacturaController;
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


    Route::resource('facturas', FacturaController::class)->only(['index', 'create', 'store']);


});



require __DIR__.'/auth.php';
