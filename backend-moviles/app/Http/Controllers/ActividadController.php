<?php
namespace App\Http\Controllers;
use App\Models\Actividad;
use Illuminate\Http\Request;

class ActividadController extends Controller {
    
    public function index(Request $request) {
        $query = Actividad::query();
        if ($request->has('materia_id')) $query->where('materia_id', $request->materia_id);
        if ($request->has('periodo')) $query->where('periodo', $request->periodo);
        return $query->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'materia_id' => 'required',
            'nombre' => 'required|string',
            'periodo' => 'required|string'
        ]);
        return Actividad::create($validated);
    }
    
    public function destroy($id) {
        Actividad::destroy($id);
        return response()->json(['msg' => 'Eliminado']);
    }
}