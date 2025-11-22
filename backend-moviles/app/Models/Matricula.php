<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matricula extends Model
{
    use HasFactory;

    // Campos que permitimos llenar
    protected $fillable = [
        'user_id', 
        'grado_id', 
        'anio'
    ];

    // Relación: Una matrícula pertenece a un Alumno (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Una matrícula pertenece a un Grado
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }
}