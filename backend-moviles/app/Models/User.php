<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; // <--- IMPORTANTE

class User extends Authenticatable implements JWTSubject // <--- IMPORTANTE
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // Asegúrate de que este campo esté aquí
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // --- AGREGA ESTOS DOS MÉTODOS OBLIGATORIOS PARA JWT ---

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role_id' => $this->role_id, // Guardamos el rol en el token para usarlo en el Frontend
        ];
    }
}