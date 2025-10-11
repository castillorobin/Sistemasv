<?php

// app/Http/Controllers/CajaController.php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function index()
    {
        $cajas = Caja::with('user')->orderByDesc('id')->paginate(10);
        return view('cajas.index', compact('cajas'));
    }

    public function create()
    {
        return view('cajas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        $userId = Auth::id();

        // Validar que no tenga una caja abierta
        $cajaAbierta = Caja::where('user_id', $userId)
            ->where('estado', 'abierta')
            ->first();

        if ($cajaAbierta) {
            return back()->with('error', 'Ya tienes una caja abierta.');
        }

        Caja::create([
            'user_id' => $userId,
            'fecha_apertura' => now(),
            'monto_inicial' => $request->monto_inicial,
            'estado' => 'abierta',
        ]);

        return redirect()->route('cajas.index')->with('success', 'Caja abierta exitosamente.');
    }
}