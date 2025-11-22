<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MateriaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [ new Middleware('auth:api') ];
    }

    public function index(Request $request)
    {
        if ($request->has('grado_id')) {
            return Materia::where('grado_id', $request->grado_id)->get();
        }
        return Materia::with('grado')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'grado_id' => 'required|exists:grados,id'
        ]);
        return Materia::create($request->all());
    }

    public function show($id)
    {
        return Materia::with('grado')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $materia = Materia::findOrFail($id);
        $request->validate([
            'nombre' => 'sometimes|string|max:100',
            'grado_id' => 'sometimes|exists:grados,id'
        ]);
        $materia->update($request->all());
        return $materia;
    }

    public function destroy($id)
    {
        Materia::destroy($id);
        return response()->json(['message' => 'Materia eliminada']);
    }
}