<?php

namespace App\Http\Controllers;

// 1. Importa estas dos clases OBLIGATORIAS en Laravel 12
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

// 2. Implementa la interfaz "HasMiddleware"
class AuthController extends Controller implements HasMiddleware
{
    // 3. BORRA la función __construct() antigua.
    // Y agrega esta función estática nueva:
    public static function middleware(): array
    {
        return [
            // Aquí definimos que 'auth:api' proteja todo EXCEPTO el login
            new Middleware('auth:api', except: ['login']),
        ];
    }

    // ... El resto de tus funciones (login, me, logout) se quedan IGUAL ...
    
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
    public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed', // 'confirmed' busca new_password_confirmation
    ]);

    $user = auth()->user();

    // Verificar que la contraseña actual sea correcta
    if (!Hash::check($request->current_password, $user->password)) {
        throw ValidationException::withMessages([
            'current_password' => ['La contraseña actual es incorrecta.'],
        ]);
    }

    // Actualizar
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json(['message' => 'Contraseña actualizada']);
}
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}