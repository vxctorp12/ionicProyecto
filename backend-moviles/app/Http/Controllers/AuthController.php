<?php

namespace App\Http\Controllers;

// 1. Importa estas dos clases OBLIGATORIAS en Laravel 12
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

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