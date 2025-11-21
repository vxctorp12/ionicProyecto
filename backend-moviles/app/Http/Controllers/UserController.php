<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    // Seguridad: Solo usuarios autenticados pueden tocar esto
    public static function middleware(): array
    {
        return [
            new Middleware('auth:api'),
        ];
    }

    // GET: Listar usuarios
    // GET: Listar usuarios (con filtro opcional)
    public function index(Request $request)
    {
        // Iniciamos la consulta
        $query = User::query();

        // Si la URL trae ?role_id=X, aplicamos el filtro
        if ($request->has('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Retornamos los resultados ordenados
        return $query->orderBy('id', 'desc')->get();
    }

    // POST: Crear usuario
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|integer|in:1,2,3' // 1:Admin, 2:Docente, 3:Alumno
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    // PUT: Actualizar usuario
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role_id' => 'sometimes|integer|in:1,2,3'
        ]);

        // Solo actualizamos la contraseña si el usuario escribió una nueva
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); // Quitamos el campo para no sobreescribirlo vacío
        }

        $user->update($validated);

        return response()->json($user);
    }

    // DELETE: Eliminar usuario
    public function destroy(string $id)
    {
        User::destroy($id);
        return response()->json(['message' => 'Usuario eliminado']);
    }
}