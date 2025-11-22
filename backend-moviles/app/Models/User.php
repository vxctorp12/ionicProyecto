<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject; 
class User extends Authenticatable implements JWTSubject 
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