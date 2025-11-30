<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NotaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [ new Middleware('auth:api') ];
    }

    public function index(Request $request)
    {
        $query = Nota::with('actividad'); 

        if ($request->has('actividad_id')) {
            $query->where('actividad_id', $request->actividad_id);
        }

        if ($request->has('matricula_id')) {
            $query->where('matricula_id', $request->matricula_id);
        }

        if ($request->has('materia_id')) {
             $query->whereHas('actividad', function($q) use ($request) {
                 $q->where('materia_id', $request->materia_id);
             });
        }

        return $query->get();
    }

    public function store(Request $request) {
    $request->validate([
        'matricula_id' => 'required',
        'actividad_id' => 'required',
        'valor' => 'required|numeric|min:0|max:10'
    ]);

    $nota = Nota::updateOrCreate(
        [
            'matricula_id' => $request->matricula_id,
            'actividad_id' => $request->actividad_id
        ],
        ['valor' => $request->valor]
    );
    return response()->json($nota, 201);
}
}