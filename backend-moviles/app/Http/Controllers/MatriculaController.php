<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MatriculaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [ new Middleware('auth:api') ];
    }

    public function index(Request $request)
    {
        $query = Matricula::with(['user', 'grado.materias']); 

        if ($request->has('grado_id')) {
            $query->where('grado_id', $request->grado_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $query->where('anio', date('Y'));

        return $query->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'grado_id' => 'required|exists:grados,id'
        ]);

        $alumno = User::find($request->user_id);
        if ($alumno->role_id !== 3) {
            return response()->json(['message' => 'El usuario seleccionado no es un alumno'], 400);
        }

        $existe = Matricula::where('user_id', $request->user_id)
                            ->where('grado_id', $request->grado_id)
                            ->where('anio', date('Y'))
                            ->exists();

        if ($existe) {
            return response()->json(['message' => 'El alumno ya está matriculado en este grado'], 409);
        }

        $matricula = Matricula::create([
            'user_id' => $request->user_id,
            'grado_id' => $request->grado_id,
            'anio' => date('Y')
        ]);

        return response()->json($matricula, 201);
    }

    public function destroy($id)
    {
        Matricula::destroy($id);
        return response()->json(['message' => 'Matrícula eliminada']);
    }
}