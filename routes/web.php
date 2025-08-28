<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClienteWebController;

Route::get('/clientes', [ClienteWebController::class, 'index'])->name('clientes.index');
Route::get('/clientes/create', [ClienteWebController::class, 'create'])->name('clientes.create');
Route::post('/clientes', [ClienteWebController::class, 'store'])->name('clientes.store');
Route::get('/clientes/{cliente}/edit', [ClienteWebController::class, 'edit'])->name('clientes.edit');
Route::put('/clientes/{cliente}', [ClienteWebController::class, 'update'])->name('clientes.update');
Route::delete('/clientes/{cliente}', [ClienteWebController::class, 'destroy'])->name('clientes.destroy');



Route::get('/', function () {
    return view('welcome');
});
