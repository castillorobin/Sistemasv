<?php

use App\Models\Caja;
use Illuminate\Support\Facades\Auth;

if (!function_exists('obtenerCajaAbiertaUsuario')) {
    function obtenerCajaAbiertaUsuario()
    {
        return Caja::where('user_id', Auth::id())
            ->whereNull('fecha_cierre')
            ->latest()
            ->first();
    }
}