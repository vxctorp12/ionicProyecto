<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    // Campos permitidos
    protected $fillable = ['nombre', 'grado_id'];

    // Relación: Una Materia pertenece a un Grado
    // Esto permite que Materia::with('grado')->get() funcione en el controlador
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }
    
    // Relación: Una Materia tiene muchas Notas
    public function notas()
    {
        return $this->hasMany(Nota::class); // Crearemos el modelo Nota después
    }
}