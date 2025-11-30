<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grado extends Model
{
    use HasFactory;

    // Campos que permitimos llenar desde la API
    protected $fillable = ['nombre'];

    // Relación: Un Grado tiene muchas Materias
    // Esto nos servirá si quisieras obtener un grado con todas sus materias
    public function materias()
    {
        return $this->hasMany(Materia::class);
    }

    // Relación: Un Grado tiene muchos Alumnos (a través de la tabla matriculas)
    // Lo usaremos más adelante para ver quiénes están inscritos
    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }
}