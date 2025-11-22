<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model {
    protected $table = 'actividades';
    protected $fillable = ['materia_id', 'nombre', 'periodo', 'porcentaje'];

    // Una actividad tiene muchas notas (una de cada alumno)
    public function notas() {
        return $this->hasMany(Nota::class);
    }
}