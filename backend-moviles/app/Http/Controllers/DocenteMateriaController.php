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

    public function index(Request $request)
    {
        $query = DocenteMateria::with(['docente', 'materia.grado']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'materia_id' => 'required|exists:materias,id'
        ]);

        $existe = DocenteMateria::where('user_id', $request->user_id)
                                ->where('materia_id', $request->materia_id)
                                ->exists();
        
        if ($existe) return response()->json(['message' => 'Asignación ya existe'], 409);

        return DocenteMateria::create($request->all());
    }

    public function destroy($id)
    {
        DocenteMateria::destroy($id);
        return response()->json(['message' => 'Asignación eliminada']);
    }
}