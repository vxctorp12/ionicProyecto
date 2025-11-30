<?php

namespace App\Http\Controllers;

use App\Models\Grado;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class GradoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [ new Middleware('auth:api') ];
    }

    public function index()
    {
        return Grado::all();
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100']);
        return Grado::create($request->all());
    }

    public function show($id)
    {
        return Grado::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $grado = Grado::findOrFail($id);
        $request->validate(['nombre' => 'required|string|max:100']);
        $grado->update($request->all());
        return $grado;
    }

    public function destroy($id)
    {
        Grado::destroy($id);
        return response()->json(['message' => 'Grado eliminado']);
    }
}