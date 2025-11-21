<?php

namespace App\Http\Controllers;

use App\Models\DocenteMateria;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DocenteMateriaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [ new Middleware('auth:api') ];
    }

    // LISTAR: Permite filtrar por docente (?user_id=5)
    public function index(Request $request)
    {
        $query = DocenteMateria::with(['docente', 'materia.grado']);

        if ($request->has('user_id')) { // Ver materias de un profe específico
            $query->where('user_id', $request->user_id);
        }

        return $query->get();
    }

    // ASIGNAR
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id', // El docente
            'materia_id' => 'required|exists:materias,id'
        ]);

        // Evitar duplicados
        $existe = DocenteMateria::where('user_id', $request->user_id)
                                ->where('materia_id', $request->materia_id)
                                ->exists();
        
        if ($existe) return response()->json(['message' => 'Asignación ya existe'], 409);

        return DocenteMateria::create($request->all());
    }

    // ELIMINAR ASIGNACIÓN
    public function destroy($id)
    {
        DocenteMateria::destroy($id);
        return response()->json(['message' => 'Asignación eliminada']);
    }
}