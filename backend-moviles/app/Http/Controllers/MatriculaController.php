<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Models\User; // Para validar que el usuario sea alumno
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MatriculaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [ new Middleware('auth:api') ];
    }

    // LISTAR: Permite filtrar por grado (ej: /api/matriculas?grado_id=1)
    // Esto es vital para ver la "Lista de Asistencia" de un salón.
    public function index(Request $request)
    {
        // Cargamos 'grado.materias' para saber qué materias tiene ese grado
        $query = Matricula::with(['user', 'grado.materias']); 

        if ($request->has('grado_id')) {
            $query->where('grado_id', $request->grado_id);
        }

        // --- AGREGAR ESTO ---
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        // --------------------

        // Opcional: filtrar por año actual
        $query->where('anio', date('Y'));

        return $query->get();
    }

    // INSCRIBIR (CREAR)
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'grado_id' => 'required|exists:grados,id'
        ]);

        // 1. Validar que el usuario sea realmente un ALUMNO (Role ID 3)
        $alumno = User::find($request->user_id);
        if ($alumno->role_id !== 3) {
            return response()->json(['message' => 'El usuario seleccionado no es un alumno'], 400);
        }

        // 2. Evitar duplicados (mismo alumno, mismo grado, mismo año)
        $existe = Matricula::where('user_id', $request->user_id)
                            ->where('grado_id', $request->grado_id)
                            ->where('anio', date('Y'))
                            ->exists();

        if ($existe) {
            return response()->json(['message' => 'El alumno ya está matriculado en este grado'], 409);
        }

        // 3. Crear Matrícula
        $matricula = Matricula::create([
            'user_id' => $request->user_id,
            'grado_id' => $request->grado_id,
            'anio' => date('Y')
        ]);

        return response()->json($matricula, 201);
    }

    // DES-MATRICULAR (BORRAR)
    public function destroy($id)
    {
        Matricula::destroy($id);
        return response()->json(['message' => 'Matrícula eliminada']);
    }
}